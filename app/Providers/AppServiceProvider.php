<?php

namespace App\Providers;

use App\Models\Genre;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrap();

        view()->composer('*', function ($view) {
            try {
                if (Schema::hasTable('genres')) {
                    $view->with('genres', Genre::all());
                } else {
                    $view->with('genres', collect());
                }
            } catch (\Throwable $e) {
                $view->with('genres', collect());
            }
        });
    }
}
