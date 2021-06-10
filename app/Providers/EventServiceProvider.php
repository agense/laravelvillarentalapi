<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

use App\Models\User;
use App\Models\Villa;
use App\Models\AccountApplication;
use App\Models\RejectedApplication;
use App\Observers\AccountApplicationObserver;
use App\Observers\RejectedApplicationObserver;
use App\Observers\UserObserver;
use App\Observers\VillaObserver;

use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        AccountApplication::observe(AccountApplicationObserver::class);
        RejectedApplication::observe(RejectedApplicationObserver::class);
        User::observe(UserObserver::class);
        Villa::observe(VillaObserver::class);
    }
}
