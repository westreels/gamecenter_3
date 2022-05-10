<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ChatMessage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'chat_message';
    }
}
