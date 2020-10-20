<?php

namespace App\Http\Controllers\Backend;

use DB;
use Auth;
use Session;
use Helpers;
use DateTime;
use PDF as DPDF;
use Carbon\Carbon;
use App\Libraries\Pdf;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Contracts\ApplicationInterface as AppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;

class AnchorNACHController extends Controller {

	protected $appRepo;
	protected $docRepo;

	public function __construct(AppRepoInterface $appRepo, InvDocumentRepoInterface $docRepo) {
		$this->appRepo  =  $appRepo;
		$this->docRepo  =  $docRepo;
		$this->middleware('auth');
		// $this->middleware('checkBackendLeadAccess');
	}

	/* nach Listing  */

	public function nachList(Request $request) {
		return view('backend.anchor_nach.list');
	}

	/* nach Listing  */

	public function createNACH(Request $request) {
		try {
			$userId = Auth::user()->user_id;
			$anchorId = Auth::user()->anchor_id;
			$userBankData = $this->appRepo->getUserBankNACH(['anchor_id' => $anchorId]);

			return view('backend.anchor_nach.create_nach')
				->with([ 'userBankData' => $userBankData ]);
				
		} catch (\Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}	
	
	/**
	 * Add Nach view
	 * 
	 * @param Request $request
	 * @return type mixed
	 */
	public function addNachDetail(Request $request)
	{
		try {
			$acc_id = $request->get('bank_account_id');
			$userId = Auth::user()->user_id;
			$anchorId = Auth::user()->anchor_id;

			$whereCondition = ['bank_account_id' => $acc_id, 'user_id' => $userId];
			$nachDetail = $this->appRepo->getNachData($whereCondition);

			return view('backend.anchor_nach.add_nach')
					->with([
						'acc_id' => $acc_id, 
						'user_id' => $userId, 
						'anchor_id' => $anchorId,
						'nachDetail' => $nachDetail
					]);

		} catch (\Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}

	/**
	 * Add Nach view
	 * 
	 * @param Request $request
	 * @return type mixed
	 */
	public function EditNachDetail(Request $request)
	{
		try {
			$usersNachId = $request->get('users_nach_id');
			$userId = Auth::user()->user_id;
			$anchorId = Auth::user()->anchor_id;

			$whereCondition = ['users_nach_id' => $usersNachId];
			$nachDetail = $this->appRepo->getNachData($whereCondition);

			return view('backend.anchor_nach.add_nach')
					->with([
						'acc_id' => $nachDetail->bank_account_id, 
						'user_id' => $userId, 
						'anchor_id' => $anchorId, 
						'nachDetail' => $nachDetail
					]);

		} catch (\Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}
	
	/**
	 * Save Nach
	 * 
	 * @param Request $request
	 * @return type
	 */
	public function saveNachDetail(Request $request) {
		try {
			$acc_id = $request->get('acc_id');
			$user_id = $request->get('user_id');
			$anchor_id = $request->get('anchor_id');
			$users_nach_id = $request->get('users_nach_id');
			$bankAccount = $this->appRepo->getBankAccountData(['bank_account_id' => $acc_id])->first();
			$compDetail = '';
			$whereCond = [];
			if ($bankAccount) {
				$whereCond = ['comp_addr_id' => $bankAccount['comp_addr_id']];
				$compDetail = $this->appRepo->getCompAddByCompanyName($whereCond);
			}
			$status = '';
			if ($request->get('submit') == 'save') {
				$status = 1;
			} else if ($request->get('submit') == 'modify'){
				$status = 2;
			} else if ($request->get('submit') == 'cancel'){
				$status = 3;
			}
			$nachData = [
				'bank_account_id' => $acc_id ? $acc_id : '',
				'user_id' => $user_id,
				'user_type' => 2,
				'acc_name' => $bankAccount['acc_name'] ? $bankAccount['acc_name'] : '',
				'acc_no' => $bankAccount['acc_no'] ? $bankAccount['acc_no'] : '',
				'ifsc_code' => $bankAccount['ifsc_code'] ? $bankAccount['ifsc_code'] : '',
				'branch_name' => $bankAccount['branch_name'] ? $bankAccount['branch_name'] : '',
				'sponsor_bank_code' => $bankAccount['sponser_bank_code'] ? $bankAccount['sponser_bank_code'] : '',
				'utility_code' => $compDetail['utility_code'] ? $compDetail['utility_code'] : '',
				'here_by_authorize' => $compDetail['cmp_name'] ? $compDetail['cmp_name'] : '',
				'frequency' => $request->get('frequency'),
				'nach_date' => !empty($request->get('nach_date')) ? Carbon::createFromFormat('d/m/Y', $request->get('nach_date'))->format('Y-m-d') : null,
				'debit_tick' => $request->get('debit_tick'),
				'amount' => $request->get('amount'),
				'debit_type' => $request->get('debit_type'),
				'phone_no' => $request->get('phone_no'),
				'email_id' => $request->get('email_id'),
				'reference_1' => $request->get('reference_1'),
				'reference_2' => $request->get('reference_2'),
				'period_from' => !empty($request->get('period_from')) ? Carbon::createFromFormat('d/m/Y', $request->get('period_from'))->format('Y-m-d') : null,
				'period_to' => !empty($request->get('period_to')) ? Carbon::createFromFormat('d/m/Y', $request->get('period_to'))->format('Y-m-d') : null,
				'period_until_cancelled' => $request->get('period_until_cancelled'),
				'is_active' => 0,
				'request_for' => $status,
				'nach_status' => config('lms.NACH_STATUS')['PENDING'],
			];
			if ($users_nach_id != null) {
				$whereCondition = ['users_nach_id' => $users_nach_id];
				$nachDetail = $this->appRepo->getNachData($whereCondition);
				if ($nachDetail->nach_status == 4) {
					$nachData += [
                		'parent_users_nach_id' => $users_nach_id
                	];
					$users_nach_id = $this->appRepo->saveNach($nachData);
					$logData = $this->appRepo->createNachStatusLog($users_nach_id, config('lms.NACH_STATUS')['PENDING']);
					$updateNachData = [
                		'nach_status_log_id' => $logData->nach_status_log_id
                	];
                	$this->appRepo->updateNach($updateNachData, $users_nach_id);
                } else {
					$this->appRepo->updateNach($nachData, $users_nach_id);
				}
			} else {
				$users_nach_id = $this->appRepo->saveNach($nachData);
				$logData = $this->appRepo->createNachStatusLog($users_nach_id, config('lms.NACH_STATUS')['PENDING']);
				$updateNachData = [
            		'nach_status_log_id' => $logData->nach_status_log_id
            	];
            	$this->appRepo->updateNach($updateNachData, $users_nach_id);
			}
			Session::flash('message',trans('success_messages.nach_updated'));
			return redirect()->route('anchor_nach_list');
		} catch (\Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}
	
	/**
	 * Nach Preview
	 * 
	 * @param Request $request
	 * @return type
	 */
	public function nachDetailPreview(Request $request) {
		try {
			$users_nach_id = $request->get('users_nach_id');
			$whereCondition = ['users_nach_id' => $users_nach_id];
			$nachDetail = $this->appRepo->getNachData($whereCondition);
			
			return view('backend.anchor_nach.nach_preview')
					->with(['nachDetail' => $nachDetail]);
		} catch (\Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}
	
	/**
	 * Download Nach Pdf
	 * 
	 * @param Request $request
	 * @return type
	 */
	public function generateNach(Request $request){
		try{
			$users_nach_id = $request->get('users_nach_id');
			$whereCondition = ['users_nach_id' => $users_nach_id];
			$nachDetail = $this->appRepo->getNachData($whereCondition);

			ob_start();
			DPDF::setOptions(['isHtml5ParserEnabled'=> true,'isRemoteEnabled', true]);               
			$pdf = DPDF::loadView('backend.anchor_nach.download_nach', ['nachDetail' => $nachDetail]);
			return $pdf->download('Nach.pdf');          
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		} 
	}

    /**
     * Upload Signed Nach Pdf
     * 
     * @param Request $request
     * @return type
     */
    public function uploadNachDocument(Request $request)
    {	
    	try {
		    $user_id = $request->get('user_id');
		    $users_nach_id = $request->get('users_nach_id');

		    return view('backend.anchor_nach.upload_nach_document')
		        	->with(['user_id' => $user_id, 'users_nach_id' => $users_nach_id]);

        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Save Nach Signed Pdf.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveNachDocument(DocumentRequest $request)
    {
        try {
        	$arrFileData = $request->all();
            $user_id = $request->get('user_id');
            $users_nach_id = $request->get('users_nach_id');
            
            $path = '/user/'. $user_id .'/nach';
			$uploadData = Helpers::uploadDirectoryFile($arrFileData, $path);
			$userFile = $this->docRepo->saveFile($uploadData);
			
            if ($userFile) {
				$logData = $this->appRepo->createNachStatusLog($users_nach_id, config('lms.NACH_STATUS')['PDF_UPLOADED']);
                $nachData = [
                	'uploaded_file_id' =>  $userFile->file_id, 
                	'nach_status' => config('lms.NACH_STATUS')['PDF_UPLOADED'], 
                	'nach_status_log_id' => $logData->nach_status_log_id,
                	'is_active' => 1
                	];
                $this->appRepo->updateNach($nachData, $users_nach_id);

                Session::flash('message',trans('success_messages.uploaded'));
                Session::flash('operation_status', 1);
                return redirect()->route('anchor_nach_detail_preview', ['users_nach_id' => $users_nach_id, 'user_id' => $user_id]);
            }

        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
}
