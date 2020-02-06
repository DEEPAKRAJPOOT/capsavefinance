<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class CibilServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$baseUrl = env('CIBIL_AUTHENTICATION_API_URL');
        //$baseUrl = 'https://test.crifhighmark.com';
        $baseUrl = 'https://hub.crifhighmark.com';
        $this->app->singleton('GuzzleHttp\Client', function($api) use ($baseUrl) { 
            return new Client([
                'base_uri' => $baseUrl,
                /*
                'headers' => [
                    'requestXML' => $this->prepareRequestXml(),
                    'userId ' => 'crif1_cpu_uat@capsavefinance.com',
                    'password' => '55DE689372D33C9876D1E09CFFF8BBBFF74B9445',
                    'mbrid' => 'NBF0002966',
                    'productType' => 'INDV',
                    'productVersion' => '1.0',
                    'reqVolType' => 'INDV',
                ]
                */
             ]);
        });
    }

   

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
   
}