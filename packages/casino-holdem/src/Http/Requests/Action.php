<?php

namespace Packages\CasinoHoldem\Http\Requests;

use App\Services\ProvablyFairGameService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Packages\CasinoHoldem\Models\CasinoHoldem;

class Action extends FormRequest
{
    protected $provablyFairGame;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->hash) {
            $this->provablyFairGame = ProvablyFairGameService::get($this->hash, CasinoHoldem::class);
            return $this->provablyFairGame
                && $this->provablyFairGame->game
                && !$this->provablyFairGame->game->is_completed
                && $this->provablyFairGame->game->account_id == $this->user()->account->id
                && $this->provablyFairGame->game->gameable;
        }

        return FALSE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'hash' => 'required|size:64'
        ];
    }
}
