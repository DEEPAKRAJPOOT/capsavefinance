<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;

class Program extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'prgm';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'prgm_id';

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
        'anchor_id',
        'anchor_user_id',
        'prgm_name',
        'industry_id',
        'sub_industry_id',
        'cibil_score',
        'product_name',
        'is_fldg_applicable',
        'anchor_limit',
        'min_loan_size',
        'max_loan_size',
        'min_interest_rate',
        'max_interest_rate',
        'min_tenor',
        'max_tenor',
        'min_tenor_old_invoice',
        'max_tenor_old_invoice',
        'margin',
        'overdue_interest_rate',
        'interest_linkage',
        'is_adhoc_facility',
        'adhoc_interest_rate',
        'is_grace_period',
        'grace_period',
        'interest_borne_by',
        'disburse_method',
        'repayment_method',
        'processing_fee',
        'check_bounce_fee',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',        
    ];

    /**
     * Get Program Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getProgramData($whereCondition=[])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $whereCondition['status'] = isset($whereCondition['status']) ? $whereCondition['status'] : 1;
        
        $appNote = self::select('prgm.prgm_id','prgm.prgm_name', 'prgm.product_name',
                'prgm.anchor_limit', 'prgm.min_loan_size',
                'prgm.max_loan_size',
                'prgm.min_interest_rate',
                'prgm.max_interest_rate',
                'prgm.min_tenor',
                'prgm.max_tenor',
                'prgm.min_tenor_old_invoice',
                'prgm.max_tenor_old_invoice',
                'prgm.margin',
                'prgm.overdue_interest_rate',
                'prgm.interest_linkage',
                'prgm.is_adhoc_facility',
                'prgm.adhoc_interest_rate',
                'prgm.is_grace_period',
                'prgm.grace_period',
                'prgm.interest_borne_by',
                'prgm.disburse_method',
                'prgm.repayment_method',
                'prgm.processing_fee',
                'prgm.check_bounce_fee',                
                'prgm.status',                
                'mst_industry.name as industry_name', 
                'mst_sub_industry.name as sub_industry_name',
                DB::raw("CASE
                            WHEN is_fldg_applicable = 0 THEN 'No'
                            WHEN is_fldg_applicable = 1 THEN 'Yes'
                            ELSE ''
                        END AS is_fldg_applicable")
                )
                ->leftJoin('mst_industry', 'prgm.industry_id', '=', 'mst_industry.id')
                ->leftJoin('mst_sub_industry', 'prgm.sub_industry_id', '=', 'mst_sub_industry.id')
                ->where($whereCondition)
                ->first();      
        return $appNote;
    }


}
