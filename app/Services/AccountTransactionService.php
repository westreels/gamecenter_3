<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\GenericAccountTransaction;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\CallApiTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AccountTransactionService
{
    use CallApiTraits;
    /**
     * Create a new account transaction
     *
     * @param Account $account
     * @param Model $transactionable
     * @param float|null $amount
     * @param bool $singleDbTransaction
     * @return AccountTransaction|null
     */
    public function create( $user, Model $transactionable, float $amount = NULL, bool $singleDbTransaction = TRUE)     //: ?AccountTransaction
    {
        $amount = $amount ?: $transactionable->amount;

        if ($amount == 0) {
            return NULL;
        }

        $create = function () use ($user, $transactionable, $amount) {

            $url = config('define.balance_dev.domain') . '/update-balance-system-game';

            $secret_key = getenv('HMAC_GAME');

            if ($amount > 0){
                $postData = [
                    'command' => 'won',
                    'createDate' => time(),
                    'balance' => $amount,
                    'social_id' => Auth::user()->social_id
                ];
            }else{
                $postData = [
                    'command' => 'lose',
                    'createDate' => time(),
                    'balance' => abs($amount),
                    'social_id' => Auth::user()->social_id
                ];
            }

            ksort($postData);
            
            $hash_hmac = hash_hmac('sha512', json_encode($postData), $secret_key);
            
            $postData = array_merge($postData, ['secureHash' => $hash_hmac]);

            $res = Http::post($url, $postData);
            
            $body = $res->getBody()->getContents() ?? null;

            $body = isset($body) ? json_decode($body, true) : [];

            // create account transaction
            $transaction = new AccountTransaction();
            // $transaction->account()->associate($account);
            $transaction->account_id = $transactionable->account_id;
            $transaction->transactionable()->associate($transactionable);
            $transaction->amount = $amount;
            $transaction->balance = $body['data']['balance'];
            // $transaction->balance = $account->balance;
            $transaction->save();

            return $transaction;
        };
        return $singleDbTransaction ? DB::transaction(Closure::fromCallable($create)) : $create();
    }

    /**
     * Create a generic transaction
     *
     * @param Account $account
     * @param int $type
     * @param float $amount
     * @return AccountTransaction|null
     */
    public function createGeneric(Account $account, int $type, float $amount): ?AccountTransaction
    {
        if ($amount == 0) {
            return NULL;
        }

        $genericTransaction = new GenericAccountTransaction();
        $genericTransaction->account()->associate($account);
        $genericTransaction->type = $type;
        $genericTransaction->amount = $amount;
        $genericTransaction->save();

        return $this->create($account, $genericTransaction);
    }
}
