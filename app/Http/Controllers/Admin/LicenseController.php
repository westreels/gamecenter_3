<?php

namespace App\Http\Controllers\Admin;

use App\Services\DotEnvService;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LicenseController extends Controller
{
    public function index() {
        return [
            'code'  => env(FP_CODE),
            'email' => env(FP_EMAIL),
        ];
    }

    public function register(Request $request, LicenseService $ls, DotEnvService $dotEnvService)
    {
        $success = FALSE;
        $result = $ls->register($request->code, $request->email);

        if ($result->success) {
            $success = TRUE;
            $message = __('Your license is successfully registered!');
            $dotEnvService->save([FP_CODE => $request->code, FP_EMAIL => $request->email, FP_HASH => $result->message]);
        } else {
            $message = $result->message;
            $dotEnvService->save(collect([FP_CODE, FP_EMAIL, FP_HASH])->mapWithKeys(function ($v) { return [$v => NULL]; })->toArray());
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}
