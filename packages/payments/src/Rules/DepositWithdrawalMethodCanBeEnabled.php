<?php

namespace Packages\Payments\Rules;

use App\Rules\ConfigVariablesAreSet;
use App\Rules\PhpExtensionIsInstalled;
use Illuminate\Contracts\Validation\Rule;
use Packages\Payments\Models\PaymentGatewayMethod;

class DepositWithdrawalMethodCanBeEnabled implements Rule
{
    private $paymentMethod;
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(PaymentGatewayMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validations = NULL;

        if ($this->paymentMethod) {
            if ($this->paymentMethod->gateway->code == 'paypal') {
                $validations = [
                    [
                        'class' => ConfigVariablesAreSet::class,
                        'params' => [
                            'payments.paypal.user',
                            'payments.paypal.password',
                            'payments.paypal.signature',
                        ]
                    ]
                ];
            } elseif ($this->paymentMethod->gateway->code == 'stripe') {
                $validations = [
                    [
                        'class' => ConfigVariablesAreSet::class,
                        'params' => [
                            'payments.stripe.public_key',
                            'payments.stripe.secret_key',
                        ]
                    ]
                ];
            } elseif ($this->paymentMethod->gateway->code == 'coinpayments') {
                $validations = [
                    [
                        'class' => ConfigVariablesAreSet::class,
                        'params' => [
                            'payments.coinpayments.merchant_id',
                            'payments.coinpayments.public_key',
                            'payments.coinpayments.private_key',
                            'payments.coinpayments.secret_key',
                        ]
                    ]
                ];
            } elseif ($this->paymentMethod->gateway->code == 'ethereum') {
                $validations = [
                    [
                        'class' => ConfigVariablesAreSet::class,
                        'params' => [
                            'payments.ethereum.api_key',
                        ]
                    ],
                    [
                        'class' => PhpExtensionIsInstalled::class,
                        'params' => 'gmp'
                    ]
                ];
            } elseif ($this->paymentMethod->gateway->code == 'bsc') {
                $validations = [
                    [
                        'class' => ConfigVariablesAreSet::class,
                        'params' => [
                            'payments.bsc.api_key',
                        ]
                    ],
                    [
                        'class' => PhpExtensionIsInstalled::class,
                        'params' => 'gmp'
                    ]
                ];
            } elseif ($this->paymentMethod->gateway->code == 'polygon') {
                $validations = [
                    [
                        'class' => ConfigVariablesAreSet::class,
                        'params' => [
                            'payments.polygon.api_key',
                        ]
                    ],
                    [
                        'class' => PhpExtensionIsInstalled::class,
                        'params' => 'gmp'
                    ]
                ];
            } elseif ($this->paymentMethod->gateway->code == 'tron') {
                $validations = [
                    [
                        'class' => PhpExtensionIsInstalled::class,
                        'params' => 'gmp'
                    ]
                ];
            }
        }

        if ($validations) {
            foreach ($validations as $validation) {
                $rule = new $validation['class']($validation['params']);

                if (!$rule->passes()) {
                    $this->message = $rule->message();
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
