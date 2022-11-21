<?php

namespace App\Http\Controllers\API;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    /**
     * Show order resource
     *
     * @param  Request $request
     * @return json response
     */
    public function show(Request $request)
    {
        $order = Order::findOrFail($request->id);

        return response()->json([

            'tax_rate' => $order->zipcode->tax_rate ?? 0,

            'order' => [
                'id'         => $order->id,
                'tax'        => number_format($order->tax, 2),
                'total_paid' => number_format($order->total, 2),
                'name'       => $order->name,
                'email'      => $order->email,
                'cc_number'  => $order->lastCCDigits,
                'payment'    => 'Credit card',
                'phone'      => $order->phone,
                'created_at' => $order->created_at->format('m/d/Y h:iA'),
                'shipping_cost' => number_format($order->shipping_cost, 2),
                'total_products' => number_format($order->subtotal, 2)
            ],

            'products' => $order->products->map(function ($product) {

                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'vendor_code'  => $product->vendor_code,
                    'upc'   => $product->upc,
                    'price' => number_format($product->pivot->price, 2),
                    'quantity'  => $product->pivot->quantity,
                    'link'       => route('product.show', $product)
                ];
            })
        ]);
    }
}
