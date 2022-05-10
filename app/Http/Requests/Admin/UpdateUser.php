<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'      => 'required|min:3|max:100|unique:users,name,' . $this->user->id . ',id', // don't validate name uniqueness if it's not updated,
            'email'     => 'required|email|max:100|unique:users,email,' . $this->user->id . ',id', // don't validate email uniqueness if it's not updated,
            'password'  => 'nullable|string|min:6',
            'role' => [
                'required',
                Rule::in(array_keys(User::roles())),
            ],
            'status' => [
                'required',
                Rule::in(array_keys(User::statuses())),
            ],
            'permissions' => 'nullable|array',
            'banned_from_chat' => 'required|boolean'
        ];
    }
}
