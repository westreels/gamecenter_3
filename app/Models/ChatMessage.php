<?php

namespace App\Models;

use App\Models\Scopes\PeriodScope;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use StandardDateFormat;
    use PeriodScope;

    protected $appends = ['created_ago'];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipients()
    {
        return $this
            ->belongsToMany(User::class, 'chat_message_recipients', 'message_id', 'user_id')
            ->as('recipients');
    }

    public function scopeFromRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Getter for created_ago attribute
     *
     * @return mixed
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
