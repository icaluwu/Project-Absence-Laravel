<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;

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
        // Set locale/timezone for Carbon
        Date::setLocale(config('app.locale'));
        date_default_timezone_set(config('app.timezone'));

        // Map policies (auto-discovery also works, but ensure explicit mapping if needed)
        Gate::policy(\App\Models\OvertimeRequest::class, \App\Policies\OvertimeRequestPolicy::class);
        Gate::policy(\App\Models\LeaveRequest::class, \App\Policies\LeaveRequestPolicy::class);
    }
}
