<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Queries\AffiliateCommissionQuery;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\GenericAccountTransaction;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function tree()
    {
        $registrations = User::select(
            'users.id AS id',
            'users.name AS name',
            'tier1.id AS tier1_id',
            'tier1.name AS tier1_name',
            'tier2.id AS tier2_id',
            'tier2.name AS tier2_name',
            'tier3.id AS tier3_id',
            'tier3.name AS tier3_name'
            )
            ->whereNull('users.referrer_id')
            ->join('users AS tier1', 'tier1.referrer_id', '=', 'users.id')
            ->leftJoin('users AS tier2', 'tier2.referrer_id', '=', 'tier1.id')
            ->leftJoin('users AS tier3', 'tier3.referrer_id', '=', 'tier2.id')
            ->orderBy('users.id')
            ->get()
            ->map
            ->setAppends([])
            ->reduce(function ($carry, $item) {
                if (!array_key_exists($item['id'], $carry)) {
                    $carry[$item['id']] = ['id' => $item['id'], 'name' => $item['name'], 'children' => []];
                }

                if ($item['tier1_id'] && !array_key_exists($item['tier1_id'], $carry[$item['id']]['children'])) {
                    $carry[$item['id']]['children'][$item['tier1_id']] = ['id' => $item['tier1_id'], 'name' => $item['tier1_name'], 'children' => []];
                }

                if ($item['tier2_id'] && !array_key_exists($item['tier2_id'], $carry[$item['id']]['children'][$item['tier1_id']]['children'])) {
                    $carry[$item['id']]['children'][$item['tier1_id']]['children'][$item['tier2_id']] = ['id' => $item['tier2_id'], 'name' => $item['tier2_name'], 'children' => []];
                }

                if ($item['tier3_id'] && !array_key_exists($item['tier3_id'], $carry[$item['id']]['children'][$item['tier1_id']]['children'][$item['tier2_id']]['children'])) {
                    $carry[$item['id']]['children'][$item['tier1_id']]['children'][$item['tier2_id']]['children'][$item['tier3_id']] = ['id' => $item['tier3_id'], 'name' => $item['tier3_name']];
                }

                return $carry;
            }, []);

        $removeKeys = function (&$array) use (&$removeKeys) {
            array_walk($array, function (&$item) use ($removeKeys) {
                if (isset($item['children'])) {
                    $removeKeys($item['children']);
                    $item['children'] = array_values($item['children']);
                }
            });
        };

        $removeKeys($registrations);

        return array_values($registrations);
    }


    public function commissions(AffiliateCommissionQuery $query)
    {
        $items = $query
            ->get()
            ->map(function ($commission) {
                $commission->account->user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin', 'is_bot', 'is_active');
                return $commission;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(AffiliateCommission $commission)
    {
        return response()->json([
            'commission' => $commission->load('account', 'account.user', 'referralAccount', 'referralAccount.user', 'commissionable')
        ]);
    }

    public function approve(AffiliateCommission $commission)
    {
        if (!$commission->is_pending) {
            return response()->json([
                'success' => FALSE,
                'message' => __('This affiliate commission is already approved / rejected.')
            ]);
        }

        $commission->update(['status' => AffiliateCommission::STATUS_APPROVED]);

        $accountService = new AccountService($commission->account);
        $accountService->createGenericTransaction(GenericAccountTransaction::TYPE_AFFILIATE_COMMISSION, $commission->amount);

        return response()->json([
            'success' => TRUE,
            'message' => __('Commission is successfully approved.')
        ]);
    }

    public function reject(AffiliateCommission $commission)
    {
        if (!$commission->is_pending) {
            return response()->json([
                'success' => FALSE,
                'message' => __('This affiliate commission is already approved / rejected.')
            ]);
        }

        $commission->update(['status' => AffiliateCommission::STATUS_REJECTED]);

        return response()->json([
            'success' => TRUE,
            'message' => __('Commission is successfully rejected.')
        ]);
    }
}
