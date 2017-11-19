<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Managers\Payment\SagePay;

class SagePayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

      $this->app['laravelsagepay'] = $this->app->share(function($app) {
  			return new SagePay;
  		});
    }
}
