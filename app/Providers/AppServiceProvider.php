<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ModuleRepository;
use App\Interfaces\ModuleRepositoryInterface;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
