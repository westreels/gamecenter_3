<?php

namespace Packages\Plinko\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class Plinko extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_plinko';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'path' => 'collection',
    ];

    protected $appends = [
        'bucket'
    ];

    public function getBucketAttribute(): int
    {
        return $this->path->sum();
    }

    /**
     * Format $gameable->result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return '';
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'path';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('The first N steps of the ball path are removed and appended to the end of the path. N is the remainder of dividing the Shift value by the number of rows.');
    }
}
