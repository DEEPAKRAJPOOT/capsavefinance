<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Disbursal extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'disbursal';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'disbursal_id';

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
        'app_id',
        'invoice_id',
        'prgm_offer_id',
        'bank_id',
        'disburse_date',
        'bank_name',
        'ifsc_code',
        'acc_no',
        'virtual_acc_id',
        'customer_id',
        'principal_amount',
        'inv_due_date',
        'tenor_days',
        'interest_rate',
        'total_interest',
        'margin',
        'disburse_amount',
        'status',
        'settlement_date',
        'accured_interest',
        'interest_refund',
        'funded_date',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save or Update Disbursal Request
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveDisbursalRequest($data, $whereCondition=[])
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($data);
        } else if (isset($data[0])) {
            return self::insert($data);
        } else {
            return self::create($data);
        }
    }
    
    /**
     * Get Disbursal Requests
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getDisbursalRequests($whereCondition=[])
    {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        $query->orderBy('disburse_date', 'DESC');
        $query->orderBy('disbursal_id', 'DESC');
        $result = $query->get();
        return $result;
    }
}
