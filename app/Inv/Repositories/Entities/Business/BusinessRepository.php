<?php

namespace App\Inv\Repositories\Entities\Business;

use Carbon\Carbon;

use App\Inv\Repositories\Contracts\BusinessInterface;
use App\Inv\Repositories\Models\Business;
// use App\Inv\Repositories\Models\UserBussinessAddress;
// use App\Inv\Repositories\Contracts\Traits\AuthTrait;
// use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
// use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;

class BusinessRepository implements BusinessInterface
{

    //use CommonRepositoryTraits, AuthTrait;

    public function __construct()
    {
        // parent::__construct();
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    public function all()
    {
        return Business::with('user')->get();
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes)
    {
        
        return Business::create($attributes);
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns
     */
    public function find($id, $columns = array('*'))
    {
       
        return (Business::find($id)) ?: false;
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $id)
    {
        $result = Business::update((int) $id, $attributes);

        return $result ?: false;
    }

    /**
     * Delete method
     *
     * @param mixed $ids
     */
    protected function delete($ids)
    {
        //
    }

    /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function save($attributes = [], $userId = null)
    {
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

        return is_null($userId) ? $this->create($attributes) : $this->update($attributes,$userId);
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