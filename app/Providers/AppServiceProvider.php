<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelData\Data;

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
        // length key to loong
        \Illuminate\Support\Facades\Schema::defaultStringLength(120);
        Model::shouldBeStrict();
        //Carbon::setLocale('fr');
    }
}
