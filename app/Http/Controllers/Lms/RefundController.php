<?php
namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;

use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class RefundController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        
	protected $appRepo;
	protected $lmsRepo;

	public function __construct(InvAppRepoInterface $app_repo,  InvLmsRepoInterface $lms_repo ){
		$this->appRepo = $app_repo;
		$this->lmsRepo = $lms_repo;
		$this->middleware('checkBackendLeadAccess');
	}

	/**
	 * Display a listing of the Refund Request
	 * @return \Illuminate\Http\Response
	 */
	public function requestList(){
		return view('lms.common.request');
	}
	
	/**
	 * Display a listing of the refund.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function customerList()
	{
		return view('lms.common.view_customer');              
	}

	/**
	 * Display confirm dialogue.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function confirmRefund(Request $request)
	{
		return view('lms.refund.confirm_refund');              
	}


	public function sendRefund(Request $request)
	{
		$transId = $request->trans_id;
		$disburseIds = $request->disbursal_ids;

		$record = array_filter(explode(",",$disburseIds));

		$allrecords = array_unique($record);
		$allrecords = array_map('intval', $allrecords);

		$allDisburse = $this->lmsRepo->getDisbursals($allrecords)->toArray();
		foreach ($allDisburse as $disbursal) {
			$refundMstId = (config('lms')['TRANS_TYPE']['PAYMENT_REVERSE']) ?? 2;
			$data = [];
			$data['amount'] = $disbursal['surplus_amount'];
			$data['disbursal_id'] = $disbursal['disbursal_id'];
			$transactionData = $this->createTransactionData($disbursal['user_id'],  $data, $transId, $refundMstId);
			$createTransaction = $this->lmsRepo->saveTransaction($transactionData);

			if (!empty($createTransaction)) {
				$disburseData = [];
				$disburseData['surplus_amount'] = 0;
				$updateDisburse = $this->lmsRepo->updateDisburse($disburseData, $disbursal['disbursal_id']);
			}
		}
		
		if (empty($allrecords)) {
			return redirect()->route('lms_refund_list')->withErrors(trans('backend_messages.noSelectedInvoice'));
		}
        
        Session::flash('message',trans('backend_messages.refunded'));
		return redirect()->route('lms_refund_list');
	}

	public function createBatch(Request $request) 
	{
		return view('lms.common.create_request', $request->all());
	}
	
	public function editBatch(Request $request) 
	{
		return view('lms.common.edit_request', $request->all());
	}
        
	public function moveReqToNextStage(Request $request) 
	{
            $reqId = $request->get('req_id');
            $back_stage = '';
            $next_stage = '';
            $back_stage_data = null;
            $next_stage_data = null;
            if ($request->has('back_stage')) {
                $back_stage = '1';
                $back_stage_data = $this->getRequestPrevStage($reqId);
            } else {
                $next_stage = '1';
                $next_stage_data = $this->getRequestNextStage($reqId);
            }
            return view('lms.common.move_next_stage')
                    ->with('reqId', $reqId)
                    ->with('back_stage', $back_stage)
                    ->with('back_stage_data', $back_stage_data)
                    ->with('next_stage', $next_stage)
                    ->with('next_stage_data', $next_stage_data);
                    
	}

        public function acceptReqStage(Request $request)
        {
            $reqId = $request->get('req_id');
            $isBackStage = $request->has('back_stage') && !empty($request->get('back_stage')) ? true : false;
            $comment = $request->get('sharing_comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                
                $addlData=[];
                $addlData['sharing_comment'] = $comment;
                if ($isBackStage) {
                    //dd('$isBackStage', $isBackStage, $request->all());
                    $this->moveRequestToPrevStage($reqId, $addlData);
                } else {
                    //dd('$isNextStage', $request->all());
                    $this->moveRequestToNextStage($reqId, $addlData);
                }
                        
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
        }
        
        public function updateRequestStatus(Request $request)
        {
            $reqId = $request->get('req_id');
            $reqData = $this->lmsRepo->getApprRequestData($reqId);
            $curReqStatus = $reqData ? $reqData->status : '';
            
            $statusList = \Helpers::getRequestStatusList($reqId);
            $statusList = ['' => 'Select Status'] + $statusList;                
                           
            return view('lms.common.view_request_status')
                    ->with('reqId', $reqId)
                    ->with('statusList', $statusList);            
        }
        
        public function saveRequestStatus(Request $request)
        {
            $reqId = $request->get('req_id');
            $reqStatus = $request->get('status');
            $comment = $request->get('comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                
                $addlData=[];
                $addlData['status'] = $reqStatus;
                $addlData['sharing_comment'] = $comment;
                $this->updateApprRequest($reqId, $addlData);
                        
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
        }
        
        public function viewProcessRefund(Request $request)
        {
            $reqId = $request->get('req_id');
            $reqData = $this->lmsRepo->getApprRequestData($reqId);
            $transId = $reqData ? $reqData->trans_id : '';
            $currStatus = $reqData ? $reqData->status : '';
            
            $refundData = $this->getRefundData($transId);
            
            $statusList = \Helpers::getRequestStatusList($reqId);
            $statusList = ['' => 'Select Status'] + $statusList;    
            
            return view('lms.common.refund_process')
                    ->with('reqId', $reqId)
                    ->with('refundData', $refundData)
                    ->with('currStatus', $currStatus)
                    ->with('statusList', $statusList); 
        }

        public function processRefund(Request $request)
        {
            $reqId = $request->get('req_id');
            $reqStatus = $request->get('status');
            $comment = $request->get('comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                if ($request->has('process') && !empty($request->get('process')) ) {
                    $reqData = $this->lmsRepo->getApprRequestData($reqId);
                    $curReqStatus = $reqData ? $reqData->status : '';
                    $trAmount = $reqData ? $reqData->amount : 0;
                    $userId = $reqData ? $reqData->user_id : 0;
                    $transId = $reqData ? $reqData->trans_id : 0;

                    $trData = [];                
                    $trData['amount'] = $trAmount;
                    $trData['parent_trans_id'] = $transId;
                    $ptrData = $this->createTransactionData($userId, $trData, null, $transType = 35, $entryType = 0);
                    $this->appRepo->saveTransaction($ptrData);

                    $addlData=[];
                    $addlData['status'] = config('lms.REQUEST_STATUS.PROCESSED');
                    $addlData['sharing_comment'] = $comment;
                    $this->updateApprRequest($reqId, $addlData);
                } else {
                    $addlData=[];
                    $addlData['status'] = $reqStatus;
                    $addlData['sharing_comment'] = $comment;
                    $this->updateApprRequest($reqId, $addlData);                    
                }
                
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }            
        }
        
}