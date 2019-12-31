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
    $result = Documents::find($documentId);
    return $result ?: false;
  }

  public function getAllDocuments()
  {
    $result = Documents::orderBy('id', 'DESC');
    return $result ?: false;
  }

  public function saveDocuments($attributes)
  {
    $status = Documents::create($attributes);
    return $status ?: false;
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
}
