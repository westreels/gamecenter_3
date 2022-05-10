<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class LogoutIfBlocked
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
        // log out user immediately if the user is blocked
        if (!$request->user()->is_active) {
            auth()->guard('web')->logout();
            abort(401);
        }

        return $next($request);
    }
}
