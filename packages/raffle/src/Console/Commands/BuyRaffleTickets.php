<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\Raffle\Console\Commands;

use App\Events\UserIsOnline;
use App\Models\User;
use Illuminate\Console\Command;
use Packages\Raffle\Models\Raffle;
use Packages\Raffle\Rules\TicketsLimit;
use Packages\Raffle\Services\RaffleService;

class BuyRaffleTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:buy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make bots randomly buy raffle tickets';

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
        $minBotsCount = config('raffle.bots.min_count');
        $maxBotsCount = config('raffle.bots.max_count');
        $minTicketsCount = config('raffle.bots.min_tickets');
        $maxTicketsCount = config('raffle.bots.max_tickets');

        if (($minBotsCount > 0 || $maxBotsCount > 0) && ($minTicketsCount > 0 && $maxTicketsCount > 0)) {
            $count = random_int($minBotsCount, $maxBotsCount);

            // retrieve bots
            $bots = User::active()
                ->bots()
                ->inRandomOrder()
                ->limit($count)
                ->get();

            // retrieve raffles that are in progress and where not all tickets are bought
            $raffles = Raffle::inProgress()
                ->withCount('tickets')
                ->get()
                ->filter(function ($raffle) {
                    return !$raffle->is_end_date_passed && $raffle->total_tickets > $raffle->tickets_count;
                });

            // if bots and running raffles exist
            if ($bots->isNotEmpty() && $raffles->isNotEmpty()) {
                // loop through bots
                $bots->each(function (User $bot) use ($raffles, $minTicketsCount, $maxTicketsCount) {
                    // pick a random raffle
                    $raffle = $raffles->random();

                    // choose number of tickets to buy randomly
                    $quantity = random_int($minTicketsCount, $maxTicketsCount);
                    // calculate the limit
                    $limit = (new TicketsLimit($raffle, $bot))->calculate()->get();

                    if ($limit > 0) {
                        if ($quantity > $limit) {
                            $quantity = $limit;
                        }

                        $raffleService = new RaffleService($raffle, $bot);
                        $raffleService->buy($quantity);
                        // it's important to refresh the raffle model from the DB, so that total number of purchased tickets is recalculated
                        $raffle->refresh();

                        tap($bot, function ($bot) { $bot->is_online = TRUE; })->save();

                        event(new UserIsOnline($bot));
                    }
                });
            }
        }
    }
}
