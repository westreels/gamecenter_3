<?php

namespace Packages\Payments\Models;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\AffiliateCommission;
use App\Models\PercentageAffiliateCommission;
use App\Models\Scopes\PeriodScope;
use App\Models\StandardDateFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Packages\Payments\Helpers\Ethereum;
use Packages\Payments\Helpers\Tron;

class Deposit extends Model implements PercentageAffiliateCommission
{
    use StandardDateFormat;
    use PeriodScope;

    const STATUS_CREATED   = 0;
    const STATUS_PENDING   = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 10;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'title',
        'status_title',
        'is_created',
        'is_pending',
        'is_completed',
        'is_cancelled',
        'payment_amount_wei',
        'payment_amount_sun',
        'created_ago'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'status',
        'response'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount'            => 'float',
        'payment_amount'    => 'float',
        'amount_min'        => 'float',
        'amount_max'        => 'float',
        'amount_avg'        => 'float',
        'parameters'        => 'object',
        'response'          => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'parameters',
        'response'
    ];

    public function commission()
    {
        return $this->morphMany(AffiliateCommission::class, 'commissionable');
    }

    /**
     * Scope a query to only include created deposits.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreated($query): Builder
    {
        return $query->where('status', self::STATUS_CREATED);
    }

    /**
     * Scope a query to only include pending deposits.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include completed deposits.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->morphOne(AccountTransaction::class, 'transactionable');
    }

    public function method()
    {
        return $this->belongsTo(DepositMethod::class, 'deposit_method_id');
    }

    public function getTitleAttribute()
    {
        return __('Deposit');
    }

    /**
     * Getter for is_created attribute
     *
     * @return bool
     */
    public function getIsCreatedAttribute()
    {
        return $this->status == self::STATUS_CREATED;
    }

    /**
     * Getter for is_pending attribute
     *
     * @return bool
     */
    public function getIsPendingAttribute()
    {
        return $this->status == self::STATUS_PENDING;
    }

    /**
     * Getter for is_completed attribute
     *
     * @return bool
     */
    public function getIsCompletedAttribute()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * Getter for is_cancelled attribute
     *
     * @return bool
     */
    public function getIsCancelledAttribute()
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    public function getStatusTitleAttribute()
    {
        if ($this->status == self::STATUS_CREATED) {
            return __('Created');
        } elseif ($this->status == self::STATUS_PENDING) {
            return __('Pending');
        } elseif ($this->status == self::STATUS_COMPLETED) {
            return __('Completed');
        } elseif ($this->status == self::STATUS_CANCELLED) {
            return __('Cancelled');
        }
    }

    public function getAffiliateCommissionBaseAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get payment amount in wei for ETH / BNB and ERC20 tokens
     *
     * @return string
     */
    public function getPaymentAmountWeiAttribute()
    {
        return is_object($this->parameters) && isset($this->parameters->contractDecimals)
            ? Ethereum::toWei($this->payment_amount, $this->parameters->contractDecimals)
            : (in_array($this->payment_currency, ['ETH', 'BNB', 'MATIC']) && $this->payment_amount
                ? Ethereum::toWei($this->payment_amount, 18)
                : '0');
    }

    /**
     * Get payment amount in sun for TRX and TRC20 tokens
     *
     * @return string
     */
    public function getPaymentAmountSunAttribute()
    {
        return is_object($this->parameters) && isset($this->parameters->contractDecimals)
            ? Tron::toSun($this->payment_amount, $this->parameters->contractDecimals)
            : ($this->payment_currency == 'TRX' && $this->payment_amount
                ? Tron::toSun($this->payment_amount)
                : '0');
    }

    /**
     * Custom getter for created_ago attribute
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : NULL;
    }
}
