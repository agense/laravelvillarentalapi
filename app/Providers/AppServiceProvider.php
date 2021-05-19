<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\VillaObserver;
use App\Models\Villa;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Villa::observe(VillaObserver::class);
    }
}
