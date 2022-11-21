<?php

namespace App\Http\Middleware;

use Closure;
use ReCaptcha\ReCaptcha;

class VerifyReCaptchaMiddleware
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
        if(!$request->has('g-recaptcha-response')) {// See Contact Form for example implementations
            return back()->with('error','We\'re sorry. We cannot verify your submission.');
        } else {
            if(!$this->verifyCaptcha($request['g-recaptcha-response'])) {
                return back()->with('error','We\'re sorry. Your submission was not verified.');
            }
        }

        return $next($request);
    }

    /**
     * ReCaptcha implementation
     */
    public function verifyCaptcha($response) {
        try {
            $recaptcha = new ReCaptcha(config('recaptcha.secret'));
            $result = $recaptcha->verify($response);
            return $result->isSuccess();
        } catch (\Exception $e) {
            return false;
        }
    }
}
