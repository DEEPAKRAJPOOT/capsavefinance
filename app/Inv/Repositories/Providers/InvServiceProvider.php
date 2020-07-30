<?php

namespace App\Inv\Repositories\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use App\Inv\Repositories\Libraries\Validations\Files\Mimes;

class InvServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Check file Mime type
        Validator::extend('checkmime', function ($attribute, $value, $parameters, $validator) {
            return (new Mimes($attribute, $value, $parameters, $validator))->isValid();
        });
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
            'App\Inv\Repositories\Contracts\QmsInterface',
            'App\Inv\Repositories\Entities\Qms\QmsRepository'
        );
        
        $this->app->bind(
            'App\Inv\Repositories\Contracts\InvoiceInterface',
            'App\Inv\Repositories\Entities\Invoice\InvoiceRepository'
        );

        $this->app->bind(
            'App\Inv\Repositories\Contracts\MasterInterface',
            'App\Inv\Repositories\Entities\Master\MasterRepository'
        );
        
        $this->app->bind(
            'App\Inv\Repositories\Contracts\LmsInterface',
            'App\Inv\Repositories\Entities\Lms\LmsRepository'
        );   
        
        $this->app->bind(
            'App\Inv\Repositories\Contracts\FinanceInterface',
            'App\Inv\Repositories\Entities\Finance\FinanceRepository'
        );

        $this->app->bind(
            'App\Inv\Repositories\Contracts\UserInvoiceInterface',
            'App\Inv\Repositories\Entities\Lms\UserInvoiceRepository'
        );

        $this->app->bind(
            'App\Inv\Repositories\Contracts\ReportInterface',
            'App\Inv\Repositories\Entities\Report\ReportsRepository'
        );
    }
}