<?php

namespace App\Listeners;

use App\Order;
use App\Events\OrderCreateEvent;
use App\Jobs\SendOrderNotification;
use App\Repositories\OrderRepository;
use App\Notifications\OrderNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreateEventListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            logger('event fired');
            logger($event->order->id);
            SendOrderNotification::dispatch($event->order);
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }
    }
}
