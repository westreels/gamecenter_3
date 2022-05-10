<?php

namespace App\Http\Controllers;

use App\Helpers\PackageManager;
use App\Http\Requests\CreateGameRoom;
use App\Http\Requests\GetGameRooms;
use App\Http\Requests\JoinGameRoom;
use App\Http\Requests\LeaveGameRoom;
use App\Models\GameRoom;
use App\Models\GameRoomPlayer;
use App\Models\User;
use Illuminate\Http\Request;

class GameRoomController extends Controller
{
    public function index(GetGameRooms $request, $packageId, PackageManager $packageManager)
    {
        // get all open rooms and players
        $rooms = GameRoom::where('gameable_type', $packageManager->get($packageId)->model)
            ->open()
            ->with('players')
            ->withCount('players')
            ->orderBy('id', 'desc')
            ->get();

        // find a room which current user has joined
        $room = $rooms
            ->filter(function ($room) use ($request) {
                return $room->player($request->user());
            })
            ->first();

        // find the game model for the given room and user
        $game = $room
            ? $room->player($request->user())->game
            : NULL;

        return $room
            ? [
                'room' => $room,
                'game' => $game ? $game->loadMissing('gameable')->append('server_time') : NULL
            ]
            : [
                // filter out rooms that are already full
                'rooms' => $rooms
                    ->filter(function ($room) {
                        return $room->players_count < $room->parameters->players_count;
                    })
                    ->values()
                    ->map
                    ->only(['id', 'name', 'parameters', 'players_count'])
            ];
    }

    /**
     * Create game room and join it automatically
     *
     * @param Request $request
     * @param $packageId
     * @param PackageManager $packageManager
     * @return array
     */
    public function create(CreateGameRoom $request, $packageId, PackageManager $packageManager)
    {
        $room = new GameRoom();
        $room->owner()->associate($request->user());
        $room->name = $request->name;
        $room->gameable_type = $packageManager->get($packageId)->model;
        $room->status = GameRoom::STATUS_OPEN;
        $room->parameters = $request->parameters;
        $room->save();

        return $this->joinGameRoom($room, $request->user());
    }

    /**
     * Join existing game room
     *
     * @param Request $request
     * @param GameRoom $gameRoom
     * @return array
     */
    public function join(JoinGameRoom $request, $packageId)
    {
        $room = GameRoom::find($request->room_id);

        return $this->joinGameRoom($room, $request->user());
    }

    /**
     * Leave existing game room
     *
     * @param JoinGameRoom $request
     * @param $packageId
     * @return array
     */
    public function leave(LeaveGameRoom $request, $packageId)
    {
        GameRoomPlayer::where('game_room_id', $request->room_id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return $this->successResponse();
    }

    private function joinGameRoom(GameRoom $room, User $user)
    {
        $player = new GameRoomPlayer();
        $player->room()->associate($room);
        $player->user()->associate($user);
        $player->save();

        return $this->successResponse(['room' => $room]);
    }
}
