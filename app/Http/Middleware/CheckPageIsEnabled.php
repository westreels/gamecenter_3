<?php

namespace App\Http\Middleware;

use Closure;

class CheckPageIsEnabled
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
        if (!config('settings.content.leaderboard.enabled') && $request->is('api/leaderboard')) {
            return response()->json(['error' => __('Forbidden')], 403);
        }

        return $next($request);
    }
}
