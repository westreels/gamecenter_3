<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // update last_seen_at only if at least 5 seconds passed since last update
        // important to check that "last_seen_at" column exists
        if ($user && isset($user->last_seen_at) && (!$user->last_seen_at || $user->last_seen_at->lte(Carbon::now()->subSeconds(5)))) {
            tap($request->user(), function ($user) { $user->is_online = TRUE; })->save();
        }

        return $next($request);
    }
}
