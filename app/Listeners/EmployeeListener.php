<?php

namespace App\Listeners;

use App\Shop\Customers\Customer;

class EmployeeListener
{
    /**
     * @param  Event $event
     * @return void
     */
    public function onUserLogin($event)
    {
        // do something
        $event->user->last_login = \Carbon\Carbon::now();
        $event->user->save();
    }
}
