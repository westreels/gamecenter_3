<?php

namespace Packages\Raffle\Models;

use App\Models\StandardDateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Raffle extends Model
{
    use StandardDateFormat;

    const STATUS_IN_PROGRESS = 0;
    const STATUS_COMPLETED = 1;

    const TRIGGER_TIME = 1;
    const TRIGGER_TICKETS = 2;

    // required to replicate a raffle
    protected $fillable = [
        'status',
        'win',
        'start_date',
        'end_date'
    ];

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'win' => 'float',
        'fee' => 'float',
        'recurring' => 'boolean'
    ];

    protected $appends = [
        'is_in_progress',
        'is_completed',
        'status_title',
        'pot_size',
        'max_pot_size',
        'end_date_unix',
        'updated_ago'
    ];

    protected $dates = ['start_date', 'end_date'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'win'       => 0,
        'status'    => self::STATUS_IN_PROGRESS,
    ];

    /**
     * Tickets of a particular raffle
     *
     * @param null $account
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|mixed
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(RaffleTicket::class);
    }

    public function winningTicket(): BelongsTo
    {
        return $this->belongsTo(RaffleTicket::class, 'winning_ticket_id');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Custom attribute: $raffle->pot_size
     *
     * @return float|int
     */
    public function getPotSizeAttribute()
    {
        return $this->tickets->count() * $this->ticket_price * (1 - $this->fee / 100);
    }

    /**
     * Custom attribute: $raffle->max_pot_size
     *
     * @return float|int
     */
    public function getMaxPotSizeAttribute()
    {
        return $this->total_tickets * $this->ticket_price * (1 - $this->fee / 100);
    }

    /**
     * Custom attribute: $raffle->fee_amount
     *
     * @return float|int
     */
    public function getFeeAmountAttribute()
    {
        return $this->tickets->count() * $this->ticket_price * $this->fee / 100;
    }

    /**
     * Custom attribute: $raffle->is_in_progress
     *
     * @return bool
     */
    public function getIsInProgressAttribute()
    {
        return $this->status == Raffle::STATUS_IN_PROGRESS;
    }

    /**
     * Custom attribute: $raffle->is_completed
     *
     * @return bool
     */
    public function getIsCompletedAttribute()
    {
        return $this->status == Raffle::STATUS_COMPLETED;
    }

    /**
     * Custom attribute: $raffle->status_title
     *
     * @return bool
     */
    public function getStatusTitleAttribute()
    {
        return $this->is_completed ? __('Completed') : __('In progress');
    }

    /**
     * Custom attribute: $raffle->duration
     *
     * @return int
     */
    public function getDurationAttribute(): int
    {
        return $this->start_date && $this->end_date ? $this->start_date->diffInSeconds($this->end_date) : 0;
    }

    /**
     * Custom attribute: $raffle->is_end_date_passed
     *
     * @return mixed
     */
    public function getIsEndDatePassedAttribute()
    {
        return $this->end_date && $this->end_date->lt(Carbon::now());
    }

    /**
     * Custom attribute: $raffle->end_date_unix
     *
     * @return int|null
     */
    public function getEndDateUnixAttribute(): ?int
    {
        return $this->end_date ? $this->end_date->timestamp : NULL;
    }

    /**
     * Custom getter for updated_ago attribute
     */
    public function getUpdatedAgoAttribute()
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : NULL;
    }
}
