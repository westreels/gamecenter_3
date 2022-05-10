<?php

namespace App\Http\Requests;

use App\Helpers\PackageManager;
use Illuminate\Foundation\Http\FormRequest;

class GetGameRooms extends FormRequest
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
        return [
            //
        ];
    }
}
