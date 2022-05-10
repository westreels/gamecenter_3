<?php

namespace Packages\Baccarat\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Baccarat\Models\Baccarat;

class Play extends PlayGame
{
    protected $gamePackageId = 'baccarat';
    protected $gameableClass = Baccarat::class;

    public function rules()
    {
        return array_merge(
            ['hash' => 'required|size:64'],
            collect(['player_bet', 'banker_bet', 'tie_bet'])
                ->mapWithKeys(function ($field) {
                    return [$field => 'present|nullable|integer|min:' . config($this->gamePackageId . '.min_bet') . '|max:' . config($this->gamePackageId . '.max_bet')];
                })
                ->toArray()
        );
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $amount = (int) $this->player_bet + (int) $this->banker_bet + (int) $this->tie_bet;

            if ($amount <= 0) {
                $validator->errors()->add('bet', __('The bet is incorrect.'));
            }
        });

        parent::withValidator($validator);
    }
}
