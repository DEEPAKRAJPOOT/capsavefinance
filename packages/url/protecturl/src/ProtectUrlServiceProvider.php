<?php

namespace Url\ProtectUrl;

use Illuminate\Support\ServiceProvider;
use Url\ProtectUrl\UrlParamProtector;

class ProtectUrlServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->app->make('Illuminate\Contracts\Http\Kernel')
                ->pushMiddleware('Url\ProtectUrl\Middleware\UrlParamRevealerMiddleware');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->registerUrlProtector();
    }

    /**
     * Register UrlProtector class.
     */
    protected function registerUrlProtector() {
        $this->app->singleton('urlprotector', function () {
            return new UrlParamProtector();
        });
    }

}
