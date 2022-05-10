<?php

namespace App\Rules;

use App\Models\GameRoom;
use Illuminate\Contracts\Validation\Rule;

class GameRoomIsNotFull implements Rule
{
    private $room;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(GameRoom $room)
    {
        $this->room = $room;
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
        return $this->room->players->count() < (int) $this->room->parameters->players_count;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The game room is already full.');
    }
}
