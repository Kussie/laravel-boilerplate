<?php

namespace App\Listeners;

use App\Events\PackageProcessingFailed;
use App\Events\PackageProcessingFinished;
use App\Events\PackageProcessingStarted;
use App\Models\Package;
use Exception;
use Illuminate\Support\Carbon;

use function get_class;
use function in_array;

class UpdatePackageStatus
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
        $eventClass = get_class($event);
        $status = match ($eventClass) {
            PackageProcessingFinished::class => 'finished',
            PackageProcessingStarted::class => 'processing',
            PackageProcessingFailed::class => 'failed',
            default => throw new Exception('Unexpected match value'),
        };

        $package = Package::find($event->package->id);
        $package->meta = $event->data ?? null;

        if (in_array($status, ['finished', 'failed'])) {
            $package->finished_processing = Carbon::now();
        } else {
            $package->started_processing = Carbon::now();
        }

        $package->status = $status;
        $package->save();
    }
}
