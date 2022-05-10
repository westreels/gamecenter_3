<?php

namespace App\Rules;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;
use App\Http\Traits\CallApiTraits;

class BalanceIsSufficient implements Rule
{
    use CallApiTraits;
    private $user;
    private $requiredAmount;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user, $requiredAmount)
    {
        $this->user = $user;

        $url = config('define.balance_dev.domain') . '/balance-game/' . $this->user->social_id;

        $body = $this->callapi('GET', $url, []);
        
        $this->user->account = $body;
        
        $this->requiredAmount = floatval($requiredAmount);
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
        return $this->user->account['data']['data']['balance'] >= $this->requiredAmount;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Insufficient balance to perform this operation.');
    }
}
