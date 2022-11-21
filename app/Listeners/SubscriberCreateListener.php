<?php

namespace App\Listeners;

use App\Mail\SubscriberCreated;
use App\Shop\Employees\Employee;
use App\Subscriber;
use Illuminate\Support\Facades\Mail;
use App\Events\SubscriberCreateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Shop\Employees\Repositories\EmployeeRepository;

class SubscriberCreateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SubscriberCreateEvent  $event
     * @return void
     */
    public function handle(SubscriberCreateEvent $event)
    {
        try {
            Mail::to(
                env('DEFAULT_EMAIL_RECEIVER')
            )
            ->send( new SubscriberCreated( $event->subscriber ) );
        } 
        catch (Exception $e) 
        {
            
        }
    }
}
