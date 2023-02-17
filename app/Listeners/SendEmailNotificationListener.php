<?php

namespace App\Listeners;

use App\Events\PackageProcessingFailed;
use App\Events\PackageProcessingFinished;
use App\Events\PackageProcessingStarted;
use App\Events\VersionApproved;
use App\Events\WebhookTriggered;
use App\Mail\ImportFailed;
use App\Mail\ImportFinished;
use App\Mail\ImportStarted;
use App\Mail\VersionApproved as VersionApprovedMail;
use App\Mail\WebhookTriggeredMail;
use App\Models\Package;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function get_class;

class SendEmailNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if (! empty($event->package)) {
            $event->package->refresh();
        }

        $eventClass = get_class($event);
        $mail = match ($eventClass) {
            PackageProcessingFinished::class => new ImportFinished($event->package, $event->data),
            PackageProcessingStarted::class => new ImportStarted($event->package),
            PackageProcessingFailed::class => new ImportFailed($event->package, $event->data),
            WebhookTriggered::class => new WebhookTriggeredMail($event->package),
            VersionApproved::class => new VersionApprovedMail(),
            default => throw new Exception('Unexpected match value'),
        };

        if (config('app.send_notification_emails')) {
            $users = User::where('processing_notifications', true)->get();
            $destinations = [];

            foreach ($users as $user) {
                $destinations[] = [
                    'email' => $user->email,
                    'to' => $user->full_name,
                ];
            }

            if ($eventClass === VersionApproved::class) {
                $package = Package::where('hash', $event->version->hash)->first();

                if (! empty($package->publisher_name) && ! empty($package->publisher_email)) {
                    $destinations[] = [
                        'email' => $package->publisher_email,
                        'to' => $package->publisher_name,
                    ];
                }
            } /*elseif ($eventClass === RulesPackageProcessingFailed::class) {
                $package = Package::where('hash', $event->version->hash)->first();
                if (!empty($package->publisher_name) && !empty($package->publisher_email)) {
                    $destinations[] = [
                        'email' => $package->publisher_email,
                        'to' => $package->publisher_name
                    ];
                }
            }*/

            if (! empty($event->package->publisher_name) && ! empty($event->package->publisher_email)) {
                $destinations[] = [
                    'email' => $event->package->publisher_email,
                    'to' => $event->package->publisher_name,
                ];
            }
            Log::debug('Sending email notifications', ['recipients' => $destinations, 'class' => $eventClass]);

            if (! empty($destinations)) {
                Mail::bcc($destinations)->send($mail);
            }
        }
    }
}
