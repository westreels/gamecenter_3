<?php

namespace App\Http\Controllers\Auth;

use App\Rules\ReCaptchaValidationPassed;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $rules = ['email' => 'required|email'];

        if (config('services.recaptcha.secret_key')) {
            $rules['recaptcha'] = ['required', new ReCaptchaValidationPassed()];
        }

        $request->validate($rules);
    }
}
