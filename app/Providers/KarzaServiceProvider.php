<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class KarzaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$baseUrl = env('KARZA_AUTHENTICATION_API_URL');
        $baseUrl = config('proin.karza_auth_api_url');
        $apiKey = config('proin.karza_auth_api_key');
         $this->app->singleton('GuzzleHttp\Client', function($api) use ($baseUrl, $apiKey) {
            return new Client([
                'base_uri' => $baseUrl,
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
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