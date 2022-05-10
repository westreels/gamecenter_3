<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Packages\Payments\Models\PaymentGateway;
use Illuminate\Http\Request;
use Packages\Payments\Models\PaymentGatewayMethod;

class PaymentGatewayController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type') == 'in'
            ? PaymentGatewayMethod::TYPE_IN
            : PaymentGatewayMethod::TYPE_OUT;

        return PaymentGateway::with(['paymentMethods' => function($query) use($type) {
                $query->whereIn('type', [$type, PaymentGatewayMethod::TYPE_BOTH]);
            }])
            ->whereHas('paymentMethods', function($query) use($type) {
                $query->whereIn('type', [$type, PaymentGatewayMethod::TYPE_BOTH]);
            })
            ->orderBy('id')
            ->get()
            ->map(function ($gateway) {
                if ($gateway->paymentMethods) {
                    foreach ($gateway->paymentMethods as $method) {
                        $method->makeVisible(['parameters', 'allowed_currencies']);
                    }
                }
                return $gateway;
            });
    }
}
