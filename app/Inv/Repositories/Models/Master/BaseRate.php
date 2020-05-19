<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;

class BaseRate extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_base_rate';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'base_rate',
        'start_date',
        'end_date',
        'is_active',
        'is_default',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];
    
    public static function getAllBaseRateList(){
        return self::with('bank')->orderBy('id', 'DESC');
    }
    
    function bank()
    {
          return $this->belongsTo('App\Inv\Repositories\Models\Master\Bank', 'bank_id','id');  
    
    }
     
    /**
     * Get Drop down list
     *
     * @return type
     */
    public static function getBaseRateDropDown()
    {
        return self::where('is_active', 1)->get();
    }

    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public static function checkIsDefaultBaseRate($bankId,$isDefault) {
        
        if (empty($isDefault) || empty($bankId)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $res = self::where(['bank_id' => $bankId, 'is_default' => $isDefault])->first();
        
        return $res;
    }

    public static function updateBaseRateEndDate($id, $bankId, $date){
        $query = BaseRate::where('id','<>',$id)->where('bank_id', $bankId)->orderBy('id', 'DESC')->first();
        if($query){
            return $query->update(['end_date'=>$date, 'is_default'=>0]);
        }else{
            return true;
        }    
    }

}
