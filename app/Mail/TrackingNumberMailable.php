<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackingNumberMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order = $this->order;
        $name = config('mail.from.name');
        $address = config('mail.from.address');

        return $this->from($address, $name)
            ->replyTo(config('mail.replyTo', $address))
            ->subject('Tracking information for your order from Green World')
            ->view('emails.customer.trackingNumber', compact('order'));
    }
}
