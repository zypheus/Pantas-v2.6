<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Auth\ModuleAccessService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', fn (User $user) => app(ModuleAccessService::class)->hasLibraryAdminAccess($user));

        Gate::define('isStaff', fn (User $user) => app(ModuleAccessService::class)->hasLibraryAccess($user));

        Gate::define('isAdminOrStaff', fn (User $user) => app(ModuleAccessService::class)->hasLibraryAccess($user));

        Gate::define('isStudent', fn (User $user) => in_array($user->role, ['student', 'faculty']) // treat faculty same
        );
    }
}
