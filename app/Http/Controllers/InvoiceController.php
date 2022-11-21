<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
	/**
	 * Show order invoice
	 * 
	 * @param  Request $request
	 * @return view
	 */
	public function show(?Order $order, Request $request)
	{
		// order id is null, a null object is injected
		// we just check if order really exists in db
		// if not then order id is saved in session.
		if (!$order->exists) {
			$order = Order::findOrFail(session('order'));
		}

		// redirect to last route with error
		// if this order is not confirmed
		if (!$order->confirmed) {
			return redirect()->back()->with('error', 'Order not confirmed yet!');
		}

		return view('front.invoices.show', [
            'order' => $order
        ]);
	}
}
