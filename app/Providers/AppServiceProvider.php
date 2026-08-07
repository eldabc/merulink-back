<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Department;
use App\Observers\DepartmentObserver;

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
        Relation::enforceMorphMap([
            'user'     => 'App\Models\User',
            'employee' => 'App\Models\Employee',
            'event_contact'  => 'App\Models\EventContact',
            'schedule_planning' => 'App\Models\SchedulePlanning',
        ]);

        Department::observe(DepartmentObserver::class);
    }
}
