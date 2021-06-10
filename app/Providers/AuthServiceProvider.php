<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Villa' => 'App\Policies\VillaPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Customize reset password url link
        ResetPassword::createUrlUsing(function ($user, string $token) {
            $baseUrl = config('default_settings.app_frontend_url');
            return $baseUrl.'reset-password?token='.$token;
        });

        /**
         * Determine if user is of type system admin and can manage all parts of the application
         */
        Gate::define('manage-app', function (User $user) {
            return $user->isSystemAdmin();
        });

        /**
         * Determine if user can access admin routes
         */
        Gate::define('access-admin', function (User $user) {
            return $user->isSystemAdmin() || $user->isSupplier();
        });

        /**
         * Determine if authenticated user is same as request user, i.e. can manage his own data
         */
        Gate::define('manage-own-data', function (User $authenticated, User $user ) {
            return $authenticated->id == $user->id;
        });

        /**
         * Determine if a user has distributor account type
         */
        Gate::define('distribute-content', function (User $user ) {
            return $user->isDistributor();
        });
    }
}
