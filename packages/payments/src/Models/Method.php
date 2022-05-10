<?php

namespace Packages\Payments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Packages\Payments\Services\MulticurrencyPaymentService;
use Packages\Payments\Services\PaymentService;

class Method extends Model
{
    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'rate'                      => 'float',
        'payment_method_parameters' => 'object',
        'parameters'                => 'object', // array of objects
        'enabled'                   => 'integer',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'enabled',
        'created_at',
        'updated_at',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentGatewayMethod::class, 'payment_method_id');
    }

    /**
     * Scope a query to only include enabled methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query): Builder
    {
        return $query->where('enabled', TRUE);
    }

    public function getStatusTitleAttribute()
    {
        return $this->enabled
            ? __('Enabled')
            : __('Disabled');
    }

    /**
     * Getter for payment_currencies attribute
     * For performance reasons it should NOT be added to $appends[] and should be appended manually on a query basis.
     *
     * @return Collection
     */
    public function getPaymentCurrenciesAttribute(): Collection
    {
        if ($this->paymentMethod) {
            $paymentService = PaymentService::createFromModel($this);

            if ($paymentService instanceof MulticurrencyPaymentService) {
                return $paymentService->fetchPaymentCurrencies();
            }
        }

        return collect();
    }
}
