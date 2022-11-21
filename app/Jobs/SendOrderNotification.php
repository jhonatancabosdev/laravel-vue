<?php

namespace App\Jobs;

use App\Order;
use App\Mail\OrderMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Notifications\TextNotification;
use App\Notifications\OrderNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;

class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;

        /**
         * Send order notification to customer
         */
        $order->notify(new OrderNotification($order));

        /**
         * Send order notification to bcc
         */
        $bcc = config('mail.bcc');
        $email = array_shift($bcc);

        $site = env('APP_NAME', 'Online Store');

        $mailable = (new OrderMailable($order))->subject("Order made on {$site} - Order #{$order->id} - {$order->order_source}");

        Mail::to($email)->bcc($bcc)->send($mailable);
    }
}
