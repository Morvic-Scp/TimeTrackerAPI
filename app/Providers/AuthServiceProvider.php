<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\project;
use App\Models\project_task;
use App\Policies\ProjectPolicy;
use App\Policies\ProjectTaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        project::class => ProjectPolicy::class,
        project_task::class => ProjectTaskPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
