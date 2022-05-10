<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class VerifyEmailException extends ValidationException
{
    /**
     * @param  \App\Models\User $user
     * @return static
     */
    public static function forUser($user)
    {
        return static::withMessages([
            'email' => [__('Please verify your email first.')],
        ]);
    }
}
