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
        'bank_account_id',
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
        'total_repaid_amt',
        'total_interest',
        'margin',
        'disburse_amount',
        'status_id',
        'disbursal_api_log_id',
        'disburse_type',
        'settlement_date',
        'accured_interest',
        'interest_refund',
        'funded_date',
        'int_accrual_start_dt',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Get Interest Accrual 
     * 
     * @return type
     */
    public function interests() { 
        return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual', 'disbursal_id', 'disbursal_id'); 
    }

    /**
     * Get App Program Offer 
     * 
     * @return type
     */
    public function offer() { 
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramOffer', 'prgm_offer_id', 'prgm_offer_id'); 
    }

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
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
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
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (!empty($whereCondition)) {
            if (isset($whereCondition['int_accrual_start_dt'])) {
                $query->where('int_accrual_start_dt', '>=', $whereCondition['int_accrual_start_dt']);
                unset($whereCondition['int_accrual_start_dt']);
            } 

            if (isset($whereCondition['status_id'])) {
                $query->whereIn('status_id', $whereCondition['status_id']);
                unset($whereCondition['status_id']);
            }
                        
            $query->where($whereCondition);
        }
        $query->orderBy('disburse_date', 'ASC');
        $query->orderBy('disbursal_id', 'ASC');
        $result = $query->get();
        return $result;
    }
    
    /**
     * Get Program Offer Data
     * 
     * @param array $whereCondition
     * @return mixed
     */
    public static function getProgramOffer($whereCondition=[])
    {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        $result = self::select('app_prgm_offer.*')
                //->join('invoice', 'invoice.invoice_id', '=', 'disbursal.invoice_id')
                ////->join('app_prgm_limit', 'invoice.program_id', '=', 'app_prgm_limit.prgm_id')
                //->join('app_prgm_limit', function ($join) {
                //    $join->on('invoice.program_id', '=', 'app_prgm_limit.prgm_id');
                //    $join->on('invoice.app_id', '=', 'app_prgm_limit.app_id');
                //})
                //->join('app_prgm_offer', 'app_prgm_limit.app_prgm_limit_id', '=', 'app_prgm_offer.app_prgm_limit_id')
                ->join('app_prgm_offer', 'disbursal.prgm_offer_id', '=', 'app_prgm_offer.prgm_offer_id')
                ->where('disbursal_id', $whereCondition['disbursal_id'])
                ->where('app_prgm_offer.is_active', 1)
                ->where('app_prgm_offer.status', 1)
                ->first();
        
        return $result;
    }
    
    
    
    /**
     * get getDisbursal list
     * 
     * @return mixed
     */
    public static function getDisbursalList()
    {
        $res = self::select('disbursal.*','invoice.invoice_no' ,'invoice.invoice_approve_amount', 'mst_status.status_name')
                ->join('invoice','disbursal.invoice_id' ,'=','invoice.invoice_id')
                ->join('mst_status','disbursal.status_id' ,'=','mst_status.id');
        return $res?: false;
    }
    /////////////* get customer id   */////////////////
    public static function  getCustomerId()
    {
        return self::with('user')->where(['disburse_type' => 2])->groupBy('user_id')->get();
    }
     /////////////* get customer id   */////////////////
    public static function  getRepaymentAmount($uid)
    {
        return self::with('invoice')->where(['disburse_type' => 2,'user_id' => $uid])->get();
    }
    
    function invoice()
    {
              return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice','invoice_id','invoice_id')->orderBy('invoice_due_date','asc');
   
    }
    
    public function  user()
    {
          return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }
    public static function   updateRepayment($attr)
    {
         $res =   self::where(['invoice_id' => $attr['invoice_id']])->first();
         if($res)
         {
             $sumAmount   =  $res->disburse_amount - $attr['repaid_amount'];
             return self::where(['invoice_id' => $attr['invoice_id']])->update(['repayment_amount' =>  $sumAmount]);
         }
    }
    
    public  static function singleRepayment($disbursal_id,$sumAmount)
    {
      
        return self::where(['disbursal_id' => $disbursal_id])->update(['repayment_amount' =>  $sumAmount]);
    }
    
    
}
