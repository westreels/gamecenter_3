<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\UpdatePassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePassword $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);
    }
}
