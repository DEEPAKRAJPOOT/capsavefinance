<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
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
		'disbursal_batch_id',
		'bank_account_id',
		'disburse_date',
		'bank_name',
		'ifsc_code',
		'acc_no',
		'virtual_acc_id',
		'customer_id',
		'principal_amount',
		'inv_due_date',
		'payment_due_date',
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
		'surplus_amount',
		'accured_interest',
		'interest_refund',
		'funded_date',
		'int_accrual_start_dt',
		'processing_fee',
		'grace_period',
		'overdue_interest_rate',
		'repayment_amount',
		'total_repaid_amount',
		'penalty_amount',
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
        return $result ? $result : [];
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
                ->join('mst_status','disbursal.status_id' ,'=','mst_status.id')->orderBy('disbursal.disbursal_id', 'DESC');
        return $res;
    }
    /////////////* get customer id   */////////////////
    public static function  getCustomerId($uid)
    {
        return User::where(['user_id' => $uid])->first();
    }
    
      /////////////* get customer id   */////////////////
    public static function  getDisburseCustomerId()
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

	public function  disbursal_batch()
	{
		  return $this->belongsTo('App\Inv\Repositories\Models\Lms\DisbursalBatch','disbursal_batch_id','disbursal_batch_id');
	}

	public function  lms_user()
	{
		  return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
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
	
	public static function getOutstandingAmount($attr)
	{
	  
		$user_id  = $attr->user_id;
		$pAmount  =   self::where('user_id',$user_id)->sum('principal_amount');
		$tAmount  =   self::where('user_id',$user_id)->sum('total_interest');
		$tRAmount =   self::where('user_id',$user_id)->sum('total_repaid_amt');
		if($attr->chrg_applicable_id==2)
		{
			  return   $pAmount+$tAmount-$tRAmount;
		}
		else if($attr->chrg_applicable_id==3)
		{
			  return  $pAmount-$tRAmount;
		}
	}
	
	public function bank_details()
	{
		return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'user_id')->where(['is_active' => 1, 'is_default' => 1]);
	}

	public static function lmsGetRefundList()
	{
		return self::with([])
				->where('surplus_amount', '!=', [0])
				->whereNotNull('surplus_amount');
	}
	
	// public static function getAllBankInvoice(){
    //     $result = \DB::select("SELECT batch_id, COUNT(DISTINCT(user_id)) as total_users, SUM(disburse_amount) as total_amt FROM rta_disbursal
	// 	WHERE batch_id IS NOT null GROUP BY batch_id ORDER BY batch_id DESC");
    //     return $result;    
	// }
	
	public static function getAllBankInvoiceCustomers($batch_id){
        $result = \DB::select("SELECT DISTINCT(rta_disbursal.user_id),rta_disbursal.app_id,customer_id,bank_name, acc_no, ifsc_code,  COUNT(invoice_id) as total_invoice, SUM(disburse_amount) as total_amt, concat(rta_users.f_name, ' ', rta_users.l_name) AS ben_name, rta_biz.biz_entity_name
		FROM rta_disbursal 
		JOIN rta_users ON (rta_users.user_id=rta_disbursal.user_id)
        JOIN rta_app ON (rta_app.app_id=rta_disbursal.app_id)
        JOIN rta_biz ON (rta_biz.biz_id=rta_app.biz_id)
		WHERE disbursal_batch_id = ? AND disbursal_batch_id IS NOT null 
		GROUP BY rta_disbursal.user_id, rta_disbursal.app_id, customer_id, bank_name, acc_no, ifsc_code",[$batch_id]);
        return $result;    
	}
	
	public static function getAllDisburseInvoice($batch_id, $disbursed_user_id){
        $result = \DB::select("SELECT rta_disbursal.app_id,rta_disbursal.invoice_id,DATE_FORMAT(disburse_date,'%Y-%m-%d') as disburse_date,DATE_FORMAT(inv_due_date,'%Y-%m-%d') as inv_due_date,disburse_amount,disburse_type,rta_invoice.invoice_no
		FROM rta_disbursal
        JOIN rta_invoice ON (rta_invoice.invoice_id=rta_disbursal.invoice_id)
		WHERE rta_disbursal.disbursal_batch_id IS NOT null AND rta_disbursal.disbursal_batch_id = ? AND rta_disbursal.user_id=?",[$batch_id, $disbursed_user_id]);
        return $result;    
    }
}
