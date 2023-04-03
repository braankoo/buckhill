<?php

namespace App\Providers;

use App\Services\Auth\JWT\JWT;
use App\Services\Auth\JWT\LcobucciJWT;
use App\Services\UserAuthService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->bind(JWT::class, function () {
            return new LcobucciJWT();
        });

        $this->app->bind(UserAuthService::class, function () {
            return new UserAuthService();
        });
    }
}
