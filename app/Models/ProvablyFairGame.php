<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProvablyFairGame extends Model
{
    use StandardDateFormat;

    protected $hidden = [
        'id', 'secret', 'server_seed', 'gameable_type', 'updated_at'
    ];

    public function game()
    {
        return $this->hasOne(Game::class);
    }

    /**
     * Setter for secret attribute, also implicitly sets seed and hash
     *
     * @param $secret
     */
    public function setSecretAttribute($secret)
    {
        $this->attributes['secret'] = $secret;
        $this->attributes['server_seed'] = Str::random(16); // generate random seed
        $this->attributes['hash'] = hash_hmac('sha256', $this->attributes['secret'], $this->attributes['server_seed']);
    }

    public function getClientHashAttribute()
    {
        return $this->secret != '' && $this->server_seed != '' && $this->client_seed ? hash_hmac('sha256', $this->secret . $this->server_seed, $this->client_seed) : '';
    }

    public function getShiftValueAttribute()
    {
        return $this->secret != '' && $this->server_seed != '' && $this->client_seed ? hexdec(substr($this->client_hash, -5)) : 0;
    }
}
