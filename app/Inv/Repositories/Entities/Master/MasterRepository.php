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

use App\Inv\Repositories\Models\Master\Industry;
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

<<<<<<< HEAD
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

 
}
=======
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
}
>>>>>>> ab5a52aed2781bc6338461ff2918b0479b24b5ad
