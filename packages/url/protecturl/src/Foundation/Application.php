<?php

namespace Url\ProtectUrl\Foundation;

use Illuminate\Foundation\Application as BaseApplication;
use Url\ProtectUrl\ProtectUrlServiceProvider;

class Application extends BaseApplication {

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders() {
        parent::registerBaseServiceProviders();

        $this->register(new ProtectUrlServiceProvider($this));
    }

}
