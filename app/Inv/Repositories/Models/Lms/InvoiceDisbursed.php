<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Carbon\Carbon;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\Lms\InterestAccrual;

class InvoiceDisbursed extends BaseModel {
	/* The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'invoice_disbursed';

	/**
	 * Custom primary key is set for the table
	 *
	 * @var integer
	 */
	protected $primaryKey = 'invoice_disbursed_id';


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'disbursal_id',
		'invoice_id',
		'disbursal_api_log_id',
		'disburse_amt',
		'interest_rate',
		'tenor_days',
		'margin',
		'inv_due_date',
		'payment_due_date',
		'customer_id',
    	'total_interest',
		'processing_fee',
		'processing_fee_gst',
		'grace_period',
		'int_accrual_start_dt',
		'overdue_interest_rate',
		'is_adhoc',
		'status_id',
		'sys_created_at',
		'sys_updated_at',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];

	/**
	 * Save or Update
	 * 
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function saveUpdateInvoiceDisbursed($data, $whereCondition=[])
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
	 * Get disbursal 
	 * 
	 * @return type
	 */
	public function disbursal() { 
		return $this->belongsTo('App\Inv\Repositories\Models\Lms\Disbursal', 'disbursal_id', 'disbursal_id'); 
	}

	public function transactions(){
		return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','invoice_disbursed_id','invoice_disbursed_id');
	}

	public function interests() { 
		return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual', 'invoice_disbursed_id', 'invoice_disbursed_id'); 
  }
  
  public function interestTemp() { 
		return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrualTemp', 'invoice_disbursed_id', 'invoice_disbursed_id'); 
	}

	public function invoice(){
		return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice','invoice_id','invoice_id');
	}

	public function accruedInterest(){
		return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id')->orderBy('interest_date', 'DESC');
	}
	
	public function appProgramOffer(){
		return $this->belongsTo('App\Inv\Repositories\Models\AppProgramOffer');
	}

	public static function getInvoiceDisbursed($disbursalIds){
		return self::whereIn('disbursal_id', $disbursalIds)
				->with('invoice.program_offer', 'disbursal')->get();
	}

	public function accruedInterestNotNull(){
		return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id')->whereNotNull('overdue_interest_rate');
	}
	/**
	 * Get Disbursal Requests
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function getInvoiceDisbursalRequests($whereCondition=[])
	{
		if (!is_array($whereCondition)) {
			throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
		}
		
		$query = self::select('*')
			->with('invoice','disbursal','accruedInterestNotNull');
				
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
		$query->orderBy('created_at', 'ASC');
		$query->orderBy('invoice_disbursed_id', 'ASC');
		$result = $query->get();        
		return $result ? $result : [];
	}
	
	  function InterestAccrual()
	 {
	   return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id')->where('overdue_interest_rate','<>', null);
	 }
	 function transaction()
	 {
	   return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','invoice_disbursed_id','invoice_disbursed_id')->where(['trans_type' =>16,'entry_type' =>0]);
	 }
	  function isRepayment()
	{
		return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice','invoice_id','invoice_id')->where(['is_repayment' =>1]);  
	
	}
	 public static function getReportAllInvoice()
	 {
		   $getPay = [];
		   $currentDate =  Carbon::now()->format('Y-m-d');
		  /* $res =  self::get(); 
		  foreach($res as $row)
		  {
			$payDate   = date('Y-m-d', strtotime($row->payment_due_date. ' + '.$row->grace_period.' days'));    
			$curdate=strtotime($currentDate);
			$payDate=strtotime($payDate);
			if($payDate > $curdate)
			{
			  $getPay[] =   self::where(['invoice_disbursed_id' =>$row->invoice_disbursed_id,'status_id' => 12])->pluck('invoice_disbursed_id');
			}
		   
		  } 
		  */
		 return self::where(['status_id' => 12])->where('payment_due_date', '>=', $currentDate)/*->with(['invoice','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])*/->orderBy('invoice_id', 'DESC');
			
	  }  
	
	  public static function getReportAllOverdueInvoice()
	 {
		  $currentDate =  Carbon::now()->format('Y-m-d');
		  return self::where(['status_id' => 12])->where('payment_due_date', '<', $currentDate)->with(['InterestAccrual','invoice','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC');
	   
	 } 
	   public static function getInvoiceRealisationList()
	 {
		   $currentDate =  Carbon::now()->format('Y-m-d');
		   return self::whereHas('isRepayment')->where(['status_id' => 12])->where('payment_due_date', '>=', $currentDate)->with(['transaction.payment','Invoice.anchor.anchorAccount','InterestAccrual','invoice','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC');
			
	  }  
	 
	   public static function pdfInvoiceDue($attr)
	 {
		   $currentDate =  Carbon::now()->format('Y-m-d');
		   $user = LmsUser::where('customer_id',$attr->customer_id)->pluck('user_id');
			if($attr->from_date!=null && $attr->to_date!=null && count($user) > 0) 
		   {  
			   $from_date = Carbon::createFromFormat('d/m/Y', $attr->from_date)->format('Y-m-d');
			   $to_date = Carbon::createFromFormat('d/m/Y', $attr->to_date)->format('Y-m-d'); 
			   return self::where('customer_id',$attr->customer_id)->where('payment_due_date', '>=', $currentDate)->WhereBetween('payment_due_date', [$from_date, $to_date])->where(['status_id' => 12])->with(['Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		   
		   }
		   else if($attr->from_date!=null && $attr->to_date!=null && count($user)==0)
		   {
			   $from_date = Carbon::createFromFormat('d/m/Y', $attr->from_date)->format('Y-m-d');
			   $to_date = Carbon::createFromFormat('d/m/Y', $attr->to_date)->format('Y-m-d'); 
			   return self::WhereBetween('payment_due_date', [$from_date, $to_date])->where('payment_due_date', '>=', $currentDate)->where(['status_id' => 12])->with(['Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		 
		   }
		   else if($attr->from_date==null && $attr->to_date==null && count($user) > 0)
		   {
			  
			  return self::where('customer_id',$attr->customer_id)->where(['status_id' => 12])->where('payment_due_date', '>=', $currentDate)->with(['Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		 
		   }
		else {
		   
			 return self::where(['status_id' => 12])->where('payment_due_date', '>=', $currentDate)->with(['Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();

		}
	 } 
	 
	  public static function pdfInvoiceOverDue($attr)
	 {
		   $currentDate =  Carbon::now()->format('Y-m-d');
		   $user = LmsUser::where('customer_id',$attr->customer_id)->pluck('user_id');
			if($attr->from_date!=null && $attr->to_date!=null && count($user) > 0) 
		   {  
			   $from_date = Carbon::createFromFormat('d/m/Y', $attr->from_date)->format('Y-m-d');
			   $to_date = Carbon::createFromFormat('d/m/Y', $attr->to_date)->format('Y-m-d'); 
			   return self::where('customer_id',$attr->customer_id)->where('payment_due_date', '<', $currentDate)->WhereBetween('payment_due_date', [$from_date, $to_date])->where(['status_id' => 12])->with(['InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		   
		   }
		   else if($attr->from_date!=null && $attr->to_date!=null && count($user)==0)
		   {
			   $from_date = Carbon::createFromFormat('d/m/Y', $attr->from_date)->format('Y-m-d');
			   $to_date = Carbon::createFromFormat('d/m/Y', $attr->to_date)->format('Y-m-d'); 
			   return self::WhereBetween('payment_due_date', [$from_date, $to_date])->where('payment_due_date', '<', $currentDate)->where(['status_id' => 12])->with(['InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		 
		   }
		   else if($attr->from_date==null && $attr->to_date==null && count($user) > 0)
		   {
			  
			  return self::where('customer_id',$attr->customer_id)->where(['status_id' => 12])->where('payment_due_date', '<', $currentDate)->with(['InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		 
		   }
		else {
		   
			 return self::where(['status_id' => 12])->where('payment_due_date', '<', $currentDate)->with(['InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();

		}
	 } 
	  
	 public static function pdfInvoiceRealisation($attr)
	 {
		   $currentDate =  Carbon::now()->format('Y-m-d');
		   $user = LmsUser::where('customer_id',$attr->customer_id)->pluck('user_id');
			if($attr->from_date!=null && $attr->to_date!=null && count($user) > 0) 
		   {  
			   $from_date = Carbon::createFromFormat('d/m/Y', $attr->from_date)->format('Y-m-d');
			   $to_date = Carbon::createFromFormat('d/m/Y', $attr->to_date)->format('Y-m-d'); 
			   return self::whereHas('isRepayment')->where('customer_id',$attr->customer_id)->where('payment_due_date', '>=', $currentDate)->WhereBetween('payment_due_date', [$from_date, $to_date])->where(['status_id' => 12])->with(['transaction.payment','Invoice.anchor.anchorAccount','InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		   
		   }
		   else if($attr->from_date!=null && $attr->to_date!=null && count($user)==0)
		   {
			   $from_date = Carbon::createFromFormat('d/m/Y', $attr->from_date)->format('Y-m-d');
			   $to_date = Carbon::createFromFormat('d/m/Y', $attr->to_date)->format('Y-m-d'); 
			   return self::whereHas('isRepayment')->WhereBetween('payment_due_date', [$from_date, $to_date])->where('payment_due_date', '>=', $currentDate)->where(['status_id' => 12])->with(['transaction.payment','Invoice.anchor.anchorAccount','InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		 
		   }
		   else if($attr->from_date==null && $attr->to_date==null && count($user) > 0)
		   {
			  
			  return self::whereHas('isRepayment')->where('customer_id',$attr->customer_id)->where(['status_id' => 12])->where('payment_due_date', '>=', $currentDate)->with(['transaction.payment','Invoice.anchor.anchorAccount','InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();
		 
		   }
		else {
		   
			 return self::whereHas('isRepayment')->where(['status_id' => 12])->where('payment_due_date', '>=', $currentDate)->with(['transaction.payment','Invoice.anchor.anchorAccount','InterestAccrual','Invoice.business','Invoice.anchor','Invoice.supplier','Invoice.userFile','Invoice.program','Invoice.program_offer','Invoice.Invoiceuser','disbursal.disbursal_batch','Invoice.lms_user'])->orderBy('invoice_id', 'DESC')->get();

		}
	 }

	 public static function getDisbursedAmountForSupplier($supplier_id, $prgm_offer_id, $is_adhoc = false){
	 	return self::whereHas('invoice', function ($q) use ($supplier_id, $prgm_offer_id){
	 		$q->where(['supplier_id' => $supplier_id, 'prgm_offer_id' => $prgm_offer_id, 'is_adhoc' => 0])->whereIn('status_id', [12,13,15]);
	 	})->sum('disburse_amt');
	 }  
}
