<?php

namespace App\Console\Commands;

use App\Models\AffiliateCommission;
use App\Models\GenericAccountTransaction;
use App\Services\AccountService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApproveAffiliateCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve pending affiliate commissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get pending commissions accounts and total
        $items = AffiliateCommission::select('account_id', DB::raw('SUM(amount) AS commissions_total'))
            ->pending()
            ->with('account')
            ->groupBy('account_id')
            ->havingRaw('SUM(amount) > ?', [0])
            ->get()
            ->map
            ->setAppends([]);

        // approve commissions and balance for each account
        $items->each(function ($item) {
            $item->account->commissions()->pending()->update(['status' => AffiliateCommission::STATUS_APPROVED]);

            $accountService = new AccountService($item->account);
            $accountService->createGenericTransaction(
                GenericAccountTransaction::TYPE_AFFILIATE_COMMISSION,
                $item->commissions_total
            );
        });

        return 0;
    }
}
