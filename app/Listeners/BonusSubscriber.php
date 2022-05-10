<?php

namespace App\Listeners;

use App\Models\Bonus;
use App\Services\AccountService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

class BonusSubscriber
{
    protected function giveBonus($event, int $type, float $amount)
    {
        $user = $event->user;

        $accountService = new AccountService($user->account);

        $accountService->createBonus($type, $amount);
    }

    public function giveSignUpBonus(Registered $event)
    {
        $this->giveBonus($event, Bonus::TYPE_SIGN_UP, config('settings.bonuses.sign_up'));
    }

    public function giveEmailVerificationBonus(Verified $event)
    {
        $this->giveBonus($event, Bonus::TYPE_EMAIL_VERIFICATION, config('settings.bonuses.email_verification'));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        // user sign up
        if (config('settings.bonuses.sign_up') > 0) {
            $events->listen(Registered::class, [self::class, 'giveSignUpBonus']);
        }

        // user email verification
        if (config('settings.bonuses.email_verification') > 0) {
            $events->listen(Verified::class, [self::class, 'giveEmailVerificationBonus']);
        }
    }
}
