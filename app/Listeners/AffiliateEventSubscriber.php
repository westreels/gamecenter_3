<?php

namespace App\Listeners;

use App\Events\GamePlayed;
use App\Models\AffiliateCommission;
use App\Services\AffiliateService;
use Illuminate\Auth\Events\Registered;

class AffiliateEventSubscriber
{
    public function userRegistered(Registered $event)
    {
        $user = $event->user;

        $affiliateService = new AffiliateService($user->account, $user);

        $affiliateService->createCommissions(
            $user,
            AffiliateCommission::TYPE_SIGN_UP
        );
    }

    public function gamePlayed(GamePlayed $event)
    {
        $game = $event->game;
        $user = $event->user; //change
        // dd($user);
        $affiliateService = new AffiliateService($game->account, $user); //change

        $loss = max(0, $game->bet - $game->win); // net loss
        $win = max(0, $game->win - $game->bet); // net win

        if ($loss > 0) {
            $affiliateService->createCommissions(
                $event->game,
                AffiliateCommission::TYPE_GAME_LOSS
            );
        }

        if ($win > 0) {
            $affiliateService->createCommissions(
                $event->game,
                AffiliateCommission::TYPE_GAME_WIN
            );
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            Registered::class,
            self::class . '@userRegistered'
        );

        $events->listen(
            GamePlayed::class,
            self::class . '@gamePlayed'
        );
    }
}
