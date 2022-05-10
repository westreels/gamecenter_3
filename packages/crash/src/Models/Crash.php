<?php

namespace Packages\Crash\Models;

use App\Models\Gameable;
use App\Models\MultiplayerGameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Crash extends Model
{
    use Gameable;
    use MultiplayerGameable;

    protected $dates = ['start_time', 'end_time'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_crash';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'winners'       => 'collection', // [ { userId: payout }, ... ]
        'max_payout'    => 'float',
    ];

    /**
     * The attributes that should be hidden from JSON output.
     *
     * @var array
     */
    protected $hidden = [
        'max_payout', 'end_time'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'start_time_unix'
    ];

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
}
