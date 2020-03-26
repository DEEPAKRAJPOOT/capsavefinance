<?php

namespace App\Inv\Repositories\Entities\Lms;

use App\Http\Requests\Request;
use Carbon\Carbon;
use DB;
use Session;
use App\Inv\Repositories\Contracts\LmsInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\ProgramCharges;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Charges;
use App\Inv\Repositories\Models\Lms\DisburseApiLog;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\ChargesTransactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Master\GstTax;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Lms\Batch;
use App\Inv\Repositories\Models\Lms\BatchLog;
use App\Inv\Repositories\Models\Lms\DisbursalBatch;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Models\Lms\ApprovalRequest;
use App\Inv\Repositories\Models\Lms\ApprovalRequestLog;
use App\Inv\Repositories\Models\Lms\RequestAssign;
use App\Inv\Repositories\Models\Lms\WfStage;
use App\Inv\Repositories\Models\Lms\RequestWfStage;
use App\Inv\Repositories\Models\Lms\Variables;
use App\Inv\Repositories\Models\Lms\Refund;
use App\Inv\Repositories\Models\Master\RoleUser;

/**
 * Lms Repository class
 */
class LmsRepository extends BaseRepositories implements LmsInterface {

	use CommonRepositoryTraits;

	/**
	 * Class constructor
	 *
	 * @return void
	 */    
	public function __construct() {
	}

	/**
	 * Create method
	 *
	 * @param array $attributes
	 */
	protected function create(array $attributes) {        
	}

	/**
	 * Update method
	 *
	 * @param array $attributes
	 */
	protected function update(array $attributes, $id) {        
	}

	/**
	 * Get all records method
	 *
	 * @param array $columns
	 */
	public function all($columns = array('*')) {        
	}

	/**
	 * Find method
	 *
	 * @param mixed $id
	 * @param array $columns     
	 */
	public function find($id, $columns = array('*')) {        
	}

	/**
	 * Save or Update Disbursal Request
	 * 
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function saveDisbursalRequest($data, $whereCondition=[])
	{
		return Disbursal::saveDisbursalRequest($data, $whereCondition);
	}
	
	/**
	 * Save Transactions
	 * 
	 * @param array $transactions
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function saveTransaction($transactions,$whereCondition=[])
	{
		return Transactions::saveTransaction($transactions,$whereCondition);
	}

	/**
	 * Save or Update Invoice Repayment
	 * 
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function saveRepayment($data, $whereCondition=[])
	{
		return InvoiceRepaymentTrail::saveRepayment($data, $whereCondition);
	}

	/**
	 * Save or Update Interest Accrual
	 * 
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function saveInterestAccrual($data, $whereCondition=[])
	{
		return InterestAccrual::saveInterestAccrual($data, $whereCondition);
	}
	
	/**
	 * Get Disbursal Requests
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function getDisbursalRequests($whereCondition=[])
	{
		return Disbursal::getDisbursalRequests($whereCondition);
	}
	
	/**
	 * Get Transactions
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function getTransactions($whereCondition=[])
	{
		return Transactions::getTransactions($whereCondition);
	}
	
	/**
	 * Get Repayments
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function getRepayments($whereCondition=[])
	{
		return InvoiceRepaymentTrail::getRepayments($whereCondition);
	}   
	

	public function getAllUserInvoice($userId)
	{
		return BizInvoice::getAllUserInvoice($userId);
	}
	
	public static function getAllUserInvoiceIds($userId)
	{
		return BizInvoice::getAllUserInvoiceIds($userId);
	}

	/**
	 * Get Accrued Interest Data
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */    
	public function getAccruedInterestData($whereCondition=[])
	{
		return InterestAccrual::getAccruedInterestData($whereCondition);
	}
	
	/**
	 * Get Program Offer Data
	 * 
	 * @param array $whereCondition
	 * @return mixed
	 */
	public function getProgramOffer($whereCondition=[])
	{
		return Disbursal::getProgramOffer($whereCondition);
	}

	
	public function getInvoices($invoiceIds)
	{
		return BizInvoice::whereIn('invoice_id', $invoiceIds)
			   ->with(['program_offer','lms_user' , 'supplier.anchor_bank_details.bank', 'supplier_bank_detail.bank'])
			   ->get();
	}  

