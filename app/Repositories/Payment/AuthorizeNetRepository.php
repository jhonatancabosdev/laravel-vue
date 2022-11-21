<?php

namespace App\Repositories\Payment;

use App\Order;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\MessagesType;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\controller as AnetController;
use net\authorize\api\contract\v1\ANetApiResponseType;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\TransactionResponseType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreateTransactionResponse;
use net\authorize\api\contract\v1\GetTransactionListRequest;
use net\authorize\api\contract\v1\GetSettledBatchListRequest;
use net\authorize\api\contract\v1\GetTransactionListResponse;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\GetSettledBatchListResponse;
use net\authorize\api\controller\GetSettledBatchListController;
use net\authorize\api\contract\v1\GetTransactionDetailsResponse;

class AuthorizeNetRepository
{
    /**
     * @var AnetAPI\MerchantAuthenticationType
     */
    protected $merchantAuthentication;

    /**
     * @var TransactionResponseType
     */
    protected $transactionResponse;

    /**
     * Common setup for API credentials
     */
    public function __construct()
    {
        $login = config('app.env') === 'local'
            ? config('authorize.local.login')
            : config('authorize.production.login');

        $key = config('app.env') === 'local'
            ? config('authorize.local.key')
            : config('authorize.production.key');

        if (! defined('AUTHORIZENET_LOG_FILE')) {
            define('AUTHORIZENET_LOG_FILE', storage_path('logs/authorize.log'));
        }

        $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $this->merchantAuthentication->setName($login);
        $this->merchantAuthentication->setTransactionKey($key);
    }

    /**
     * @param Order         $order
     * @param String|string $addressType
     *
     * @return  AnetAPI\CustomerAddressType
     */
    protected function changeAddress(Order $order, String $addressType = 'billing')
    {
        $address = $order->addresses->firstWhere('type', $addressType) ?? $order->addresses->firstWhere('type', 'billing');

        $customerAddress = (new AnetAPI\CustomerAddressType())
            ->setFirstName($order->first_name)
            ->setLastName($order->last_name);
        // ->setEmail($order->email)
        // ->setPhoneNumber($order->phone);

        if (is_null($address)) {
            return $customerAddress;
        }
    
        return $customerAddress->setCity($address->city)
            ->setAddress("{$address->address_1} {$address->address_2}")
            ->setState($address->state->abv)
            ->setZip($address->zipcode)
            ->setCountry($address->country->iso3);
    }

    /**
     * @param  mixed $transactionResponse
     *
     * @return array;
     */
    protected function getTransactionResponseErrors($transactionResponse)
    {
        $errors = collect($transactionResponse->getErrors())->map(function ($error) {
            return $error->getErrorText();
        })
        ->reject(function ($error) {
            return strpos('success', $error) !== false;
        });

        if (! $errors->isEmpty()) {
            throw new \Exception($errors->implode(' & '));
        }

        $messages = collect($transactionResponse->getMessages())->map(function ($message) {
            return $message->getText();
        })
        ->reject(function ($message) {
            return strpos('success', $message) !== false;
        });

        return $messages->toArray();
    }

    /**
     * @param  Order  $order
     *
     * @return CreateTransactionRequest
     */
    protected function prepareAuthCaptureTransactionRequest(Order $order)
    {
        // Create the payment data for a credit card
        $creditCard = (new AnetAPI\CreditCardType())
            ->setCardNumber($order->getRawCcNumberAttribute())
            ->setExpirationDate($order->getExpirationDateAttribute());
        
        // Create a transaction
        $transactionRequestType = (new AnetAPI\TransactionRequestType())
            ->setTransactionType("authCaptureTransaction")
            ->setAmount((float) number_format(floatval($order->total), 2, '.', ''))
            ->setBillTo($this->changeAddress($order))
            ->setShipTo($this->changeAddress($order, 'shipping'));

        $transactionRequestType->setOrder(
            (new AnetAPI\OrderType())->setInvoiceNumber((string) $order->id)
        );

        $transactionRequestType->setCustomer(
            (new AnetAPI\CustomerDataType())->setEmail($order->email)
        );

        $transactionRequestType->setPayment(
            (new AnetAPI\PaymentType())->setCreditCard($creditCard)
        );

        $transactionRequestType->setShipping(
            (new AnetAPI\ExtendedAmountType())->setName('Shipping')
                ->setAmount((float) number_format(floatval($order->shipping_cost), 2, '.', ''))
        );

        $transactionRequestType->setTax(
            (new AnetAPI\ExtendedAmountType())->setAmount((float) number_format(floatval($order->tax), 2, '.', ''))
        );

        return (new AnetAPI\CreateTransactionRequest())
            ->setMerchantAuthentication($this->merchantAuthentication)
            ->setRefId((string) $order->id)
            ->setTransactionRequest($transactionRequestType);
    }

