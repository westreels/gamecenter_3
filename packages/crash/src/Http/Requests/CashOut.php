<?php

namespace Packages\Crash\Http\Requests;

use App\Helpers\PackageManager;
use App\Models\MultiplayerGame;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Packages\Crash\Models\Crash;

class CashOut extends FormRequest
{
    protected $gameableClass = Crash::class;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $gameable = $this->multiplayerGame->gameable;
        $game = $gameable->games()->where('games.account_id', $this->user()->account->id)->first();

        $packageManager = app()->make(PackageManager::class);
        $packageId = $packageManager->getPackageIdByClass($this->gameableClass);

        $now = Carbon::now();

        return $this->multiplayerGame instanceof MultiplayerGame
            && $this->multiplayerGame->gameable_type == $this->gameableClass
            && optional($game)->is_in_progress
            && $gameable->start_time->lte($now)
            && $gameable->end_time->addMilliseconds(config($packageId . '.allowed_end_time_lag'))->gte($now);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException(__('Cash out can not be completed at this time.'));
    }
}
