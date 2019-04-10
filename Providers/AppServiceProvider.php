<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $ActiveCart = [];
        //view()->share('ActiveCart', $ActiveCart);
        //$ActiveCart = app('App\Http\Controllers\ActiveCartController')->index();
        //view()->share('ActiveCart', $ActiveCart);
        view()->composer('*', function($view) {
            // $ActiveCart = app('App\Http\Controllers\ActiveCartController')->index();
            //$curSym = $ActiveCart['MarketInfo']['currencySymbol']
            //view()->share('ActiveCart', $ActiveCart);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
