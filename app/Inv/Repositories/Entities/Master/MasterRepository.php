<?php

namespace App\Inv\Repositories\Entities\Master;

use Carbon\Carbon;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\AuthTrait;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\Master\Charges;
use App\Inv\Repositories\Models\Master\Voucher as Vouchers;
use App\Inv\Repositories\Models\Master\Documents;
use App\Inv\Repositories\Models\Master\Entity;
use App\Inv\Repositories\Models\Master\DoaLevel;
use App\Inv\Repositories\Models\Master\Industry;
use App\Inv\Repositories\Models\Master\State as StateModel;
use App\Inv\Repositories\Models\Master\City as CityModel;
use App\Inv\Repositories\Models\Master\DoaLevelRole;
use App\Inv\Repositories\Models\ProgramDoaLevel;
use App\Inv\Repositories\Models\Product;
use App\Inv\Repositories\Models\ProductDoc;
use App\Inv\Repositories\Models\Master\Bank;
use App\Inv\Repositories\Models\DeoLevelStates;
use App\Inv\Repositories\Models\Master\Status;
use App\Inv\Repositories\Models\Master\Company;
use App\Inv\Repositories\Models\Master\GstTax;
use App\Inv\Repositories\Models\Master\Segment;
use App\Inv\Repositories\Models\Master\Constitution;
use App\Inv\Repositories\Models\Master\Equipment;
use App\Inv\Repositories\Models\Master\FacilityType;
use App\Inv\Repositories\Models\Master\BaseRate;
use App\Inv\Repositories\Models\Master\Activity;
use App\Inv\Repositories\Models\Master\ChargeGST;
use App\Inv\Repositories\Models\Master\Tds;
use App\Inv\Repositories\Models\Master\BorrowerLimit;
use App\Inv\Repositories\Models\Master\Voucher;
use App\Inv\Repositories\Models\Master\LocationType;
use App\Inv\Repositories\Models\Master\SecurityDocument;
use App\Inv\Repositories\Models\Master\Group;
use App\Inv\Repositories\Models\Master\NewGroup;
use App\Inv\Repositories\Models\Master\MakerChecker;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\AppGroupDetail;

/**
 * 
 */
class MasterRepository extends BaseRepositories implements MasterInterface
{
  use CommonRepositoryTraits, AuthTrait;

  function __construct()
  {
    parent::__construct();
  }

  public function create(array $attributes)
  {
    return Charges::create($attributes);
  }

  public function update(array $attributes, $id)
  {
    //
  }

  public function destroy($ids)
  {
    //
  }

  public function findChargeById($chargeId)
  {
    if (empty($chargeId) || !ctype_digit($chargeId)) {
      throw new BlankDataExceptions('No Data Found');
    }
    $result = Charges::find($chargeId);
    return $result ?: false;
  }

  public function getAllCharges()
  {
    $result = Charges::orderBy('id', 'DESC');
    return $result ?: false;
  }

  public function getAllVouchers()  {
    $result = Vouchers::orderBy('voucher_name', 'ASC');
    return $result ?: false;
  } 
  
  
  public function saveCharges($attributes)
  {
     return  Charges::saveCharge($attributes);

   
  }
 

  public function updateCharges($attributes, $chargeId)
  {
    $status = Charges::where('id', $chargeId)->first()->update($attributes);
    return $status ?: false;
  }

  public function findDocumentById($documentId)
  {
    if (empty($documentId) || !ctype_digit($documentId)) {
      throw new BlankDataExceptions('No Data Found');
    }
    $result = Documents::with('product_document')
            ->where('id', $documentId)
            ->first();

    return $result ?: false;
  }

  public function getAllDocuments()
  {
    $result = Documents::with('product_document.product')->orderBy('id', 'DESC');
    return $result ?: false;
  }

  public function saveDocuments($attributes)
  {
    $status = Documents::create($attributes);
    return $status ?: false;
  }

  public function updateProductDocuments($productIds, $docId)
  {

    $product = ProductDoc::where('doc_id', $docId)
            ->update(['is_active' => 0]);
    if(!empty($productIds)) {
        foreach ($productIds as $productId) {
            $result = ProductDoc::updateOrCreate(
                [
                    'product_id' => $productId, 
                    'doc_id' => $docId
                ], 
                [
                    'product_id' => $productId, 
                    'doc_id' => $docId, 
                    'is_active' => 1
                ]);
        }
    }

    return true;
  }

