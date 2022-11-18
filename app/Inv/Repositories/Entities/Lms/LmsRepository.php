<?php

namespace App\Inv\Repositories\Entities\Lms;

use DB;
use Auth;
use Session;
use Carbon\Carbon;
use BlankDataExceptions;
use App\Http\Requests\Request;
use InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Master\Company;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Models\Lms\Batch;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Lms\Refund;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Lms\Charges;
use App\Inv\Repositories\Models\Lms\CronLog;
use App\Inv\Repositories\Models\Lms\WfStage;
use App\Inv\Repositories\Models\LmsUsersLog;
use App\Inv\Repositories\Models\Lms\BatchLog;
use App\Inv\Repositories\Models\ColenderShare;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\Lms\Variables;
use App\Inv\Repositories\Models\Master\GstTax;
use App\Inv\Repositories\Models\Lms\EodProcess;
use App\Inv\Repositories\Models\ProgramCharges;
use App\Inv\Repositories\Contracts\LmsInterface;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\Lms\RefundBatch;
use App\Inv\Repositories\Models\Master\RoleUser;
use App\Inv\Repositories\Models\Lms\CibilReports;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\ChargeGST;
use App\Inv\Repositories\Models\Lms\CibilUserData;
use App\Inv\Repositories\Models\Lms\EodProcessLog;
use App\Inv\Repositories\Models\Lms\RequestAssign;
use App\Inv\Repositories\Models\Master\TallyEntry;
use App\Inv\Repositories\Models\AppOfferAdhocLimit;
use App\Inv\Repositories\Models\Lms\DisbursalBatch;
use App\Inv\Repositories\Models\Lms\DisburseApiLog;
use App\Inv\Repositories\Models\Lms\RequestWfStage;
use App\Inv\Repositories\Models\Lms\ApprovalRequest;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\WriteOffRequest;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;
use App\Inv\Repositories\Models\Lms\WriteOffStatusLog;
use App\Inv\Repositories\Models\Lms\ApprovalRequestLog;
use App\Inv\Repositories\Models\Lms\DisbursalStatusLog;
use App\Inv\Repositories\Models\Lms\ChargesTransactions;
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;
use App\Inv\Repositories\Models\Lms\TransactionComments;
use App\Inv\Repositories\Models\Lms\TransactionsRunning;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqBatch;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\Lms\DisbursalApiLog;
use App\Inv\Repositories\Models\UserNach;
use App\Inv\Repositories\Models\Lms\NachRepaymentReq;
use App\Inv\Repositories\Models\Lms\NachRepaymentReqBatch;
use App\Inv\Repositories\Models\Lms\NachTransReq;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use App\Inv\Repositories\Models\Lms\CustomerTransactionSOA;
use App\Inv\Repositories\Models\InvoiceStatusLog;
use App\Inv\Repositories\Models\Lms\ChargeTransactionDeleteLog;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\OfferAdhocDocument;

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
	 * Save or Update Disbursal Request
	 *
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function saveUpdateInvoiceDisbursed($data, $whereCondition=[])
	{
		return InvoiceDisbursed::saveUpdateInvoiceDisbursed($data, $whereCondition);
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
	 * Save Running Transactions
	 *
	 * @param array $transactions
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function saveTransactionRunning($transactions,$whereCondition=[])
	{
		return TransactionsRunning::saveTransactionRunning($transactions,$whereCondition);
	}

	/**
	 * Save TransactionsComments
	 *
	 * @param array $transactions
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function saveTxnComment($comments,$whereCondition=[])
	{
		return TransactionComments::saveTxnComments($comments,$whereCondition);
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
	 * Save or Update Interest Accrual Temp
	 *
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function saveInterestAccrualTemp($data, $whereCondition=[])
	{
		return InterestAccrualTemp::saveInterestAccrualTemp($data, $whereCondition);
	}

	/**
	 * Get Disbursal Requests
	 *
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function getInvoiceDisbursalRequests($whereCondition=[])
	{
		return InvoiceDisbursed::getInvoiceDisbursalRequests($whereCondition);
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
			   ->with(['program_offer','lms_user' , 'supplier.anchor_bank_details.bank', 'supplier_bank_detail.bank', 'program', 'processing_fee'])
			   ->get();
	}

	public function getUserBankDetail($userId)
	{
		return User::where('user_id', $userId)
			   ->with(['lms_user' , 'anchor_bank_details.bank', 'supplier_bank_detail.bank'])
			   ->first();
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

		return $data =  LmsUser::with(['bank_details.bank', 'app.invoices.program_offer', 'user.anchor_bank_details.bank', 'app.invoices.program', 'app.invoices.processing_fee'])
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
				->groupBy('user_id')
				->get();
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
	 * update disbursaal
	 *
	 * @param array $whereCondition | required
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function updateDisburseByUserAndBatch($data, $updatingIds = [])
	{
		if (is_array($updatingIds)) {
			$response =  Disbursal::whereIn('disbursal_id', $updatingIds)
				->update($data);
		}
		return ($response) ?? $response;
	}
	/**
	 * Create disbursaal status log
	 *
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function createDisbursalStatusLog($disbursalId, $statusId = null, $remarks = '', $createdBy)
	{
		$curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

		return DisbursalStatusLog::create([
                    'disbursal_id' => $disbursalId,
                    'status_id' => $statusId,
                    'disbursal_comm_txt' => $remarks,
                    'created_by' => $createdBy,
                    'created_at' => $curData,
                ]);
	}
	 /**
	 * Get Repayments
	 *
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function updateInvoiceDisbursed($data, $invoiceDisbursalIds)
	{
		if (!is_array($invoiceDisbursalIds)) {
			return InvoiceDisbursed::where('invoice_disbursed_id', $invoiceDisbursalIds)
				->update($data);
		} else {
			return InvoiceDisbursed::whereIn('invoice_disbursed_id', $invoiceDisbursalIds)
					->update($data);
		}
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
		  return Application::getSentionUserDetails($uid);
	   } catch (Exception $ex) {
		  return $ex;
	   }


	}
	  public static function getSingleChargeAmount($attr)
	{
	   try
	   {
	   		if (!empty($attr['prog_id'])) {
		  		return ProgramCharges::getSingleChargeAmount($attr);
	   		} else {
   				return Charges::getSingleChargeAmount($attr);
	   		}
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

	public static function getAllTransCharges()
	{
		try
	   {
		  return ChargesTransactions::getAllTransCharges();
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
	   		if (isset($attr->prog_id)) {
		  		return ProgramCharges::getTransName($attr);
		  	} else {
		  		return Charges::getTransName($attr);

		  	}
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
		  return Transactions::getUserLimitOutstanding($attr);
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
	  return Disbursal::with(['lms_user','lms_user.user', 'lms_user.bank_details', 'lms_user.user.anchor_bank_details', 'lms_user.userCreatedBy'])->whereIn('disbursal_id', $disburseIds)->with('user.anchor', 'user.anchor.salesUser','user.biz')
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
		return RefundReq::where('status','=',$request->status);
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
        // return Transactions::getSoaList();
		return CustomerTransactionSOA::getSoaList();
    }

    public function getColenderList() {
        return Transactions::getColenderList();
	}
    public function getColenderSoaList() {
        return Transactions::getColenderSoaList();
	}
    public function getColenderSoaListByLender($lenderId) {
        return Transactions::getColenderSoaListByLender($lenderId);
    }

	public function getConsolidatedSoaList() {
        return CustomerTransactionSOA::getConsolidatedSoaList();
    }

    public function getRepaymentAmount($userId, $transType)
    {
        return Transactions::getUserBalance($userId);
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
    public static function createDisbursalBatch($file, $batchId = null, $disbursalApiLogId = null)
    {
        if (!empty($batchId)) {
            $disburseBatch['batch_id'] = $batchId ?? null;
            $disburseBatch['file_id'] = ($file) ? $file->file_id : '';
            $disburseBatch['disbursal_api_log_id'] = $disbursalApiLogId ?? null;
            $disburseBatch['batch_status'] = 1;
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

	public function findInvoicesByUserAndBatchId($data)
	{
		return InvoiceDisbursed::whereHas('disbursal', function ($query) use($data){
				 	$query->where($data);
			  	})
				->pluck('invoice_id');
	}

	public function findDisbursalByUserAndBatchId($data)
	{
		return Disbursal::where($data)->where('status_id', 10)
				->pluck('disbursal_id');
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

	public function findInvoiceDisbursedByInvoiceId($invoiceId)
	{
		return InvoiceDisbursed::where('invoice_id', $invoiceId)
				->get();
	}

    /**
     * Get charge Data
     *
     * @param array $where
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function getChargeData($where)
    {
        return ProgramCharges::getChargeData($where);
    }

    public function lmsGetCustomerRefund($ids)
    {
        return RefundReq::whereIn('refund_req_id', $ids)
			   ->get();
    }

    public function getAprvlRqDataByIds($ids = [])
    {
    	if (empty($ids)) {
	        return RefundReq::with(['payment.user.anchor_bank_details.bank', 'payment.lmsUser.bank_details.bank', 'batch.disbursal_api_log'])
	        	->where('status', 8)
			   	->get();
    	} else {
    		return RefundReq::whereIn('refund_req_id', $ids)
			   	->with(['payment.user.anchor_bank_details.bank', 'payment.lmsUser.bank_details.bank', 'batch.disbursal_api_log'])
			   	->get();
    	}
    }

	public static function updateAprvlRqst($data, $reqId)
	{
		if (!is_array($reqId)) {
			return RefundReq::where('refund_req_id', $reqId)
				->update($data);
		} else {
			return RefundReq::whereIn('refund_req_id', $reqId)
					->update($data);
		}
	}

	public static function createRefundBatch($file, $data = [])
    {
    	$disburseBatch = [];
        if ($data) {
            $disburseBatch['batch_no'] = ($data['batch_no']) ?? null;
            $disburseBatch['file_id'] = ($file) ? $file->file_id : '';
            $disburseBatch['batch_status'] = config('lms.BATCH_STATUS')['SENT_TO_BANK'];
            $disburseBatch['refund_type'] = $data['refund_type'] ?? 0;
            $disburseBatch['disbursal_api_log_id'] = $data['disbursal_api_log_id'] ?? null;
        }
        return RefundReqBatch::create($disburseBatch);
    }

    /**
     * Save Eod Process Data
     *
     * @param array $data
     * @param integer $eodProcessId
     * @return mixed
     */
    public function saveEodProcess($data, $eodProcessId=null)
    {
        return EodProcess::saveEodProcess($data, $eodProcessId);
    }

    /**
     * Get Eod Process Data
     *
     * @param array $whereCond
     * @return mixed
     */
    public function getEodProcess($whereCond=[])
    {
        return EodProcess::getEodProcess($whereCond);
    }

    public function saveEodProcessLog($data, $eodProcessId=null)
    {
        return EodProcessLog::saveEodProcessLog($data, $eodProcessId);
    }

    public function getEodProcessLog($whereCond=[])
    {
        return EodProcessLog::getEodProcessLog($whereCond);
    }

    public function updateEodProcess($data, $whereCond)
    {
        return EodProcess::updateEodProcess($data, $whereCond);
    }

	public static function getRunningTrans($userId){
		return TransactionsRunning::getRunningTrans($userId);
	}

	public static function getUnsettledTrans($userId, $where = []){
		return Transactions::getUnsettledTrans($userId, $where);
	}

	public static function getSettledTrans($userId){
		return Transactions::getSettledTrans($userId);
	}

	public static function getRefundTrans($userId){
		return Transactions::getRefundTrans($userId);
	}

	public static function getPaymentDetail($paymentId, $userId){
		return Payment::where('payment_id','=',$paymentId)
		->where('user_id','=',$userId)
		->first();
	}

	public static function getUnsettledInvoices($data){
		return Transactions::getUnsettledInvoices($data);
	}

	public static function getTransDetail($data){
		return Transactions::getTransDetail($data);
	}

	public static function getUnsettledInvoiceTransactions($data){
		return Transactions::getUnsettledInvoiceTransactions($data);
	}

	public static function getUnsettledChargeTransactions($data){
		return Transactions::getUnsettledChargeTransactions($data);
	}

	public static  function calInvoiceRefund($invoiceDisbursalId, $paymentDate)
	{
		return Transactions::calInvoiceRefund($invoiceDisbursalId, $paymentDate);
	}
	public static  function getInvoiceDisbursed($disbursalIds)
	{
		return InvoiceDisbursed::getInvoiceDisbursed($disbursalIds);
	}
	public static  function appLimitByUserId($userId)
	{
		return AppLimit::appLimitByUserId($userId);
	}
	public static  function appPrgmOfferById($id)
	{
		return AppProgramOffer::getAppPrgmOfferById($id);
	}
	public static  function getUserAdhocLimitById($id)
	{
		return AppOfferAdhocLimit::find($id);
	}

	public static  function getAdhocDocuments($app_offer_adhoc_limit_id){
		return OfferAdhocDocument::getAdhocDocuments($app_offer_adhoc_limit_id);
	}

	public static function getInvoiceSettleStatus(int $invoiceId, $statusOnly = false){

		if (empty($invoiceId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
		if (!is_int($invoiceId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
		}

		$response = [
			'invoice_id'=>null,
			'is_settled' => false,
			'case'=>null,
			'payment'=>0,
			'receipt'=>0,
			'principal'=>[],
			'interest'=>[],
			'margin'=>[],
			'overdue'=>[]
		];

		$invDisbursed = InvoiceDisbursed::where('invoice_id','=',$invoiceId)->first();

		if($invDisbursed){
			$response['invoice_id'] = $invDisbursed->invoice_id;
			$response['payment'] = 0;
			$response['case'] = $invDisbursed->invoice->program_offer->payment_frequency;
			$response['is_settled'] = false;
			$response['receipt'] = 0;
			$response['principal_repayment_amt'] = 0;

			$transactions = Transactions::whereNull('parent_trans_id')
			->whereNull('payment_id')
			->where('invoice_disbursed_id','=',$invDisbursed->invoice_disbursed_id)
			->get();

			$response['repayment_amt'] = Transactions::whereNotNull('payment_id')
			->where('entry_type','1')
			->where('invoice_disbursed_id','=',$invDisbursed->invoice_disbursed_id)
			->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED'), config('lms.TRANS_TYPE.INTEREST'), config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
			->sum('amount');

			$transactionsRunning = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbursed->invoice_disbursed_id)
			->get()
			->filter(function($item) {
				return $item->outstanding > 0;
			});

			foreach($transactionsRunning as $transRunning){
				switch ($transRunning->trans_type) {
					case config('lms.TRANS_TYPE.INTEREST'):
						$response['interest'][] = [
							'trans_running_id' => $transRunning->trans_running_id,
							'amount'=> $transRunning->amount,
							'outstanding'=>$transRunning->outstanding,
							'trans_date'=>$transRunning->trans_date,
							'payment_frequency'=>$transRunning->invoiceDisbursed->invoice->program_offer->payment_frequency,
							'interest_borne_by'=>$transRunning->invoiceDisbursed->invoice->program->interest_borne_by,
						];
						break;
					case config('lms.TRANS_TYPE.INTEREST_OVERDUE'):
						$response['overdue'][] = [
							'trans_running_id' => $transRunning->trans_running_id,
							'amount'=> $transRunning->amount,
							'outstanding'=>$transRunning->outstanding,
							'trans_date'=>$transRunning->trans_date
						];
						break;
				}
			}

			foreach ($transactions as $trans){
				switch ($trans->trans_type) {
					case config('lms.TRANS_TYPE.PAYMENT_DISBURSED'):
						$response['principal'][] = [
							'trans_id' => $trans->trans_id,
							'amount'=> $trans->amount,
							'outstanding'=>$trans->outstanding,
							'trans_date'=>$trans->trans_date
						];
						break;
					case config('lms.TRANS_TYPE.INTEREST'):
						$response['interest'][] = [
							'trans_id' => $trans->trans_id,
							'amount'=> $trans->amount,
							'outstanding'=>$trans->outstanding,
							'trans_date'=>$trans->trans_date,
							'payment_frequency'=>$trans->invoiceDisbursed->invoice->program_offer->payment_frequency,
							'interest_borne_by'=>$trans->invoiceDisbursed->invoice->program->interest_borne_by,
							'trans_running_id' => $trans->trans_running_id,
						];
						break;
					case config('lms.TRANS_TYPE.MARGIN'):
						$response['margin'][] = [
							'trans_id' => $trans->trans_id,
							'amount'=> $trans->amount,
							'outstanding'=>$trans->outstanding,
							'trans_date'=>$trans->trans_date
						];
						break;
					case config('lms.TRANS_TYPE.INTEREST_OVERDUE'):
						$response['overdue'][] = [
							'trans_id' => $trans->trans_id,
							'amount'=> $trans->amount,
							'outstanding'=>$trans->outstanding,
							'trans_date'=>$trans->trans_date
						];
						break;
				}
			}

			foreach($response['principal'] as $val){
				$response['payment'] += (float) $val['amount'];
				$response['receipt'] +=	(float) $val['amount'] - (float) $val['outstanding'];
				$response['principal_repayment_amt'] += (float) $val['amount'] - (float) $val['outstanding'];
			}

			foreach($response['interest'] as $val){
				$response['payment'] += (float) $val['amount'];
				$response['receipt'] +=	(float) $val['amount'] - (float) $val['outstanding'];
				// if($val['payment_frequency'] == 1 && $val['interest_borne_by'] == 1 && $val['trans_running_id'] == NULL)
				// {
				// 	$response['principal_repayment_amt'] += (float) $val['amount'] - (float) $val['outstanding'];
				// }
			}

			foreach($response['overdue'] as $val){
				$response['payment'] += (float) $val['amount'];
				$response['receipt'] +=	(float) $val['amount'] - (float) $val['outstanding'];
			}

			foreach($response['margin'] as $val){
				//$response['receipt'] +=	(float) $val['amount'] - (float) $val['outstanding'];
			}

			if($response['payment'] <= $response['receipt']){
				$response['is_settled'] = true;
			}
		}
		if($statusOnly){
			return $response['is_settled'];
		}else{
			return $response;
		}
	}

	public static function getMaxDpdTransaction($userId, $transType){
		return Transactions::getMaxDpdTransaction($userId, $transType);
	}

	public static function getBatchDisbursalList(){
		return DisbursalBatch::with('disbursal')
				->orderBy('created_at', 'DESC');
	}

    /**
     * Get System Start Date
     *
     * @return timestamp
     */
    public function getSysStartDate()
    {
        return EodProcess::getSysStartDate();
    }

    /**
     * Get Disbursal transactions
     *
     * @param string $transStartDate
     * @param string $transEndDate
     *
     * @return mixed
     */
    public function checkDisbursalTrans($transStartDate, $transEndDate)
    {
        return Transactions::checkDisbursalTrans($transStartDate, $transEndDate);
    }

	/**
     * Get Running transactions
     *
     * @param string $transStartDate
     * @param string $transEndDate
     *
     * @return mixed
     */
    public function checkRunningTrans($transStartDate, $transEndDate)
    {
        return Transactions::checkRunningTrans($transStartDate, $transEndDate);
    }

    /**
     * Get Total Disbursed Amount
     *
     * @param array $disbursalIds
     * @return mixed
     */
    public function getTotalDisbursedAmt($disbursalIds)
    {
        return Disbursal::getTotalDisbursedAmt($disbursalIds);
    }

    /**
     * Get Latest Eod Process
     *
     * @return mixed
     */
    public function getLatestEodProcess($whereCond=[])
    {
        return EodProcess::getLatestEodProcess($whereCond);

    }

    /**
     * Save write off
     *
     * @param array $dataArr
     * @return type
     */
    public function saveWriteOffReq($dataArr)
    {
        return WriteOffRequest::saveWriteOffReq($dataArr);
    }

    /**
     * Get write off
     *
     * @param integer $userId
     * @return array
     */
    public function getWriteOff($userId)
    {
        return WriteOffRequest::getWriteOff((int) $userId);
    }

    /**
     * Save write off log
     *
     * @param array $dataArr
     * @return type
     */
    public function saveWriteOffReqLog($dataArr)
    {
        return WriteOffStatusLog::saveWriteOffReqLog($dataArr);
    }

    /**
     * Update write off
     *
     * @param array $dataArr
     * @return type
     */
    public function updateWriteOffReqById($woReqId, $dataArr)
    {
        return WriteOffRequest::updateWriteOffReqById((int) $woReqId, $dataArr);
    }

	/**
	 * Mark User write Off
	 * @param int $uid
	 * @return type
	 */
	public function writeOff($uid){
		$mytime = Carbon::now();
        $cDate   =  $mytime->toDateTimeString();
        $create_uid = Auth::user()->user_id;
        $getLogId = LmsUsersLog::create(['user_id' => $uid,'status_id' => 41,'created_by' => $create_uid,'created_at' => $cDate]);
        UserDetail::where(['user_id' => $uid])->update(['is_active' => 0,'lms_users_log_id' => $getLogId->lms_users_log_id]);
	}


	public function getColenderShareWithUserId($userId)
        {
		return ColenderShare::getColenderShareWithUserId((int)$userId);
	}

	public function getColenderApplications()
       {
            $roleData = User::getBackendUser(\Auth::user()->user_id);

			 if ($roleData[0]->is_superadmin != 1) {
				$getAppId  = ColenderShare::where(['is_active' => 1, 'co_lender_id' => \Auth::user()->co_lender_id])->pluck('app_id');
				$result = LmsUser::whereIn('app_id',$getAppId)->with('user')->orderBy('lms_user_id','DESC');
			} else {
				$getAppId  = ColenderShare::where(['is_active' => 1])->pluck('app_id');
				$result = LmsUser::whereIn('app_id',$getAppId)->with(['user', 'getBusinessId'])->orderBy('lms_user_id','DESC');
			}

            return $result ?: false;
	}

         public static function getChrgLog($id)
	{
	   try
	   {
		  return ChargeGST::getLastChargesGSTById($id);
	   } catch (Exception $ex) {
		  return $ex;
	   }

	}

    public function getEodDataCount() {
        return EodProcess::getEodDataCount();
    }

    public function postedTxnsInTally() {
        return Transactions::postedTxnsInTally();
    }

    public function getActualTallyAmount() {
       return TallyEntry::getActualPostedAmount();
    }


    public function getCibilReports(array $whereCondition = [], $whereRawCondition = NULL) {
       return CibilReports::getCibilReports($whereCondition, $whereRawCondition);
    }

    public function getCibilUserData(array $whereCondition = [], $whereRawCondition = NULL) {
       return CibilUserData::getCibilUserDataList($whereCondition, $whereRawCondition);
    }

    public function insertCibilUserData(array $userData = []) {
       return CibilUserData::insertBulkData($userData);
    }

    public function getAllBusinessData(array $where = []) {
        return Business::with('app')->whereHas('app', function ($q) use ($where){
        	$q->where($where);
        })->get();
    }

    public function getAllBusinessAddrData(array $whereCond = []) {
        return BusinessAddress::getBizAddresses($whereCond);
    }

    public function getDisbursalByUserAndBatchId($data)
	{
		return Disbursal::where($data)
				->first();
	}


	public function getEodList(){
		return EodProcess::whereNotIn('status',[config('lms.EOD_PROCESS_STATUS.WATING')])->orderBy('eod_process_id','DESC')->get();
	}

	public function createCronLog($data){
		return CronLog::createCronLog($data);
	}

	public function updateCronLog($data,$cronLogId){
		return CronLog::updateCronLog($data,$cronLogId);
	}

	public function getUnsettledPayments($userId){
		return Payment::where('user_id','=',$userId)
		->where('is_settled','=','0')
		->get();
	}

    public function isApportPaymentValid($paymentId, $userId){
        $isValid = false;
        $validPaymentId = Payment::where('user_id',$userId)
        ->where('is_settled','0')
        ->where('action_type','1')
        ->orderBy('date_of_payment','asc')
        ->orderBy('payment_id','asc')
        ->first();

        if($validPaymentId->payment_id == $paymentId){
            $isValid = true;
        }
        return $isValid;
    }

    public function getAprvlRqUserByIds($ids = [])
    {
    	if (empty($ids)) {
	        return "No record found.";
    	} else {
    		return RefundReq::getAprvlRqUserByIds($ids);
    	}
    }

    /**
	 * Save or Update Disbursal Api Log
	 *
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public function saveUpdateDisbursalApiLog($data, $whereCondition=[])
	{
		return DisbursalApiLog::saveUpdateDisbursalApiLog($data, $whereCondition);
	}

	/**
     * Get disbursal batch
     *
     * @param integer $batchId
     * @return array
     */
	public function lmsGetDisbursalBatchRequest()
	{
		return DisbursalBatch::lmsGetDisbursalBatchRequest();
	}

	/**
     * Get disbursal batch
     *
     * @param integer $batchId
     * @return array
     */
	public function getdisbursalBatchByDBId($disbursalBatchId)
	{
		return DisbursalBatch::with('disbursal_api_log', 'disbursalOne')
				->where('disbursal_batch_id', $disbursalBatchId)
				->where('batch_status', 1)
				->latest()
				->first();
	}

	/**
	 * update disbursal batch
	 *
	 * @param array $whereCondition | required
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function updateDisbursalBatchById($data, $updatingId = [])
	{
		if (is_array($updatingId)) {
			$response =  DisbursalBatch::whereIn('disbursal_batch_id', $updatingId)
				->update($data);
		} else {
			$response =  DisbursalBatch::where('disbursal_batch_id', $updatingId)
				->update($data);
		}
		return $response ?? false;
	}

	public static function updateDisbursalByTranId($data = [], $whereCondition = [])
	{
		$response =  Disbursal::where($whereCondition)
			->update($data);
		return ($response) ?? $response;
	}

	/**
     * Get refund batch
     *
     * @param integer $batchId
     * @return array
     */
	public function lmsGetRefundBatchRequest()
	{
		return RefundReqBatch::lmsGetRefundBatchRequest();
	}

	public function lmsGetCustomerRefundById($id = null)
    {
        return RefundReq::where('refund_req_id', $id)
			   ->first();
    }

    public function getrefundBatchByDBId($efundrBatchId)
	{
		return RefundReqBatch::with('disbursal_api_log')
				->where('refund_req_batch_id', $efundrBatchId)
				->first();
	}

	public static function updateRefundBatchById($data, $updatingId = [])
	{
		if (is_array($updatingId)) {
			$response =  RefundReqBatch::whereIn('refund_req_batch_id', $updatingId)
				->update($data);
		} else {
			$response =  RefundReqBatch::where('refund_req_batch_id', $updatingId)
				->update($data);
		}
		return $response ?? false;
	}

	public function lmsGetRefundReqById($id = null)
    {
        return RefundReq::where('refund_req_id', $id)
			   ->first();
    }

    public static function updateRefundByTranId($data = [], $updatingId = null)
	{
		$response =  RefundReq::updateOrCreate(['tran_no' => $updatingId],$data);
		return ($response) ?? $response;
	}

    public function getAllNach($whereCond = []){
        return UserNach::getNach($whereCond);
    }

	public function getUserNaches($nachIds)
	{
		return UserNach::whereIn('users_nach_id', $nachIds)
			   ->get();
	}

	public static function createNachReqBatch($file, $batchNo = null)
    {
        if (!empty($batchNo)) {
            $disburseBatch['batch_no'] = $batchNo ?? null;
            $disburseBatch['file_id'] = ($file) ? $file->file_id : '';
            $disburseBatch['batch_status'] = 1;
        }
        return NachRepaymentReqBatch::create($disburseBatch);
    }
    public static function saveNachReq($data)
    {
        return NachRepaymentReq::create($data);
    }

    /**
     * Update Nach repayment Data By Condition
     *
     * @param arr $attr
     * @param arr $whereCond
     * @return type
     */
    public function updateRepaymentReq($attr, $whereCond){
        return NachRepaymentReq::updateRepaymentReq($attr, $whereCond);
    }

    public function getNachRepaymentReq($whereCondition){
        return NachRepaymentReq::where($whereCondition)->orderBy('created_at', 'DESC');
    }

	public static function getNACHUnsettledTrans($userId, $where = []){
		return Transactions::getNACHUnsettledTrans($userId, $where);
	}

    public static function saveNachTrans($data)
    {
        return NachTransReq::create($data);
    }

    public function getNachRepaymentReqFirst($whereCondition){
        return NachRepaymentReq::where($whereCondition)->first();
	}

    public function updateNachTransReq($attr, $whereCond){
        return NachTransReq::updateNachTransReq($attr, $whereCond);
	}

	public function getUnsettledRunningTrans(){
        return TransactionsRunning::getUnsettledRunningTrans();
    }

	public function saveInvoiceDisbursedDetails($attr, $whereCond){
		return InvoiceDisbursedDetail::saveInvoiceDisbursedDetails($attr, $whereCond);
	}

	public function findInvoiceDisburseByDisbursalId($data)
	{
		return InvoiceDisbursed::where($data)
				->pluck('invoice_disbursed_id');
	}

	public function updateInvoiceDisburseStatus($invoiceDisburseIds, $status)
	{
		$response =  InvoiceDisbursed::whereIn('invoice_disbursed_id', $invoiceDisburseIds)
				->update(['status_id' => $status]);
		return $response;
	}

	public function findInvoiceDisbursedInvoiceIdByInvoiceId($invoiceId)
	{
		return InvoiceDisbursed::where('invoice_id', $invoiceId)
				->whereIn('status_id', [10,12])
				->get();
	}

	public function getDisbursalByDBId($disbursalBatchId){
		return Disbursal::where('disbursal_batch_id',$disbursalBatchId)->get();
	}

	public function deleteDisbursalStatusLogByDidArr($IdsArr){
		return DisbursalStatusLog::whereIn('disbursal_id',$IdsArr)->where('status_id',10)->delete();
	}

	public function deleteInvoiceStatusLogByInvIdArr($IdsArr){
		return InvoiceStatusLog::whereIn('invoice_id',$IdsArr)->where('status_id',10)->delete();
	}

	public static  function deleteInvoiceDisbursed($disbursalIds)
	{
		return InvoiceDisbursed::whereIn('disbursal_id',$disbursalIds)->delete();
	}

	public function deleteDisbursalByDBId($disbursalBatchId){
		return Disbursal::where('disbursal_batch_id',$disbursalBatchId)->delete();
	}

	public function deleteDisbursalBatchByDBId($disbursalBatchId)
	{
		return DisbursalBatch::where('disbursal_batch_id', $disbursalBatchId)->delete();
	}
	// END TDS
    public function getLastGSTRecord()
    {
        return GstTax::getLastGSTRecord();
    }

	public function getTDSOutstatingAmount($userId)
    {
        $transaction = Transactions::getUnsettledSettledTDSTrans($userId);
        return $transaction->sum('TDSAmount');
    }

	public static function getUnsettledSettledTDSTrans($data)
	{
		return Transactions::getUnsettledSettledTDSTrans($data);
	}

	public static function getOverdueData()
	{
		return InterestAccrual::getOverdueData();
	}

	public static function getAccountStatus($userId)
	{
		return LmsUsersLog::getAccountStatus($userId);
	}

	public function maxDpdTransaction($userId){	
		return Transactions::maxDpdTransaction($userId);
	}
	
	public function getCapsavAddr(){
		return Company::getCapsavAddr();
	}

    public function getAllBusinessForSheet($whereCond) {
		$startDate = Carbon::createFromFormat('Y-m-d', $whereCond['date'], 'Asia/Kolkata')->firstOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s');
		$endDate = Carbon::createFromFormat('Y-m-d', $whereCond['date'], 'Asia/Kolkata')->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
		$statusIds = $whereCond['status_ids'];
		$invoices = BizInvoice::select(DB::raw('max(invoice_id) as maxInv,supplier_id, count(invoice_id) as invCnt '))
		->whereHas('invoice_disbursed', function ($q) use ($startDate, $endDate, $statusIds) {
			//$q->where('created_at', '>=', $startDate);
			$q->where('created_at', '<=', $endDate);
			$q->whereIn('status_id', $statusIds);
		})
		->groupBy('supplier_id')
		->get();

		$collection = null;

		$userAppBizDetail = \DB::table('app_prgm_offer') 
		->select('app.user_id', \DB::raw('SUBSTRING_INDEX(GROUP_CONCAT('.config("database.connections.mysql.prefix").'app_status_log.app_id ORDER BY '.config("database.connections.mysql.prefix").'app_status_log.created_at DESC),",",1) AS app_id , 
		SUBSTRING_INDEX(GROUP_CONCAT('.config("database.connections.mysql.prefix").'app.biz_id ORDER BY '.config("database.connections.mysql.prefix").'app_status_log.created_at DESC),",",1) AS biz_id' ) ) 
		->join('app_prgm_limit','app_prgm_limit.app_prgm_limit_id', '=', 'app_prgm_offer.app_prgm_limit_id') 
		->join('app','app.app_id', '=', 'app_prgm_offer.app_id') 
		->join('app_status_log', 'app_status_log.app_id', '=', 'app.app_id') 
		->whereIn('app_status_log.status_id',[21, 22, 25, 50]) 
		->where('app_prgm_limit.product_id',1) 
		->where('app_status_log.created_at', '<=', $endDate)
		->groupBY('app.user_id') 
		->get();

		$supplierAppBiz = [];
		foreach ($userAppBizDetail as $key => $appBizId) {
			$supplierAppBiz[$appBizId->user_id] = ['app_id'=>$appBizId->app_id, 'biz_id'=>$appBizId->biz_id];
		}

		foreach ($invoices as $key => $inv) {

			$r = BizInvoice::with(['invoice_disbursed'])
			->where('invoice_id',$inv->maxInv)->get();
			if($r){
				if(!empty($supplierAppBiz[$appBizId->user_id])){
					$r[0]->biz_id = $supplierAppBiz[$inv->supplier_id]['biz_id'];
					$r[0]->app_id = $supplierAppBiz[$inv->supplier_id]['app_id'];
				}

				$r[0]->invCount = $inv->invCnt;
				if($collection){
					$collection = $collection->merge($r);
				}else{
					$collection = $r;
				}
			}
		}
		return $collection;
    }

	public function saveChargeTransDeleteLog($attr)
	{
		return  ChargeTransactionDeleteLog::saveChargeTransDeleteLog($attr);
	}

	public function getRoleActiveUsers($roleIds = [])
    {
        return RoleUser::getRoleActiveUsers($roleIds);
    }

    public function getInvoice($invoiceId = false)
	{
		return BizInvoice::where('invoice_id', $invoiceId)
			   ->with(['program_offer','lms_user' , 'supplier.anchor_bank_details.bank', 'supplier_bank_detail.bank', 'program','supplier.apps.disbursed_invoices.invoice_disbursed'])
			   ->first();
	}

	/**
     * Get disbursal batch cron
     *
     * @param integer $batchId
     * @return array
     */
	public function lmsGetDisbursalBatchRequestCron()
	{
		return DisbursalBatch::lmsGetDisbursalBatchRequestCron();
	}
	
    public function mailsForPendingCases()
	{
		return AppApprover::mailsForPendingCases();
	}

	public function checkUtrNo($whereCond){
		return Payment::where($whereCond)->first();
	}

	public function checkUtrAlert($utrNumber,$userId){
		$data = Payment::where('utr_no',$utrNumber)
						->whereNotIn('user_id',[$userId])->first();
		return $data;
	}
	public function chequeAlert($chequeNumber,$userId){
		$data = Payment::where('cheque_no',$chequeNumber)
						->whereNotIn('user_id',[$userId])->first();
		return $data;
	}
	public function checkUnrAlert($unrNumber,$userId){
		$data = Payment::where('unr_no',$unrNumber)
						->whereNotIn('user_id',[$userId])->first();
		return $data;
	}
}