    /**
     * Process order payment
     *
     * @param  Order  $order
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function process(Order $order)
    {
        $authRequest = $this->prepareAuthCaptureTransactionRequest($order);

        $response = (new AnetController\CreateTransactionController($authRequest))
            ->executeWithApiResponse(
                config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
            );

        if (! $response instanceof CreateTransactionResponse) {
            $order->cc_number = encrypt(substr($order->getRawCcNumberAttribute(), -4));
            $order->save();
            throw new \Exception("Error Processing Request");
        }

        $transactionResponse = $response->getTransactionResponse();

        if (is_null($transactionResponse)) {
            $order->cc_number = encrypt(substr($order->getRawCcNumberAttribute(), -4));
            $order->save();
            throw new \Exception('Unfortunately the transaction was unsuccessful. Please try again later.');
        }

        if (in_array($transactionResponse->getResponseCode(), ['1', '4'])) {
            $this->transactionResponse = $transactionResponse;
            $order->transaction_id = $this->transactionResponse->getTransId();
            $order->transaction_response = json_encode($transactionResponse);
            $order->markAsConfirmed();
            return $transactionResponse;
        }

        $order->cc_number = encrypt(substr($order->getRawCcNumberAttribute(), -4));
        $order->save();

        try {
            $order->transaction_response = json_encode($transactionResponse);
            $order->save();
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }

        $messages = $this->getTransactionResponseErrors($transactionResponse);

        throw new \Exception(
            count($messages) === 0
                ? 'Unfortunately the transaction was unsuccessful. Please try again later.'
                : implode(' & ', $messages)
        );
    }

    /**
     * @param  Order  $order
     *
     * @return mixed
     */
    public function refund(Order $order)
    {
        $status = $this->getTransactionDetails($order)->getTransactionStatus();

        switch ($status) {
            case 'capturedPendingSettlement':
                $response = $this->checkResponse($this->makeTransaction($order, 'voidTransaction'));
                break;

            case 'settledSuccessfully':
                $response = $this->checkResponse($this->makeTransaction($order, 'refundTransaction'));
                break;

            default:
                throw new \Exception("Can't process refund for this type of transaction: ({$status})");
        }

        $order->markAsRefunded();

        return $response ?? null;
    }

    /**
     * @return ANetApiResponseType
     */
    public function makeTransaction(Order $order, string $transactionType = "voidTransaction"): ANetApiResponseType
    {
        $creditCard = (new AnetAPI\CreditCardType())
            ->setCardNumber($order->getRawCcNumberAttribute())
            ->setExpirationDate($order->getExpirationDateAttribute());

        //create a transaction
        $transactionRequest = (new AnetAPI\TransactionRequestType())
            ->setBillTo($this->changeAddress($order))
            ->setShipTo($this->changeAddress($order, 'shipping'))
            ->setTransactionType($transactionType)
            ->setAmount((float) number_format($order->total, 2, '.', ''))
            ->setPayment(
                (new AnetAPI\PaymentType())->setCreditCard($creditCard)
            )
            ->setRefTransId($order->transaction_id);

        $request = (new AnetAPI\CreateTransactionRequest())
            ->setMerchantAuthentication($this->merchantAuthentication)
            ->setRefId((string) $order->id)
            ->setTransactionRequest($transactionRequest);

        return (new AnetController\CreateTransactionController($request))->executeWithApiResponse(
            config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
        );
    }

    /**
     * @param  Order   $order
     * @param  mixed  $id
     * @param  mixed  $amount
     * @param  integer $intervalLength
     *
     * @return mixed
     */
    public function createSubscription(Order $order, $id, $amount, int $intervalLength = 30)
    {
        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength($intervalLength);
        $interval->setUnit("days");

        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate(new \DateTime(now()->addMonth()));
        $paymentSchedule->setTotalOccurrences("12");
        $paymentSchedule->setTrialOccurrences("1");

        $creditCard = (new AnetAPI\CreditCardType())
            ->setCardNumber($order->getRawCcNumberAttribute())
            ->setExpirationDate($order->getExpirationDateAttribute());

        $subscription = (new AnetAPI\ARBSubscriptionType())
            ->setName("Subscription - " . config('app.name'))
            ->setPaymentSchedule($paymentSchedule)
            ->setAmount((float) number_format(floatval($amount), 2, '.', ''))
            ->setTrialAmount("0.00")
            ->setPayment(
                (new AnetAPI\PaymentType())
                    ->setCreditCard($creditCard)
            )
            ->setOrder(
                (new AnetAPI\OrderType())
                    ->setInvoiceNumber($id)
                    ->setDescription(config('app.name'))
            )
            ->setBillTo(
                (new AnetAPI\NameAndAddressType())
                    ->setFirstName($order->first_name)
                    ->setLastName($order->last_name)
            );

        $request = (new AnetAPI\ARBCreateSubscriptionRequest())
            ->setMerchantAuthentication($this->merchantAuthentication)
            ->setRefId($id)
            ->setSubscription($subscription);

        $response = (new AnetController\ARBCreateSubscriptionController($request))
            ->executeWithApiResponse(
                config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
            );

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return $response->getSubscriptionId();
        }

