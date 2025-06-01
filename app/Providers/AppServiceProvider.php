<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ModuleRepository;
use App\Interfaces\ModuleRepositoryInterface;
use App\Repositories\OrganizationRepository;
use App\Interfaces\OrganizationRepositoryInterface;
// use App\Services\OrganizationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ModuleRepositoryInterface::class,
            ModuleRepository::class
        );
        $this->app->bind(
            OrganizationRepositoryInterface::class,
            OrganizationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
