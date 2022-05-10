<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GameRoom extends Model
{
    const STATUS_OPEN  = 0;
    const STATUS_CLOSED = 1;

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'object'
    ];

    /**
     * The attributes that should be hidden from JSON output.
     *
     * @var array
     */
    protected $hidden = [
        'status',
        'gameable_type'
    ];

    /**
     * Getter for is_open attribute
     *
     * @return bool
     */
    public function getIsOpenAttribute()
    {
        return $this->status == self::STATUS_OPEN;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Players who joined the room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function players()
    {
        return $this->hasMany(GameRoomPlayer::class);
    }

    /**
     * Get player by ID
     *
     * @param Model $model
     * @return GameRoomPlayer|null
     */
    public function player(Model $model): ?GameRoomPlayer
    {
        $column = $model instanceof Game ? 'game_id' : 'user_id';

        return $this->players()->where($column, $model->id)->get()->first();
    }

    /**
     * Get player by ID
     *
     * @param int $id
     * @param string $column
     * @return GameRoomPlayer|null
     */
    public function playerById(int $id, $column = 'user_id'): ?GameRoomPlayer
    {
        return $this->players()->where($column, $id)->get()->first();
    }

    /**
     * Players who joined the room and started a game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activePlayers()
    {
        return $this->players()->whereNotNull('game_id');
    }

    public function scopeOpen($query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
