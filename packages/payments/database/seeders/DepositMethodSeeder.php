<?php

use Illuminate\Database\Seeder;
use Packages\Payments\Models\DepositMethod;
use Packages\Payments\Models\PaymentGatewayMethod;

class DepositMethodSeeder extends Seeder
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
                'payment_method_code' => 'express',
                'name' => 'PayPal',
                'currency' => 'USD',
                'rate' => 100,
                'payment_method_parameters' => [],
                'parameters' => [],
            ],
            [
                'payment_method_code' => 'card',
                'name' => 'Debit / Credit Card',
                'currency' => 'USD',
                'rate' => 100,
                'payment_method_parameters' => [],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'coinpayments',
                'name' => 'Crypto',
                'currency' => 'LTCT',
                'rate' => 10000,
                'payment_method_parameters' => [],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'ethereum',
                'name' => 'ETH (Metamask)',
                'currency' => 'ETH',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'address' => '0xAa5CE79bfC453761b084a780044AFf329F002538'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'erc20',
                'name' => 'ERC20 Token (Metamask)',
                'currency' => 'WEENUS',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => '0xaFF4481D10270F50f203E0763e2597776068CBc5',
                    'contract_decimals' => 18,
                    'address' => '0xAa5CE79bfC453761b084a780044AFf329F002538'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'bnb',
                'name' => 'BNB (Metamask)',
                'currency' => 'BNB',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'address' => '0xAa5CE79bfC453761b084a780044AFf329F002538'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'bep20',
                'name' => 'BEP20 Token (Metamask)',
                'currency' => 'BUSD',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => '0x8301F2213c0eeD49a7E28Ae4c3e91722919B8B47',
                    'contract_decimals' => 18,
                    'address' => '0xAa5CE79bfC453761b084a780044AFf329F002538'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'matic',
                'name' => 'MATIC (Metamask)',
                'currency' => 'MATIC',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'address' => '0xAa5CE79bfC453761b084a780044AFf329F002538'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'erc20_polygon',
                'name' => 'ERC20 Polygon Token (Metamask)',
                'currency' => 'TST',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => '0x2d7882bedcbfddce29ba99965dd3cdf7fcb10a1e',
                    'contract_decimals' => 18,
                    'address' => '0xAa5CE79bfC453761b084a780044AFf329F002538'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'tron',
                'name' => 'TRX (Tronlink)',
                'currency' => 'TRX',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'address' => 'TJ4GkhFnFete6Z899PphT8KdJ5vc1wvvu4'
                ],
                'parameters' => []
            ],
            [
                'payment_method_code' => 'trc20',
                'name' => 'TRC20 Token (Tronlink)',
                'currency' => 'FPLG',
                'rate' => 10000,
                'payment_method_parameters' => [
                    'contract_address' => 'TCTFYnpLALRwQ7t84Bc38VvhPc8G5AF45p',
                    'contract_decimals' => 18,
                    'address' => 'TJ4GkhFnFete6Z899PphT8KdJ5vc1wvvu4'
                ],
                'parameters' => []
            ],
        ];

        foreach ($methods as $method) {
            $paymentMethodId = isset($method['payment_method_code'])
                ? (PaymentGatewayMethod::where('code', $method['payment_method_code'])
                        ->whereIn('type', [PaymentGatewayMethod::TYPE_IN, PaymentGatewayMethod::TYPE_BOTH])
                        ->first()
                        ->id ?? NULL
                )
                : NULL;

            DepositMethod::firstOrCreate(
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
