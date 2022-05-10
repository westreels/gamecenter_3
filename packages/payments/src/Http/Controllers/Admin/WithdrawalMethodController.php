<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Helpers\Queries\Query;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Packages\Payments\Http\Requests\Admin\CreateUpdateDepositWithdrawalMethod;
use Packages\Payments\Models\WithdrawalMethod;
use Packages\Payments\Services\PaymentService;

class WithdrawalMethodController extends Controller
{
    public function index(Request $request)
    {
        $query = Query::make(WithdrawalMethod::class);

        $items = $query
            ->addOrderBy(['id', 'name'])
            ->getBuilder()
            ->with('paymentMethod', 'paymentMethod.gateway')
            ->get()
            ->map
            ->append('status_title')
            ->map(function ($method) {
                $method->makeVisible('enabled');

                if ($method->paymentMethod) {
                    $method->paymentMethod->makeVisible(['parameters', 'allowed_currencies']);
                }
                return $method;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(WithdrawalMethod $withdrawalMethod)
    {
        $method = $withdrawalMethod
            ->loadMissing('paymentMethod', 'paymentMethod.gateway')->makeVisible('enabled');

        if ($method->paymentMethod) {
            $method->paymentMethod->makeVisible(['parameters', 'allowed_currencies']);
        }

        return $method;
    }

    public function store(CreateUpdateDepositWithdrawalMethod $request)
    {
        $withdrawalMethod = new WithdrawalMethod();
        $withdrawalMethod->name = $request->name;
        $withdrawalMethod->description = $request->description;
        $withdrawalMethod->currency = $request->currency;
        $withdrawalMethod->rate = $request->rate;
        $withdrawalMethod->decimal_places = !$request->payment_method_id ? $request->decimal_places : NULL;
        $withdrawalMethod->payment_method_parameters = $request->payment_method_parameters;
        $withdrawalMethod->parameters = $request->parameters;
        $withdrawalMethod->enabled = $request->enabled;

        if ($request->payment_method_id) {
            $withdrawalMethod->paymentMethod()->associate($request->payment_method_id);
        }

        $withdrawalMethod->save();

        return $this->successResponse(__('Withdrawal method successfully created.'));
    }

    public function update(CreateUpdateDepositWithdrawalMethod $request, WithdrawalMethod $withdrawalMethod)
    {
        $withdrawalMethod->name = $request->name;
        $withdrawalMethod->description = $request->description;
        $withdrawalMethod->currency = $request->currency;
        $withdrawalMethod->rate = $request->rate;
        $withdrawalMethod->decimal_places = !$withdrawalMethod->payment_method_id ? $request->decimal_places : NULL;
        $withdrawalMethod->payment_method_parameters = $request->payment_method_parameters;
        $withdrawalMethod->parameters = $request->parameters;
        $withdrawalMethod->enabled = $request->enabled;
        $withdrawalMethod->save();

        return $this->successResponse(__('Withdrawal method successfully updated.'));
    }

    public function destroy(WithdrawalMethod $withdrawalMethod)
    {
        $withdrawalMethod->delete();

        return $this->successResponse(__('Withdrawal method successfully deleted.'));
    }

    public function info(WithdrawalMethod $withdrawalMethod)
    {
        $paymentService = PaymentService::createFromModel($withdrawalMethod);

        abort_if(!method_exists($paymentService, 'fetchAccountInfo'), 404);

        $cacheKey = 'payments.withdrawal-methods.' . $withdrawalMethod->id . '.info';

        // get data from cache
        $data = Cache::get($cacheKey);

        // if data is not present in the cache or it's expired
        if (!$data) {
            // request is successful
            if ($data = $paymentService->fetchAccountInfo()) {
                Cache::put($cacheKey, $data, now()->addMinutes(30));
            // if request is not successful return an error message
            } else {
                return $this->errorResponse($paymentService->getErrorMessage());
            }
        }

        return $this->successResponse(['data' => $data]);
    }

    public function balance(WithdrawalMethod $withdrawalMethod)
    {
        $paymentService = PaymentService::createFromModel($withdrawalMethod);

        abort_if(!method_exists($paymentService, 'fetchBalance'), 404);

        $cacheKey = 'payments.withdrawal-methods.' . $withdrawalMethod->id . '.balance';

        // get data from cache
        $data = Cache::get($cacheKey);

        // if data is not present in the cache or it's expired
        if (!$data) {
            // request is successful
            if ($data = $paymentService->fetchBalance()) {
                Cache::put($cacheKey, $data, now()->addMinutes(5));
                // if request is not successful return an error message
            } else {
                return $this->errorResponse($paymentService->getErrorMessage());
            }
        }

        return $this->successResponse(['data' => $data]);
    }
}
