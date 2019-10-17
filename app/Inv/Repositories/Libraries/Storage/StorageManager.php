<?php

namespace App\Inv\Repositories\Libraries\Storage;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;

class StorageManager implements StorageManagerInterface
{
    /**
     * Stores underlying storage class
     *
     * @var mixed
     */
    protected $engine;

    /**
     * Get the storage class
     *
     * @param mixed $type
     * @return object
     */

    /**
     * Get the storage class
     *
     * @param mixed $type
     * @return object
     */
    public function engine($type = null)
    {
        if (($type) && in_array($type, ['local', 'cloud'])) {
            $this->engine = call_user_func([$this, $type]);
        } else {
            $remoteStore  = Config::get('invstorage.remote', false);
            $this->engine = ($remoteStore === true) ? $this->cloud() : $this->local();
        }

        return $this->engine;
    }

    /**
     * Returns an instance of the local I/O class
     *
     * @staticvar boolean $local
     * @return \App\Inv\Repositories\Libraries\Storage\Local
     */
    protected function local()
    {
        static $local = false;

        if ($local === false) {
            $local = App::make(\App\Inv\Repositories\Libraries\Storage\Local::class);
        }

        return $local;
    }

    /**
     * Returns an instance of the cloud I/O class
     *
     * @staticvar boolean $cloud
     * @return \App\Inv\Repositories\Libraries\Storage\Cloud
     */
    protected function cloud()
    {
        static $cloud = false;

        if ($cloud === false) {
            $cloud = App::make(\App\Inv\Repositories\Libraries\Storage\Cloud::class);
        }

        return $cloud;
    }
}