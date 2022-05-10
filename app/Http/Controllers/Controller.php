<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successResponse($data = NULL)
    {
        $result = ['success' => TRUE];

        if (!is_null($data)) {
            $result = array_merge($result, is_string($data) ? ['message' => $data] : (array) $data);
        }

        return $result;
    }

    public function errorResponse(string $message)
    {
        return [
            'success' => FALSE, 'message' => $message
        ];
    }
}
