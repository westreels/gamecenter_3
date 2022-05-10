<?php

namespace Packages\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Payments\Models\WithdrawalMethod;

class WithdrawalMethodController extends Controller
{
    public function index()
    {
        return WithdrawalMethod::enabled()
            ->with('paymentMethod', 'paymentMethod.gateway')
            ->orderBy('id')
            ->get()
            ->map
            ->append('payment_currencies');
    }
}
