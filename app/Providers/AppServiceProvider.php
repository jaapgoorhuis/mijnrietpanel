<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::if('admin', function () {
            return auth()->user()?->is_admin;
        });

        Blade::if('reseller', function () {
            return auth()->user()->companys->is_reseller;
        });

        Blade::if('user', function () {
            return auth()->user()?->is_admin == 0;
        });

        Blade::if('userNonReseller', function () {
            $user = auth()->user();
            return $user && $user->is_admin == 0 && $user->is_reseller == 0;
        });

        Blade::if('architect', function () {
            $user = auth()->user();
            return $user && $user->is_architect;
        });
    }
}
