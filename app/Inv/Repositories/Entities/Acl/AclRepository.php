<?php

namespace App\Inv\Repositories\Entities\Acl;

use Carbon\Carbon;
use App\Inv\Repositories\Models\Relationship;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Otp;
use App\Inv\Repositories\Contracts\AclInterface;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Models\Master\Role;
use App\Inv\Repositories\Models\Master\Permission as PermissionModel;
use App\Inv\Repositories\Models\Master\PermissionRole as PermissionRole;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\AnchorUser;
use App\Inv\Repositories\Contracts\Traits\AuthTrait;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use DB;

class AclRepository extends BaseRepositories implements AclInterface {

    //use CommonRepositoryTraits,
    //    AuthTrait;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Save method
     *
     * @param array $attributes
     */
    public function save($attributes = []) {
        
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
     * Delete method
     *
     * @param mixed $ids
     */
    public function delete($ids) {
        
    }

    public function create(array $attributes) {
        
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    public function update(array $attributes, $id) {
        
    }

    /**
     * Delete method
     *
     * @param mixed $ids
     */
    public function destroy($ids) {
        
    }

}