  public function updateDocuments($attributes, $documentId)
  {
    $status = Documents::where('id', $documentId)->first()->update($attributes);
    return $status ?: false;
  }

  // Entity
  public function findEntityById($entity_id)
  {
    if (empty($entity_id) || !ctype_digit($entity_id)) {
      throw new BlankDataExceptions('No Data Found');
    }
    $result = Entity::find($entity_id);
    return $result ?: false;
  }

  public function getAllEntities()
  {
    $result = Entity::orderBy('id', 'DESC');
    return $result ?: false;
  }
  
  public function saveEntity($attributes)
  {
    $status = Entity::create($attributes);
    return $status ?: false;
  }

  public function updateEntity($attributes, $entityId)
  {
    $status = Entity::where('id', $entityId)->first()->update($attributes);
    return $status ?: false;
  }

    public function findIndustryById($industryId){
      if (empty($industryId) || !ctype_digit($industryId)) {
            throw new BlankDataExceptions('No Data Found');
      }
      $result = Industry::find($industryId);
      return $result ?: false;
    }

    public function getAllIndustries(){
      $result = Industry::orderBy('id', 'DESC');
      return $result ?: false;
    }

    public function saveIndustries($attributes){
        $status = Industry::create($attributes);
        return $status ?: false;
    }

    public function updateIndustries($attributes, $industryId){
        $status = Industry::where('id', $industryId)->first()->update($attributes);
        return $status ?: false;

    }
    
    /**
     * Get DoA Levels
     * 
     * @return mixed
     */
    public function getDoaLevels()
    {
        $result = DoaLevel::getDoaLevels();
        return $result;
    }
    
    /**
     * Get DoA Level Data by doa_level_id
     * 
     * @param mixed $doa_level_id
     * @return mixed
     */
    public function getDoaLevelById($doa_level_id)
    {
        $result = DoaLevel::getDoaLevelById((int) $doa_level_id);
        return $result;
    }
    
    /**
     * Get all State
     * 
     * @param integer $countryId | optional
     * @return mixed
     */
    public function getState($countryId=101)
    {
        $result = StateModel::getStateList($countryId)->orderBy('name')->get();
        return $result;        
    }
    
    /**
     * Get City By State Id
     * 
     * @param integer $stateId
     * @return mixed
     */
    public function getCity($stateId)
    {
        $result = CityModel::getCity($stateId);
        return $result;        
    }
    
    /**
     * Get Latest DoA Data
     * 
     * @return mixed
     */
    public function getLatestDoaData()
    {
        $result = DoaLevel::getLatestDoaData();
        return $result;
    }
        
    /**
     * Save DoA Data
     * 
     * @param array $data
     * @param integer $doa_level_id
     * @return mixed
     */
    public function saveDoaLevelData($data, $doa_level_id=null)
    {
        $result = DoaLevel::saveDoaLevelData($data, $doa_level_id);
        return $result;
    }
    
    /**
     * Update DoA Data
     * 
     * @param array $data
     * @param array $whereCond
     * @return mixed
     */
    public function updateDoaLevelData($data, $whereCond=[])
    {
        $result = DoaLevel::updateDoaLevelData($data, $whereCond);
        return $result;
    }
    
    /**
     * Get DoA Levels
     * 
     * @param array $where
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public function getDoaLevelData($where)
    {
        return DoaLevel::getDoaLevelData($where);
    }
    
    /**
     * Get DoA Level Roles
     * 
     * @param array $doa_level_id
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public function getDoaLevelRoles($doa_level_id)
    {
        return DoaLevelRole::getDoaLevelRoles($doa_level_id);
    }
    
    /**
     * Save DoA Level Roles
     * 
     * @param array $data
     * @return type mixed
     */
    public function saveDoaLevelRoles($data)
    {
        return DoaLevelRole::saveDoaLevelRoles($data);
    }
    
