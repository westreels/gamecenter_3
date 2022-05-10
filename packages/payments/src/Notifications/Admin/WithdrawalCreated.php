<?php

namespace Packages\Payments\Notifications\Admin;

use App\Notifications\Admin\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Packages\Payments\Models\Withdrawal;

class WithdrawalCreated extends AdminNotification
{
    private $withdrawal;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Withdrawal $withdrawal)
    {
        parent::__construct();

        $this->withdrawal = $withdrawal;
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('New withdrawal'))
            ->greeting(__('Hello'))
            ->line(__('User :name (:email) made a new withdrawal request for :n credits.', [
                'name'  => $this->withdrawal->account->user->name,
                'email' => $this->withdrawal->account->user->email,
                'n'     => $this->withdrawal->amount,
            ]))
            ->line(__('Current user balance is :n credits.', [
                'n'  => $this->withdrawal->account->balance,
            ]))
            ->action(__('View withdrawal'), url(sprintf('admin/withdrawals/%d', $this->withdrawal->id)));
    }
}
