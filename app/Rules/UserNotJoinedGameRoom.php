<?php

namespace App\Rules;

use App\Models\GameRoomPlayer;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserNotJoinedGameRoom implements Rule
{
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return GameRoomPlayer::where('user_id', $this->user->id)->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You can not join more than one game room at the same time.');
    }
}