    /**
     * Save DoA Level Roles
     * 
     * @param array $data
     * @return type mixed
     */
    public function deleteDoaLevelRoles($doa_level_id)
    {
        return DoaLevelRole::deleteDoaLevelRoles($doa_level_id);
    }     
    
    
    
    
    /**
     * get D0A level list 
     * 
     * @return type mixed
     */
    public function getDoaLevelList()
    {
        return DoaLevel::getDoaLevelList();
    }

    /**
     * Insert D0A level
     * 
     * @param type $attr array
     * @return type mixed
     */
    public function insertDoaLevel($attr)
    {
        return ProgramDoaLevel::insertDoaLevel($attr);
    }

    /**
     * delete  DOA Level
     * 
     * @param type $where array
     * @return type mixed
     */
    public function deleteDoaLevelBywhere($where)
    {
        return ProgramDoaLevel::deleteDoaLevelBywhere($where);
    }
    
    /**
     * get program DOA level data
     * 
     * @param type $where Array
     * @return type mixed
     */
    public function getProgramDoaLevelData($where)
    {
        return ProgramDoaLevel::getProgramDoaLevelData($where);
    }
    
    /**
     * get product data list
     * 
     * @return type mixed
     */
    
    public function getProductDataList()
    {
        return Product::getProductDataList();
    }
    
    
    
    /**
     * get Bank list
     * 
     * @return type mixed
     */
    public function getBankList()
    {
        return Bank::getBankList();
    }
    
    
    
    /**
     * get city
     * 
     * @param type $state_id
     * @return type mixed
     */
    public function getCityWhereIn($state_id)
    {
        return CityModel::getCityWhereIn($state_id);
    }
    
    
    
    
     
    /**
     * Save Deo level states 
     * 
     * @param type $attributes
     * @return type mixed
     */
    public function saveDeoLevelStates($attributes)
    {
        return DeoLevelStates::saveDeoLevelStates($attributes);
    }
    
    
    /**
     * delete deo level
     * 
     * @param array $attributes
     * @return mixed
     */
    
    public function deleteDeoLevelStates($attributes)
    {
        return DeoLevelStates::deleteDeoLevelStates($attributes);
    }
    
    
    /**
     * get app status
     * 
     * @return mixed
     */
    public function getAppStatus($status_type)
    {
        return Status::getStatusList($status_type);
    }


    public function getActiveProducts()
    {
        $result = Product::where('is_active', 1)
                ->get();
        return $result ?: false;
    }
    
    public function saveCompanies($attributes) {

        $status = Company::saveCompanies($attributes);

        return $status ?: false;
    }    
    
    public function getAllCompanies($keyword) {

        $result = Company::getAllCompanies($keyword);
        
        return $result ?: false;
    }
    
    public function findCompanyById($companyId) {
        
        $result = Company::findCompanyById($companyId)->toarray();
  
        return $result ?: false;
    }
    
    public function updateCompanies($attributes, $companyId) {
        
        $status = Company::updateCompanies($attributes, $companyId);
        
        return $status ?: false;
    }

