<?php

namespace App\Inv\Repositories\Entities\Application;

use DB;
use Session;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\AppDocument;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\LiftingDetail;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Models\FiAddress;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\AppNote;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\Offer;
use App\Inv\Repositories\Models\Agency;
use App\Inv\Repositories\Models\Master\Industry;

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
    public function getAppDataByAppId($appId = null){
        if(is_null($appId)){
            throw new BlankDataExceptions('No Data Found');
        }
        return Application::find($appId);
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
     * update Applications for Application list data tables
     */
    public function updateAppAssignById($app_id, $arrUserData = []) 
    {
        return AppAssignment::updateAppAssignById((int)$app_id, $arrUserData);
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

    /**
     * Assign application
     * 
     * @param type $dataArr
     */
    public function assignApp($dataArr)
    {
        return AppAssignment::saveData($dataArr);
    }

    /**
     * Get User Applications for Application list data tables for frontend
     */
    public function getUserApplications() 
    {
        return Application::getUserApplications();
    }

    /**
     * function for get all FI lists
     * @return type
     */
     
    public function getFiLists($dataArr)
    {
      $result = BusinessAddress::getFiLists($dataArr);
      return $result ?: false;
    }

     /**
     * function for get all RCU documents list
     * @return type
     */
     
    public function getRcuLists($appId)
    {
      $result = AppDocumentFile::getRcuLists($appId);
//      dd($result);
      return $result ?: false;
    }
    
     /**
     * function for get all RCU documents filess list
     * @return type
     */
     
    public function getRcuDocuments($appId, $docId)
    {
      $result = AppDocumentFile::getRcuDocuments($appId, $docId);
      return $result ?: false;
    }
    
    /**
     * Get Program Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getProgramData($whereCondition=[])
    {
        $prgmData = Program::getProgramData($whereCondition);
        return $prgmData ? $prgmData : [];
    }
         
    /**
     * Get Anchor Data By Application Id
     * 
     * @param integer $app_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function getAnchorDataByAppId($app_id)
    {
        $prgmData = Application::getAnchorDataByAppId($app_id);
        return $prgmData ? $prgmData : [];
    }  
    
    /**
     * Get Offer Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getOfferData($whereCondition=[])
    {
        $offerData = Offer::getOfferData($whereCondition);
        return $offerData ? $offerData : [];
    }

    /**
     * Save Offer Data
     * 
     * @param array $offerData
     * @param integer $offerId optional
     * 
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function saveOfferData($offerData=[], $offerId=null)
    {
        $offerData = Offer::saveOfferData($offerData, $offerId);
        return $offerData ? $offerData : false;
    }
    
    /**
     * Update Offer Data By Application Id
     * 
     * @param integer $app_id
     * @param array $arr
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function updateOfferByAppId($app_id, $arr = [])
    {        
        return Offer::updateOfferByAppId((int) $app_id, $arr);
    }    

    /**
     * get address for FI
     * 
     * @param integer $biz_id
     * @return all address result
     */
    public function getAddressforFI($biz_id){
        $result = BusinessAddress::getAddressforFI($biz_id);
        return $result ?: false;
    }


    /**
     * get address for FI
     * 
     * @param integer $biz_id
     * @return all address result
     */
    

    public function creates($attributes){
        $result =  LiftingDetail::creates(($attributes));
        return $result ?: false;
    }

    public function getLiftingDetail($appId){
        $result =  LiftingDetail::where('app_id',$appId)->get();
        return !$result->isEmpty() ? $result : false;
    }

     public function updateLiftingDetail($attributes, $anchor_lift_detail_id){
        $anchor =  LiftingDetail::where('anchor_lift_detail_id',$anchor_lift_detail_id)->first();
        $updateAnchorData = $anchor->update($attributes);
        return $updateAnchorData ? true : false;
    }

    /**
     * insert into FI address
     * 
     * @param array $data
     * @return status
     */
    public function insertFIAddress($data){
        $result = FiAddress::insertFiAddress($data);
        return $result ?: false;
    }

    /**
     * get all agency list
     * @return agency
     */
    public function getAllAgency(){
        $agency = Agency::get();
        return $agency ?: false;
    }

    public function changeAgentFiStatus($request){
      $status = FiAddress::changeAgentFiStatus($request);
      if($status){
        return response()->json(['status'=>$status, 'message'=>'Status changed successfully']);
      }else{
        return response()->json(['status'=>0, 'message'=>'Something went wrong, Try again later.']);
      }
    }

    public function changeCmFiStatus($request){
      $status = $this->application->changeAgentFiStatus($request);
      return $status;
    }
    
    
    
    /**
     * Get industry 
     * 
     * @return type mixed
     */
    public function getIndustryDropDown()
    {
        return Industry::getIndustryDropDown();
    }
    
    
    /**
     * Get sub industry 
     * 
     * @param type $where Array
     * @return type mixed
     */
    public function getSubIndustryByWhere($where)
    {
        return \App\Inv\Repositories\Models\Master\SubIndustry::getSubIndustryByWhere($where);
    }
    
    
    
    /**
     * Save program
     * 
     * @param type $attr array
     * @return type mixed
     */
    public function saveProgram($attr)
    {
        return Program::saveProgram($attr);
    }
    
    
    /**
     * program list by id
     * 
     * @param type $id int
     * @return type mixed
     */
    public function getProgramListById($id)
    {
        return Program::getProgramListById($id);
    }
    
    
    /**
     * get selected program data
     * 
     * @param type $attr array
     * @param type $selected array
     * @return type mixed
     */
    public function getSelectedProgramData($attr, $selected = null)
    {
        return Program::getSelectedProgramData($attr, $selected);
    }
    
    
    /**
     * get document list 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getDocumentList($where)
    {
        return DocumentMaster::getDocumentList($where);
    }

}
