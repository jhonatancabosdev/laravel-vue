<?php

namespace App\Repositories\Payment;

use App\Order;
use App\Address;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use Ramsey\Uuid\Uuid;
use PayPal\Api\Payment;
use PayPal\Api\Currency;
use PayPal\Api\Agreement;
use Illuminate\Support\Arr;
use PayPal\Api\ChargeModel;
use Illuminate\Http\Request;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
use PayPal\Api\ShippingAddress;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use App\Custom\PaymentMethods\Paypal;
use PayPal\Api\Payment as PayPalPayment;

class PaypalRepository
{
    /**
     * @var Paypal
     */
    protected $paypal;

    /**
     * @var string
     */
    protected $cancelUrl;

    /**
     * @var string
     */
    protected $successUrl;

    /**
     * @var mixed
     */
    protected $transaction;

    /**
     * @return void
     */
    public function init()
    {
        $this->cancelUrl = route('guest.checkout.index');

        $this->successUrl = route('checkout.confirm');

        $this->paypal = new Paypal(
            config('paypal.'.config('app.env').'.client_id'),
            config('paypal.'.config('app.env').'.client_secret'),
            config('paypal.'.config('app.env').'.mode')
        );
    }

    /**
     * @param mixed $order
     *
     * @return string
     * @throws \Exception
     */
    public function process($order)
    {
        if (! ($this->paypal instanceof Paypal)) {
            $this->init();
        }

        $this->paypal->setPayer();

        $this->paypal->setItems($order->products);

        $this->paypal->setOtherFees($order->subtotal, $order->tax, $order->shipping_cost);

        $this->paypal->setAmount($order->total);
        
        $this->paypal->setTransactions($order->id);

        $order->addresses->each(function ($address) {
            if ($address->type === 'billing') {
                $this->paypal->setBillingAddress($address);
            } elseif ($address->type === 'shipping') {
                $this->paypal->setShippingAddress($address);
            }
        });

        // set success url and cancel url
        $response = $this->paypal->createPayment($this->successUrl, $this->cancelUrl);

        // if payment failed user is redirect to this url
        if (is_object($response) && isset($response->links) && isset($response->links[1])) {
            return $response->links[1]->href;
        }
        
        return $this->cancelUrl;
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     * @return  mixed
     */
    public function check(Request $request)
    {
        if (! ($this->paypal instanceof Paypal)) {
            $this->init();
        }

        $payment = PayPalPayment::get($request->paymentId, $this->paypal->getApiContext());
        $execution = $this->paypal->setPayerId($request->PayerID);
        $this->transaction = $payment->execute($execution, $this->paypal->getApiContext());

        // check for transaction status and email
        if (is_null($this->transaction) || $this->transaction->getState() !== 'approved') {
            throw new \Exception("Declined transaction.");
        }

        return $this;
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     * @return  mixed
     */
    public function executeAgreement(Request $request, Order $order)
    {
        $request->validate([
            'token' => 'required'
        ]);

        if (! ($this->paypal instanceof Paypal)) {
            $this->init();
        }

        $agreement = new \PayPal\Api\Agreement();
        $agreement->execute($request->input('token'), $this->paypal->getApiContext());
        $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $this->paypal->getApiContext());

        $order->products->each(function ($product) use ($agreement) {
            $product->pivot->subscription_id = $agreement->getId();
            $product->pivot->is_active_subscription = true;
            $product->pivot->save();
        });

        return $this;
    }

    /**
     * Update order attributes and confirm order
     *
     * @param mixed $orders
     * @throws \Exception
     * @return self
     */
    public function confirm($orders): self
    {
        if (! is_null($this->transaction)) {
            $transactionId = $this->transaction->getId();
        }

        foreach (Arr::wrap($orders) as $order) {
            $order->transaction_id = isset($transactionId) ? $transactionId : '';
            $order->payment_method = 'paypal';
            $order->confirmed = true;
            $order->confirmed_at = date('Y-m-d H:i:s');
            $order->save();
        }

        return $this;
    }

    /**
     * @param  mixed $createdPlan
     * @return mixed
     */
    protected function updatePlan($createdPlan)
    {
        if (! ($this->paypal instanceof Paypal)) {
            $this->init();
        }

        $patch = new Patch();
        $value = new PayPalModel('{
           "state":"ACTIVE"
         }');

        $patch->setOp('replace')
            ->setPath('/')
            ->setValue($value);

        $patchRequest = new PatchRequest();
        $patchRequest->addPatch($patch);

        $createdPlan->update($patchRequest, $this->paypal->getApiContext());

        return Plan::get($createdPlan->getId(), $this->paypal->getApiContext());
    }

    /**
     * @return Plan
     */
    protected function createPlan(Order $order, int $intervalLength = 30)
    {
        if (! ($this->paypal instanceof Paypal)) {
            $this->init();
        }

        $this->paypal->setPayer();

        $plan = new Plan();

        $plan->setName(config('app.name'))
            ->setDescription('Template creation.')
            ->setType('FIXED')
            ->setState('CREATED');

        $paymentDefinition = new PaymentDefinition();

        $paymentDefinition->setName('Regular Payments')
            ->setType('REGULAR')
            ->setFrequency('DAY')
            ->setFrequencyInterval($intervalLength)
            ->setCycles('12')
            ->setAmount(
                new Currency(['value' => $order->subtotal, 'currency' => 'USD'])
            )
            ->setChargeModels([
                (new ChargeModel())->setType('SHIPPING')->setAmount(new Currency(['value' => $order->shipping_cost, 'currency' => 'USD'])),
                (new ChargeModel())->setType('TAX')->setAmount(new Currency(['value' => $order->tax, 'currency' => 'USD']))
            ]);

        $merchantPreferences = new MerchantPreferences();

        $merchantPreferences->setReturnUrl($this->successUrl)
            ->setCancelUrl($this->cancelUrl)
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CONTINUE")
            ->setMaxFailAttempts("0")
            ->setSetupFee(new Currency(['value' => $order->total, 'currency' => 'USD']));

        $plan->setPaymentDefinitions([$paymentDefinition])
            ->setMerchantPreferences($merchantPreferences);

        $self = $this;

        return tap($plan->create($this->paypal->getApiContext()), function ($plan) use ($self) {
            $self->updatePlan($plan);
        });
    }

    /**
     * @param  Order   $order
     * @param  mixed  $id
     * @param  mixed  $amount
     * @param  integer $intervalLength
     *
     * @return mixed
     */
    public function createSubscription(Order $order, int $intervalLength = 30)
    {
        if (! ($this->paypal instanceof Paypal)) {
            $this->init();
        }

        $this->paypal->setPayer();

        $agreement = new Agreement();

        $agreement->setName(config('app.name') .' Item subscription')
            ->setDescription(config('app.name') .' Item subscription')
            ->setStartDate(now()->addMinutes(10)->toIso8601String())
            ->setPayer($this->paypal->getPayer())
            ->setPlan(
                (new Plan())->setId(
                    $this->createPlan($order, $intervalLength)->getId()
                )
            )
            ->setShippingAddress(
                (new ShippingAddress())->setLine1($order->shippingAddress->address_1)
                    ->setCity($order->shippingAddress->city)
                    ->setState($order->shippingAddress->state->abv)
                    ->setPostalCode($order->shippingAddress->zipcode)
                    ->setCountryCode($order->shippingAddress->country->iso)
            );

        $agreement = $agreement->create($this->paypal->getApiContext());

        return $agreement->getApprovalLink();
    }

    /**
     * @param  mixed $agreementId
     * @return mixed
     */
    public function getAgreement($agreementId)
    {
        $this->init();
        
        return Agreement::get($agreementId, $this->paypal->getApiContext());
    }

    /**
     * @param  mixed $paymentId
     * @return mixed
     */
    public function getPayment($paymentId)
    {
        $this->init();
        
        return Payment::get($paymentId, $this->paypal->getApiContext());
    }
}