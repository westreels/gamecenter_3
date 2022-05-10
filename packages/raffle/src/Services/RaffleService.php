<?php

namespace Packages\Raffle\Services;

use App\Services\AccountService;
use Illuminate\Support\Carbon;
use Packages\Raffle\Events\RaffleCompleted;
use Packages\Raffle\Events\RaffleTicketsBought;
use Packages\Raffle\Models\Raffle;

class RaffleService
{
    protected $raffle;
    protected $user;

    public function __construct(Raffle $raffle, $user = NULL)
    {
        $this->raffle = $raffle;
        $this->user = $user ?: request()->user();
    }

    /**
     * Buy raffle ticket(s)
     *
     * @param int $quantity
     * @throws \Exception
     */
    public function buy(int $quantity = 1)
    {
        if ($quantity < 1) {
            throw new \Exception('Quantity can not be less than 1.');
        }

        $accountService = new AccountService($this->user->account);

        for ($i=1; $i <= $quantity; $i++) {
            // create a RaffleTicket model
            $raffleTicket = $this->raffle->tickets()->create([
                'raffle_id'     => $this->raffle->id,
                'account_id'    => $this->user->account->id
            ]);

            // create account transaction
            $accountService->createTransaction($raffleTicket, -$this->raffle->ticket_price);
        }

        event(new RaffleTicketsBought($this->raffle, $this->user, $quantity));
    }

    /**
     * Complete raffle
     *
     * @param Raffle $raffle
     */
    public static function complete(Raffle $raffle)
    {
        // if some tickets were purchased
        if ($raffle->pot_size > 0) {
            // draw a random ticket
            $winningTicket = $raffle->tickets->random();

            // update raffle
            $raffle->winningTicket()->associate($winningTicket);
            $raffle->win = $raffle->pot_size;

            // create account transaction
            $accountService = new AccountService($winningTicket->account);
            $accountService->createTransaction($winningTicket, $raffle->pot_size);
        }

        // complete the raffle
        $raffle->status = Raffle::STATUS_COMPLETED;
        // set end date for raffles that don't have end date (finish after all tickets are purchased)
        $raffle->end_date = $raffle->end_date ?: Carbon::now();
        $raffle->save();

        // clone recurring raffle
        if ($raffle->recurring) {
            self::replicate($raffle);
        }

        event(new RaffleCompleted($raffle));
    }

    public static function replicate(Raffle $raffle)
    {
        $clonedRaffle = $raffle
            ->replicate(['tickets_count', 'winning_ticket_id', 'status', 'win', 'start_date', 'end_date'])
            ->fill([
                'status'        => Raffle::STATUS_IN_PROGRESS,
                'win'           => 0,
                'start_date'    => Carbon::now(),
                'end_date'      => $raffle->completion_trigger == Raffle::TRIGGER_TIME
                    ? Carbon::now()->addSeconds($raffle->duration)
                    : NULL
            ]);

        $clonedRaffle->save();
    }
}
