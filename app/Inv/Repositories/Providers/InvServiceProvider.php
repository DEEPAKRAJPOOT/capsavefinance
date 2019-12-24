<?php

namespace App\Inv\Repositories\Providers;

use Illuminate\Support\ServiceProvider;

class InvServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind(
            'App\Inv\Repositories\Contracts\UserInterface',
            'App\Inv\Repositories\Entities\User\UserRepository'
        );

        $this->app->bind(
            'App\Inv\Repositories\Contracts\ApplicationInterface',
            'App\Inv\Repositories\Entities\Application\ApplicationRepository'
        );

        $this->app->bind(
            'App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface',
            'App\Inv\Repositories\Libraries\Storage\StorageManager'
        );

        $this->app->bind(
            'App\Inv\Repositories\Contracts\DocumentInterface',
            'App\Inv\Repositories\Entities\Document\DocumentRepository'
        );
         $this->app->bind(
            'App\Inv\Repositories\Contracts\AclInterface',
            'App\Inv\Repositories\Entities\Acl\AclRepository'
        );
          $this->app->bind(
            'App\Inv\Repositories\Contracts\InvoiceInterface',
            'App\Inv\Repositories\Entities\Invoice\InvoiceRepository'
        );
    }
}