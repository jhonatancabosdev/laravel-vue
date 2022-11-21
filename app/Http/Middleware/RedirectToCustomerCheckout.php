<?php

namespace App\Http\Middleware;

use Closure;

class RedirectToCustomerCheckout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if( !auth()->guard($guard)->check() ){
            return $next($request);
        }

        return redirect()->route('checkout.index');
    }
}
