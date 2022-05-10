<?php

namespace App\Http\Requests;

use App\Helpers\PackageManager;
use App\Rules\MaxOpenGameRoomsLimit;
use App\Rules\UserNotJoinedGameRoom;
use Illuminate\Foundation\Http\FormRequest;

class CreateGameRoom extends FormRequest
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
        $rules = [
            'name' => [
                'required',
                'min:3',
                'max:50',
                new MaxOpenGameRoomsLimit($this->user()),
                new UserNotJoinedGameRoom($this->user()) // user has not joined any other rooms yet
            ]
        ];

        // add custom game room parameters validation
        foreach (config($this->packageId . '.parameters') as $parameter) {
            if ($parameter['validation']) {
                $rules['parameters.' . $parameter['id']] = $parameter['validation'];
            }
        }

        return $rules;
    }
}
