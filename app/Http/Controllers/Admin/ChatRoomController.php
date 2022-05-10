<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Queries\Query;
use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function index()
    {
        $query = Query::make(ChatRoom::class);

        $items = $query
            ->addOrderBy(['id', 'name', 'messages_count'])
            ->getBuilder()
            ->withCount('messages')
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(ChatRoom $room)
    {
        return $room;
    }

    public function update(Request $request, ChatRoom $room)
    {
        $room->name = $request->name;
        $room->enabled = $request->enabled;

        return $room->save();
    }

    public function store(Request $request)
    {
        $chatRoom = new ChatRoom();
        $chatRoom->name = $request->name;
        $chatRoom->save();

        return $chatRoom->save();
    }

    public function destroy(Request $request, ChatRoom $room)
    {
        return $room->delete();
    }
}
