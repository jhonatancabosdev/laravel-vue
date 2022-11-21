<?php

namespace TCG\Voyager\Http\Controllers\Bread;

use App\Order;
use App\TrackingNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\TextNotification;
use App\Events\TrackingNumberCreatedEvent;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\TrackingNumbersResource;
use App\Notifications\TrackingNumberNotification;

class TrackingNumbersController extends Controller
{
    /**
     * Create new tracking number
     *
     * @param  Request $request
     * @return view
     */
    public function create(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'tracking_number' => 'required|array'
        ]);

        $order = Order::findOrFail($request->input('id'));

        $tracking = $request->input('tracking_number');

        if ($tracking['carrier_name'] === null || $tracking['number'] === null) {
            throw new \Exception("Invalid tracking number fields");
        }

        $tracking['order_id'] = $order->id;

        $trackingNumber = TrackingNumber::create($tracking);

        event(new TrackingNumberCreatedEvent($order, $trackingNumber));

        return $trackingNumber;
    }
}
