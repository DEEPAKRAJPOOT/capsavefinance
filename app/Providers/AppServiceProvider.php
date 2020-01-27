<?php

namespace App\Providers;

use Url\ProtectUrl\UrlGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Specified key was too long error's solution to run migrate
        Schema::defaultStringLength(191);
        Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
        ini_set("memory_limit", "100M");
        ini_set('post_max_size', '50M');
        ini_set('upload_max_filesize', '50M');
        //Add this custom validation rule.
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            return preg_match('/^[\pL\s]+$/u', $value); 

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->setCustomUrlGenerator();
    }

    /**
     * Set our custom url generator here.
     *
     * @return void
     */
    protected function setCustomUrlGenerator()
    {
        $this->app->instance('url',
            new UrlGenerator(
            $this->app['router']->getRoutes(), $this->app->make('request')
        ));
    }
}