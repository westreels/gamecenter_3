<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\ReCaptchaValidationPassed;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, User $user)
    {
        return UserService::user($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name'      => 'required|min:3|max:100|unique:users',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ];

        if (config('services.recaptcha.secret_key')) {
            $rules['recaptcha'] = ['required', new ReCaptchaValidationPassed()];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $referrerId = Cookie::has('ref') ? intval(Cookie::get('ref')) : NULL;

        // if referrer is passed
        if ($referrerId) {
            // get the referrer user
            $referrer = User::where('id', $referrerId)->active()->first();

            Log::info(sprintf('Referrer ID %d, user %s', $referrerId, ($referrer ? 'exists' : 'does NOT exist')));

            // check that it's found and login IP address is different from current IP (if referrals registrations from the same IP are not allowed)
            if (!$referrer || (!config('settings.affiliate.allow_same_ip') && $referrer->last_login_from == request()->ip())) {
                $referrerId = NULL;
            }
        }

        return UserService::create([
            'referrer_id'   => $referrerId,
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => $data['password']
        ]);
    }
}
