<?php

namespace App\Rules;

use App\Models\GameRoomPlayer;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserJoinedGameRoomAndNotPlaying implements Rule
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
        $player = GameRoomPlayer::where('game_room_id', $value)->where('user_id', $this->user->id)->first();

        // player joined the room but not playing at the moment
        return $player && !$player->game_id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You can not leave this room.');
    }
}
