<?php

namespace App\Mail;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ImportStarted extends Mailable
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
        return $this->markdown('mail.import-started')
            ->subject('An import has started')
            ->with([
                'packageName' => $this->package->filename,
                'importDate' => Carbon::now()->timezone('Australia/Sydney'),
            ])
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->from(config('mail.from.address'), config('mail.from.name'));
    }
}
