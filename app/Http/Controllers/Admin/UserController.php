<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Queries\UserQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUser;
use App\Models\User;
use App\Notifications\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(UserQuery $query)
    {
        $items = $query
            ->get()
            ->map
            ->makeVisible('referrer')
            ->map
            ->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin', 'is_bot', 'is_active');

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(User $user)
    {
        return [
            'user' => $user
                ->append('two_factor_auth_enabled')
                ->makeVisible('referrer')
                ->loadMissing('referrer'),
            'roles' => User::roles(),
            'permissions' => User::permissions(),
            'access_modes' => User::accessModes(),
        ];
    }

    public function update(UpdateUser $request, User $user)
    {
        foreach ($request->all() as $property => $value) {
            if ($property == 'password' && $value) {
                $user->password = Hash::make($value);
            } elseif ($property == 'avatar') {
                $user->avatar = Str::afterLast($request->avatar, '/');
            } else {
                $user->{$property} = $value;
            }
        }

        return $user->save();
    }

    /**
     * Delete the specified user
     *
     * @param Request $request
     * @param User $user
     * @return bool|null
     * @throws \Exception
     */
    public function destroy(Request $request, User $user)
    {
        // check that the user being deleted is not current
        if ($request->user()->id == $user->id) {
            abort(409, __('You can not delete currently logged user.'));
        }

        // delete user
        return $user->delete();
    }

    /**
     * Send mail
     *
     * @param Request $request
     * @param User $user
     * @return bool
     */
    public function mail(Request $request, User $user)
    {
        try {
            $user->notify(new UserMessage($request->subject, $request->greeting, $request->message));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, $e->getMessage());
        }

        return TRUE;
    }

    /**
     * Search a user
     */
    public function search(Request $request)
    {
        $clause = is_int($request->search) ? 'id = ' . $request->search : 'LOWER(name) LIKE "%' . $request->search . '%"';
        return User::select('id', 'name', 'email')->whereRaw($clause)->orderBy('name')->get();
    }
}
