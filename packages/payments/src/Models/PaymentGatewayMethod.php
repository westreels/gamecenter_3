<?php

namespace Packages\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayMethod extends Model
{
    const TYPE_IN       = 1; // incoming payment methods
    const TYPE_OUT      = 2; // outgoing payment methods
    const TYPE_BOTH     = 3; // incoming & outgoing payment methods

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'object', // array of objects
        'input_parameters' => 'object', // array of objects
        'allowed_currencies' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'type',
        'parameters',
        'allowed_currencies',
        'created_at',
        'updated_at'
    ];

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }
}
