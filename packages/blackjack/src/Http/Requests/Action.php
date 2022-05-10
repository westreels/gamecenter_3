<?php

namespace Packages\Blackjack\Http\Requests;

use App\Services\ProvablyFairGameService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Packages\Blackjack\Models\Blackjack;

class Action extends FormRequest
{
    protected $provablyFairGame;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize()
    {
        // get action name from route
        $action =  str_replace('split-', '', Str::after(Route::currentRouteName(), 'games.blackjack.'));

        if ($this->hash) {
            $this->provablyFairGame = ProvablyFairGameService::get($this->hash, Blackjack::class);
            return $this->provablyFairGame
                && $this->provablyFairGame->game
                && !$this->provablyFairGame->game->is_completed
                && $this->provablyFairGame->game->account_id == $this->user()->account->id
                && $this->provablyFairGame->game->gameable
                && in_array($action, $this->provablyFairGame->game->gameable->actions);
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
