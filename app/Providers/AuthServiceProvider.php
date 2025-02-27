<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // TODO: Refactor this to use a Policy and add the rule to middleware
        Gate::define('access-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('access-clients', function (User $user) {
            return $user->isAdmin();
        });
    }
}
