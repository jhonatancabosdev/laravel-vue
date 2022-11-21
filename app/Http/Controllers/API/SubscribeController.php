<?php 

namespace App\Http\Controllers\API;

use App\Subscriber;
use Ctct\Auth\CtctOAuth2;
use Ctct\ConstantContact;
use Illuminate\Support\Collection;
use Ctct\Exceptions\CtctException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Ctct\Exceptions\OAuth2Exception;
use App\Events\SubscriberCreateEvent;
use Ctct\Components\Contacts\Contact;
use App\Http\Requests\SubscribeRequest;

class SubscribeController extends Controller
{
	private $key;
	private $token;
	private $list;

	public function __construct() {
		$this->key = config('services.constant_contact.api_key');
		// $this->token = $this->getAccessToken();
		$this->token = config('services.constant_contact.access_token');
		$this->list = config('services.constant_contact.mailing_list');
	}

	/**
	 * Store new email
	 * 
	 * @param  SubscribeRequest $request [description]
	 * @return [type]                    [description]
	 */
	public function store(SubscribeRequest $request)
	{
		// create new subscription
		$subscriber = tap(new Subscriber(), function($subscriber) use ($request){
			$subscriber->name = $request->name;
			$subscriber->email = $request->email;
			$subscriber->save();
		});

		$this->constantContactAdd($subscriber);

		// send email to admin
		event( new SubscriberCreateEvent($subscriber) );

		// return json response
		return response()->json(['ok' => true]);
	}

	private function constantContactAdd($subscriber) {
		// Constant Contact API_KEY and ACCESS_TOKEN in config/services.php

		$cc = new ConstantContact($this->key);

		
		try {// attempt to fetch lists in the account, catching any exceptions and printing the errors to screen
			$lists = $cc->listService->getLists($this->token);
		} catch (CtctException $ex) {
			$errors = '';
			foreach ($ex->getErrors() as $error) {
				$errors .= "\\n" . $error;
			}
			if (!isset($lists)) {
				$lists = null;
			}
		}

		if ($subscriber && $subscriber->email != '') {// check if the form was submitted
			$action = "Getting Contact By Email Address";
			try {
				// check to see if a contact with the email address already exists in the account
				$response = $cc->contactService->getContacts($this->token, array("email" => $subscriber->email));

				
				$name = explode(' ', $subscriber->name);
				if (empty($response->results)) {// create a new contact if one does not exist
					$action = "Creating Contact";
					$contact = new Contact();
					$contact->addEmail($subscriber->email);
					$contact->addList($this->list);// Know which list to add
					$contact->first_name = $name[0];
					$contact->last_name = sizeof($name) > 1 ? $name[1] : '';
					/*
					* The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
					* Contact that this action is being performed by the contact themselves, and gives the ability to
					* opt contacts back in and trigger Welcome/Change-of-interest emails.
					*
					* See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
					*/
					
					$returnContact = $cc->contactService->addContact($this->token, $contact, true);

					
				} else {// update the existing contact if address already existed
					$action = "Updating Contact";

					$contact = $response->results[0];
					if ($contact instanceof Contact) {
						$contact->addList($this->list);
						$contact->first_name = $name[0];
						$contact->last_name = count($name) > 1 ? $name[1] : '';
						
						/*
						* The third parameter of updateContact defaults to false, but if this were set to true it would tell
						* Constant Contact that this action is being performed by the contact themselves, and gives the ability to
						* opt contacts back in and trigger Welcome/Change-of-interest emails.
						*
						* See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
						*/
						$returnContact = $cc->contactService->updateContact($this->token, $contact, true);
					} else {
						$e = new CtctException();
						$e->setErrors(array("type", "Contact type not returned"));
						throw $e;
					}
				}

				// catch any exceptions thrown during the process and print the errors to screen
			} catch (CtctException $ex) {
				$errors = '';
				$errors .= '<span class="label label-important">Error ' . $action . '</span>';
				$errors .= '<div class="container alert-error"><pre class="failure-pre">';
				$errors .= $ex->getErrors();
				$errors .= '</pre></div>';
				dd($errors);
			}
		} else {
			dd('invalid form submission.');
		}
	}

    /**
     * Helper function to return required headers for making an http request with constant contact
     * @param $accessToken - OAuth2 access token to be placed into the Authorization header
     * @return array - authorization headers
     */
    private static function getHeaders($accessToken) {
		return array(
			'User-Agent' => 'ConstantContact AppConnect PHP Library v3.x.x',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
            'x-ctct-request-source' => 'sdk.php.3.x.x'
        );
	}
	
	private function getAccessToken() {// Needs improved workflow
		$oauth = new CtctOAuth2($this->key, config('services.constant_contact.consumer_secret'), 'http://localhost');
		$authCode = config('services.constant_contact.authorization_code');
		return $oauth->getAccessToken($authCode);
	}
}