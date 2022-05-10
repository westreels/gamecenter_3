<?php

namespace Packages\Raffle\Listeners;

use Packages\Raffle\Events\RaffleCompleted;
use Packages\Raffle\Notifications\RaffleWinnerNotification;

class SendNotificationToWinner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RaffleCompleted  $event
     * @return void
     */
    public function handle(RaffleCompleted $event)
    {
        $raffle = $event->raffle;

        if ($raffle->winningTicket) {
            $user = $raffle->winningTicket->account->user;
            if (!$user->is_bot) {
                $user->notify(new RaffleWinnerNotification($raffle));
            }
        }
    }
}
