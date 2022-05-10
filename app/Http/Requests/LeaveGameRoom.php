<?php

namespace App\Http\Requests;

use App\Helpers\PackageManager;
use App\Models\GameRoom;
use App\Rules\UserJoinedGameRoomAndNotPlaying;
use Illuminate\Foundation\Http\FormRequest;

class LeaveGameRoom extends FormRequest
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
        GameRoom::where('id', intval($this->room_id))->open()->firstOrFail();

        return [
            'room_id' => [
                new UserJoinedGameRoomAndNotPlaying($this->user()) // user has joined this room
            ]
        ];
    }
}