	/**
	 * Get Repayments
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function getInvoiceSupplier($invoiceIds)
	{
		return BizInvoice::groupBy('supplier_id')
				->whereIn('invoice_id', $invoiceIds)
				->pluck('supplier_id');
	} 
	/**
	 * Get Repayments
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function lmsGetInvoiceClubCustomer($userIds, $invoiceIds)
	{
	  
		return $data =  LmsUser::with(['bank_details.bank', 'app.invoices.program_offer', 'user.anchor_bank_details.bank'])
				->with(['app.invoices' => function ($query) use($invoiceIds) {
					  if (!empty($invoiceIds)) { 
						  $query->whereIn('invoice_id', $invoiceIds);
					  }
				  }])
				->whereHas('app.invoices', function($query) use ($userIds) {
					if (!empty($userIds)) {
						$query->whereIn('supplier_id', $userIds);
					}
				})
				->get();
		// dd($data);
	}    

	/**
	 * Get Repayments
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function updateInvoiceStatus($invoiceId, $status)
	{
		return BizInvoice::where('invoice_id', $invoiceId)
				->update(['status_id' => $status]);
	}    

	/**
	 * Get Sum of Accrued Interest
	 *      
	 * @param array $whereCond
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function sumAccruedInterest($whereCond) 
	{        
		return InterestAccrual::sumAccruedInterest($whereCond);
	}    

	 /**
	 * Get Count of Accrued Interest
	 *      
	 * @param array $whereCond
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function countAccruedInterest($whereCond) 
	{        
		return InterestAccrual::countAccruedInterest($whereCond);
	}  


	/**
	 * create Disburse Api Log
	 */
	public static function createDisburseApi($data)
	{
		return DisburseApiLog::create($data);
	}

