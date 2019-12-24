<?php

namespace App\Inv\Repositories\Entities\Invoice;
use DB;
use Session;
use App\Inv\Repositories\Contracts\InvoiceInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Models\AppNote;


/**
 * Application repository class
 */
class InvoiceRepository extends BaseRepositories implements InvoiceInterface {


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

    


}
