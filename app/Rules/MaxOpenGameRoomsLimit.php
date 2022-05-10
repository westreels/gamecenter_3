<?php

namespace App\Rules;

use App\Models\GameRoom;
use App\Models\GameRoomPlayer;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class MaxOpenGameRoomsLimit implements Rule
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
        return GameRoom::where('user_id', $this->user->id)
                ->open()
                ->count() < (int) config('settings.games.multiplayer.rooms_creation_limit');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You can have no more than :n open rooms at the same time.', ['n' => config('settings.games.multiplayer.rooms_creation_limit')]);
    }
}
