<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;
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
            return auth()->user()?->is_admin == 1;
        });

        Blade::if('reseller', function () {
            return auth()->user()?->company?->is_reseller == 1;
        });

        Blade::if('user', function () {
            $user = auth()->user();

            return $user &&
                $user->is_admin == 0 &&
                $user->is_architect == 0 &&
                $user->is_production_employee == 0;
        });
        Blade::if('userNonReseller', function () {
            $user = auth()->user();

            return $user &&
                $user->is_admin == 0 &&
                $user->is_architect == 0 &&
                $user->is_production_employee == 0 &&
                $user->is_reseller == 0;
        });

        Blade::if('architect', function () {
            return auth()->user()?->is_architect == 1;
        });

        Blade::if('production', function () {
            return auth()->user()?->is_production_employee == 1;
        });

        Blade::if('limited', function () {
            $user = auth()->user();

            return $user && (
                    $user->is_architect == 1 ||
                    $user->is_production_employee == 1
                );
        });

        Blade::if('normal', function () {
            $user = auth()->user();

            return $user &&
                $user->is_admin == 0 &&
                $user->is_architect == 0 &&
                $user->is_production_employee == 0;
        });

        Lang::handleMissingKeysUsing(function (
            string $key,
            array $replace,
            string $locale
        ) {
            if ($locale === 'nl') {
                return last(explode('.', $key));
            }

            return $key;
        });
    }
}
