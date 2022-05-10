<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|min:3|max:100|unique:users,name,' . $this->user()->id,
            'email'     => 'required|email|max:100|unique:users,email,' . $this->user()->id,
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
