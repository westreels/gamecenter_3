<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        // if email verification is enabled
        if (config('settings.email_verification') && !$request->user()->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? abort(403, __('Your email address is not verified.'))
                : Redirect::to('/email');
        }

        return $next($request);
    }
}
