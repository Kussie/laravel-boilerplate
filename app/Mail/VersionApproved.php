<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VersionApproved extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.version-approved')
            ->subject('A ruleset has been approved')
            ->with([
                //'version' => $this->version,
            ])
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->from(config('mail.from.address'), config('mail.from.name'));
    }
}
