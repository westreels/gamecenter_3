<?php

namespace App\Models;

use App\Models\Scopes\PeriodScope;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use StandardDateFormat;
    use PeriodScope;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'transactionable_type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'float',
    ];

    protected $appends = ['created_ago'];

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Custom getter for created_ago attribute
     */
    public function getCreatedAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : NULL;
    }
}
