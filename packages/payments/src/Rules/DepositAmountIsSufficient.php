<?php

namespace Packages\Payments\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Packages\Payments\Models\Deposit;

class DepositAmountIsSufficient implements Rule
{
    private $user;
    private $minTotalDeposit;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->minTotalDeposit = config('payments.min_total_deposit_to_withdraw');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute = NULL, $value = NULL)
    {
        return (float) Deposit::where('account_id', $this->user->account->id)->completed()->sum('amount') >= $this->minTotalDeposit;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You need to deposit at least :n credits before being able to withdraw.', ['n' => $this->minTotalDeposit]);
    }
}
