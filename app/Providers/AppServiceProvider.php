<?php

namespace App\Providers;

use App\Models\Equipment;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use App\Observers\ActivityObserver;
use App\Policies\EquipmentPolicy;
use App\Policies\MessagePolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
        Model::preventAccessingMissingAttributes(! app()->isProduction());

        // Policies
        Gate::policy(Equipment::class, EquipmentPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);

        // Observers
        Equipment::observe(ActivityObserver::class);
        User::observe(ActivityObserver::class);
    }
}
