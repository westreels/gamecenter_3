<?php

namespace Packages\GameProviders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Packages\GameProviders\Services\CallbackService;

class CallbackController extends Controller
{
    protected $callbackService;

    public function __construct(CallbackService $callbackService)
    {
        $this->callbackService = $callbackService;
    }

    public function callback(Request $request)
    {
        return $this->callbackService->requestIsValid()
            ? response()->json($this->callbackService->process()->getSuccessResponse(), $this->callbackService->getHttpCode())
            : response()->json($this->callbackService->getErrorResponse(), $this->callbackService->getHttpCode());
    }
}
