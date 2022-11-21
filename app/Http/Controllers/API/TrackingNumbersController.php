<?php

namespace App\Http\Controllers\API;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackingNumbersResource;

class TrackingNumbersController extends Controller
{
    /**
     * Show tracking numbers
     *
     * @param  Request $request
     * @return json response
     */
    public function show(Request $request)
    {
        $order = Order::findOrFail($request->id);

        $order->loadMissing('trackingNumbers');

        return TrackingNumbersResource::collection($order->trackingNumbers);
    }
}
