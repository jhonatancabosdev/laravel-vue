<?php

namespace App\Http\Middleware;

use Closure;
use App\Order;

class CheckoutValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * All routes under this middleware must have order id created
         */
        if (session()->has('order') && session('order') > 0) {
            return $next($request);
        }

        /**
         * Check if current requested order is for logged customer
         */
        if (auth()->guard('customer')->check() && $request->segment(2) !== null) {

            $order = Order::findOrFail($request->segment(2));

            if (!$order->customer) {
                return redirect()->route('home')
                    ->with("error", "You don't have access to this page.");
            }

            $loggedCustomer = auth()->guard('customer')->user();

            if ($order->customer->is($loggedCustomer)) {
                return $next($request);
            }
        }

        return redirect()->route('home')
                    ->with("error", "You don't have access to this page.");
    }
}
