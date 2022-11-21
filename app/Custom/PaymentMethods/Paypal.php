<?php

namespace App\Custom\PaymentMethods;

use App\Address;
use Illuminate\Support\Collection;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal
{
    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var Payer
     */
    private $payer;

    /**
     * @var Amount
     */
    private $amount;

    /**
     * @var array
     */
    private $transactions = [];

    /**
     * @var array
     */
    private $itemList;

    /**
     * @var mixed
     */
    private $otherFees;

    /**
     * Paypal construct
     *
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $mode
     */
    public function __construct($clientId, $clientSecret, $mode)
    {
        $apiContext = new ApiContext(new OAuthTokenCredential($clientId, $clientSecret));

        $apiContext->setConfig([
                                  'mode'                    => $mode, 'log.LogEnabled' => config('app.debug'),
                                  'log.FileName'            => storage_path('logs/paypal.log'),
                                  'log.LogLevel'            => config('logging.channels.slack.level'),
                                  'cache.enabled'           => true,
                                  'cache.FileName'          => storage_path('logs/paypal.cache'),
                                  'http.CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1,
                               ]);

        $this->apiContext = $apiContext;
    }

    /**
     * Returns the Paypal API Context
     *
     * @return ApiContext
     */
    public function getApiContext()
    {
        return $this->apiContext;
    }

    /**
     * Payer setter
     *
     * @return  void
     */
    public function setPayer()
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $this->payer = $payer;
    }

    /**
     * @param  Collection  $products
     */
    public function setItems(Collection $products)
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = (new Item())
                ->setName($product->short_description)
                ->setQuantity(
                    (int)$product->pivot->quantity
                )
                ->setCurrency(
                    strtoupper(config('cart.currency'))
                )
                ->setPrice($product->pivot->price);
        }

        $itemList = new ItemList();
        $itemList->setItems($items);

        $this->itemList = $itemList;
    }

    /**
     * @param  float  $subtotal
     * @param  float  $tax
     * @param  float  $shipping
     */
    public function setOtherFees($subtotal, $tax = 0, $shipping)
    {
        $details = new Details();
        $details->setTax($tax)
            ->setSubtotal($subtotal)
            ->setShipping($shipping);

        $this->otherFees = $details;
    }

    /**
     * @param $amt
     */
    public function setAmount(float $amt)
    {
        $amount = new Amount();
        $amount->setCurrency(strtoupper(config('cart.currency')))
            ->setTotal($amt)
            ->setDetails($this->otherFees);

        $this->amount = $amount;
    }

    /**
     * Set transactions
     */
    public function setTransactions(int $invoiceId)
    {
        $transaction = new Transaction();
        $transaction->setAmount($this->amount)
            ->setItemList($this->itemList)
            ->setDescription('Payment via Paypal')
            ->setInvoiceNumber($invoiceId);

        $this->transactions = $transaction;
    }

    /**
     * @param  string  $returnUrl
     * @param  string  $cancelUrl
     *
     * @return Payment
     */
    public function createPayment(string $returnUrl, string $cancelUrl)
    {
        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($this->payer)
            ->setTransactions([
                $this->transactions
            ]);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)->setCancelUrl($cancelUrl);

        $payment->setRedirectUrls($redirectUrls);

        return $payment->create($this->apiContext);
    }

    /**
     * @param  string  $payerID
     *
     * @return PaymentExecution
     */
    public function setPayerId(string $payerID)
    {
        $execution = new PaymentExecution();
        $execution->setPayerId($payerID);
        return $execution;
    }

    /**
     * @param  Address  $address
     *
     * @return InvoiceAddress
     */
    public function setBillingAddress(Address $address)
    {
        $billingAddress = new InvoiceAddress();
        $billingAddress->line1 = $address->address_1;
        $billingAddress->line2 = $address->address_2;
        $billingAddress->city = $address->city;
        $billingAddress->state = $address->state_code;
        $billingAddress->postal_code = $address->zipcode;
        $billingAddress->country_code = $address->country->iso;

        return $billingAddress;
    }

    /**
     * @param  Address  $address
     *
     * @return ShippingAddress
     */
    public function setShippingAddress(Address $address)
    {
        $shipping = new ShippingAddress();
        $shipping->line1 = $address->address_1;
        $shipping->line2 = $address->address_2;
        $shipping->city = $address->city;
        $shipping->state = $address->state_code;
        $shipping->postal_code = $address->zipcode;
        $shipping->country_code = $address->country->iso;

        return $shipping;
    }
}