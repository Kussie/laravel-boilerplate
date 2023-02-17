<?php

namespace App\Mail;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class WebhookTriggeredMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Package $package)
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
        return $this->markdown('mail.webhook-triggered')
            ->subject('The import process is about to start')
            ->with([
                'packageName' => $this->package->filename,
                'triggerDate' => Carbon::now()->timezone('Australia/Sydney'),
            ])
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->from(config('mail.from.address'), config('mail.from.name'));
    }
}
