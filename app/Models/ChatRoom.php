<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use StandardDateFormat;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status_title', 'created_ago', 'updated_ago'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }

    /**
     * Scope a query to only include enabled rooms.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query): Builder
    {
        return $query->where('enabled', TRUE);
    }

    public function getStatusTitleAttribute()
    {
        return $this->enabled
            ? __('Enabled')
            : __('Disabled');
    }

    /**
     * Custom getter for created_ago attribute
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : NULL;
    }

    /**
     * Custom getter for updated_ago attribute
     */
    public function getUpdatedAgoAttribute()
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : NULL;
    }
}
