<?php

namespace App\Inv\Repositories\Entities\Application;

use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use Session;
use DB;




/**
 * Application repository class for right
 */
class ApplicationRepository extends BaseRepositories implements ApplicationInterface {

    use CommonRepositoryTraits;

    /**
     * Class constructor
     *
     * @return void
     */
    
    public function __construct(AppDocumentFile $document) {
        $this->document=$document;

    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes) {
        return Rights::saveRights($attributes);
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $rightId) {
        return Rights::updateRights((int) $rightId, $attributes);
    }

    /**
     * Get all records method
     *
     * @param array $columns
     */
    public function all($columns = array('*')) {
        return Rights::all($columns);
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns     
     */
    public function find($id, $columns = array('*')) {
        $varRightData = Rights::find((int) $id, $columns);

        return $varRightData;
    }

     public function saveBusinessInfo($attributes = [], $userId = null){
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

        if(is_null($userId)){
            throw new BlankDataExceptions('No Data Found');
        }
        return Business::creates($attributes, $userId);
    }


}
