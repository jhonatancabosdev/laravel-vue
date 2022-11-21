<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\TrackingNumberNotification;

class TrackingNumberCreatedListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->order->markAsShipped();
        
        $event->trackingNumber->notify(
            new TrackingNumberNotification($event->order)
        );
    }
}
