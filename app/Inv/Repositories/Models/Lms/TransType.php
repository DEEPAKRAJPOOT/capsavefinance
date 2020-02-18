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
/** 
 * @Author: Rent Alpha
 * @Date: 2020-02-17 14:41:47 
 * @Desc:  
 */
    public static function getManualTranType(){
       $result=self::select('*')
        ->where("is_visible","=", 1)
        ->where("is_active","=", 1)
        ->get();
        return $result?$result:'';
    }
}
