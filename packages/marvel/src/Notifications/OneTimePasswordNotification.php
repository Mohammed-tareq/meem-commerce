<?php

namespace Marvel\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification as SpatieNotification;

class OneTimePasswordNotification extends SpatieNotification
{
    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->from(config('app.name').'@gmail.com', config('app.name'))
            ->subject($this->subject())
            ->markdown('one-time-passwords::mail', [
                'oneTimePassword' => $this->oneTimePassword,
            ]);
    }

    public function subject(): string
    {
        return __('message.your otp code');
    }
}
