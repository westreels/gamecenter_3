<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Bonus;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $cacheTtl = 30*60; // 30 minutes

    public function getData(Request $request, string $key)
    {
        $method = 'get' . Str::studly($key);

        return method_exists($this, $method)
            ? $this->successResponse(['data' => call_user_func_array([$this, $method], [$request])])
            : $this->errorResponse(__('Unknown method') . ' ' . $method);
    }

    protected function getUsersCount(): int
    {
        return Cache::remember('admin.dashboard.users-count', $this->cacheTtl, function () {
            return User::all()->count();
        });
    }

    protected function getUsersBase(Request $request): Collection
    {
        // user sign ups grouped by difference in days between now and sign up date
        $signUps = Cache::remember('admin.dashboard.users-base', $this->cacheTtl, function () {
            return User::selectRaw('ABS(DATEDIFF(created_at, CURDATE())) AS diff, COUNT(*) AS signup_count')
                ->where('created_at', '>', Carbon::now()->subDays(6))
                ->groupBy('diff')
                ->orderBy('diff', 'asc')
                ->get()
                ->keyBy('diff')
                ->map(function ($item) {
                    return $item['signup_count'];
                });
        });

        $carry = $this->getUsersCount();

        // cumulative user base growth over last 7 days
        return collect()->pad(7, 0)
            ->map(function ($item, $key) use ($signUps, &$carry) {
                if ($key > 0) {
                    $carry = $carry - ($signUps[$key-1] ?? 0);
                }

                return  $carry;
            })
            ->reverse()
            ->values();
    }

    protected function getUsersSignUpsComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return User::period($period)->count();
        };

        return $this->getComparison('users-sign-ups', $getData);
    }

    protected function getUsersSignUpsByMethod(Request $request): Collection
    {
        $getData = function () use ($request) {
            return User::selectRaw('IFNULL(social_profiles.provider_name, "email") AS source, COUNT(*) AS count')
                ->period($request->query('period'))
                ->leftJoin('social_profiles', 'social_profiles.user_id', '=', 'users.id')
                ->groupBy('source')
                ->orderBy('count', 'desc')
                ->get()
                ->map
                ->setAppends([]);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.users-sign-ups-by-method', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAffiliatesCommissionsHistory(Request $request): Collection
    {
        return $this->getWeeklySeries('admin.dashboard.affiliates-comissions-history', function () {
            return AffiliateCommission::selectRaw('SUM(amount) AS commissions_total, WEEK(created_at, 1) AS week_number')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->commissions_total;
        });
    }

    protected function getAffiliatesReferralsComparison(): Collection
    {
        $getData = function ($period) {
            return User::period($period)
                ->whereNotNull('referrer_id')
                ->count();
        };

        return $this->getComparison('affiliates-referrals', $getData);
    }

    protected function getAffiliatesCommissionsByStatus(Request $request): Collection
    {
        $getData = function () use ($request) {
            return collect([
                (float) AffiliateCommission::period($request->query('period'))
                    ->pending()
                    ->sum('amount'),
                (float) AffiliateCommission::period($request->query('period'))
                    ->approved()
                    ->sum('amount'),
                (float) AffiliateCommission::period($request->query('period'))
                    ->rejected()
                    ->sum('amount')
            ]);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.affiliates-commissions-by-status', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getAffiliatesCommissionsByType(Request $request): Collection
    {
        $getData = function () use ($request) {
            return AffiliateCommission::select(
                    'type',
                    DB::raw('COUNT(id) AS commissions_count'),
                    DB::raw('SUM(amount) AS commissions_total')
                )
                ->period($request->query('period'))
                ->groupBy('type')
                ->orderBy('type')
                ->get()
                ->map
                ->setAppends(['title']);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.affiliates-commissions-by-type', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getGamesGgrComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return Game::completed()
                ->selectRaw('SUM(bet) - SUM(win) AS ggr')
                ->period($period)
                ->first()
                ->ggr ?: 0;
        };

        return $this->getComparison('games-bets', $getData);
    }

    protected function getGamesGgrMarginComparison(Request $request): Collection
    {
        $getData = function ($period) {
            return Game::completed()
                ->selectRaw('100 * (1 - SUM(win) / SUM(bet)) AS ggr_margin')
                ->period($period)
                ->first()
                ->ggr_margin ?: 0;
        };

        return $this->getComparison('games-ggr-margin', $getData);
    }

    protected function getGamesWageredByWeek(Request $request): Collection
    {
        return $this->getWeeklySeries('admin.dashboard.games-wagered-by-week', function () {
            return Game::completed()
                ->selectRaw('SUM(bet) AS bet_total, WEEK(created_at, 1) AS week_number')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->bet_total;
        });
    }

    protected function getGamesGgrByWeek(Request $request): Collection
    {
        return $this->getWeeklySeries('admin.dashboard.games-ggr-by-week', function () {
            return Game::completed()
                ->selectRaw('SUM(bet) - SUM(win) AS ggr, WEEK(created_at, 1) AS week_number')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->ggr;
        });
    }

    protected function getGamesStats(Request $request): Collection
    {
        $getData = function () use ($request) {
            return Game::completed()
                ->selectRaw('gameable_type,
                    COUNT(id) AS bet_count,
                    SUM(bet) AS bet_total,
                    SUM(win) AS win_total,
                    100 * (1 - SUM(win) / SUM(bet)) AS ggr_margin')
                ->period($request->query('period'))
                ->whereHas('account.user', function ($query) use ($request) {
                    $query->userRole($request->query('roles'));
                })
                ->groupBy('gameable_type')
                ->get()
                ->map
                ->makeHidden(['created'])
                ->map
                ->setAppends(['title'])
                ->sortBy('title') // need to order the result collection, because order by gameable_type is not always correct
                ->values();
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.games-stats', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getBonusesByWeek(Request $request): Collection
    {
        return $this->getWeeklySeries('admin.dashboard.bonuses-by-week', function () {
            return Bonus::selectRaw('SUM(amount) AS amount, WEEK(created_at, 1) AS week_number')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
                ->groupBy('week_number')
                ->orderBy('week_number', 'asc')
                ->get()
                ->keyBy('week_number')
                ->map
                ->amount;
        });
    }

    protected function getBonusesStats(Request $request): Collection
    {
        $getData = function () use ($request) {
            return Bonus::selectRaw('type,
                    COUNT(id) AS count,
                    MIN(amount) AS amount_min,
                    MAX(amount) AS amount_max,
                    AVG(amount) AS amount_avg,
                    SUM(amount) AS amount')
                ->period($request->query('period'))
                ->whereHas('account.user', function ($query) use ($request) {
                    $query->userRole($request->query('roles'));
                })
                ->groupBy('type')
                ->get()
                ->map
                ->setAppends(['title']);
        };

        return empty($request->query()) // cache requests without filters
            ? Cache::remember('admin.dashboard.bonuses-stats', $this->cacheTtl, $getData)
            : $getData();
    }

    protected function getComparison(string $cacheKey, callable $getData): Collection
    {
        $currentPeriod = request()->query('period') ?: 'month';
        $previousPeriod = 'prev_' . $currentPeriod;

        $previousPeriodData = $currentPeriod == 'month'
            ? Cache::remember('admin.dashboard.' . $cacheKey . '-previous', $this->cacheTtl, function() use ($getData, $previousPeriod) { return $getData($previousPeriod); })
            : $getData($previousPeriod);

        $currentPeriodData = $currentPeriod == 'month'
            ? Cache::remember('admin.dashboard.' . $cacheKey . '-current', $this->cacheTtl, function() use ($getData, $currentPeriod) { return $getData($currentPeriod); })
            : $getData($currentPeriod);

        return collect([$previousPeriodData, $currentPeriodData]);
    }

    protected function getWeeklySeries(string $cacheKey, callable $getData): Collection
    {
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($getData) {
            $data = $getData();
            $series = collect();

            for ($i = 7; $i >= 0; $i--) {
                $weekNumber = Carbon::now()->subWeeks($i)->weekOfYear;
                $series->put($weekNumber, $data->has($weekNumber) ? $data->get($weekNumber) : 0);
            }

            return $series->values();
        });
    }
}
