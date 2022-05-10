<?php

namespace Packages\Raffle\Models;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\StandardDateFormat;
use Illuminate\Database\Eloquent\Model;

class RaffleTicket extends Model
{
    use StandardDateFormat;

    protected $table = 'raffle_tickets';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'title'
    ];

    protected $fillable = [
        'raffle_id',
        'account_id'
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->morphOne(AccountTransaction::class, 'transactionable');
    }

    public function getTitleAttribute()
    {
        return __('Raffle ticket');
    }
}