        $errorMessages = $response->getMessages()->getMessage();

        throw new \Exception($errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText());
    }

    /**
     * @param  string $subscriptionId
     * @return mixed
     */
    public function cancelSubscription(string $subscriptionId)
    {
        $request = (new AnetAPI\ARBCancelSubscriptionRequest())
            ->setMerchantAuthentication($this->merchantAuthentication)
            ->setRefId('ref' . time())
            ->setSubscriptionId($subscriptionId);

        $response = (new AnetController\ARBCancelSubscriptionController($request))
            ->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return true;
        }
        
        $errorMessages = $response->getMessages()->getMessage();

        throw new \Exception($errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText());
    }

    /**
     * @param mixed $response
     *
     * @return \net\authorize\api\contract\v1\TransactionResponseType
     *
     * @throws \Exception
     */
    public function checkResponse($response)
    {
        if (! $response instanceof CreateTransactionResponse) {
            throw new \Exception("Error Processing Request");
        }

        $this->transactionResponse = $response->getTransactionResponse();

        if ((int) $this->transactionResponse->getResponseCode() === 1) {
            return $this->transactionResponse;
        }

        $errors = collect($this->transactionResponse->getErrors())->map(function ($error) {
            return $error->getErrorText();
        })->reject(function ($error) {
            return strpos('success', $error) !== false;
        });

        throw new \Exception($errors->implode(' & '));
    }

    /**
     *
     * @param Order $order
     *
     * @return \net\authorize\api\contract\v1\TransactionDetailsType
     *
     * @throws \Exception
     */
    public function getTransactionDetails(Order $order)
    {
        $request = new AnetAPI\GetTransactionDetailsRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setTransId($order->transaction_id);

        $controller = new AnetController\GetTransactionDetailsController($request);

        $response = $controller->executeWithApiResponse(
            config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
        );

        if ($response instanceof GetTransactionDetailsResponse && (strtolower($response->getMessages()->getResultCode()) === 'ok')) {
            return $response->getTransaction();
        }

        throw new \Exception(
            collect($response->getMessages()->getMessage())->first()
        );
    }

    /**
     *
     * @param string $transactionId
     *
     * @return \net\authorize\api\contract\v1\TransactionDetailsType
     *
     * @throws \Exception
     */
    public function getTransaction(string $transactionId)
    {
        $request = new AnetAPI\GetTransactionDetailsRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setTransId($transactionId);

        $controller = new AnetController\GetTransactionDetailsController($request);

        $response = $controller->executeWithApiResponse(
            config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
        );

        if ($response instanceof GetTransactionDetailsResponse && (strtolower($response->getMessages()->getResultCode()) === 'ok')) {
            return $response->getTransaction();
        }

        try {
            throw new \Exception(collect($response->getMessages()->getMessage())->first());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param  \DateTime $firstSettlementDate
     * @param  \DateTime $lastSettlementDate
     *
     * @return array
     */
    public function getSettledBatchList($firstSettlementDate, $lastSettlementDate)
    {
        try {
            $request = (new AnetAPI\GetSettledBatchListRequest())->setIncludeStatistics(false)
                    ->setMerchantAuthentication($this->merchantAuthentication)
                    ->setFirstSettlementDate($firstSettlementDate)
                    ->setLastSettlementDate($lastSettlementDate);

            $response = (new AnetController\GetSettledBatchListController($request))
                ->executeWithApiResponse(
                    config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
                );

            if ($response instanceof GetSettledBatchListResponse && strtolower($response->getMessages()->getResultCode()) === 'ok') {
                return Arr::wrap($response->getBatchList());
            }

            return [];
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param  string $batchId
     *
     * @return array
     */
    public function getTransactionList($batchId)
    {
        $request = new AnetAPI\GetTransactionListRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setBatchId($batchId);

        try {
            $controller = new AnetController\GetTransactionListController($request);
            $response = $controller->executeWithApiResponse(
                config('app.env') !== 'local' ? ANetEnvironment::PRODUCTION : ANetEnvironment::SANDBOX
            );

            return ($response instanceof GetTransactionListResponse) && strtolower($response->getMessages()->getResultCode()) === 'ok'
                ? Arr::wrap($response->getTransactions())
                : [];
        } catch (\Exception $exception) {
            return [];
        }
    }
}
