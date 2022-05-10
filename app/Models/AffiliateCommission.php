<?php

namespace App\Models;

use App\Models\Scopes\PeriodScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    use StandardDateFormat;
    use PeriodScope;

    const TYPE_SIGN_UP = 1;
    const TYPE_GAME_LOSS = 2;
    const TYPE_GAME_WIN = 3;
    const TYPE_DEPOSIT = 4;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'title',
        'status_title',
        'is_pending',
        'created_ago'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'commissions_total' => 'float'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status'
    ];

    public function commissionable()
    {
        return $this->morphTo();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function referralAccount()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope a query to only include pending commissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query): Builder
    {
        return $query->where('status', '=', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved commissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query): Builder
    {
        return $query->where('status', '=', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected commissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query): Builder
    {
        return $query->where('status', '=', self::STATUS_REJECTED);
    }

    public function getTitleAttribute()
    {
        switch ($this->type) {
            case self::TYPE_SIGN_UP:
                return __('Sign up');
                break;

            case self::TYPE_GAME_LOSS:
                return __('Game loss');
                break;

            case self::TYPE_GAME_WIN:
                return __('Game win');
                break;

            case self::TYPE_DEPOSIT:
                return __('Deposit');
                break;

            default:
                return __('Commission');
        }
    }

    public function getStatusTitleAttribute()
    {
        if ($this->status == self::STATUS_PENDING) {
           return __('Pending');
        } elseif ($this->status == self::STATUS_APPROVED) {
            return __('Approved');
        } elseif ($this->status == self::STATUS_REJECTED) {
            return __('Rejected');
        }
    }

    /**
     * Getter for is_pending attribute
     */
    public function getIsPendingAttribute()
    {
        return $this->status == self::STATUS_PENDING;
    }

    /**
     * Custom getter for created_ago attribute
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : NULL;
    }
}
