<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as Notification;

class VerifyEmail extends Notification
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $url = parent::verificationUrl($notifiable);

        return str_replace('/api', '', $url);
    }
}
