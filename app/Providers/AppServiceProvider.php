<?php

namespace App\Providers;
use Illuminate\pagination;
use Illuminate\Support\ServiceProvider;
use Schema;
use Illuminate\Support\Facades\Gate;
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
        Schema::defaultStringLength(191);
       
       Gate::before(function ($user, $ability) {
            return $user->username=='admin'? true : null;
        });
    }
}
