<?php

namespace Packages\Raffle\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Packages\Raffle\Models\Raffle;
use Packages\Raffle\Services\RaffleService;

class CompleteRaffles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete raffles';

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
     * @return mixed
     */
    public function handle()
    {
        // find and finish raffles that are due to complete
        Raffle::InProgress()
            ->with('tickets')
            ->withCount('tickets') // tickets_count
            ->where(function ($query) {
                $query->where('end_date', '<=', Carbon::now())
                    ->orWhereNull('end_date');
            })
            ->get()
            ->filter(function ($raffle) {
                return !!$raffle->end_date || $raffle->total_tickets == $raffle->tickets_count;
            })
            ->each(function ($raffle) {
                RaffleService::complete($raffle);
            });
    }
}
