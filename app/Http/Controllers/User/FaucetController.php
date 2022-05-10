<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\ClaimFaucet;
use App\Models\Bonus;
use App\Rules\FaucetIsAllowed;
use App\Services\AccountService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class FaucetController extends Controller
{
    public function show(Request $request)
    {
        $rule = new FaucetIsAllowed($request->user());

        return [
            'allowed'   => $rule->passes(),
            'time'      => $rule->getAllowedTime()->getTimestamp()
        ];
    }

    public function update(ClaimFaucet $request)
    {
        $accountService = new AccountService($request->user()->account);

        return [
            'success'   => !!$accountService->createBonus(Bonus::TYPE_FAUCET, config('settings.bonuses.faucet.amount')),
            'time'      => Carbon::now()->addHours(config('settings.bonuses.faucet.interval'))->getTimestamp()
        ];
    }
}
