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

  public function saveCharges($attributes)
  {
    $status = Charges::create($attributes);
    return $status ?: false;
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
      $result = BaseRate::orderBy('id', 'DESC');
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

}