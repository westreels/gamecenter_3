<?php

namespace Packages\Payments\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Packages\Payments\Models\Deposit;
use Packages\Payments\Models\Withdrawal;

class WithdrawalDoesNotExceedProfit implements Rule
{
    private $user;
    private $amount;
    private $maxWithdrawalAmount;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user, float $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $amount
     * @return bool
     */
    public function passes($attribute = NULL, $amount = NULL)
    {
        if (config('payments.withdrawal_only_profits')) {
            $totalDepositsAmount = (float)Deposit::where('account_id', $this->user->account->id)->completed()->sum('amount');
            $totalWithdrawalsAmount = (float)Withdrawal::where('account_id', $this->user->account->id)->notCancelled()->sum('amount');
            $totalGamesProfit = (float)$this->user->account->games()->select(DB::raw('SUM(GREATEST(win - bet, 0)) AS total_profit'))->value('total_profit');

            $this->maxWithdrawalAmount = max(0, $totalDepositsAmount + $totalGamesProfit - $totalWithdrawalsAmount);

            return $this->amount <= $this->maxWithdrawalAmount;
        }

        return TRUE;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->maxWithdrawalAmount == 0
            ? __('Your current profit is zero, there is nothing to withdraw.')
            : __('You can withdraw no more than :n credits.', ['n' => $this->maxWithdrawalAmount]);
    }
}
