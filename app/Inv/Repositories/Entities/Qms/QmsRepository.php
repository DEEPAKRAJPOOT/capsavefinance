<?php

namespace App\Inv\Repositories\Entities\Qms;

use DB;
use Session;
use App\Inv\Repositories\Contracts\QmsInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\Qms;


/**
 * Application repository class
 */
class QmsRepository extends BaseRepositories implements QmsInterface {

    use CommonRepositoryTraits;

    /**
     * Class constructor
     *
     * @return void
     */    
    public function __construct() {
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes) {        
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $id) {        
    }

    /**
     * Get all records method
     *
     * @param array $columns
     */
    public function all($columns = array('*')) {        
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns     
     */
    public function find($id, $columns = array('*')) {        
    }

    /**
    *create business information details
    * @param mixed $userId
    * @param array $attributes     
    */



    public function saveQuery($attr, $id = null)
    {
        return Qms::saveQuery($attr, $id);
    }


    public function showQueryList($id = null)
    {
        return Qms::showQueryList($id);
    }
}
