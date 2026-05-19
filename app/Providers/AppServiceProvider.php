<?php

namespace App\Providers;

use App\Repositories\Contracts\DriverRepositoryInterface;
use App\Repositories\Contracts\RaceRepositoryInterface;
use App\Repositories\Contracts\SessionRepositoryInterface;
use App\Repositories\DriverRepository;
use App\Repositories\RaceRepository;
use App\Repositories\SessionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(RaceRepositoryInterface::class, RaceRepository::class);
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
