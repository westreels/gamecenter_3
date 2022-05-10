<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Helpers\Queries\Query;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Packages\Payments\Http\Requests\Admin\CreateUpdateDepositWithdrawalMethod;
use Packages\Payments\Models\DepositMethod;
use Packages\Payments\Services\PaymentService;

class DepositMethodController extends Controller
{
    public function index(Request $request)
    {
        $query = Query::make(DepositMethod::class);

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

    public function show(DepositMethod $depositMethod)
    {
        $method = $depositMethod
            ->loadMissing('paymentMethod', 'paymentMethod.gateway')->makeVisible('enabled');

        if ($method->paymentMethod) {
            $method->paymentMethod->makeVisible(['parameters', 'allowed_currencies']);
        }

        return $method;
    }



    public function store(CreateUpdateDepositWithdrawalMethod $request)
    {
        $depositMethod = new DepositMethod();
        $depositMethod->name = $request->name;
        $depositMethod->description = $request->description;
        $depositMethod->currency = $request->currency;
        $depositMethod->rate = $request->rate;
        $depositMethod->decimal_places = !$request->payment_method_id ? $request->decimal_places : NULL;
        $depositMethod->payment_method_parameters = $request->payment_method_parameters;
        $depositMethod->parameters = $request->parameters;
        $depositMethod->enabled = $request->enabled;

        if ($request->payment_method_id) {
            $depositMethod->paymentMethod()->associate($request->payment_method_id);
        }

        $depositMethod->save();

        return $this->successResponse(__('Deposit method successfully created.'));
    }

    public function update(CreateUpdateDepositWithdrawalMethod $request, DepositMethod $depositMethod)
    {
        $depositMethod->name = $request->name;
        $depositMethod->description = $request->description;
        $depositMethod->currency = $request->currency;
        $depositMethod->rate = $request->rate;
        $depositMethod->decimal_places = !$depositMethod->payment_method_id ? $request->decimal_places : NULL;
        $depositMethod->payment_method_parameters = $request->payment_method_parameters;
        $depositMethod->parameters = $request->parameters;
        $depositMethod->enabled = $request->enabled;
        $depositMethod->save();

        return $this->successResponse(__('Deposit method successfully updated.'));
    }

    public function destroy(DepositMethod $depositMethod)
    {
        $depositMethod->delete();

        return $this->successResponse(__('Deposit method successfully deleted.'));
    }

    public function info(DepositMethod $depositMethod)
    {
        $paymentService = PaymentService::createFromModel($depositMethod);

        abort_if(!method_exists($paymentService, 'fetchAccountInfo'), 404);

        $cacheKey = 'payments.deposit-methods.' . $depositMethod->id . '.info';

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

    public function balance(DepositMethod $depositMethod)
    {
        $paymentService = PaymentService::createFromModel($depositMethod);

        abort_if(!method_exists($paymentService, 'fetchBalance'), 404);

        $cacheKey = 'payments.deposit-methods.' . $depositMethod->id . '.balance';

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
