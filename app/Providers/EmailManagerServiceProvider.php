<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Managers\Email\EmailManager;

class EmailManagerServiceProvider extends ServiceProvider
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
        $this->app->singleton(EmailManager::class, function() {
            return new EmailManager();
        });
        
    }

}
