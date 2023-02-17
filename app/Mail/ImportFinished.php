<?php

namespace App\Mail;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ImportFinished extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $version;

    /**
     * Create a new message instance.
     */
    public function __construct(public Package $package, public array $data)
    {
        ////
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // generate error log file
        /*$warnings = array_unique($this->data['warnings']);
        $warnings = array_map('strip_tags', $warnings);
        file_put_contents(storage_path('import_errors.txt'), implode(PHP_EOL, $warnings));

        // Figure out if there were any new errors
        $newErrors = [];
        $previousVersion = Version::where('type', $this->version->type)->where('version', '<=', $this->version->version)->where('id', '!=', $this->version->id)->orderByDesc('version')->orderBy('created_at', 'desc')->first();
        if ($previousVersion) {
            $previousPackage = Package::where('hash', $previousVersion->hash)->where('status', 'finished')->first();

            if ($previousPackage) {
                if (isset($previousPackage->censored_meta['warnings']) && !empty($previousPackage->censored_meta['warnings'])) {
                    $previousErrors = array_unique($previousPackage->censored_meta['warnings']);
                    $previousErrors = array_map('strip_tags', $previousErrors);
                    $newErrors = array_diff($warnings, $previousErrors);
                }
            }
        }*/

        return $this->markdown('mail.import-finished')
            ->with([
                'ruleVersion' => $this->version->version,
                'ruleType' => $this->version->type,
                'packageName' => $this->package->filename,
                'importDate' => Carbon::now()->timezone('Australia/Sydney'),
                'links' => $this->data['links'],
                //'newErrors' => $newErrors,
                //'errorCount' => count($warnings),
                'processTime' => $this->package->created_at->diffInMinutes($this->package->finished_processing),
            ])
            ->attach(storage_path('import_errors.txt'), [
                'as' => 'import_errors.txt',
                'mime' => 'text/plain',
            ])
            ->subject('DRA - An import has finished')
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->from(config('mail.from.address'), config('mail.from.name'));
    }
}