    /**
     * master GST list
     * 
     * @param array $attributes
     * @return mixed
    */
    public function findGstById($gst_id)
    {
        if (empty($gst_id) || !ctype_digit($gst_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = GstTax::find($gst_id);
        return $result ?: false;
    }
    public function getAllGST()
    {
        $result = GstTax::getAllGST();
        return $result;
    }

    public function saveGst($attributes, $tax_id=null)
    {
        return GstTax::saveGst($attributes, $tax_id);
    }

    public function updateGST($attributes, $tax_id)
    {
        $status = GstTax::updateGST($attributes, $tax_id);
        return $status ?: false;
    }
    
    public function updateGstEndDate($id, $date)
    {
        return GstTax::updateGstEndDate($id, $date);
    } 

    /**
     * master Borrower limit list
     * 
     * @param array $attributes
     * @return mixed
     */

    public function getCurrentBorrowerLimitData(){

        $result = BorrowerLimit::getCurrentBorrowerLimitData();
        return $result ? $result: false;
    }

    public function updatePrevLimitStatus(){

        $status = BorrowerLimit::updatePrevLimitStatus();
        return $status ?: false;
    }

    public function findLimitById($limit_id)
    {
        if (empty($limit_id) || !ctype_digit($limit_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = BorrowerLimit::find($limit_id);
        return $result ? $result : false;
    }

    public function findLastLimit(){

        $result = BorrowerLimit::findLastLimit();
        return $result ? $result : false;        
    }

    public function getavailFutureDate(){

        $result = BorrowerLimit::getavailFutureDate();
        return $result ? true: false;
    }

    public function getAllLimit()
    {
        $result = BorrowerLimit::getAllLimit();
        return $result;
    }

    public function expirePastLimit(){

        $result = BorrowerLimit::expirePastLimit();
        return $result;
    }

    public function updateLimitEndDate($id, $date)
    {
        return BorrowerLimit::updateLimitEndDate($id, $date);
    }

    public function saveLimit($attributes, $limit_id=null){
        
        return BorrowerLimit::saveLimit($attributes,$limit_id);
    }

    public function updateLimit($attributes, $limit_id)
    {
        $status = BorrowerLimit::updateLimit($attributes, $limit_id);
        return $status ?: false;
    }
    

    /**
     * master Segments list
     * 
     * @param array $attributes
     * @return mixed
     */

    public function findSegmentById($segment_id)
    {
        if (empty($segment_id) || !ctype_digit($segment_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = Segment::find($segment_id);
        return $result ?: false;
    }
    public function getSegmentLists()
    {
        $result = Segment::getSegmentLists();
        return $result;
    }

    public function saveSegment($arrSegmentData)
    {
        return Segment::saveSegment($arrSegmentData);
    }

    public function updateSegment($arrSegmentData, $segment_id)
    {
        $status = Segment::updateSegment($arrSegmentData, $segment_id);
        return $status ?: false;
    }

    /**
     * master Constitution list
     * 
     * @param array $attributes
     * @return mixed
     */
    public function findConstitutionById($consti_id)
    {
        if (empty($consti_id) || !ctype_digit($consti_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = Constitution::find($consti_id);
        return $result ?: false;
    }
    public function getAllConstitution()
    {
        $result = Constitution::getAllConstitution();
        return $result;
    }

    public function saveConstitution($arrConstiData) 
    {
        return Constitution::saveConstitution($arrConstiData);
    }

    public function updateConstitution($arrConstiData, $consti_id)
    {
        $status = Constitution::updateConstitution($arrConstiData, $consti_id);
        return $status ? $status : false;
    }

    /*
     * Business Address
     * 
     * return type boolean
     */
    public function getAddStateList()
    {
        return StateModel::getAllStateList();
    }

    /**
     * master Equipments list
     * 
     * @param array $attributes
     * @return mixed
    */
    public function findEquipmentsById($equipment_id)
    {
        if (empty($equipment_id) || !ctype_digit($equipment_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = Equipment::find($equipment_id);
        return $result ?: false;
    }

    public function getEquipments()
    {
        $result = Equipment::getAllEquipmentList();
        return $result;
    }

    public function saveEquipment($arrEquipmentData) 
    {
        return Equipment::saveEquipment($arrEquipmentData);
    }

    public function updateEquipment($arrEquipmentData, $equipment_id)
    {
        $status = Equipment::updateEquipment($arrEquipmentData, $equipment_id);
        return $status ? $status : false;
    }

    /*
     * Get Facility Type List
     * 
     * return array
     */
    public function getFacilityTypeList()
    {
        return FacilityType::getFacilityTypeList();
    }   
    
    /*
     * Get Base Rate List
     * 
     * return array
     */
    public function getAllBaseRateList(){
//      $result = BaseRate::orderBy('id', 'DESC');
        $result = BaseRate::getAllBaseRateList();
      return $result ?: false;
    }
    
    public function saveBaseRate($attributes){
        $status = BaseRate::create($attributes);
        return $status ?: false;
    }
    
    public function findBaseRateById($baseRateId){
      if (empty($baseRateId) || !ctype_digit($baseRateId)) {
            throw new BlankDataExceptions('No Data Found');
      }
      $result = BaseRate::find($baseRateId);
      return $result ?: false;
    }
    
    public function updateBaseRate($attributes = [], $baseRateId){
        $status = BaseRate::where('id', $baseRateId)->first()->update($attributes);
        return $status ?: false;

    }
    
    public function getBaseRateDropDown()
    {
        return BaseRate::getBaseRateDropDown();
    }
    
    /*

     * check that company is registered.
     */
    public function checkIsRegCompany($cmp_name, $is_reg) {
        
        $result = Company::checkIsRegCompany($cmp_name, $is_reg);
  
        return $result;
    }
    
    /*
     * check that company address is unique by company name.
     */
    public function getCompAddByCompanyName($attributes) {
        
        return Company::getCompAddByCompanyName($attributes);
  
    }
    
    /*
     * get companies ids of same name by a particular company id.
     */
    public function getCompNameByCompId($compId) {
        
        return Company::getCompNameByCompId($compId);
  
    }
    
    /*
     * check that base rate is default set for a bank.
     */
    public function checkIsDefaultBaseRate($bankId, $isDefault) {
        
        $result = BaseRate::checkIsDefaultBaseRate($bankId, $isDefault);
  
        return $result;
    }
    
    /**
     * Get Activity Data
     * 
     * @param array $whereCond
     * @return type mixed     
     * @throws InvalidDataTypeExceptions 
     */
    public function getActivity($whereCond=[])
    {
        return Activity::getActivity($whereCond);

    }

    public function updateBaseRateEndDate($id, $bankId, $date)
    {
        return BaseRate::updateBaseRateEndDate($id, $bankId, $date);
    }    

   
    public function saveChargesGST($attributes)
    {
     return  ChargeGST::saveChargesGST($attributes);
    }
    
    public function getLastChargesGSTById($chargeId)
    {
      return ChargeGST::getLastChargesGSTById($chargeId);
    }

    
    /**
     * Get Last DoA Level Id
     * 
     * @return mixed
     */
    public function getLastDoaLevelId()
    {
        return DoaLevel::getLastDoaLevelId();
    }    

      
    /**
     * Get all product type
     * 
    
     */
    public function getProductType()
    {
        return Product::getProductType();
             
    }
    public function getProIdByDoaLevel($doid)
    {
        return DoaLevel::getProIdByDoaLevel($doid);
             
    }

    /**
     * start TDS in master 
     */

    public function findTDSById($tds_id)
    {
        if (empty($tds_id) || !ctype_digit($tds_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = Tds::find($tds_id);
        return $result ?: false;
    }

    public function getTDSLists()
    {
        $result = Tds::getTDSLists();
        return $result;
    }

    public function saveTds($tdsData) 
    {
        return Tds::saveTds($tdsData);
    }

    public function updateTds($tdsData, $tds_id)
    {
        $status = Tds::updateTds($tdsData, $tds_id);
        return $status ? $status : false;
    }

    public function updateTdsEndDate($id, $date)
    {
        return Tds::updateTdsEndDate($id, $date);
    }   
    // END TDS
    public function getLastGSTRecord()
    {
        return GstTax::getLastGSTRecord();
    }
    
    /**
     * get Bank list
     * 
     * @return type mixed
     */
    public function getAllBankList()
    {
        $result = Bank::orderBy('id', 'DESC');
        return $result ?: false;
    }

    public function getBankById($id)
    {
        $result = Bank::find($id);
        return $result ?: false;
    }

    public function saveBank($attributes, $id = null){
        return Bank::saveBank($attributes, $id);
    }

    // Check Docuement Exists or not
    public function checkDocumentExist($where){
        return Documents::checkDocumentExist($where);
    }

    // Check Docuement Exists in case of Edit
    public function checkDocumentExistEditCase($where, $document_id){
        return Documents::checkDocumentExistEditCase($where, $document_id);
    }

    // Check DOA name exists
    public function getDoaNameExists($where){
        return DoaLevel::getDoaNameExists($where);
    }

    // Check DOA name exists in Edit case
    public function getDoaNameEditCaseExists($where, $doa_id){
        return DoaLevel::getDoaNameEditCaseExists($where, $doa_id);
    }

    // Check DOA name exists in Edit case
    public function checkIndustryName($IndustryName, $industryId){
        return Industry::checkIndustryName($IndustryName, $industryId);
    }

    // Check DOA name exists in Edit case
    public function checkVoucherName($voucherName){
        return Voucher::checkVoucherName($voucherName);
    }

    // Check Segment name exists in Edit case
    public function checkSegmentName($segmentName, $segmentId){
        return Segment::checkSegmentName($segmentName, $segmentId);
    }

    // Check Entity name exists in Edit case
    public function checkEntityName($entityName, $entitytId){
        return Entity::checkEntityName($entityName, $entitytId);
    }

    // Check Constution name exists in Edit case
    public function checkConsitutionName($constiName, $constitId){
        return Constitution::checkConsitutionName($constiName, $constitId);
    }

    // Check Equipment name exists in Edit case
    public function checkEquipmentName($equipmentName, $equipmentId){
        return Equipment::checkEquipmentName($equipmentName, $equipmentId);
    }

    // Check Bank name exists in Edit case
    public function checkBankName($bankName, $banktId) {
        return Bank::checkBankName($bankName, $banktId);
    }

    public function getAllLocationType(){
        $result = LocationType::orderBy('location_id', 'DESC');
        return $result ?: false;
    }

    // Check Unique Location type
    public function checkLocationType($locationType, $locationId){
        return LocationType::checkLocationType($locationType, $locationId);
    } 
    
    public function saveLocationType($attributes){
        $status = LocationType::create($attributes);
        return $status ?: false;
    }

    public function findLocationById($locationId){
      if (empty($locationId) || !ctype_digit($locationId)) {
            throw new BlankDataExceptions('No Data Found');
      }
      $result = LocationType::find($locationId);
      return $result ?: false;
    }    

    public function updateLocationType($attributes, $locationId){
        $status = LocationType::where('location_id', $locationId)->first()->update($attributes);
        return $status ?: false;
    } 
    
    // Security Document
    public function getAllSecurityDocument(){
        $result = SecurityDocument::orderBy('security_doc_id', 'DESC');
        return $result ?: false;
    }
   
    public function checkSecurityDocument($securityDocumentName, $securityDocId){
        return SecurityDocument::checkSecurityDocumentName($securityDocumentName, $securityDocId);
    } 
    
    public function saveSecurityDocument($attributes){
        $status = SecurityDocument::create($attributes);
        return $status ?: false;
    }

    public function findSecurityDocumentById($securityDocId){
        if (empty($securityDocId) || !ctype_digit($securityDocId)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        $result = SecurityDocument::find($securityDocId);
        return $result ?: false;
    }    

    public function updateSecurityDocument($attributes, $securityDocId){
        $status = SecurityDocument::where('security_doc_id', $securityDocId)->first()->update($attributes);
        return $status ?: false;
    }

    public function getAllActiveGroup(){
        return Group::getAllActiveGroup();
    }
    /**
     * Get All group 
     * 
     * @return type
     */
    public function getAllNewGroup() 
    {
        return NewGroup::getAllGroup();
    }

    /**
     * Check unique group name
     * 
     * @param array $attributes
     * @param array $whereCond
     * @return type
     */
    public function checkGroupName($groupName, $groupId)
    {
        return NewGroup::checkGroupName($groupName, $groupId);
    }

    /**
     * Create and update group
     * 
     * @param array $attributes
     * @param int $id
     * @return type
     */
    public function updateOrCreateNewGroup($attributes, $id = null) 
    {
        return NewGroup::updateOrCreateGroup($attributes, (int) $id);
    }

    /**
     * Get group By Id
     * 
     * @param int $id
     * @return type
     */
    public function getNewGroupById($id) 
    {
        return NewGroup::getGroupById((int) $id);
    }

    public function saveMakerChecker($attributes)
    {
        return MakerChecker::create($attributes);
    }

    public function checkGroupNameSuggestions($groupName)
    {
        return NewGroup::checkGroupNameSuggestions($groupName);
    }

    public function checkGroupIsApproved($groupId)
    {
        return MakerChecker::checkGroupIsApproved($groupId);        
    }

    public function getAllNewActiveGroup($whereIn = [])
    {
        return NewGroup::getAllActiveGroup($whereIn);
    }

    public function getAllNewGroupUcicData($groupId)
    {
        return AppGroupDetail::getUcicDataByGroupId($groupId);
    }
}