<?php

namespace App\Http\Requests;

use App\Helpers\PackageManager;
use App\Rules\BalanceIsSufficient;
use Illuminate\Foundation\Http\FormRequest;

class MakeAssetPrediction extends FormRequest
{
    protected $gameableClass;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->asset->is_active;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $packageManager = app()->make(PackageManager::class);
        $packageId = $packageManager->getPackageIdByClass($this->gameableClass);

        return [
            'bet' => [
                'required',
                'integer',
                'min:' . config($packageId . '.min_bet'),
                'max:' . config($packageId . '.max_bet'),
            ],
            'direction' => 'required|in:-1,1',
            'duration' => 'required|in:' .  collect(config($packageId . '.durations'))
                    ->pluck('value')
                    ->implode(','),
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->bet;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
