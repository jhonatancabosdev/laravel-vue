<?php

namespace App\Mail;

use App\Order;
use App\Carrier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $order->loadMissing(['customer', 'products', 'shipping', 'addresses', 'orderStatus', 'discount']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $shipping = !is_null($this->order->shipping_id) ? $this->order->shipping->name : $this->order->carriers->first();

        if (! is_null($shipping) && $shipping instanceof Carrier) {
            $shipping = $shipping->service_name;
        }

        $data = [
            'order'            => $this->order,
            'products'         => $this->order->products,
            'customer'         => $this->order->customer,
            'shipping_name'    => $shipping,
            'billing_address'  => $this->order->billing_address,
            'shipping_address' => $this->order->shipping_address,
            'status'           => $this->order->orderStatus,
            'payment'          => $this->order->paymentMethod,
            'discount'         => $this->order->discount
        ];

        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('emails.orders.created', $data);
    }
}

