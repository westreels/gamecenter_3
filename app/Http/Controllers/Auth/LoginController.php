<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\ReCaptchaValidationPassed;
use App\Services\AccountService;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    protected function loginSocial(Request $request)
    {
        $data = $request->only([
            'code',
        ]);

        $user = resolve(AuthService::class)->handle($data, $request);

        return response()->json($user, 200);
        // try {
        //     $user = resolve(AuthService::class)->handle($data, $request);

        //     return response()->json($user, 200);
        // } catch (AuthenticationException $exception) {
        //     return response()->json([
        //         'status_code' => 401,
        //         'message' => $exception->getMessage(),
        //         'errors' => [],
        //     ], 401);
        // } catch (\Exception $exception) {
        //     return response()->json([
        //         'status_code' => 500,
        //         'message' => 'Error server',
        //         'errors' => [],
        //     ], 500);
        // }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // check user status
        return array_merge($request->only($this->username(), 'password'), ['status' => User::STATUS_ACTIVE]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ];

        if (config('services.recaptcha.secret_key')) {
            $rules['recaptcha'] = ['required', new ReCaptchaValidationPassed()];
        }

        $request->validate($rules);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->last_login_at = Carbon::now();
        $user->last_login_from = $request->ip();
        $user->save();

        // create a new account if it's not created yet
        if (!$user->account) {
            AccountService::create($user);
        }

        return UserService::user($user);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
