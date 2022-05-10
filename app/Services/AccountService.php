<?php

namespace App\Services;

use App\Http\Traits\CallApiTraits;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Bonus;
use App\Models\GenericAccountTransaction;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AccountService
{
    use CallApiTraits;
    private $account;

    /**
     * AccountService constructor.
     *
     * @param null $account
     */
    // public function __construct(Account $account)
    // {
    //     $this->account = $account->getKey() ? $account : request()->account;
    // }

    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Create a new account transaction of specific type
     *
     * @param Model $transactionable
     * @param null $amount
     * @return AccountTransaction|NULL
     */
    public function createTransaction(Model $transactionable, float $amount = NULL): ?AccountTransaction
    {
        $amount = $amount ?: $transactionable->amount;

        if (floatval($amount) == 0)
            return NULL;

        return DB::transaction(function () use ($transactionable, $amount) {

            $url = config('define.balance_dev.domain') . '/update-balance-system-game';

            $secret_key = getenv('HMAC_GAME');
            
            if(!Auth::user()){
                return null;
            }

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
            $transaction->account_id = $transactionable->account_id;
            $transaction->amount = $amount;
            $transaction->balance = $body['data']['balance'];
            $transactionable->transaction()->save($transaction);

            return $transaction;
        });
    }

    /**
     * Create a generic transaction
     *
     * @param int $type
     * @param float $amount
     * @return AccountTransaction|NULL
     */
    public function createGenericTransaction(int $type, float $amount): ?AccountTransaction
    {
        if (floatval($amount) == 0)
            return NULL;

        $genericTransaction = new GenericAccountTransaction();
        $genericTransaction->account()->associate($this->account);
        $genericTransaction->type = $type;
        $genericTransaction->amount = $amount;
        $genericTransaction->save();

        return $this->createTransaction($genericTransaction);
    }

    /**
     * Create a new bonus
     *
     * @param int $type
     * @param float $amount
     * @return Bonus|null
     */
    public function createBonus(int $type, float $amount): ?Bonus
    {
        if (floatval($amount) == 0)
            return NULL;

        // create bonus
        $bonus = new Bonus();
        $bonus->account()->associate($this->account);
        $bonus->type = $type;
        $bonus->amount = $amount;
        $bonus->save();

        $this->createTransaction($bonus, $amount);

        return $bonus;
    }

    /**
     * Create a new user account
     *
     * @param User $user
     * @return Account
     */
    public static function create(User $user): Account
    {
        $account = new Account();
        $account->user()->associate($user);
        $account->save();

        return $account;
    }
}
