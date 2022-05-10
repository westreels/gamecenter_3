<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Helpers\PackageManager;
use App\Http\Controllers\Admin\DashboardController as ParentDashboardController;
use App\Models\Bonus;
use App\Models\Game;
use App\Models\GenericAccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Packages\Payments\Models\Deposit;
use Packages\Payments\Models\Withdrawal;

class DashboardController extends ParentDashboardController
{
    protected function getStats(Request $request): Collection
    {
        $period = request()->query('period');
        $roles = request()->query('roles');

        return Cache::remember('admin.dashboard.accounting-stats-' . ($period ?: 'all') . '-' . ($roles ? implode('-', $roles) : 'all'), $this->cacheTtl, function () use ($period, $roles) {
            $packageManager = app()->make(PackageManager::class);
            $raffleModelClass = '\\Packages\\Raffle\\Models\\Raffle';

            $data = collect(array(
                'ggr' => (int) Game::completed()
                    ->selectRaw('SUM(bet) - SUM(win) AS ggr')
                    ->period($period)
                    ->whereHas('account.user', function ($query) use ($roles) {
                        $query->userRole($roles);
                    })
                    ->first()
                    ->ggr ?: 0,

                'bonuses' => (int) Bonus::period($period)
                    ->whereHas('account.user', function ($query) use ($roles) {
                        $query->userRole($roles);
                    })
                    ->sum('amount'),

                'commissions' => (int) GenericAccountTransaction::commissions()
                    ->period($period)
                    ->whereHas('account.user', function ($query) use ($roles) {
                        $query->userRole($roles);
                    })
                    ->sum('amount'),

                'debits' => abs(GenericAccountTransaction::debits()
                    ->period($period)
                    ->whereHas('account.user', function ($query) use ($roles) {
                        $query->userRole($roles);
                    })
                    ->sum('amount')),

                'credits' => (int) GenericAccountTransaction::credits()
                    ->period($period)
                    ->whereHas('account.user', function ($query) use ($roles) {
                        $query->userRole($roles);
                    })
                    ->sum('amount'),

                'raffle_fees' => $packageManager->enabled('raffle')
                    ? (int) $raffleModelClass::completed()->period($period)->sum('fee')
                    : 0
            ));

            $data->put('ngr', $data->get('ggr') - $data->get('bonuses') - $data->get('commissions') - $data->get('credits') + $data->get('debits') + $data->get('raffle_fees'));

            return $data;
        });
    }

    protected function getAccountingBalance(Request $request): Collection
    {
        $getData = function () use ($request) {
            return collect([
                (float) Deposit::completed()->period($request->query('period'))->sum('amount'),
                (float) Withdrawal::completed()->period($request->query('period'))->sum('amount')
            ]);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.accounting-balance', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAccountingDepositsComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return (float) Deposit::completed()
                ->period($period)
                ->sum('amount');
        };

        return $this->getComparison('accounting-deposits', $getData);
    }

    protected function getAccountingDepositsHistory(Request $request): Collection
    {
        return $this->getWeeklySeries('admin.dashboard.accounting-deposits-history', function () {
            return Deposit::select(                    DB::raw('SUM(amount) AS amount'),
                    DB::raw('WEEK(created_at, 1) AS week_number')
                )
                ->completed()
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->amount;
        });
    }

    protected function getAccountingWithdrawalsHistory(Request $request): Collection
    {
        return $this->getWeeklySeries('admin.dashboard.accounting-withdrawals-history', function () {
            return Withdrawal::select(
                    DB::raw('SUM(amount) AS amount'),
                    DB::raw('WEEK(created_at, 1) AS week_number')
                )
                ->completed()
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->amount;
        });
    }

    protected function getAccountingDepositsByStatus(Request $request): Collection
    {
        $getData = function () use ($request) {
            return Deposit::select(
                    'status',
                    DB::raw('COUNT(id) AS count'),
                    DB::raw('MIN(amount) AS amount_min'),
                    DB::raw('MAX(amount) AS amount_max'),
                    DB::raw('AVG(amount) AS amount_avg'),
                    DB::raw('SUM(amount) AS amount')
                )
                ->period($request->query('period'))
                ->groupBy('status')
                ->orderBy('status')
                ->get();
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.accounting-deposits-by-status', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAccountingWithdrawalsByStatus(Request $request): Collection
    {
        $getData = function () use ($request) {
            return Withdrawal::select(
                    'status',
                    DB::raw('COUNT(id) AS count'),
                    DB::raw('MIN(amount) AS amount_min'),
                    DB::raw('MAX(amount) AS amount_max'),
                    DB::raw('AVG(amount) AS amount_avg'),
                    DB::raw('SUM(amount) AS amount')
                )
                ->period($request->query('period'))
                ->groupBy('status')
                ->orderBy('status')
                ->get();
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.accounting-withdrawals-by-status', $this->cacheTtl, $getData)
            : $getData();
    }
}
