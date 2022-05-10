<?php

use Illuminate\Database\Seeder;
use Packages\Payments\Models\PaymentGatewayMethod;
use Packages\Payments\Models\WithdrawalMethod;

class WithdrawalMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = [
            [
                'name' => 'Wire Transfer',
                'currency' => 'USD',
                'rate' => 100,
                'payment_method_parameters' => [],
                'parameters' => [
                    [
                        'id' => 'name',
                        'type' => 'input',
                        'name' => __('Name'),
                        'description' => '',
                        'validation' => 'required|min:3|max:50',
                        'default' => ''
                    ],
                    [
                        'id' => 'iban',
                        'type' => 'input',
                        'name' => __('Bank account number'),
                        'description' => '',
                        'validation' => 'required',
                        'default' => ''
                    ],
                    [
                        'id' => 'bank_code',
                        'type' => 'input',
                        'name' => __('Bank BIC / SWIFT code'),
                        'description' => '',
                        'validation' => 'required',
                        'default' => ''
                    ],
                    [
                        'id' => 'bank_name',
                        'type' => 'input',
                        'name' => __('Bank name'),
                        'description' => '',
                        'validation' => 'required',
                        'default' => ''
                    ],
                    [
                        'id' => 'bank_branch',
                        'type' => 'input',
                        'name' => __('Bank branch'),
                        'description' => '',
                        'validation' => 'required',
                        'default' => ''
                    ],
                    [
                        'id' => 'bank_address',
                        'type' => 'input',
                        'name' => __('Bank address'),
                        'description' => '',
                        'validation' => 'required',
                        'default' => ''
                    ],
                    [
                        'id' => 'comment',
                        'type' => 'textarea',
                        'name' => __('Comment'),
                        'description' => '',
                        'validation' => '',
                        'default' => ''
                    ]
                ]
            ],
            [
                'payment_method_code' => 'coinpayments',
                'name' => 'Crypto',
                'currency' => 'LTCT',
                'rate' => 10000,
                'payment_method_parameters' => [],
                'parameters' => [
                    [
                        'id' => 'comment',
                        'type' => 'textarea',
                        'name' => __('Comment'),
                        'description' => '',
                        'validation' => '',
                        'default' => ''
                    ]
                ]
            ],
            [
                'payment_method_code' => 'ethereum',
                'name' => 'ETH (Metamask)',
                'currency' => 'ETH',
                'rate' => 10000,
                'payment_method_parameters' => [],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'erc20',
                'name' => 'ERC20 Token (Metamask)',
                'currency' => 'WEENUS',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => '0xaFF4481D10270F50f203E0763e2597776068CBc5',
                    'contract_decimals' => 18
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'bnb',
                'name' => 'BNB (Metamask)',
                'currency' => 'BNB',
                'rate' => 10000,
                'payment_method_parameters' => [],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'bep20',
                'name' => 'BEP20 Token (Metamask)',
                'currency' => 'BUSD',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => '0x8301F2213c0eeD49a7E28Ae4c3e91722919B8B47',
                    'contract_decimals' => 18
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'matic',
                'name' => 'MATIC (Metamask)',
                'currency' => 'MATIC',
                'rate' => 10000,
                'payment_method_parameters' => [],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'erc20_polygon',
                'name' => 'ERC20 Polygon Token (Metamask)',
                'currency' => 'TST',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => '0x2d7882bedcbfddce29ba99965dd3cdf7fcb10a1e',
                    'contract_decimals' => 18
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'tron',
                'name' => 'Tron (Tronlink)',
                'currency' => 'TRX',
                'rate' => 10000,
                'payment_method_parameters' => [],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'trc20',
                'name' => 'TRC20 Token (Tronlink)',
                'currency' => 'FPLG',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => 'TCTFYnpLALRwQ7t84Bc38VvhPc8G5AF45p',
                    'contract_decimals' => 18
                ],
                'parameters' => []
            ],
        ];

        foreach ($methods as $method) {
            $paymentMethodId = isset($method['payment_method_code'])
                ? (PaymentGatewayMethod::where('code', $method['payment_method_code'])
                        ->whereIn('type', [PaymentGatewayMethod::TYPE_OUT, PaymentGatewayMethod::TYPE_BOTH])
                        ->first()
                        ->id ?? NULL
                )
                : NULL;

            WithdrawalMethod::firstOrCreate(
                [
                    'payment_method_id' => $paymentMethodId
                ],
                [
                    'name' => $method['name'],
                    'currency' => $method['currency'],
                    'rate' => $method['rate'],
                    'payment_method_parameters' => $method['payment_method_parameters'],
                    'parameters' => $method['parameters'],
                    'enabled' => FALSE,
                ]
            );
        }
    }
}
