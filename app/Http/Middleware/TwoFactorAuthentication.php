<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Route;

class TwoFactorAuthentication
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
        $user = $request->user();
        $route = Route::currentRouteName();

        // If 2FA is enabled and not yet passed
        if ($user->two_factor_auth_enabled
            && !$user->two_factor_auth_passed
            && !in_array($route, ['user', 'user.security.2fa.verify'])) {
            throw new AuthorizationException(__('Two-factor authentication should be passed.'));
        }

        return $next($request);
    }
}
