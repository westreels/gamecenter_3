<?php

namespace Packages\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Payments\Models\DepositMethod;

class DepositMethodController extends Controller
{
    public function index()
    {
        return DepositMethod::enabled()
            ->with('paymentMethod', 'paymentMethod.gateway')
            ->orderBy('id')
            ->get()
            ->map
            ->append('payment_currencies');
    }
}
