<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Queries\AccountQuery;
use App\Helpers\Queries\AccountTransactionQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountCredit;
use App\Http\Requests\Admin\AccountDebit;
use App\Models\Account;
use App\Models\GenericAccountTransaction;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(AccountQuery $query)
    {
        $items = $query
            ->get()
            ->map(function ($account) {
                $account->user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin', 'is_bot', 'is_active');
                return $account;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(Account $account)
    {
        return [
            'account' => $account->load('user')
        ];
    }

    /**
     * Debit user account
     * It's important to type hint Account, so $this->account can be automatically resolved in AccountDebit request
     *
     * @param AccountDebit $request
     * @param Account $account
     * @param AccountService $accountService
     */
    public function debit(AccountDebit $request, Account $account, AccountService $accountService)
    {
        $accountService->createGenericTransaction(GenericAccountTransaction::TYPE_DEBIT, -$request->amount);

        return TRUE;
    }

    /**
     * Credit user account
     * It's important to type hint Account, so $this->account can be automatically resolved in AccountDebit request
     *
     * @param AccountDebit $request
     * @param Account $account
     * @param AccountService $accountService
     */
    public function credit(AccountCredit $request, Account $account, AccountService $accountService)
    {
        $accountService->createGenericTransaction(GenericAccountTransaction::TYPE_CREDIT, $request->amount);

        return TRUE;
    }

    public function transactions(AccountTransactionQuery $query, Account $account)
    {
        $items = $query
            ->addWhere(['account_id', '=', $account->id])
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }
}
