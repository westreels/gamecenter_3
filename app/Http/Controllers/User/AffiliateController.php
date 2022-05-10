<?php

namespace App\Http\Controllers\User;

use App\Helpers\Queries\AffiliateCommissionQuery;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function commissions(Request $request, AffiliateCommissionQuery $query)
    {
        $account = $request->user()->account;

        $items = $query->addWhere(['account_id', '=', $account->id])
            ->get()
            ->makeHidden(['referral_account_id', 'commissionable_id', 'commissionable_type', 'ip']);

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function stats(Request $request)
    {
        $registrations = User::selectRaw('COUNT(DISTINCT tier1.id) AS tier1_count, 
            COUNT(DISTINCT tier2.id) AS tier2_count, 
            COUNT(DISTINCT tier3.id) AS tier3_count')
            ->where('users.id', $request->user()->id)
            ->leftJoin('users AS tier1', 'tier1.referrer_id', '=', 'users.id')
            ->leftJoin('users AS tier2', 'tier2.referrer_id', '=', 'tier1.id')
            ->leftJoin('users AS tier3', 'tier3.referrer_id', '=', 'tier2.id')
            ->groupBy('users.id')
            ->get()
            ->map
            ->setAppends([])
            ->first();

        $commissionsByType = AffiliateCommission::select('type', DB::raw('SUM(amount) AS commissions_total'))
            ->where('account_id', $request->user()->account->id)
            ->groupBy('type')
            ->orderBy('type')
            ->get()
            ->map
            ->setAppends(['title']);

        $commissionsByTier = AffiliateCommission::select('tier', DB::raw('SUM(amount) AS commissions_total'))
            ->where('account_id', $request->user()->account->id)
            ->groupBy('tier')
            ->orderBy('tier')
            ->get()
            ->map
            ->setAppends([])
            ->keyBy('tier');

        return response()->json([
            'registrations' => $registrations,
            'commissions_by_type' => $commissionsByType,
            'commissions_by_tier' => $commissionsByTier
        ]);
    }
}
