<?php

namespace App\Http\Middleware;

use App\Models\GameRoom;
use Closure;
use Illuminate\Support\Facades\DB;

class LockGameRoom
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

        // get the game room ID
        $roomId = (int) $request->room_id ?: ($request->room && $request->room instanceof GameRoom ? $request->room->id : 0);

        // obtain a row level lock on the given game room
        // other processes which will try to obtain the lock at the same time will wait until current request completes
        if ($roomId){
            GameRoom::where('id', $roomId)->lockForUpdate()->first();
        }

        // complete the request
        $response = $next($request);

        // commit the DB transaction and thus unlock the game room
        // other processes waiting for the lock release will resume
        DB::commit();

        return $response;
    }
}
