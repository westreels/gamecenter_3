<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Packages\Payments\Events\DepositCancelled;
use Packages\Payments\Events\DepositCompleted;
use Packages\Payments\Helpers\Queries\Admin\DepositQuery;
use Packages\Payments\Http\Requests\Admin\ChangeDepositStatus;
use Packages\Payments\Models\Deposit;
use Packages\Payments\Services\PaymentService;

class DepositController extends Controller
{
    public function index(DepositQuery $query)
    {
        $items = $query
            ->get()
            ->map(function ($deposit) {
                $deposit->account->user->append(
                    'two_factor_auth_enabled',
                    'two_factor_auth_passed',
                    'is_admin',
                    'is_bot',
                    'is_active'
                );
                return $deposit;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(Deposit $deposit)
    {
        return [
            'deposit' => $deposit
                ->load('account.user', 'method')
                ->makeVisible(['response', 'parameters'])
        ];
    }

    public function transaction(Deposit $deposit)
    {
        $paymentService = PaymentService::createFromModel($deposit->method);

        abort_if(!method_exists($paymentService, 'fetchTransaction'), 404);

        $cacheKey = 'payments.deposits.' . $deposit->id . '.transaction';

        // get data from cache
        $data = Cache::get($cacheKey);

        // if data is not present in the cache or it's expired
        if (!$data) {
            // request is successful, stored data in cache
            if ($data = $paymentService->fetchTransaction($deposit->external_id)) {
                Cache::put($cacheKey, $data, now()->addMinute());
            // if request is not successful return an error message
            } else {
                return $this->errorResponse($paymentService->getErrorMessage());
            }
        }

        return $this->successResponse(['data' => $data]);
    }

    public function cancel(ChangeDepositStatus $request, Deposit $deposit)
    {
        $deposit->update(['status' => Deposit::STATUS_CANCELLED]);

        event(new DepositCancelled($deposit));

        return $this->successResponse(__('Deposit is rejected.'));
    }

    public function complete(ChangeDepositStatus $request, Deposit $deposit)
    {
        $deposit->update(['status' => Deposit::STATUS_COMPLETED]);

        event(new DepositCompleted($deposit));

        return $this->successResponse(__('Deposit is completed.'));
    }
}
