<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Managers\Acl\AclManager;

class AclManagerServiceProvider extends ServiceProvider
{
    protected $defer = true;

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
        $this->app->singleton(AclManager::class, function() {
            return new AclManager();
        });

    }

}
