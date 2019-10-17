<?php

namespace App\Inv\Repositories\Factory\Repositories;

use BadMethodCallException;

/**
 * Base class for all repository classes
 */
abstract class BaseRepositories
{

    /**
     * Laravel container object
     *
     * @var object
     */
    protected $app;

    /**
     * Create method
     *
     * @param array $attributes
     */
    abstract protected function create(array $attributes);

    /**
     * Update method
     *
     * @param array $attributes
     */
    abstract protected function update(array $attributes, $id);

    /**
     * Delete method
     *
     * @param mixed $ids
     */
    abstract protected function destroy($ids);

    /**
     * Class constructor
     *
     * @param void
     * @return void
     * @since 0.1
     */
    public function __construct()
    {
        $this->app = app();
        $this->boot();
    }

    /**
     * Boot all required methods from here
     *
     * @param void
     * @return void
     * @since 0.1
     */
    protected function boot()
    {
        $this->runOnce();
    }

    /**
     * Call all methods from here those would be executed only once
     * while two child classes extends this base class at one in case
     * of dependency injection
     *
     * @param void
     * @return void
     * @since 0.1
     */
    protected function runOnce()
    {
        static $executed = false;

        if ($executed === false) {
            // Call all methods from here those would be executed only once
            $this->setConfig();

            $executed = true;
        }
    }

    /**
     * Set all configs at the run time
     *
     * @param void
     * @return void
     * @since 0.1
     */
    protected function setConfig()
    {
        $configs = glob(__DIR__ . '/../../config/*.php');

        foreach ($configs as $config) {
            // Take out the basename from the filename with path
            $value = pathinfo($config, PATHINFO_FILENAME);

            // Merge the config items
            $this->mergeConfigFrom($config, 'inv_' . $value);
        }
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_merge(require $path, $config));
    }
    /**
     * Handle calls to missing methods on the repository.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
}
