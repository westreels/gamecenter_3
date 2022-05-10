<?php

namespace Packages\GameProviders\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Packages\GameProviders\Helpers\Api;

class CallbackIsAuthorized
{
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->api->callbackIsAuthorized($request)) {
            Log::error(sprintf('Callback request is not authorized: %s', $request->getContent()));
            return response()->json($this->api->getUnauthorizedResponseData(), 403);
        }

        return $next($request);
    }
}
