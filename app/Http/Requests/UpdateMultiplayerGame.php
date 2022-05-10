<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateMultiplayerGame extends FormRequest
{
    protected $player;
    protected $game;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->player = $this->room->player($this->user());
        $this->game = $this->player ? $this->player->game : NULL;

        return $this->room->is_open // room is open
            && $this->room->activePlayers->count() == $this->room->parameters->players_count // all players joined
            && $this->player // user joined this room
            && $this->game // user has started the game
            && $this->game->is_in_progress; // game is in progress
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [];
    }
}
