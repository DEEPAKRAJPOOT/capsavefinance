<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

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
        'anchor_id',
        'anchor_user_id',
        'product_id',
        'parent_prgm_id',
        'prgm_name',
        'prgm_type',
        'industry_id',
        'sub_industry_id',
        'cibil_score',
        'product_name',
        'is_fldg_applicable',
        'anchor_limit',
        'anchor_sub_limit',
        'min_loan_size',
        'max_loan_size',
        'interest_rate',
        'invoice_upload',
        'bulk_invoice_upload',
        'invoice_approval',
        'min_interest_rate',
        'max_interest_rate',
        'min_tenor',
        'max_tenor',
        'min_tenor_old_invoice',
        'max_tenor_old_invoice',
        'margin',
        'overdue_interest_rate',
        'base_rate_id',
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
     * get program doc 
     * 
     * @return type mixed
     */
    public function programDoc()
    {
        return $this->hasMany('App\Inv\Repositories\Models\ProgramDoc', 'prgm_id', 'prgm_id')
                        ->Join('mst_doc', 'prgm_doc.doc_id', 'mst_doc.id');
    }

    /**
     * get program charges
     * 
     * @return type mixed
     */
    public function programCharges()
    {
        return $this->hasMany('App\Inv\Repositories\Models\ProgramCharges', 'prgm_id', 'prgm_id');
    }

    /**
     * Get Program Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getProgramData($whereCondition = [])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $whereCondition['prgm.status'] = isset($whereCondition['status']) ? $whereCondition['status'] : 1;
        $whereCondition['prgm.prgm_id'] = isset($whereCondition['prgm_id']) ? $whereCondition['prgm_id'] : 1;
        
        unset($whereCondition['status']);
        unset($whereCondition['prgm_id']);
        
        $appNote = self::select('prgm.prgm_id', 'prgm.prgm_name', 'prgm.product_name',
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
                        END AS is_fldg_applicable"),
                        'prgm_chrg_process_fee.chrg_calculation_type as processing_fee_type',
                        'prgm_chrg_process_fee.chrg_calculation_amt as processing_fee_amt',
                        'prgm_chrg_doc_fee.chrg_calculation_type as document_fee_type',
                        'prgm_chrg_doc_fee.chrg_calculation_amt as document_fee_amt'              
                )
                ->leftJoin('mst_industry', 'prgm.industry_id', '=', 'mst_industry.id')
                ->leftJoin('mst_sub_industry', 'prgm.sub_industry_id', '=', 'mst_sub_industry.id')
                ->leftJoin('prgm_chrg as prgm_chrg_process_fee', function($join){
                    $join->on('prgm_chrg_process_fee.prgm_id', '=', 'prgm.prgm_id');
                    $join->on('prgm_chrg_process_fee.charge_id', '=', DB::raw('3'));   //Processing Fee
                })
                ->leftJoin('prgm_chrg as prgm_chrg_doc_fee', function($join){
                    $join->on('prgm_chrg_doc_fee.prgm_id', '=', 'prgm.prgm_id');
                    $join->on('prgm_chrg_doc_fee.charge_id', '=', DB::raw('4'));   //Document Fee
                })                
                ->where($whereCondition)
                ->orderBy('prgm.prgm_id', 'DESC')
                ->first();
        return $appNote;
    }

    /**
     * Save program
     * 
     * @param type $arrAnchor
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function saveProgram($attr = [])
    {
        //Check data is Array
        if (!is_array($attr)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($attr)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        /**
         * Create anchor
         */
        $arrAnchorVal = self::create($attr);

        return ($arrAnchorVal->prgm_id ?: false);
    }

    /**
     * get Program list by id 
     * 
     * @param type $id int
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getProgramListById($id)
    {
        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        $res = self::select('prgm.*', 'u.f_name','mp.product_name')
                ->join('users as u', 'prgm.anchor_id', '=', 'u.anchor_id')
                ->join('mst_product as mp', 'prgm.product_id', '=', 'mp.id')
                ->where(['u.user_type' => 2])
                ->where('prgm.parent_prgm_id', '0');
        if (!empty($id)) {
            $res = $res->where('prgm.anchor_id', $id);
        }

        return ($res ?: false);
    }

    /**
     * get program data
     * 
     * @param type $where 
     * @param string $selected
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getSelectedProgramData($where, $selected = null, $relations = [])
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        if (empty($selected)) {
            $selected = '*';
        }

        $res = self::select($selected);

        if (isset($where['prgm_id']) && !empty($where['prgm_id'])) {
            $res = $res->where('prgm_id', $where['prgm_id']);
        }
        if (isset($where['is_null_parent_prgm_id']) && !empty($where['is_null_parent_prgm_id'])) {
            $res = $res->where('parent_prgm_id', '!=', '0');
        }


        if (isset($where['parent_prgm_id']) && !empty($where['parent_prgm_id'])) {
            $res = $res->where('parent_prgm_id', $where['parent_prgm_id']);
        }

        if (!empty($relations)) {
            $res = $res->with($relations);
        }

        $res = $res->get();



        return ($res ?: false);
    }

    /**
     * get sub program data 
     * 
     * @param type $id
     * @return type
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getSubProgramListByParentId($anchor_id, $program_id)
    {
        if (empty($anchor_id)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        if (!is_int($anchor_id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        if (empty($program_id)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        if (!is_int($program_id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        $res = self::select('prgm.*', 'u.f_name')
                ->join('users as u', 'prgm.anchor_id', '=', 'u.anchor_id')
                ->where(['u.user_type' => 2, 'prgm.anchor_id' => $anchor_id])
                ->where('prgm.parent_prgm_id', $program_id);

        return ($res ?: false);
    }

    public static function getAnchorsByProduct($product_id)
    {
        if (empty($product_id)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        if (!is_int($product_id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        return Program::distinct('anchor_id')->with('anchors.prgmData')->where(['product_id' => $product_id, 'status' => 1])->where('parent_prgm_id','<>',0)->get(['anchor_id']);
    }

    public function anchors()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id', 'anchor_id')->where('is_active', 1);
    }

    public static function getProgramsByAnchor($anchor_id)
    {
        if (empty($anchor_id)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        if (!is_int($anchor_id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        return Program::where(['anchor_id' => $anchor_id, 'status' => 1])->where('parent_prgm_id','<>',0)->get(['prgm_id', 'product_id', 'anchor_id', 'prgm_name', 'anchor_sub_limit', 'min_loan_size', 'max_loan_size', 'min_interest_rate', 'max_interest_rate', 'interest_rate']);
    }

    public function product()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Product', 'product_id', 'id');
    }
    
    
   

    /**
     * Update program 
     * 
     * @param type $attributes
     * @param type $conditions
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function updateProgramData($attributes = [], $conditions = [])
    {
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

       
        /**
         * Check Data is Array
         */
        if (!is_array($conditions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($conditions)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::where($conditions)->update($attributes);

        return ($res ?: false);
    }

    public static function getPrgmsByAnchor($anchor_ids, $uesr_type){
        if (!is_array($anchor_ids)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        return Program::whereIn('anchor_id', $anchor_ids)->with(['programCharges.chargeName','baseRate'])->where('prgm_type', $uesr_type)->where('parent_prgm_id', '<>', 0)->get();
    }

    public function baseRate()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\BaseRate', 'base_rate_id', 'id');
    }

}
