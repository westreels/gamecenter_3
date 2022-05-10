<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use StandardDateFormat;

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'float'
    ];

    protected $appends = ['created_ago', 'updated_ago'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class)->completed();
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
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
