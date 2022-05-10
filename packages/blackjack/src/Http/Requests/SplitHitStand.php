<?php

namespace Packages\Blackjack\Http\Requests;

use Illuminate\Http\Request;

class SplitHitStand extends Action
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return parent::authorize() && $this->provablyFairGame->game->gameable->player_score2 > 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return array_merge(
            parent::rules($request),
            [
                'hand' => 'required|integer|min:1|max:2'
            ]
        );
    }
}
