<?php

namespace Packages\VideoPoker\Http\Requests;

use App\Services\ProvablyFairGameService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Packages\VideoPoker\Models\VideoPoker;

class Draw extends FormRequest
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
            $this->provablyFairGame = ProvablyFairGameService::get($this->hash, VideoPoker::class);

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
            'hash'      => 'required|size:64',
            'hold'      => 'array',
            'hold.*'    => 'required|integer|min:0|max:4', // array of cards to hold should contain integers from 0 to 4
        ];
    }
}
