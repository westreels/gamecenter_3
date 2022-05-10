<?php

namespace Packages\Payments\Listeners;

use Illuminate\Support\Facades\Notification;
use Packages\Payments\Events\DepositCancelled;
use Packages\Payments\Events\DepositCompleted;
use Packages\Payments\Events\WithdrawalCancelled;
use Packages\Payments\Events\WithdrawalCompleted;
use Packages\Payments\Events\WithdrawalCreated;
use Packages\Payments\Notifications\Admin\DepositCompleted as DepositCompletedAdminNotification;
use Packages\Payments\Notifications\Admin\WithdrawalCreated as WithdrawalCreatedAdminNotification;
use Packages\Payments\Notifications\DepositCancelled as DepositCancelledUserNotification;
use Packages\Payments\Notifications\DepositCompleted as DepositCompletedUserNotification;
use Packages\Payments\Notifications\WithdrawalCancelled as WithdrawalCancelledUserNotification;
use Packages\Payments\Notifications\WithdrawalCompleted as WithdrawalCompletedUserNotification;

class NotificationSubscriber
{
    private $adminEmail;

    public function __construct()
    {
        $this->adminEmail = config('settings.notifications.admin.email');
    }

    public function sendDepositCancelledUserNotification(DepositCancelled $event)
    {
        $event->deposit->account->user->notify(new DepositCancelledUserNotification($event->deposit));
    }

    public function sendDepositCompletedUserNotification(DepositCompleted $event)
    {
        $event->deposit->account->user->notify(new DepositCompletedUserNotification($event->deposit));
    }

    public function sendWithdrawalCancelledUserNotification(WithdrawalCancelled $event)
    {
        $event->withdrawal->account->user->notify(new WithdrawalCancelledUserNotification($event->withdrawal));
    }

    public function sendWithdrawalCompletedUserNotification(WithdrawalCompleted $event)
    {
        $event->withdrawal->account->user->notify(new WithdrawalCompletedUserNotification($event->withdrawal));
    }

    public function sendDepositCompletedAdminNotification(DepositCompleted $event)
    {
        if ($event->deposit->amount >= config('payments.notifications.admin.deposit.treshold')) {
            Notification::route('mail', $this->adminEmail)
                ->notify(new DepositCompletedAdminNotification($event->deposit));
        }
    }

    public function sendWithdrawalCreatedAdminNotification(WithdrawalCreated $event)
    {
        if ($event->withdrawal->amount >= config('payments.notifications.admin.withdrawal.treshold')) {
            Notification::route('mail', $this->adminEmail)
                ->notify(new WithdrawalCreatedAdminNotification($event->withdrawal));
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        // user notifications
        $events->listen(DepositCancelled::class, self::class . '@sendDepositCancelledUserNotification');
        $events->listen(DepositCompleted::class, self::class . '@sendDepositCompletedUserNotification');
        $events->listen(WithdrawalCancelled::class, self::class . '@sendWithdrawalCancelledUserNotification');
        $events->listen(WithdrawalCompleted::class, self::class . '@sendWithdrawalCompletedUserNotification');

        // admin notifications
        if ($this->adminEmail) {
            if (config('payments.notifications.admin.deposit.enabled')) {
                $events->listen(DepositCompleted::class, self::class . '@sendDepositCompletedAdminNotification');
            }

            if (config('payments.notifications.admin.withdrawal.enabled')) {
                $events->listen(WithdrawalCreated::class, self::class . '@sendWithdrawalCreatedAdminNotification');
            }
        }
    }
}
