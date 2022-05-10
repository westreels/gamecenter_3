<?php

namespace App\Services;

use App\Events\ChatMessageSent;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;

class ChatMessageService
{
    public function send(ChatRoom $room, User $sender, string $message, array $recipients = [])
    {
        $chatMessage = new ChatMessage();
        $chatMessage->room()->associate($room);
        $chatMessage->user()->associate($sender);
        $chatMessage->message = $message;
        $chatMessage->save();

        if (!empty($recipients)) {
            $chatMessage->recipients()->attach($recipients);
        }

        broadcast(new ChatMessageSent($room, $chatMessage));

        return $chatMessage;
    }
}
