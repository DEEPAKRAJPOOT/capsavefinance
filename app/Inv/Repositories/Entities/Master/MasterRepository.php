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
/**
 * 
 */
class MasterRepository extends BaseRepositories implements MasterInterface{
	 use CommonRepositoryTraits, AuthTrait;

	function __construct(){
		parent::__construct();
	}

  public function create(array $attributes){
     return Charges::create($attributes);
  }

  public function update(array $attributes, $id){
     //
  }

  public function destroy($ids){
     //
  }

	public function findChargeById($chargeId){
      if (empty($chargeId) || !ctype_digit($chargeId)) {
            throw new BlankDataExceptions('No Data Found');
      }
      $result = Charges::find($chargeId);
      return $result ?: false;
    }

    public function getAllCharges(){
      $result = Charges::orderBy('id', 'DESC');
      return $result ?: false;
    }

    public function saveCharges($attributes){
        $status = Charges::create($attributes);
        return $status ?: false;
    }

    public function updateCharges($attributes, $chargeId){
        $status = Charges::where('id', $chargeId)->first()->update($attributes);
        return $status ?: false;

    }
}