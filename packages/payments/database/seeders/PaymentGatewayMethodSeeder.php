<?php

use Illuminate\Database\Seeder;
use Packages\Payments\Models\PaymentGateway;
use Packages\Payments\Models\PaymentGatewayMethod;

class PaymentGatewayMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gateways = PaymentGateway::all()->keyBy('code');

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'express'
            ],
            [
                'payment_gateway_id' => $gateways['paypal']->id,
                'name' => 'Express Checkout',
                'parameters' => [],
                'input_parameters' => [],
                'allowed_currencies' => [
                    'AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','INR','ILS','JPY','MYR','MXN',
                    'TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','THB','USD'
                ]
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'card'
            ],
            [
                'payment_gateway_id' => $gateways['stripe']->id,
                'name' => 'Debit / Credit Card',
                'parameters' => [],
                'input_parameters' => [],
                'allowed_currencies' => [
                    'USD','AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM',
                    'BBD','BDT','BGN','BIF','BMD','BND','BOB','BRL','BSD','BWP','BZD','CAD',
                    'CDF','CHF','CLP','CNY','COP','CRC','CVE','CZK','DJF','DKK','DOP','DZD',
                    'EGP','ETB','EUR','FJD','FKP','GBP','GEL','GIP','GMD','GNF','GTQ','GYD',
                    'HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','ISK','JMD','JPY','KES',
                    'KGS','KHR','KMF','KRW','KYD','KZT','LAK','LBP','LKR','LRD','LSL','MAD',
                    'MDL','MGA','MKD','MMK','MNT','MOP','MRO','MUR','MVR','MWK','MXN','MYR',
                    'MZN','NAD','NGN','NIO','NOK','NPR','NZD','PAB','PEN','PGK','PHP','PKR',
                    'PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SEK','SGD',
                    'SHP','SLL','SOS','SRD','STD','SZL','THB','TJS','TOP','TRY','TTD','TWD',
                    'TZS','UAH','UGX','UYU','UZS','VND','VUV','WST','XAF','XCD','XOF','XPF',
                    'YER','ZAR','ZMW',
                ]
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'coinpayments'
            ],
            [
                'payment_gateway_id' => $gateways['coinpayments']->id,
                'name' => 'Crypto',
                'parameters' => [],
                'input_parameters' => [],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'coinpayments'
            ],
            [
                'payment_gateway_id' => $gateways['coinpayments']->id,
                'name' => 'Crypto',
                'parameters' => [],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'ethereum'
            ],
            [
                'payment_gateway_id' => $gateways['ethereum']->id,
                'name' => 'Ethereum',
                'parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited coins will be sent to.'),
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => ['ETH', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'ethereum'
            ],
            [
                'payment_gateway_id' => $gateways['ethereum']->id,
                'name' => 'Ethereum',
                'parameters' => [],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => ['ETH', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'erc20'
            ],
            [
                'payment_gateway_id' => $gateways['ethereum']->id,
                'name' => 'ERC20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ],
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited tokens will be sent to.'),
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'erc20'
            ],
            [
                'payment_gateway_id' => $gateways['ethereum']->id,
                'name' => 'ERC20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ]
                ],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'bnb'
            ],
            [
                'payment_gateway_id' => $gateways['bsc']->id,
                'name' => 'BNB',
                'parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited coins will be sent to.'),
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => ['BNB', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'bnb'
            ],
            [
                'payment_gateway_id' => $gateways['bsc']->id,
                'name' => 'BNB',
                'parameters' => [],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => ['BNB', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'bep20'
            ],
            [
                'payment_gateway_id' => $gateways['bsc']->id,
                'name' => 'BEP20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('BEP20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('BEP20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ],
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited tokens will be sent to.'),
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'bep20'
            ],
            [
                'payment_gateway_id' => $gateways['bsc']->id,
                'name' => 'BEP20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('BEP20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('BEP20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ]
                ],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'matic'
            ],
            [
                'payment_gateway_id' => $gateways['polygon']->id,
                'name' => 'Polygon',
                'parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited coins will be sent to.'),
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => ['MATIC', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'matic'
            ],
            [
                'payment_gateway_id' => $gateways['polygon']->id,
                'name' => 'Polygon',
                'parameters' => [],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => ['MATIC', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'erc20_polygon'
            ],
            [
                'payment_gateway_id' => $gateways['polygon']->id,
                'name' => 'ERC20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ],
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited tokens will be sent to.'),
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'erc20_polygon'
            ],
            [
                'payment_gateway_id' => $gateways['polygon']->id,
                'name' => 'ERC20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|size:42',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('ERC20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ]
                ],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'tron'
            ],
            [
                'payment_gateway_id' => $gateways['tron']->id,
                'name' => 'Tron',
                'parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => '',
                        'validation'    => 'required|min:32|max:64',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => ['TRX', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'tron'
            ],
            [
                'payment_gateway_id' => $gateways['tron']->id,
                'name' => 'Tron',
                'parameters' => [],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => ['TRX', 'USD'],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_IN,
                'code' => 'trc20'
            ],
            [
                'payment_gateway_id' => $gateways['tron']->id,
                'name' => 'TRC20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('TRC20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|min:20|max:50',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('TRC20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ],
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Deposit address'),
                        'description'   => __('The address, where deposited tokens will be sent to.'),
                        'validation'    => 'required|min:20|max:50',
                        'default'       => ''
                    ],
                ],
                'input_parameters' => [],
                'allowed_currencies' => [],
            ]
        );

        PaymentGatewayMethod::firstOrCreate(
            [
                'type' => PaymentGatewayMethod::TYPE_OUT,
                'code' => 'trc20'
            ],
            [
                'payment_gateway_id' => $gateways['tron']->id,
                'name' => 'TRC20 Token',
                'parameters' => [
                    [
                        'id'            => 'contract_address',
                        'type'          => 'input',
                        'name'          => __('TRC20 token contract address'),
                        'description'   => '',
                        'validation'    => 'required|min:20|max:50',
                        'default'       => ''
                    ],
                    [
                        'id'            => 'contract_decimals',
                        'type'          => 'input',
                        'name'          => __('TRC20 token contract decimals'),
                        'description'   => '',
                        'validation'    => 'integer|min:0|max:20',
                        'default'       => 18
                    ]
                ],
                'input_parameters' => [
                    [
                        'id'            => 'address',
                        'type'          => 'input',
                        'name'          => __('Address'),
                        'description'   => '',
                        'validation'    => 'required',
                        'default'       => '',
                    ]
                ],
                'allowed_currencies' => [],
            ]
        );
    }
}
