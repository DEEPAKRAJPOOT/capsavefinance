<?php

namespace Url\ProtectUrl;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UrlParamProtector {

    /**
     * Session key.
     *
     * @var string
     */
    protected $sessionKey = '__url_protector__';

    /**
     * Request class.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Values those needs to be merged in request object.
     *
     * @var array
     */
    protected $valuesToBeMerged;

    /**
     * Create and returns VALID RFC 4211 COMPLIANT
     * Universally Unique IDentifiers (UUID) version 4.
     *
     * @return string
     */
    protected function getNewGuid() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf(
                '%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
        );
    }

    /**
     * Create the route key.
     *
     * @param string $routeName
     * @param array $parameters
     * @return string
     */
    protected function getRouteKey($routeName, array $parameters = []) {
        if (count($parameters) <= 0) {
            $paramToString = '';
        } else {
            $paramToString = implode('-', array_map(
                            function ($k, $v) {
                        return $k . '-' . $v;
                    }, array_keys($parameters), array_values($parameters)
            ));
        }

        $routeKey = 'route__' . $routeName . (empty($paramToString) ? '' : '-' . $paramToString);

        return $routeKey;
    }

    /**
     * Returns a GUID for a URL parameter.
     *
     * @param string $routeName
     * @param array $parameters
     * @return string
     */
    public function protect($routeName, array $parameters) {
        $routeKey = $this->getRouteKey($routeName, $parameters);
        
        if (Session::has($this->sessionKey . '.' . $routeKey) === false) {
            $guid = Str::lower($this->getNewGuid());

            Session::put($this->sessionKey . '.' . $routeKey, [
                'guid' => $guid,
                'loggedin_user_id' => (Auth::guest() ? 0 : Auth::user()->id),
                'params' => $parameters,
            ]);
        } else {                      
            $guid = Session::get($this->sessionKey . '.' . $routeKey . '.guid');
        }       
        return $guid;
    }

    /**
     * Check whether guid passed is a valid one or not.
     *
     * @param string $guid
     * @return boolean
     */
    protected function isValidGuid($guid) {
        if(empty(Session::get($this->sessionKey))) {
            return false;
        }

        foreach (Session::get($this->sessionKey) as $key => $value) {
            if (!empty($value['guid']) && $value['guid'] === $guid) {
                $this->valuesToBeMerged = $value['params'];
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether guid passed is a valid one or not.
     *
     * @param string $guid
     * @return boolean
     */
    public function isValidGuidForPost($guid) {
        foreach (Session::get($this->sessionKey) as $key => $value) {
            if (!empty($value['guid']) && $value['guid'] === $guid && Auth::user()->id === $value['loggedin_user_id']) {
                $this->valuesToBeMerged = $value['params'];
                return true;
            }
        }

        return false;
    }

    /**
     * Merge the request with our revealed values.
     */
    protected function mergeRequest() {
        $this->request->merge($this->valuesToBeMerged);
    }

    /**
     * Check whether a "__signature" is correct or not.
     *
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    public function reveal(Request &$request) {

        $this->request = $request;        
        //dd(Session::get($this->sessionKey));
        $guid = ($this->request->query('__signature') ?: false);

        if ($guid === false) {
            return false;
        }

        if ($this->isValidGuid($guid) === false) {
            App::abort(400);
        }

        $this->mergeRequest();
    }

}
