<?php

namespace App\Providers;

use App\Helpers\AppUtils;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('app-utils', function ($app){
            return new AppUtils;
        });
    }
}
