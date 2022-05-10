<?php

namespace App\Services;

use App\Helpers\Utils;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    use AuthenticatesUsers;

    const REQUEST_OK = 200;

    /**
     * @param array $data Data
     *
     * @return array
     */
    public function handle (array $data, $request)
    {
        $data = $this->getAccessToken($data);

        $profileData = $this->getProfile($data);

        $user = User::where('social_id', $profileData['user_id'])->first();

        $name = trim(($profileData['last_name'] ?? '') . ' ' . ($profileData['first_name'] ?? ''));

        if ($user) {
            $user->update([
                'email' => $profileData['email'],
                'name' => $name ?: $profileData['username'],
                'avatar' => $profileData['avatar'] ?? null,
                'last_login_at' => Carbon::now(),
            ]);
        } else {
            $code =Utils::generateRandomString(32);
            $user = User::create([
                'social_id' => $profileData['user_id'],
                'name' => $name ?: $profileData['username'],
                'role' => User::ROLE_USER,
                'status' => User::STATUS_ACTIVE,
                'email' => $profileData['email'],
                'code' => $code,
                'avatar' => $profileData['avatar'] ?? null,
                'password' => bcrypt($profileData['user_id']),
                'last_login_at' => Carbon::now(),
            ]);
            
            event(new Registered($user));
        }

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        Auth::loginUsingId($user->id);

        return $this->authenticated($request, $user);
    }

    /**
     * @param array $data Data
     *
     * @return mixed
     */
    private function getProfile (array $data)
    {
        try {
            $client = new Client();

            $url = config('define.social.domain') . '/api/profile?access_token=' . $data['access_token'];
            
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'server_key' => config('define.social.server_key')
                ]
            ]);
        
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse() ?? null;
        }

        $body = $response->getBody()->getContents() ?? null;

        $body = isset($body) ? json_decode($body, true) : [];

        // dd($body);
        if (isset($body['api_status']) && $body['api_status'] == self::REQUEST_OK) {
            return $body['data'];
        }

        throw new AuthenticationException('Login information is invalid.');
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
            $account = AccountService::create($user);
            $client = new Client();
            
            $url = config('define.api_balance.domain') . '/api/created-balance';

            $res = $client->request('POST', $url, [
                'form_params' => [
                    'social_id' => $user->social_id,
                    'id' => $account->id,
                    'balance' => config('settings.bonuses.sign_up')
                ]
            ]);
            $body = $res->getBody()->getContents() ?? null;

            $body = isset($body) ? json_decode($body, true) : [];

            // $user->account['balance'] = $body['balance'];
            $data = $user->account;
            $data['balance'] = $body['balance'];
            $user->account = $data;
            return $user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin')
                ->loadMissing('account', 'profiles');
            // return UserService::account($user, $account);
        }else{
            $client = new Client();
            $account = $user->account;
            $url = config('define.api_balance.domain') . '/api/created-balance';

            $res = $client->request('POST', $url, [
                'form_params' => [
                    'social_id' => $user->social_id,
                    'id' => $account->id,
                    'balance' => config('settings.bonuses.sign_up')
                ]
            ]);
            $body = $res->getBody()->getContents() ?? null;

            $body = isset($body) ? json_decode($body, true) : [];
             
            $data = $user->account;
            $data['balance'] = $body['balance'];
            $user->account = $data;
            return $user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin')
                ->loadMissing('account', 'profiles');
            // return UserService::user($user);
        }

    }

    /**
     * @param array $profileData Data
     *
     * @return mixed
     */
    private function updateUser (int $userId, array $profileData, array $responseLogin)
    {
        return $this->repository->update([
            'avatar' => $profileData['avatar'],
            'last_login_at' => Carbon::now(),
            'passwor' => $profileData['email'],
            'email' => $profileData['email'],
            'first_name' => $profileData['first_name'],
            'last_name' => $profileData['last_name'],
            'username' => $profileData['username'],
            'properties' => json_encode($profileData),
            'access_token' => $responseLogin['access_token'],
        ], $userId);
    }

    /**
     * @param array $data Data
     *
     * @return mixed
     */
    private function getAccessToken (array $data)
    {
        try {
            $client = new Client();

            $queries = [
                'code' => $data['code'],
                'app_id' => '1a332b034f8372c513ca',
                'app_secret' => 'beedae6421773ad9a1377f212d8a4698a5b08bd',
            ];
            $queries = http_build_query($queries);


            $url = config('define.social.domain') . '/authorize?' . $queries;

            $response = $client->request('GET', $url);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse() ?? null;
        }

        $body = $response->getBody()->getContents() ?? null;

        $body = isset($body) ? json_decode($body, true) : [];

        if (isset($body['status']) && $body['status'] == self::REQUEST_OK) {
            return $body;
        }

        throw new AuthenticationException('Login information is invalid.');
    }

    /**
     * @param array $data Data
     *
     * @return array
     */
    private function getRequestBody (array $data)
    {
        return [
            'server_key' => config('define.social.server_key'),
            'username' => $data['username'],
            'password' => $data['password'],
            'device_type' => 'admin_web',
        ];
    }
}
