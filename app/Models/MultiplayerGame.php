<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MultiplayerGame extends Model
{
    protected $dates = ['start_time', 'end_time'];

    protected $appends = ['start_time_unix', 'end_time_unix'];

    public function provablyFairGame()
    {
        return $this->belongsTo(ProvablyFairGame::class);
    }

    public function gameable()
    {
        return $this->morphTo();
    }

    /**
     * Mutator for start_time column
     * Carbon object is explicitly converted to string in order to save milliseconds, because otherwise they are not saved properly (PHP PDO bug?)
     *
     * @param  Carbon  $date
     */
    public function setStartTimeAttribute(Carbon $date)
    {
        $this->attributes['start_time'] = $date->toDateTimeString('millisecond');
    }

    /**
     * Mutator for end_time column
     * Carbon object is explicitly converted to string in order to save milliseconds, because otherwise they are not saved properly (PHP PDO bug?)
     *
     * @param  Carbon  $date
     */
    public function setEndTimeAttribute(Carbon $date)
    {
        $this->attributes['end_time'] = $date->toDateTimeString('millisecond');
    }

    /**
     * Getter for start_time_unix attribute
     *
     * @return int|null
     */
    public function getStartTimeUnixAttribute(): ?int
    {
        return $this->start_time ? $this->start_time->valueOf() : NULL;
    }

    /**
     * Getter for end_time_unix attribute
     *
     * @return int|null
     */
    public function getEndTimeUnixAttribute(): ?int
    {
        return $this->end_time ? $this->end_time->valueOf() : NULL;
    }

    /**
     * Getter for is_betting_in_progress
     *
     * @return bool
     */
    public function getIsBettingInProgressAttribute(): bool
    {
        $now = Carbon::now();

        return $this->start_time
            && $this->end_time
            && $this->start_time->lte($now)
            && $this->end_time->gte($now);
    }
}
