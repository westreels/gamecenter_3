<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendChatMessage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->room->enabled && !$this->user()->banned_from_chat;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required|min:1|max:' . config('settings.interface.chat.message_max_length'),
            'recipients' => 'nullable|array',
            'recipients.*' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('status', User::STATUS_ACTIVE);
                }),
            ]
        ];
    }
}
