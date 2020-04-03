<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class TransType extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'mst_trans_type';

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
    public $userstamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'trans_name',
        'is_visible',
        'is_active',
        'is_taxable',
        'is_tds',
        'is_payment',
        'chrg_master_id	',
        'priority',        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save or Update Transactions Type
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveTransType($data, $whereCondition=[])
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($data);
        } else {
            return self::create($data);
        }
    }

    function charge()
    {
       return $this->belongsTo('App\Inv\Repositories\Models\Master\Charges', 'chrg_master_id','id');
    }  

    /** 
     * @Author: Rent Alpha
     * @Date: 2020-02-17 14:41:47 
     * @Desc:  
     */
    public static function getManualTranType(){
       $result=self::select('*')
        ->where("is_visible","=", 1)
        ->where("is_active","=", 1)
        ->where("is_manual","=",1)
        ->orderBy("priority","asc")
        ->get();
        return $result?$result:'';
    }


    public static function getAllUnsettledTransTypes(array $where = array(), int $action_type) {
        $cond = '';
        $trans_type = [];
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "t1.$key = '$value'";
            }
           $cond = ' AND ' .implode(' AND ', $wh);   
        }

        if($action_type == 1)
        array_push($trans_type,(int)config('lms.TRANS_TYPE.REPAYMENT'));
        
        $query = "SELECT t1.trans_type, t1.amount AS debit_amount, IFNULL(SUM(t2.amount), 0) as credit_amount, (t1.amount - IFNULL(SUM(t2.amount), 0)) as remaining 
        FROM `get_all_charges` t1 
        LEFT JOIN get_all_charges as t2 ON t1.trans_id = t2.parent_trans_id 
        WHERE t1.entry_type = 0  ". $cond ." GROUP BY t1.trans_id HAVING remaining > 0 ";
        $result = \DB::SELECT(\DB::raw($query));
        
        

        foreach ($result as $key => $value) {
            array_push($trans_type,$value->trans_type);
        }

        return self::select('id','credit_desc','trans_name')
        ->where("is_visible","=", 1)
        ->where("is_active","=", 1)
        ->whereIn('id',$trans_type)
        ->orderBy("priority","asc")
        ->get();
    }
    
}
