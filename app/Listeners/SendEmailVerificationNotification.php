<?php

namespace App\Listeners;

use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendEmailVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param mixed $event
     */
    public function handle($event): void
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail() && config('features.auth.send_email_verification_email')) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}
