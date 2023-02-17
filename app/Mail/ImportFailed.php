<?php

namespace App\Mail;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ImportFailed extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Package $package, public array $data)
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
        $data = [
            'failureReason' => $this->data['reason'],
            'packageName' => $this->package->filename,
            'importDate' => Carbon::now()->timezone('Australia/Sydney'),
            'processTime' => $this->package->created_at->diffInMinutes($this->package->finished_processing),
            'failFile' => $this->data['trigger_file'] ?? 'NA',
        ];

        return $this->markdown('mail.import-failed')
            ->subject('An import has failed')
            ->with($data)
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->from(config('mail.from.address'), config('mail.from.name'));
    }
}
