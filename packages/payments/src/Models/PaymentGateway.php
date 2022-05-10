<?php

namespace Packages\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'deposit_parameters' => 'object', // array of objects
        'withdrawal_parameters' => 'object', // array of objects
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deposit_supported',
        'withdrawal_supported',
        'created_at',
        'updated_at',
    ];

    public function paymentMethods()
    {
        return $this->hasMany(PaymentGatewayMethod::class, 'payment_gateway_id');
    }
}
