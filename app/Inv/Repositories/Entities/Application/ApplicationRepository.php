<?php

namespace App\Inv\Repositories\Entities\Application;

use DB;
use Session;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\AppNote;



/**
 * Application repository class
 */
class ApplicationRepository extends BaseRepositories implements ApplicationInterface {

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

    /**
    *update company details
    * @param mixed $bizId
    * @param array $attributes     
    */

    public function updateCompanyDetail($attributes = [], $bizId = null, $userId){
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

        if(is_null($bizId)){
            throw new BlankDataExceptions('No Data Found');
        }
        return Business::updateCompanyDetail($attributes, $bizId, $userId);
    }

    /**
     * Get Applications for Application list data tables
     */
    public function getApplications() 
    {
        return Application::getApplications();
    }

    /**
     * Get business information according to app id
     */
    public function getBusinessInfo($appId = null){
        if(is_null($appId)){
            throw new BlankDataExceptions('No Data Found');
        }
        return Application::get($appId);
    }

    /**
     * Get Application by app id
     */
    public function getApplicationById($bizId) 
    {
        return Business::getApplicationById($bizId);
    }
    
    /**
     * Update Application Status
     * 
     * @param integer $appId
     * @param array $attributes
     * @return boolean
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public function updateAppStatus($appId, $attributes = []){
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

        if(empty($appId)){
            throw new BlankDataExceptions('No Data Found');
        }
        return true;
    }

    /**
     * Update Application Assignee
     * 
     * @param type $appId
     * @param type $attributes
     * @return boolean
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public function updateAssignee($appId, $attributes = []){
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

        if(empty($appId)){
            throw new BlankDataExceptions('No Data Found');
        }
        return true;        
    }
    
    /**
     * Get Applications for Application list data tables
     */
    public function getApplicationsDetail($user_id) 
    {
        return Application::getApplicationsDetail($user_id);
    }
    
    /**
     * Get Applications for Application list data tables
     */
    public function getApplicationPoolData() 
    {
        return Application::getApplicationPoolData();
    }    
        
    /**
     * Save application note
     * 
     * @param array $noteData
     * @return mixed
     */
    public function saveAppNote($noteData) 
    {
        return AppNote::create($noteData);
    }

    
    /**
     * Get Applications for Application list data tables
     */
    public function saveShaircase($attributes) 
    {
        return AppAssignment::saveData($attributes);
    }
    
     /**
     * update Applications for Application list data tables
     */
    public function updateAppDetails($app_id, $arrUserData = []) 
    {
        return Application::updateAppDetails((int)$app_id, $arrUserData);
    }
 
    /**
     * Get Application Data By Biz Id
     * 
     * @param integer $biz_id
     * @return mixed
     */
    public function getAppDataByBizId($biz_id)
    {
       return Application::getAppDataByBizId((int)$biz_id); 
    }
    
    /**
     * Update Application Data By application Id
     * 
     * @param integer $app_id
     * @param array $arrData
     *
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function updateAppData($app_id, $arrData)
    {
       return Application::updateAppData((int)$app_id, $arrData);  
    }    
}
