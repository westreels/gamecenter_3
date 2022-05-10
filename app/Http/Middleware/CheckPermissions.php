<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermissions
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
        $module = $request->segment(3);

        if (($request->getMethod() == 'GET' && !$user->hasReadOnlyAccess($module)) || ($request->getMethod() != 'GET' && !$user->hasFullAccess($module))) {
            abort(403, __('You do not have permissions to complete this operation.'));
        }

        return $next($request);
    }
}
