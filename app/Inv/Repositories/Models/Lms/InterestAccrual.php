<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class InterestAccrual extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'interest_accrual';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'interest_accrual_id';

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
        'disbursal_id',
        'interest_date',
        'principal_amount',
        'accrued_interest',
        'interest_rate',
        'overdue_interest_rate',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save or Update Interest Accrual
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveInterestAccrual($data, $whereCondition=[])
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($data);
        } else {
            return self::create($data);
        }
    }
    
    /**
     * Get Accrued Interest Data
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getAccruedInterestData($whereCondition=[])
    {                 
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (isset($whereCondition['disbursal_id'])) {
            $query->where('disbursal_id', $whereCondition['disbursal_id']);
        }
        if (isset($whereCondition['interest_date_gte'])) {
            $query->where('interest_date', '>=', $whereCondition['interest_date_gte']);
        }   
        if (isset($whereCondition['interest_date_eq'])) {
            $query->where('interest_date', '=', $whereCondition['interest_date_eq']);
        }          
        $query->orderBy('interest_accrual_id', 'ASC');
        $result = $query->get();                
        return $result;
    }
    
    /**
     * Get Sum of Accrued Interest
     *      
     * @param array $whereCond
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function sumAccruedInterest($whereCond)
    {                
        if (isset($whereCond['disbursal_id'])) {
           $query = self::where('disbursal_id', $whereCond['disbursal_id']); 
        } 
        
        if (isset($whereCond['interest_date_lte'])) {
           $query = self::where('interest_date', '<=', $whereCond['interest_date_lte']);  
        }
        
        if (isset($whereCond['interest_date_gte'])) {
           $query = self::where('interest_date', '>=', $whereCond['interest_date_gte']);  
        }
        
        $result = $query->sum('accrued_interest');
        return $result;
    }

    public static function countAccruedInterest($whereCond)
    {
        if (isset($whereCond['disbursal_id'])) {
            $query = self::where('disbursal_id', $whereCond['disbursal_id']); 
         } 
         
         if (isset($whereCond['interest_date_lte'])) {
            $query = self::where('interest_date', '<=', $whereCond['interest_date_lte']);  
         }
         
         if (isset($whereCond['interest_date_gte'])) {
            $query = self::where('interest_date', '>=', $whereCond['interest_date_gte']);  
         }
         
         $result = $query->count();
         return $result;
    }
}
