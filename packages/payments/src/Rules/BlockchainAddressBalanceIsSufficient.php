<?php

namespace Packages\Payments\Rules;

use Illuminate\Contracts\Validation\Rule;
use Packages\Payments\Services\PaymentService;

class BlockchainAddressBalanceIsSufficient implements Rule
{
    protected $paymentService;
    protected $address;
    protected $balance;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(PaymentService $paymentService, $address)
    {
        $this->paymentService = $paymentService;
        $this->address = $address;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $depositAmount
     * @return bool
     */
    public function passes($attribute, $depositAmount)
    {
        $this->balance = $this->paymentService->fetchAddressBalance($this->address);
        return $this->balance >= $depositAmount / $this->paymentService->getPaymentCurrencyRate();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Insufficient balance (:balance) to perform this operation.', ['balance' => $this->balance]);
    }
}
