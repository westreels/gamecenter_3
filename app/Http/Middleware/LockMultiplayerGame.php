<?php

namespace App\Http\Middleware;

use App\Models\MultiplayerGame;
use Closure;
use Illuminate\Support\Facades\DB;

class LockMultiplayerGame
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // start a new DB transaction, this is requried to obtain a row level lock
        DB::beginTransaction();

        // obtain a row level lock on all related games
        // other processes which will try to obtain the lock at the same time will wait until current request completes
        $multiplayerGame = $request->route('multiplayerGame');
        if ($multiplayerGame instanceof MultiplayerGame) {
            $multiplayerGame->gameable()->lockForUpdate()->get();
        }

        // complete the request
        $response = $next($request);

        // commit the DB transaction and thus unlock the locked table rows
        // other processes waiting for the lock release will resume
        DB::commit();

        return $response;
    }
}
