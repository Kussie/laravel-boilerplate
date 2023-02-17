<?php

namespace App\Providers;

use App\Events\PackageProcessingFailed;
use App\Events\PackageProcessingFinished;
use App\Events\PackageProcessingStarted;
use App\Events\ResendVerification;
use App\Events\VersionApproved;
use App\Events\WebhookTriggered;
use App\Listeners\CleanUpPackageAssets;
use App\Listeners\CleanUpPackageFiles;
use App\Listeners\ClearCache;
use App\Listeners\SendEmailNotificationListener;
use App\Listeners\UpdateCurrentVersions;
use App\Listeners\UpdatePackageStatus;
use App\Listeners\WarmCache;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            \App\Listeners\SendEmailVerificationNotification::class,
        ],
        ResendVerification::class => [
            SendEmailVerificationNotification::class,
        ],
        PackageProcessingStarted::class => [
            UpdatePackageStatus::class,
            SendEmailNotificationListener::class,
        ],
        PackageProcessingFinished::class => [
            UpdatePackageStatus::class,
            CleanUpPackageFiles::class,
            ClearCache::class,
            WarmCache::class,
            SendEmailNotificationListener::class,
        ],
        PackageProcessingFailed::class => [
            UpdatePackageStatus::class,
            CleanUpPackageFiles::class,
            CleanUpPackageAssets::class,
            ClearCache::class,
            SendEmailNotificationListener::class,
        ],
        WebhookTriggered::class => [
            SendEmailNotificationListener::class,
        ],
        VersionApproved::class => [
            UpdateCurrentVersions::class,
            SendEmailNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
