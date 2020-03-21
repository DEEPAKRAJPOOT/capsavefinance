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
                $back_stage_data = '';
            } else {
                $next_stage = '1';
                $next_stage_data = '';
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
            $isBackStage = $request->has('back_stage') ? true : false;
            $comment = $request->get('sharing_comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                
                $addlData=[];
                $addlData['sharing_comment'] = $comment;
                if ($isBackStage) {
                    $this->moveRequestToPrevStage($reqId, $addlData);
                } else {
                    $this->moveRequestToNextStage($reqId, $addlData);
                }
                        
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
        }
        
}