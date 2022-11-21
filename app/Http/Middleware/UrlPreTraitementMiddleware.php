<?php

namespace App\Http\Middleware;

use Closure;

class UrlPreTraitementMiddleware
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
         * Check if current request has gclid, we need to save it on session
         * This is stored with customer orders after checking out.
         */
        if ($request->has('gclid')) {
            session(['gclid' => $request->gclid]); 
        }

        return $next($request);
    }
}
