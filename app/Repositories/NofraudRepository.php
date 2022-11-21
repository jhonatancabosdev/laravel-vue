<?php

namespace App\Repositories;

use App\Gateways\NofraudGateway;
use App\NofraudTransaction;
use App\Order;
use Exception;
use Illuminate\Support\Collection;
use stdClass;

class NofraudRepository
{

    /**
     * @var NofraudGateway
     */
    protected $client;

    /**
     * @var stdClass
     */
    protected $payload;

    public function __construct(NofraudGateway $nofraud)
    {
        $this->client = $nofraud;
        $this->payload = new stdClass;
    }

    public function process(Order $order)
    {
        $this->setCustomer($order);
        $this->setPayment($order);
        $this->setBillTo($order);
        $this->setShipTo($order);
        $this->setCustomerIp();
        $this->setLineItems($order->products);
        $this->setOrder($order);

        $nofraud = $order->nofraudTransaction()->create();

        try {
            $response = $this->client->post('/', $this->payload);

            $nofraud->update([
                'transaction_id' => $response->id ?? null,
                'decision' => $response->decision ?? 'fail',
                'message' => $response->message ?? null
            ]);

           if($nofraud->decision === 'fail'){
                throw new Exception($response->message ?? 'Declined');
           } 
      
        }catch (Exception $e){
            $nofraud->update([
                'message' => $e->getMessage(),
                'decision' => 'fail',
            ]);

            throw new Exception('Unfortunately the transaction was unsuccessful. Please try again later.');
        }
    }

    public function setCustomer(Order $order)
    {
        $customer = new stdClass;
        $customer->email = $order->email;

        $this->payload->customer = $customer;
    }

    public function setBillTo(Order $order)
    {
        $billTo = new stdClass;
        $billTo->firstName = $order->first_name;
        $billTo->lastName = $order->last_name;
        $billTo->address = substr($order->billing_address->address_1 ?? '', 0, 127);
        $billTo->city = $order->billing_address->city ?? '';
        $billTo->state = $order->billing_address->state->abv ?? '';
        $billTo->zip = $order->billing_address->zipcode ?? '';
        $billTo->country = $order->billing_address->country->iso ?? 'US';
        $billTo->phoneNumber = $order->phone;

        $this->payload->billTo = $billTo;
    }

    public function setPayment(Order $order)
    {
        $card = new stdClass;
        $card->cardNumber = $order->cc_number ? decrypt($order->cc_number) : $order->cc_number;
        $card->expirationDate = $order->cc_expiration_month . $order->cc_expiration_year;
        $card->cardCode = $order->cc_cvv;

        $creditCard = new stdClass;
        $creditCard->creditCard = $card;

        $this->payload->payment = $creditCard;
    }

    public function setCustomerIp()
    {
        $this->payload->customerIP = app('request')->ip();
    }

    public function setLineItems(Collection $products)
    {
        $lineItems = [];

        foreach($products as $product){
            $item = new stdClass;
            $item->sku = $product->sku;
            $item->name = $product->name;
            $item->price = $product->price;
            $item->quantity = (int)$product->pivot->quantity;

            $lineItems[] = $item;
        }

        $this->payload->lineItems = $lineItems;
    }

    public function setOrder(Order $order)
    {   
        $info = new stdClass;
        $info->invoiceNumber = $order->id;

        $this->payload->order = $info;

        $this->payload->gatewayName = $order->payment_method == "credit_card" ? "Authorize.net" : "Paypal";
        $this->payload->gatewayStatus = "pass";
        $this->payload->amount = $order->total;
        $this->payload->shippingAmount = $order->shipping_cost;
    }

    public function setShipTo(Order $order)
    {
        $shipTo = new stdClass;
        $shipTo->firstName = $order->first_name;
        $shipTo->lastName = $order->last_name;
        $shipTo->address = substr($order->shipping_address->address_1 ?? '', 0, 127);
        $shipTo->city = $order->shipping_address->city ?? '';
        $shipTo->state = $order->shipping_address->state->abv ?? '';
        $shipTo->zip = $order->shipping_address->zipcode ?? '';
        $shipTo->country = $order->shipping_address->country->iso ?? 'US';
        $shipTo->phoneNumber = $order->phone;

        $this->payload->shipTo = $shipTo;
    }
}
