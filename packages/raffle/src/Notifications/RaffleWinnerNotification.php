<?php

namespace Packages\Raffle\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Packages\Raffle\Models\Raffle;

class RaffleWinnerNotification extends Notification
{
    use Queueable;

    protected $raffle;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Raffle $raffle)
    {
        $this->raffle = $raffle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject(__('Your raffle ticket won'))
            ->greeting(__('Hello'))
            ->line(__('Congratulations!'))
            ->line(__('You won :amount credits in ":title" raffle.', [
                'title' => $this->raffle->title,
                'amount' => $this->raffle->win
            ]))
            ->line(__('Keep playing!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
