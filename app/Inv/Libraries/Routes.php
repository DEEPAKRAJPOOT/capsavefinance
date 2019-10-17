<?php

namespace App\Inv\Libraries;

use Route;
use Config;

class Routes
{
    /**
     * Routes instance
     *
     * @var App\Inv\Libraries\Routes
     */
    protected static $instance;

    /**
     * All bakend routes
     *
     * @var array
     */
    protected $backendRoutes = [];

    /**
     * All api routes
     *
     * @var array
     */
    protected $apiRoutes = [];

    /**
     * All front end routes
     *
     * @var array
     */
    protected $frontendRoutes = [];

    /**
     * All open routes
     *
     * @var array
     */
    protected $openRoutes = [];

    /**
     * Class constructor
     */
    protected function __construct()
    {
        //
    }

    /**
     * Signleton class instance creator
     *
     * @return App\Inv\Libraries\Routes
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Read all registered route and set those in individual groups
     *
     * @param  void
     * @return void
     */
    public function setAllRoutes()
    {
        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $value) {
            switch ($value->domain()) {
                case config('proin.backend_uri'):
                    $this->backendRoutes[]  = $value->getName();
                    break;
                case config('proin.api_uri'):
                    $this->apiRoutes[]      = $value->getName();
                    break;
                case config('proin.frontend_uri'):
                    $this->frontendRoutes[] = $value->getName();
                    break;
                default:
                    $this->openRoutes[]     = $value->getName();
                // No need to break here
            }
        }

        Config::set(
            'proin.backend_routes',
            array_values(
                array_unique(
                    $this->backendRoutes
                )
            )
        );

        Config::set(
            'proin.frontend_routes',
            array_values(
                array_unique(
                    $this->frontendRoutes
                )
            )
        );

        Config::set(
            'proin.api_routes',
            array_values(
                array_unique(
                    $this->apiRoutes
                )
            )
        );

        Config::set(
            'proin.open_routes',
            array_values(
                array_unique(
                    $this->openRoutes
                )
            )
        );
    }
}