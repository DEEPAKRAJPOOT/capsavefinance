<?php

namespace App\Inv\Repositories\Entities\Application;

use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\DocumentMaster;
use DB;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use Session;




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
    
    protected $CompanyAddress;
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


}
