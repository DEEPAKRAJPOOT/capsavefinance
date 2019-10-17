<?php

namespace Url\ProtectUrl;

use ErrorException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator {

    /**
     * Get the URL to a named route.
     *
     * @param  string  $name
     * @param  mixed   $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function route($name, $parameters = [], $absolute = true) {

        $encryptedParameters = $parameters;

        if (Auth::guest() === false) {
            $encryptedParameters = is_array($parameters) ? $this->encrypt($name, $parameters) : $parameters;
        }

        return parent::route($name, $encryptedParameters, $absolute);
    }

    /**
     * 
     * @return type
     * 
     * @throws ErrorException
     */
    protected function getCrypt() {
        $app = App::getInstance();

        if (isset($app['urlencryptor'])) {
            return $app['urlencryptor'];
        }

        throw new ErrorException('URL Encryptor was not found.');
    }

    /**
     * 
     * @return type
     * @throws ErrorException
     */
    protected function getProtector() {
        $app = App::getInstance();
        if (isset($app['urlprotector'])) {
            return $app['urlprotector'];
        }

        throw new ErrorException('URL Protector was not found.');
    }

    /**
     * Encrypts the parameter passed as querystring in URL.
     *
     * @param array $parameters
     * @return array
     */
    protected function encrypt($routeName, $parameters = []) {
        if (is_string($parameters)) {
            return $parameters;
        }

        if (count($parameters) === 0) {
            return [];
        }

        $protected = $this->getProtector()->protect($routeName, $parameters);

        return ['__signature' => $protected];
    }

}