	 /**
	 * Get Repayments
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function updateDisburse($data, $disbursalIds)
	{
		if (!is_array($disbursalIds)) {
			return Disbursal::where('disbursal_id', $disbursalIds)
				->update($data);
		} else {
			return Disbursal::whereIn('disbursal_id', $disbursalIds)
					->update($data);
		}
	}
	 /**
	 * Get Repayments
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function updateDisburseByUserAndBatch($data, $updatingIds = [])
	{
		if (is_array($updatingIds)) {
			$response =  Disbursal::where($updatingIds)
				->update($data);
		}
		return ($response) ?? $response;
	}          
	 
	 /**
	 *      
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function getVirtualAccIdByUserId($userId)
	{
		return LmsUser::where('user_id', $userId)
				->pluck('virtual_acc_id')->first();
	} 
	
	/****
	 * get trans  type
	 */
	  public static function getTrnasType($whr)
	{
		try{
			return Charges::getTransData($whr);
		} catch (Exception $ex) {
		   return $ex;
		}
		
			   
	}  
	/****
	 * get trans  type
	 */
	  public static function getProgramUser($userId)
	{
	   try
	   {
		  return User::getProgramUser($userId); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}  
	  /****
	 * get address
	 */
	  public static function getUserAddress($app_id)
	{
	   try
	   {
		  return Application::getUserAddress($app_id); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}
	
	 /****
	 * get address
	 */
	  public static function companyAdress()
	{
	   try
	   {
		  return Application::companyAdress(); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	} 
	public static function getUserDetails($uid)
	{
	   try
	   {
		  return User::getUserDetails($uid); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}    
	  public static function getSingleChargeAmount($attr)
	{
	   try
	   {
		  return ProgramCharges::getSingleChargeAmount($attr); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}   
	
	  public static function saveCharge($attr)
	{
	   try
	   {
		  return Transactions::saveCharge($attr); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}   
	
	public static function getAllTransCharges($user_id)
	{
		try
	   {
		  return ChargesTransactions::getAllTransCharges($user_id); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
		
	}
	  public static function saveChargeTrans($attr)
	{
	   try
	   {
		  return ChargesTransactions::saveChargeTrans($attr); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}    
	 
	  public static function getAllUserChargeTransaction()
	{
	   try
	   {
		  return Transactions::getAllUserChargeTransaction(); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}    
	  public static function getTransName($attr)
	{
	   try
	   {
		  return ProgramCharges::getTransName($attr); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}    
	/**
	 * Get program offer limit amount  //
	 *      
	 * @param array $whereCondition
	 * @param array $data
	 * 
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	  public static function getLimitAmount($attr)
	{
	   try
	   {
		  return AppProgramOffer::getLimitAmount($attr); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}    
	  public static function getOutstandingAmount($attr)
	{
	   try
	   {
		  return Disbursal::getOutstandingAmount($attr); 
	   } catch (Exception $ex) {
		  return $ex;
	   }
	   
			   
	}     
	
	
	/**
	 * Update Transactions
	 *      
	 * @param array $whereCondition
	 * @param array $data
	 * 
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function updateTransaction($whereCondition, $data)
	{
		return Transactions::updateTransaction($whereCondition, $data);
	}
	
	/** 
	 * @Author: Rent Aplha
	 * @Date: 2020-02-17 14:53:10 
	 * @Desc:  
	 */    
	public static function getManualTranType(){        
	 $result=TransType::getManualTranType();
	 return  $result? $result:false;
	}
	  /** 
	 * @Author: Rent Aplha
	 * @Date: 2020-02-17 14:53:10 
	 * @Desc:  
	 */    
	public static function getActiveGST(){        
		$result=GstTax::getActiveGST();
		return  $result? $result:false;
	   }

	   /** 
		* @Author: Rent Alpha
		* @Date: 2020-02-18 13:04:19 
		* @Desc:  
		*/       
	   public function getAllLmsUser(){
		$result=LmsUser::getLmsUser();
		return  $result? $result:false;
	   }

   public function getAllRefundLmsUser()
   {
	  return LmsUser::with('user')->groupBy('user_id')
			   ->whereHas('transaction', function ($query) {
				  $query->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_REFUND'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')]);
			   });
   }

   public function getDisbursals($disburseIds)
   {
	  return Disbursal::whereIn('disbursal_id', $disburseIds)
			->get();
   }

   public function getCreateBatchData($request)
   {
	  $transType = $request->trans_type;
	  return Transactions::where('trans_type',$transType);
   }

   public function getEditBatchData($request)
   {
	  $transType = $request->trans_type;
	  $batch_id = $request->batch_id;

	  return BatchLog::whereHas('batch', function ($query) use ($request) {
		 $query->where('batch_id','=',$request->batch_id)->whereIn('status',[1,4]);
	  })->whereHas('transactions', function ($query) use($request){
		 $query->where('trans_type','=',$request->trans_type);
	  })->whereIn('status',[1,4])->get();
	 
   }

   public function getRequestList($request)
   {
	  return ApprovalRequest::getAllApprRequests();
   }

   public function createBatch()
   {
	  return Batch::createBatch(1);
   }

    public static function getUserInvoiceIds($userId)
    {
        return BizInvoice::getUserInvoiceIds($userId);
    }
    
    public function getSoaList()
    {
        return Transactions::getSoaList();
    }
    
    public function getRepaymentAmount($userId, $transType)
    {
        return Transactions::getRepaymentAmount($userId, $transType);
    }
    
      public function searchBusiness($search)
    {
        try
       {
            return Business::searchBusiness($search);
       } catch (Exception $ex) {
            return $ex;
       }
       
    }

    /**
     * create Disburse Api Log
     */
    public static function saveBatchFile($data)
    {
        return UserFile::create($data);
    }

    /**
     * create Disburse Api Log
     */
    public static function createDisbursalBatch($file, $batchId = null)
    {   
        if (!empty($batchId)) {
            $disburseBatch['batch_id'] = ($batchId) ?? $batchId;
            $disburseBatch['file_id'] = ($file) ? $file->file_id : '';
        }
        return DisbursalBatch::create($disburseBatch);
    }
    
    public function getRefundData($transId, $variableName=null)
    {
        return Refund::getRefundData($transId, $variableName);
    }
    
    /**
     * Get Wf stage Details 
     *
    */
    public function getWfStages($reqType)
    {
        return WfStage::getWfStages($reqType);
    }
    
    /**
     * Get workflow detail by wf stage code
     * 
     * @param string $req_type 
     * @param string $wf_stage_code
     * @return mixed
     * @throws BlankDataExceptions
     */
    public function getWfDetailById($wf_stage_code)
    {
        return WfStage::getWfDetailById($wf_stage_code);
    }
  
    /**
     * Get next workflow by $wf_order_no
     *
     * @param string $req_type 
     * @param string $wf_order_no
     * 
     * @return mixed
     * @throws BlankDataExceptions
     */
    public function getNextWfStage($req_type, $wf_order_no)
    {
        return WfStage::getNextWfStage($req_type, $wf_order_no);
    }        
        
    /**
     * Get Workflow Detail By Order No
     *
     * @param string $req_type 
     * @param integer $wf_order_no
     *
     * @return mixed
     */
    public function getWfDetailByOrderNo($req_type, $wf_order_no)
    {
        return WfStage::getWfDetailByOrderNo($req_type, $wf_order_no);
    }
    
    /**
     * Get Wf stage Details 
     *
    */
    public function updateWfStage($wf_stage_id, $req_id, $arrData = [])
    {
        return RequestWfStage::updateWfStage($wf_stage_id, $req_id, $arrData);
    }
    
            
    /**
     * Save application workflow stage
     * 
     * @param array $arrData
     * @return mixed
     * @throws BlankDataExceptions
     */
    public function saveWfDetail($arrData)
    {
        return RequestWfStage::saveWfDetail($arrData);
    }
    
    /**
     * Get Current WfStage by req id
     * 
     * @param integer $req_id
     * @return mixed
     */    
    public function getCurrentWfStage($req_id) 
    {
        return RequestWfStage::getCurrentWfStage($req_id);
    }

    /**
     * Get request workflow stage by $wf_stage_code and $req_id
     * 
     * @param string $wf_stage_code
     * @param integer $req_id
     * 
     * @return mixed
     */
    public function getRequestWfStage($wf_stage_code, $req_id) 
    {
        return RequestWfStage::getRequestWfStage($wf_stage_code, $req_id);
    }
    
    public function updateRequestAssignById($req_id, $data)
    {
        return RequestAssign::updateRequestAssignById((int) $req_id, $data);
    }
    
    public function assignRequest($data)
    {
        return RequestAssign::assignRequest($data);
    }
    
    /**
     * Get Backend Users By Role Id
     * 
     * @param integer $role_id
     * @return array
     */
    public function getBackendUsersByRoleId($role_id, $usersNotIn=[])
    {
        return RoleUser::getBackendUsersByRoleId($role_id, $usersNotIn);
    }
    
    public function saveApprRequestData($reqData=[], $reqId=null)
    {
        return ApprovalRequest::saveApprRequestData($reqData, $reqId);
    }

    /**
     * Save Approval Request Log Data
     * 
     * @param array $reqLogData
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveApprRequestLogData($reqLogData)
    {
        return ApprovalRequestLog::saveApprRequestLogData($reqLogData);
    }
    
    public function getApprRequestData($reqId)
    {
        return ApprovalRequest::getApprRequestData($reqId);
    }
    
    /**
     * Update Approval Request Log Data
     * 
     * @param array $whereCond
     * @param array $reqLogData
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function updateApprRequestLogData($whereCond, $reqLogData)
    {
        return ApprovalRequestLog::updateApprRequestLogData($whereCond, $reqLogData);
    }
    
    /**
     * Get Approval Request Log Data
     * 
     * @param array $whereCond
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getApprRequestLogData($whereCond)
    {
        return ApprovalRequestLog::getApprRequestLogData($whereCond);
    } 
    
    /**
     * Get previous workflow by $wf_order_no
     *
     * @param string $req_type 
     * @param string $wf_order_no
     * 
     * @return mixed
     * @throws BlankDataExceptions
     */
    public function getPrevWfStage($req_type, $wf_order_no)
    {
        return WfStage::getPrevWfStage($req_type, $wf_order_no);
    }
    
    public function getAssignedReqData($reqId)
    {
        return RequestAssign::getAssignedReqData($reqId);
    }    
    
    public function isRequestOwner($reqId, $assignedUserId)
    {
        return RequestAssign::isRequestOwner($reqId, $assignedUserId);
    }

    public function saveRefundData($refundData)
    {
        return Refund::saveRefundData($refundData);
    }
    
    public function getVariables()
    {
        return Variables::getVariables();
    }
    
    /**
     * Get Request current assignee
     * 
     * @param integer $reqId
     * @return mixed
     */
    public function getReqCurrentAssignee($reqId)
    {
        return RequestAssign::getReqCurrentAssignee($reqId);
    }

	public function findDisbursalByUserAndBatchIds($data)
	{
		return Disbursal::where($data)
				->pluck('invoice_id');
	}

	public function updateInvoicesStatus($invoiceIds, $status)
	{
		$response =  BizInvoice::whereIn('invoice_id', $invoiceIds)
				->update(['status_id' => $status]);
		return $response;
	}

	public function getAllUserBatchInvoice($data)
	{
		return BizInvoice::getAllUserBatchInvoice($data);
	} 
        
    /**
     * Check Charge Name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public function checkChargeName($chargeName, $excludeChargeId=null)
    {
        return Charges::checkChargeName($chargeName, $excludeChargeId);
    }

    public function getallBatch()
	{
		return DisbursalBatch::get();
	}        
    
}
