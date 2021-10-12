<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use Helpers;
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
        'invoice_disbursed_id',
        'interest_date',
        'principal_amount',
        'accrued_interest',
        'interest_rate',
        'overdue_interest_rate',
        'sys_created_at',
        'sys_updated_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
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
        
        $data['sys_updated_at'] = Helpers::getSysStartDate();
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($data);
        } else {
            $data['sys_created_at'] = Helpers::getSysStartDate();
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
                
        if (isset($whereCondition['invoice_disbursed_id'])) {
            $query->where('invoice_disbursed_id', $whereCondition['invoice_disbursed_id']);
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
        if (isset($whereCond['invoice_disbursed_id'])) {
            $query = self::where('invoice_disbursed_id', $whereCond['invoice_disbursed_id']); 
        } 
        
        if (isset($whereCond['interest_date_lte'])) {
            $query->where('interest_date', '<=', $whereCond['interest_date_lte']);  
        }
        
        if (isset($whereCond['interest_date_gte'])) {
            $query->where('interest_date', '>=', $whereCond['interest_date_gte']);  
        }
        if(isset($whereCond['overdue_interest_rate_not_null'])){
            $query->whereNotNull('overdue_interest_rate');
        }
        $result = $query->sum('accrued_interest');
        return $result;
    }

    public static function countAccruedInterest($whereCond)
    {
        if (isset($whereCond['invoice_disbursed_id'])) {
            $query = self::where('invoice_disbursed_id', $whereCond['invoice_disbursed_id']); 
        } 
        
        if (isset($whereCond['interest_date_lte'])) {
            $query->where('interest_date', '<=', $whereCond['interest_date_lte']);  
        }
        
        if (isset($whereCond['interest_date_gte'])) {
            $query->where('interest_date', '>=', $whereCond['interest_date_gte']);  
        }
        if(isset($whereCond['overdue_interest_rate_not_null'])){
            $query->whereNotNull('overdue_interest_rate');
        }
         $result = $query->count();
         return $result;
    }

    public static function getOverdueData() {
        $data = DB::select('
            SELECT MAX(cnt) as od_days, SUM(amt) as utilized_amt, (SUM(amt) - SUM(od_settled_amt)) as od_outstanding, supplier_id, SUM(write_off) AS write_off_amt, SUM(settled) AS settled_amt, SUM(total_outstanding) AS total_outstanding_amt
            FROM (
            SELECT  a.supplier_id, c.invoice_disbursed_id, (COUNT(c.interest_accrual_id) + b.grace_period) AS cnt, SUM(c.accrued_interest) AS amt,
            (d.interset_write_off + d.principal_write_off + d.overdue_write_off + d.margin_write_off + d.charge_write_off) AS write_off,
            (d.principal_repayment + d.principal_waived_off + d.principal_tds  + d.interest_repayment + d.interest_waived_off + d.interest_tds + d.overdue_repayment + d.overdue_waived_off + d.overdue_tds + d.margin_repayment + d.margin_waived_off + d.margin_tds + d.charge_repayment + d.charge_waived_off + d.charge_tds)
            AS settled,
            SUM(d.overdue_repayment + d.overdue_waived_off + d.overdue_tds + d.overdue_write_off) AS od_settled_amt,
            d.total_outstanding_amount AS total_outstanding
            FROM rta_interest_accrual AS c
            JOIN rta_invoice_disbursed AS b ON c.invoice_disbursed_id = b.invoice_disbursed_id
            JOIN rta_invoice AS a ON a.invoice_id = b.invoice_id AND a.is_repayment = 0
            JOIN `rta_invoice_disbursed_details` AS d ON d.invoice_id = a.invoice_id
            WHERE c.overdue_interest_rate IS NOT NULL
            GROUP BY a.supplier_id, c.invoice_disbursed_id  
            ) AS temp 
            GROUP BY temp.supplier_id
        ');
        return $data;
    }
}
