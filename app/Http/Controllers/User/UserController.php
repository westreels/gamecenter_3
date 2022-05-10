<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\GetUser;
use App\Http\Requests\UpdateUser;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\CallApiTraits;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    use CallApiTraits;
    public function show(Request $request)
    {
        $user = $request->user();
        $account = $request->user()->account;
    
        $url = config('define.api_balance.domain') . '/api/get-balance/social/' . $user->social_id;

        $body = $this->callapi('GET', $url, []);

        $user->account->balance = $body['balance'];
        
        return $user
                ->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin')
                ->loadMissing('account', 'profiles');
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $variables = collect($request->only('name', 'hide_profit'));

        // user can change the email only if it's not verified
        if (!$user->hasVerifiedEmail()) {
            $variables->put('email', $request->email);
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // delete previous avatar if it's set
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $fileName = $user->id . '_' . time() . '.' . $request->avatar->extension();
            // save uploaded logo in storage
            $request->avatar->storeAs('avatars', $fileName, 'public');
            $variables->put('avatar', $fileName);
        }

        return tap($user)->update($variables->toArray())->loadMissing('account');
    }

    /**
     * Public user profile
     *
     * @param GetUser $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(GetUser $request, User $user)
    {
        $isAdminOrCurrentUser = $request->user()->is_admin || $request->user()->id == $user->id;

        $getUserStats = function () use ($user, $isAdminOrCurrentUser) {
            return $user
                ->account
                ->games()
                ->selectRaw('COUNT(*) AS bet_count')
                ->selectRaw('IFNULL(SUM(IF(win > bet,1,0)),0) AS win_count')
                ->selectRaw('IFNULL(SUM(bet),0) AS bet_total')
                ->when(!$user->hide_profit || $isAdminOrCurrentUser, function ($query) {
                    $query->selectRaw('IFNULL(SUM(win-bet),0) AS profit_total')
                        ->selectRaw('IFNULL(MAX(win-bet),0) AS profit_max');
                })
                ->get()
                ->map
                ->makeHidden(['title', 'profit', 'is_completed', 'created'])
                ->first();
        };

        $stats = $isAdminOrCurrentUser
            ? $getUserStats()
            : Cache::remember('user.' . $user->id . '.profile', 15*60, $getUserStats);

        return response()->json([
            'user' => $user->only('id', 'name', 'avatar', 'created_ago'),
            'stats' => $stats
        ]);
    }

}
