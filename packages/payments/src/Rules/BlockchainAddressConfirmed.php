<?php

namespace Packages\Payments\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class BlockchainAddressConfirmed implements Rule
{
    protected $user;
    protected $model;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user, string $model)
    {
        $this->user = $user;
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->model::where('user_id', $this->user->id)->where('address', $value)->confirmed()->count() == 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Please confirm that you own this address first.');
    }
}
