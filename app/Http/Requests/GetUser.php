<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only admin can view blocked users
        return $this->user && ($this->user->is_active || $this->user()->is_admin);
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
