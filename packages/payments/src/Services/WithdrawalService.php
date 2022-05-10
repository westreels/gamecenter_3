<?php

namespace Packages\Payments\Services;

use App\Exceptions\BusinessException;
use App\Exceptions\ForbiddenException;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Packages\Payments\Events\WithdrawalCreated;
use Packages\Payments\Models\Withdrawal;

class WithdrawalService
{
    /**
     * Call api sign
     *
     * @param array $params
     */
    public function callApiSign(array $params)
    {
        $client = new Client();

        $url = config('payments.url_pay_withdrawal') . '/api/sign/1';

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'API_KEY' => config('payments.api_key_withdrawal'),
                    'domain' => [
                        'name' => 'BIZVERSE-VRA',
                        'version' => '1',
                        'verifyingContract' => '0x98d8D64700f99042FB78a09bbA6B0F1b784F7a7a',
                        'chainId' => '137',
                    ],
                    'types' => [
                        'TransferPermission' => [
                            [
                                'name' => 'from',
                                'type' => 'address',
                            ],
                            [
                                'name' => 'to',
                                'type' => 'address',
                            ],
                            [
                                'name' => 'amount',
                                'type' => 'uint256',
                            ],
                            [
                                'name' => 'nonce',
                                'type' => 'uint256',
                            ],
                        ]
                    ],
                    'message' => [
                        'from' => config('payments.address_wallet_total'),
                        'to' => $params['recipient'],
                        'amount' => $params['amount'],
                        'nonce' =>  $this->getNonce(),
                    ]
                ])
            ]);
        } catch (GuzzleException $e) {
            dd($e);
            Log::error($e);
            $response = $e->getResponse() ?? null;
        }

        $response = $response->getBody()->getContents() ?? null;

        $response = $response ? json_decode($response, true) : $response;

        $status = isset($response) && isset($response['success']) && $response['success'];

        if ($status) {
            return $response['data'];
        }

        throw new BusinessException('An error occurred while accessing the server');
    }

    /**
     * getNonce
     */
    public function getNonce()
    {
        $client = new Client();

        $url = config('payments.noralis_withdrawal_url') . '/basic/nonceVRA';

        try {
            $response = $client->request('GET', $url);
        } catch (GuzzleException $e) {
            dd($e);
            Log::error($e);
            $response = $e->getResponse() ?? null;
        }

        $response = $response->getBody()->getContents() ?? null;

        $response = $response ? json_decode($response, true) : $response;

        if (isset($response['result'])) {
            return $response['result'];
        }

        throw new BusinessException('An error occurred while accessing the server');
    }

    /**
     * Withdrawal
     *
     * @param Withdrawal $withdrawal
     */
    public function pay(Withdrawal $withdrawal)
    {
        $client = new Client();

        $url = config('payments.url_pay_withdrawal') . '/api/withdraw/1';

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'API_KEY' => config('payments.api_key_withdrawal'),
                    'recipient' => $withdrawal->parameters->address,
                    'amount' => intval($withdrawal->amount) * 10000,
                ])
            ]);
        } catch (GuzzleException $e) {
            $response = $e->getResponse() ?? null;
        }

        $response = $response->getBody()->getContents() ?? null;

        $response = $response ? json_decode($response, true) : $response;

        $status = isset($response) && isset($response['success']) && $response['success'];

        if ($status) {
            $withdrawal->status = Withdrawal::STATUS_COMPLETED;
            $withdrawal->save();
            return $response['data'];
        }

        throw new BusinessException('An error occurred while accessing the server');
    }

    /**
     * Create withdrawal
     *
     * @param array $data Data
     *
     * @return Withdrawal
     */
    public function createWithdrawal(array $data)
    {
        $hash = hash_hmac('sha512', json_encode($data['data']), config('define.hash_secret_key'));

        if ($hash !== $data['hash']) {
            throw new ForbiddenException('Hash is invalid.');
        }

        $withdrawal = new Withdrawal();
        $user = User::where('social_id', $data['data']['user_id'])->first();

        $withdrawal->account()->associate($user->account);
        $withdrawal->amount = $data['data']['amount'];
        $withdrawal->payment_amount = $data['data']['amount'];
        $withdrawal->status = Withdrawal::STATUS_COMPLETED;
        $withdrawal->payment_currency = 'VRA';
        $withdrawal->save();

        event(new WithdrawalCreated($withdrawal));
        return $withdrawal;
    }
}
