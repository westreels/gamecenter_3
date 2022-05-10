<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Bonus;
use App\Models\GenericAccountTransaction;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\CallApiTraits;
use Illuminate\Support\Facades\Auth;

class AccountServiceTest
{
    use CallApiTraits;
    private $account;

    /**
     * AccountService constructor.
     *
     * @param null $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account->getKey() ? $account : request()->account;
    }

    // public function __construct($account)
    // {
    //     $this->account = $account;
    // }

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

            $url = config('define.api_balance.domain') . '/api/save-balance';
            // dd($this->account);
            $user = User::where('id', $this->account)->first();
            $data = [
                'form_params' => [
                    'social_id' => $user->social_id,
                    'amount' => $amount
                ]
            ];
            $body = $this->callapi('POST', $url, $data);

            // update account balance
            // if ($amount > 0)
            //     $this->account->increment('balance', $amount);
            // else
            //     $this->account->decrement('balance', abs($amount));

            // create account transaction
            $transaction = new AccountTransaction();
            // $transaction->account()->associate($this->account);
            $transaction->account_id = $transactionable->account_id;
            $transaction->amount = $amount;
            // $transaction->balance = $this->account->balance;
            $transaction->balance = $body['balance'];
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
