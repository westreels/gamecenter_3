<?php

namespace Packages\Blackjack\Http\Requests;

class HitStand extends  Action
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return parent::authorize();
    }
}
