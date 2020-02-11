<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\ProgramCharges;



class ChargesTransactions extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chrg_transactions';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'chrg_trans_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */

    public $timestamps = false;


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
        'prgm_id',
        'chrg_master_id',
        'percent',
        'chrg_applicable_id',
        'amount',
        'created_at',
        'created_by'
    ];

    



    /**
     * get Charge list
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
    */
    
    public static function saveChargeTrans($attr)
    {
        $res =   self::create($attr);
        return ($res) ? $res->chrg_trans_id : false;
    }
    
    
    public static function getAllTransCharges()
    {
        
       return  self::with(['transaction','ChargeMaster']);
        
    }
    
    public function transaction()
    {
              return $this->hasOne('App\Inv\Repositories\Models\Lms\Transactions','charge_id','chrg_trans_id');
   
    }
    
    public function ChargeMaster()
    {
            return $this->hasOne('App\Inv\Repositories\Models\Master\Charges','id','chrg_master_id');
      
    }
}
