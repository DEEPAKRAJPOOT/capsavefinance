<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\Ui\DataRenderer;

class DataProviderServiceProvider extends ServiceProvider
{
    /**
     * Defered loading
     *
     * @var bool
     */
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
        $this->app->singleton(
            'App\Contracts\Ui\DataProviderInterface',
            function () {
                return new DataRenderer();
            }
        );
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['App\Contracts\Ui\DataProviderInterface'];
    }
}
