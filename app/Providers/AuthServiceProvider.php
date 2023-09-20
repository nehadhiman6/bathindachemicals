<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Auth\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    public function boot()
    {
        $this->registerPolicies();

        if (app()->runningInConsole()) {
            return true;
        }

            foreach ($this->getPermissions() as $permission) {
                Gate::define($permission->name, function ($user) use ($permission) {

                    if ($user->id == 1) {
                        return true;
                    }
                    return $user->hasRole($permission->roles);
                });
            }
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
