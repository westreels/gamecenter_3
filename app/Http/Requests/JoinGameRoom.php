<?php

namespace App\Http\Requests;

use App\Helpers\PackageManager;
use App\Models\GameRoom;
use App\Rules\BalanceIsSufficient;
use App\Rules\GameRoomIsNotFull;
use App\Rules\UserNotJoinedGameRoom;
use Illuminate\Foundation\Http\FormRequest;

class JoinGameRoom extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(PackageManager $packageManager)
    {
        $package = $packageManager->get($this->packageId);

        return !!$package && $package->enabled;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $room = GameRoom::where('id', intval($this->room_id))->open()->firstOrFail();

        return [
            'room_id' => [
                new GameRoomIsNotFull($room), // not all players have joined
                new UserNotJoinedGameRoom($this->user()), // user has not joined any other rooms yet
                new BalanceIsSufficient($this->user(), $room->parameters->bet)
            ]
        ];
    }
}
