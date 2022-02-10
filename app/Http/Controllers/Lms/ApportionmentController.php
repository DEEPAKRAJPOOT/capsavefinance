<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Helpers;
use Session;
use Exception;
use PHPExcel; 
use Carbon\Carbon;
use PHPExcel_IOFactory; 
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\Payment;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ManualApportionmentHelper;
use App\Contracts\Ui\DataProviderInterface;
use App\Inv\Repositories\Models\BizInvoice;
use App\Http\Requests\Lms\AdjustmentRequest;
use App\Helpers\ManualApportionmentHelperTemp;
use App\Http\Requests\Lms\ApportionmentRequest;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;
use App\Inv\Repositories\Models\Lms\TransactionsRunning;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Models\Lms\CustomerTransactionSOA;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use App\Helpers\FileHelper;
use App\Inv\Repositories\Models\Lms\PaymentApportionment;
use Illuminate\Support\Facades\Response;

class ApportionmentController extends Controller
{
    use ActivityLogTrait;
    private $enablePaymentBeforeInvoiceDate = false;

    public function __construct(InvLmsRepoInterface $lms_repo ,DataProviderInterface $dataProvider, InvUserRepoInterface $user_repo,InvAppRepoInterface $app_repo, MasterInterface $master){
        $this->lmsRepo = $lms_repo;
        $this->dataProvider = $dataProvider;
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
        $this->master = $master;
        $this->middleware('checkBackendLeadAccess');
	}

    /**
     * View Running Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewRunningTrans(Request $request){
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 
            $result = $this->getUserLimitDetais($userId);
            $unsettledAmt = $this->getUnsettledAmt($userId);
            return view('lms.apportionment.runningTransactions')
            ->with('userId', $userId)
            ->with('userDetails', $userDetails)
            ->with('sanctionPageView',$sanctionPageView)
            ->with('unsettledAmt',$unsettledAmt)
            ->with(['userInfo' =>  $result['userInfo'],
                    'application' => $result['application'],
                    'anchors' =>  $result['anchors']]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * View Unsettled Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewUnsettledTrans(Request $request){
        try {
            set_time_limit(0);
            $oldData = [];
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            if(Helpers::_is_base64_encoded(old('payment'))){
                //(old('payment'))?unserialize(base64_decode(old('payment'))):
                //(old('check'))?unserialize(base64_decode(old('check'))):
                $oldData['payment'] = [];
                $oldData['check'] = [];
            }else{
                $oldData['payment'] = (old('payment'))?old('payment'):[];
                $oldData['check'] = (old('check'))?old('check'):[];
            }
            $userId = $request->user_id;
            $paymentId = null;
            $payment = null;
            $payment_amt = 0;
            $userDetails = $this->getUserDetails($userId);             
            $paySug = false;
            if($request->has('payment_id') && $request->payment_id){
                $paymentId = $request->payment_id;
                $payment = $this->getPaymentDetails($paymentId,$userId); 
                $payment_amt = $payment['payment_amt']; 
                $transList = self::getUnsettledTrans($userId, $payment['date_of_payment']);
                $invDisbList = $transList->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'), config('lms.TRANS_TYPE.INTEREST_OVERDUE')])->pluck('invoice_disbursed_id')->toArray();
                $invDisbList = array_unique($invDisbList);
                if(count($invDisbList) <= 50 ){
                    $paySug  = true;
                    $Obj = new ManualApportionmentHelperTemp($this->lmsRepo);
                    foreach($invDisbList as $invDisbId){
                        $Obj->intAccrual($invDisbId, null, $payment['date_of_payment'], $paymentId);
                    }
                    if(!$payment['isApportPayValid']){
                        Session::flash('error', trans('Please select Valid Payment!'));
                        return redirect()->back()->withInput();
                    }
                }
            }
            if(!$paySug && $paymentId){
                Session::flash('error', trans('We have disabled the suggestive amount on manual apportionment screen as records are than 50.'));
            }
            $result = $this->getUserLimitDetais($userId);
            if ($request->has('redirect') && $request->redirect) {
                sleep(2);
            }
            $paymentApportionment =  PaymentApportionment::checkApportionmentHold($userId, $paymentId);
            return view('lms.apportionment.unsettledTransactions')
            ->with('paymentId', $paymentId)  
            ->with('userId', $userId)
            ->with('payment',$payment) 
            ->with('payment_amt', $payment_amt)
            ->with('userDetails', $userDetails)
            ->with('oldData',$oldData)
            ->with('sanctionPageView',$sanctionPageView)
            ->with('paySug', $paySug?1:0)
            ->with(['userInfo' =>  $result['userInfo'],
                    'application' => $result['application'],
                    'anchors' =>  $result['anchors']])
            ->with('paymentApportionment',$paymentApportionment);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * View Settled Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewSettledTrans(Request $request){
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId);
             $result = $this->getUserLimitDetais($userId);
             $paymentAppor = PaymentApportionment::checkApportionmentHold($userId);
            return view('lms.apportionment.settledTransactions')
                ->with('userDetails', $userDetails)
                ->with('sanctionPageView',$sanctionPageView)
                 ->with(['userInfo' =>  $result['userInfo'],
                            'application' => $result['application'],
                            'anchors' =>  $result['anchors']
                ])
                ->with('paymentAppor', $paymentAppor);    
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * View Refund Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewRefundTrans(Request $request){
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 
             $result = $this->getUserLimitDetais($userId);
            return view('lms.apportionment.refundTransactions')
                ->with('userDetails', $userDetails)
                ->with('sanctionPageView',$sanctionPageView) 
                 ->with(['userInfo' =>  $result['userInfo'],
                            'application' => $result['application'],
                            'anchors' =>  $result['anchors']]); 
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * get Transaction Detail for waiveOff
     * @param Request $request
     * @return array
     */
    public function getTransDetailWaiveOff(Request $request){
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $transId = $request->get('trans_id');
            $payment_id = $request->get('payment_id');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            if($TransDetail->transType->chrg_master_id){
                $gst = $TransDetail->transType->charge->gst_percentage;
            }else{
                $gst = 0;
            }
            return view('lms.apportionment.waiveOffTransaction', ['TransDetail' => $TransDetail,'payment_id' => $payment_id, 'sanctionPageView'=>$sanctionPageView, 'gst'=>$gst]); 
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * get Transaction Detail for Reversal
     * @param Request $request
     * @return array
     */
    public function getTransDetailReversal(Request $request){
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $transId = $request->get('trans_id');
            $payment_id = $request->get('payment_id');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            return view('lms.apportionment.reversalTransaction', ['TransDetail' => $TransDetail,'payment_id' => $payment_id, 'sanctionPageView'=>$sanctionPageView]); 
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * save waiveoff Detail
     * @param Request $request
     * @return array
     */
    public function saveWaiveOffDetail(Request $request){
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $transId = $request->get('trans_id');
            $paymentId = $request->get('payment_id');
            $amount = (float)$request->get('amount');
            $comment = $request->get('comment');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            if (empty($TransDetail)) {
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Selected Transaction to be waived off is not valid']);
            }
            $is_interest_charges = ($TransDetail->transType->chrg_master_id > 0 || in_array($TransDetail->trans_type, [config('lms.TRANS_TYPE.INTEREST')]));
            if(!$is_interest_charges){
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Waived off is possible only Interest and Charges.']);
            }
            $outstandingAmount = $TransDetail->outstanding;
            if ($amount > $outstandingAmount)  {
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be Waived Off must be less than or equal to '. $outstandingAmount]);
            }
            if ($amount <= 0)  {
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be Waived Off must have some values ']);
            }

            if (empty($comment))  {
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Comment / Remarks is required to Waived off the amount.']);
            }
            $txnInsertData = [
                    'payment_id' => NULL,
                    'link_trans_id'=> $transId,
                    'parent_trans_id' => $TransDetail->parent_trans_id??$transId,
                    'invoice_disbursed_id' => $TransDetail->disburse->invoice_disbursed_id ?? NULL,
                    'user_id' => $TransDetail->user_id,
                    'trans_date' => Helpers::getSysStartDate(),
                    'amount' => $amount,
                    'settled_outstanding' => $amount,
                    'entry_type' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.WAVED_OFF'),
                    'trans_mode' => 2,
                    'gl_flag' => 0,
                    'soa_flag' => 1,
                    'pay_from' => 1,
                    'is_settled' => 2,
            ];
            $resp = $this->lmsRepo->saveTransaction($txnInsertData);
            if (!empty($resp->trans_id)) {
                $commentData = [
                    'trans_id' => $resp->trans_id,
                    'comment' => $comment,
                ];
                if($TransDetail->disburse->invoice_disbursed_id){
                    $Obj = new ManualApportionmentHelper($this->lmsRepo);
                    $this->updateInvoiceRepaymentFlag([$TransDetail->disburse->invoice_disbursed_id]);
                    $Obj->refundProcess($TransDetail->disburse->invoice_disbursed_id);
                }
                $comment = $this->lmsRepo->saveTxnComment($commentData);

                $whereActivi['activity_code'] = 'apport_waiveoff_save';
                $activity = $this->master->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Unsettled Transaction Waived Off (Manage Sanction Cases) '. null;
                    $arrActivity['app_id'] = null;
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($txnInsertData), $arrActivity);
                }                 
                
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Amount successfully waived off']);
            }
        } catch (Exception $ex) {
             return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * save reversal Detail
     * @param Request $request
     * @return array
     */
    public function saveReversalDetail(Request $request){
        try {
            $newTransactions = array();
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $transId = $request->get('trans_id');
            $paymentId = $request->get('payment_id')??null;
            $amount = (float)$request->get('amount');
            $comment = $request->get('comment');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            
            if (empty($TransDetail)) {
                return redirect()->route('apport_settled_view', ['payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Selected Transaction to be reversed is not valid']);
            }else{
                if(strtotime(\Helpers::convertDateTimeFormat($TransDetail->sys_created_at, 'Y-m-d H:i:s', 'Y-m-d')) != strtotime(\Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'Y-m-d'))){
                    return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Error: Transactions can only be reversed within a day.']);
                }
            }
            $outstandingAmount = $TransDetail->amount;
            
            if ($amount > $outstandingAmount)  {
                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be reversed must be less than or equal to '. $outstandingAmount]);
            }
            if ($amount <= 0)  {
                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be reversed must have some values ']);
            }
            
            if (empty($comment))  {
                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Comment / Remarks is required to reversed the amount.']);
            }
            
            if (empty($TransDetail)) {
                return redirect()->route('apport_settled_view',[ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Payment Detail missing for this transaction!']);
            }

            $txnInsertData = [
                    'payment_id' => NULL,
                    'link_trans_id'=> $transId,
                    'parent_trans_id' => $TransDetail->parent_trans_id,
                    'invoice_disbursed_id' => $TransDetail->invoice_disbursed_id ?? NULL,
                    'user_id' => $TransDetail->user_id,
                    'trans_date' => $TransDetail->trans_date,
                    'amount' => $amount,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                    'trans_mode' => 2,
                    'gl_flag' => 0,
                    'soa_flag' => 1,
                    'pay_from' => 1,
                    'is_settled' => 2,
            ];
            
            $resp = $this->lmsRepo->saveTransaction($txnInsertData);

            if($TransDetail->invoice_disbursed_id && in_array($TransDetail->trans_type, [config('lms.TRANS_TYPE.INTEREST'), config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])){
                $cancelRevTrans =Transactions::where('trans_type', config('lms.TRANS_TYPE.CANCEL'))
                ->where('entry_type',1)
                ->where('invoice_disbursed_id', $TransDetail->invoice_disbursed_id)
                ->where('payment_id', $TransDetail->payment->payment_id)
                ->where('settled_outstanding','>',0)
                ->get();
                
                foreach ($cancelRevTrans as $crt) {
                    $newTransactions[] = [
                        'payment_id' => NULL,
                        'link_trans_id'=> $crt->trans_id,
                        'parent_trans_id' => $TransDetail->parent_trans_id,
                        'invoice_disbursed_id' => $TransDetail->invoice_disbursed_id ?? NULL,
                        'user_id' => $TransDetail->user_id,
                        'trans_date' => $TransDetail->trans_date,
                        'amount' => $crt->settled_outstanding,
                        'entry_type' => 0,
                        'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                        'trans_mode' => 1,
                        'gl_flag' => 0,
                        'soa_flag' => 0,
                        'pay_from' => 1,
                        'is_settled' => 2,
                    ];
                }
            }
            foreach ($newTransactions as $newTrans) {
                $this->lmsRepo->saveTransaction($newTrans);
            }
            if($TransDetail->payment_id){
                $paymentDetails = Payment::find($TransDetail->payment_id);
                $paymentData = [
                    'user_id' => $paymentDetails->user_id,
                    'biz_id' => $paymentDetails->biz_id,
                    'virtual_acc' => $paymentDetails->virtual_acc,
                    'action_type' => $paymentDetails->action_type,
                    'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                    'parent_trans_id' => $resp->trans_id,
                    'amount' => $amount,
                    'date_of_payment' => $paymentDetails->date_of_payment,
                    'gst' => $paymentDetails->gst,
                    'sgst_amt' => $paymentDetails->sgst_amt,
                    'cgst_amt' => $paymentDetails->cgst_amt,
                    'igst_amt' => $paymentDetails->igst_amt,
                    'payment_type' => $paymentDetails->payment_type,
                    'utr_no' => $paymentDetails->utr_no,
                    'unr_no' => $paymentDetails->unr_no,
                    'cheque_no' => $paymentDetails->cheque_no,
                    'tds_certificate_no' => $paymentDetails->tds_certificate_no,
                    'file_id' => $paymentDetails->file_id,
                    'description' => $paymentDetails->description,
                    'is_settled' => 0,
                    'is_manual' => $paymentDetails->is_manual,
                    'generated_by' => 1,
                    'is_refundable' => 1
                ];
                $paymentId = Payment::insertPayments($paymentData);
            }

            if (!empty($resp->trans_id)) {
                $commentData = [
                    'trans_id' => $resp->trans_id,
                    'comment' => $comment,
                ];
                $comment = $this->lmsRepo->saveTxnComment($commentData);

                if($TransDetail->invoice_disbursed_id){
                    $Obj = new ManualApportionmentHelper($this->lmsRepo);
                    $Obj->intAccrual($TransDetail->invoice_disbursed_id, $TransDetail->trans_date);
                    $Obj->transactionPostingAdjustment($TransDetail->invoice_disbursed_id, $TransDetail->trans_date, $TransDetail->invoiceDisbursed->invoice->program_offer->payment_frequency ?? null, $paymentId);
                    $this->updateInvoiceRepaymentFlag([$TransDetail->invoice_disbursed_id]);
                    $Obj->refundProcess($TransDetail->invoice_disbursed_id);
                }

                $whereActivi['activity_code'] = 'apport_reversal_save';
                $activity = $this->master->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Settled Transaction Reverse Amount (Manage Sanction Cases) '. null;
                    $arrActivity['app_id'] = null;
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['txnInsertData'=>$txnInsertData, 'paymentData'=>$paymentData]), $arrActivity);
                }                 

                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Amount successfully reversed']);
            }
        } catch (Exception $ex) {
             return redirect()->route('apport_settled_view',['payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * Get Unsettled Transactions 
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getUnsettledTrans(int $userId, $payment_date = null){
        
        //$invoiceList = $this->lmsRepo->getUnsettledInvoices(['user_id','=',$userId]);

        $transactionList = new Collection();
        
        $condition = ['user_id' => $userId];
        if(isset($payment_date)){
            $condition['invoiceDisbursed'] = ['int_accrual_start_dt'=> $payment_date];
        }
        $invoiceTrans = $this->lmsRepo->getUnsettledInvoiceTransactions($condition);   
        $invoiceTrans = $invoiceTrans->sortBy('paymentDueDate');

        foreach($invoiceTrans as $trans){
            $transactionList->push($trans);
        }

        return $transactionList; 
    }

    /**
     * Get Settled Transactions
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getSettledTrans($userId){
        return $this->lmsRepo->getSettledTrans($userId);
    }

    /**
     * Get Refund Transactions
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getRefundTrans($userId){
        return $this->lmsRepo->getRefundTrans($userId);
    }
    
    /**
     * Get User Details
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getUserDetails($userId){
        try {
            $lmsUser = $this->userRepo->lmsGetCustomer($userId);
            $woData = $this->lmsRepo->getWriteOff($userId)->first();
            $user = $this->userRepo->find($userId);
            $addresses = $user->biz->address;
            if(!$addresses->isEmpty()){
                $default_address = $addresses[0];
                foreach ($addresses as $key => $addr) {
                if($addr->is_default == 1){
                        $default_address = $addr;
                        break;
                }
                if($addr->address_type == 0){
                        $default_address = $addr;
                }
                }
            }
            if (!empty($default_address)) {
                $fullAddress = $default_address->addr_1 . ' ' . $default_address->addr_2 . ' ' . $default_address->city_name . ' ' . ($default_address->state->name ?? '') . ' ' . $default_address->pin_code ; 
            } 
             
            return [
                'customer_id' => $lmsUser->customer_id,
                'customer_name' => $user->f_name.' '.$user->m_name.' '.$user->l_name,
                'user_id' => $userId,
                'address' => $fullAddress ?? '',
                'biz_entity_name'=>  $user->biz->biz_entity_name ?? '',
                'status_id' => $user->userDetail->lmsUsersLog->status_id ?? null,
                'wo_status_id' =>$woData->status_id ?? null
            ];
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Get Payment Details
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getPaymentDetails($paymentId, $userId){
        try {
            $payment = $this->lmsRepo->getPaymentDetail($paymentId, $userId);
            
            return [
                'payment_id' => $payment->payment_id,
                'amount'=>$payment->amount,
                'date_of_payment'=> $payment->date_of_payment, 
                'paymentmode'=> $payment->paymentmode,
                'transactionno'=> $payment->transactionno,
                'payment_amt' => $payment->amount,
                'is_settled' => $payment->is_settled,
                'created_at' => $payment->created_at,
                'invoice_id' => ($payment->invoice_id)?$payment->invoice->parent_invoice_id:null,
                'isApportPayValid' => $payment->isApportPayValid,
                'user_id' => $payment->user_id,
                'action_type' => $payment->action_type,
                'trans_type' => $payment->trans_type
            ];
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function getUnsettledAmt($userId){
        return Payment::where('user_id','=',$userId)->where('is_settled','=',0)->sum('amount');
    }
    
    /**
     * View Running Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listrunningTrans(Request $request){
        $userId = $request->user_id;

        $transactions = $this->lmsRepo->getRunningTrans($userId);
        return $this->dataProvider->getRunningTrans($request,$transactions);
    }

    /**
     * View Unsettled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listUnsettledTrans(Request $request){
        ini_set("memory_limit", "-1");
        $userId = $request->user_id;
        $paymentSuggestion = $request->paySug;
        $paymentId = null;
        $payment_date = null;
        $payment = null;
        $transactions = null;
        $showSuggestion = $paymentSuggestion?true:false;
        $date_of_payment = null;
        if($request->has('payment_id')){
            $paymentId = $request->payment_id;
            $payment = $this->lmsRepo->getPaymentDetail($paymentId,$userId);    
            $date_of_payment = $payment->date_of_payment;
        }
        
        $transactions = $this->getUnsettledTrans($userId, $date_of_payment);
        return $this->dataProvider->getUnsettledTrans($request,$transactions,$payment,$showSuggestion);
    }
    
    /**
     * View Settled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listSettledTrans(Request $request){
        ini_set("memory_limit", "-1");
        $userId = $request->user_id;  
        $transactions = $this->getSettledTrans($userId);
        return $this->dataProvider->getSettledTrans($request,$transactions);
    }

    /**
     * View Refund Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listRefundTrans(Request $request){
        $userId = $request->user_id;
        $transactions = $this->getRefundTrans($userId);
        return $this->dataProvider->getRefundTrans($request,$transactions);
    }

    /**
     * Unsettled Transaction marked Settled
     */
    public function markSettleConfirmation(ApportionmentRequest $request){
        DB::beginTransaction();
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $amtToSettle = 0;
            $unAppliedAmt = 0;
            $transIds = [];
            $transactions = [];
            $transactionList = [];
            
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $payments = ($request->payment)?$request->payment:[];
            $checks   = ($request->has('check'))?$request->check:[];
            if($request->has('type') && $request->type == 'uploadCsv'){
                $checks  = ($request->has('check'))?unserialize(base64_decode($request->check)):[];
                $payments = ($request->payment)?unserialize(base64_decode($request->payment)):[];
            }
            $userDetails = $this->getUserDetails($userId); 
            $paymentDetails = $this->getPaymentDetails($paymentId,$userId);

            if(!$paymentDetails['isApportPayValid'] || empty($checks) || $paymentDetails['is_settled'] == 1){
                Session::flash('untrans_error', trans('Apportionment is not possible for the selected Payment. Please select valid payment for the unsettled payment screen.'));
                DB::rollback();
                return redirect()->back()->withInput();
            }
            $repaymentAmt = $paymentDetails['amount']; 
            
            foreach ($checks as $Ckey => $Cval) {
                if($Cval === 'on' && $payments[$Ckey] > 0){
                    array_push($transIds, $Ckey);
                }
            }

            if(!empty($transIds)){
                $transactions = Transactions::where('user_id','=',$userId)
                ->whereIn('trans_id',$transIds)
                ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                ->get();
            }

            foreach ($transactions as $trans){

                $bill_date_check = true;
                if ($this->enablePaymentBeforeInvoiceDate === true) {
                    $userInvoiceDate = $trans->userInvTrans->getUserInvoice->created_at ?? NULL;
                    $dateOfPayment = $paymentDetails['date_of_payment'] ?? NULL; 
                    if (isset($userInvoiceDate)) {
                        $userInvoiceDate = date('Y-m-d', strtotime($userInvoiceDate));
                    }
                    if (isset($dateOfPayment)) {
                        $paymentDate = date('Y-m-d', strtotime($dateOfPayment));
                    }
                    if (isset($userInvoiceDate) && preg_replace('#[^0-9]+#', '', $paymentDate) < preg_replace('#[^0-9]+#', '', $userInvoiceDate)) {
                        $bill_date_check = false;
                    }
                }
                $invoiceList[$trans->invoice_disbursed_id] = [
                    'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                    'date_of_payment'=>$paymentDetails['date_of_payment']
                ];     
                $isValid = ((float)$payments[$trans->trans_id] <= (float)$trans->outstanding) && $bill_date_check;
                $transactionList[] = [
                    'trans_id' => $trans->trans_id,
                    'trans_date' => $trans->trans_date,
                    'value_date' => $trans->parenttransdate,
                    'invoice_no' => ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                    'trans_type' => $trans->trans_type,
                    'trans_name' =>  $trans->transName,
                    'total_repay_amt' => (float)$trans->amount,
                    'outstanding_amt' => (float)$trans->outstanding,
                    'payment_date' =>  $paymentDetails['date_of_payment'],
                    'pay' => ($payments[$trans->trans_id])?(float)$payments[$trans->trans_id]:null,
                    'is_valid' => $isValid?1:0
                ];
                if($isValid)
                    $amtToSettle += $payments[$trans->trans_id];
            }

            $unAppliedAmt = $repaymentAmt-$amtToSettle;


            $request->session()->put('apportionment', [
                'user_id' => $userId,
                'payment_id' => $paymentId,
                'payment' => $payments,
                'check' => $checks,
            ]);

            $payment = Payment::find($paymentId);

            if (!$this->verifyUnSettleTransInitiator($payment, $settleConfirmation = true)){
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Someone is already trying to settle transactions')->withInput();
            }

            $payment->update(['is_settled' => Payment::PAYMENT_SETTLED_PROCESSING]);
            
            DB::commit();
            return view('lms.apportionment.markSettledConfirm',[
                'paymentId' => $paymentId,
                'userId' => $userId,
                'payment' => $paymentDetails,
                'userDetails' => $userDetails,
                'transactions' => $transactionList,
                'unAppliedAmt' => $unAppliedAmt,
                'sanctionPageView'=>$sanctionPageView
            ]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    private function verifyUnSettleTransInitiator($payment, $settleConfirmation = false)
    {
        $paymentUpdatedBy = $payment->updated_by;
        if ($payment && $paymentUpdatedBy) {
            if (gettype($paymentUpdatedBy) === 'string') {
                $paymentUpdatedBy = intval($payment->updated_by);
            }

            if ($payment->is_settled == Payment::PAYMENT_SETTLED_PROCESSING && Auth::user()->user_id !== $paymentUpdatedBy) {
                return false;
            }

            if ($settleConfirmation && $payment->is_settled == Payment::PAYMENT_SETTLED_PROCESSED && Auth::user()->user_id !== $paymentUpdatedBy) {
                return false;
            }
        }
        return true;
    }

    public function markSettleSave(Request $request){
        DB::beginTransaction();
        try {
            ini_set('max_execution_time', 2000);
            $payment = Payment::find($request->payment_id);

            if (!$this->verifyUnSettleTransInitiator($payment)) {
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Someone is already trying to settle transactions')->withInput();
            }

            if ($payment && $payment->is_settled == Payment::PAYMENT_SETTLED_PROCESSED) {
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Someone is already trying to settle transactions')->withInput();
            }

            if ($payment && $payment->is_settled == Payment::PAYMENT_SETTLED) {
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Transactions already settled')->withInput();
            }

            $payment->update(['is_settled' => Payment::PAYMENT_SETTLED_PROCESSED]);

            $Obj = new ManualApportionmentHelper($this->lmsRepo);
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            if($request->session()->has('apportionment')){
                $amtToSettle = 0; 
                $transIds = [];
                $userId = $request->session()->get('apportionment.user_id');
                $paymentId = $request->session()->get('apportionment.payment_id');
                $payments = $request->session()->get('apportionment.payment');
                $checks = $request->session()->get('apportionment.check');

                $paymentDetails = $this->getPaymentDetails($paymentId,$userId);
                if(!$paymentDetails['isApportPayValid'] || empty($checks) || $paymentDetails['is_settled'] == 1){
                    Session::flash('error', trans('Apportionment is not possible for the selected Payment. Please select valid payment for the unsettled payment screen.'));
                    DB::rollback();
                    return redirect()->route('unsettled_payments');
                }
                $repaymentAmt = (float) $paymentDetails['amount']; 
                $invoiceList = [];
                $transactionList = [];

                foreach ($checks as $Ckey => $Cval) {
                    if($Cval === 'on' && $payments[$Ckey] > 0){
                        array_push($transIds, $Ckey);
                    }
                }

                $transactions = [];
                if(!empty($transIds)){
                    $transactions = Transactions::where('user_id','=',$userId)
                    ->whereIn('trans_id',$transIds)
                    ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                    ->get();
                }

                $transactionList[] = [
                    'payment_id' => $paymentId,
                    'apportionment_id'=> $paymentId,
                    'link_trans_id' => null,
                    'parent_trans_id' => null,
                    'invoice_disbursed_id' => null,
                    'user_id' => $userId,
                    'trans_date' => $paymentDetails['date_of_payment'],
                    'amount' => 0,
                    'entry_type' => 1,
                    'soa_flag' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.REPAYMENT'),
                    'trans_mode' => 2,
                ];

                foreach ($transactions as $trans){
                    $bill_date_check = true;
                    if ($this->enablePaymentBeforeInvoiceDate === true) {
                        $userInvoiceDate = $trans->userInvTrans->getUserInvoice->created_at ?? NULL;
                        $dateOfPayment = $paymentDetails['date_of_payment'] ?? NULL; 
                        if (isset($userInvoiceDate)) {
                            $userInvoiceDate = date('Y-m-d', strtotime($userInvoiceDate));
                        }
                        if (isset($dateOfPayment)) {
                            $paymentDate = date('Y-m-d', strtotime($dateOfPayment));
                        }
                        if (isset($userInvoiceDate) && preg_replace('#[^0-9]+#', '', $paymentDate) < preg_replace('#[^0-9]+#', '', $userInvoiceDate)) {
                            $bill_date_check = false;
                        }
                    }
                    
                    if($trans->invoice_disbursed_id){

                        $invoiceList[$trans->invoice_disbursed_id] = [
                            'invoice_id'=> $trans->invoiceDisbursed->invoice_id,
                            'payment_due_date'=>$trans->invoiceDisbursed->payment_due_date,
                            'grace_period'=>$trans->invoiceDisbursed->grace_period,
                            'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                            'date_of_payment'=>$paymentDetails['date_of_payment'],
                            'payment_frequency' => $trans->invoiceDisbursed->invoice->program_offer->payment_frequency,
                        ];             
                    }

                    $isValid = ((float)$payments[$trans->trans_id] <= (float)$trans->outstanding) && $bill_date_check;
                    if($isValid){
                        $transactionList[] = [
                            'payment_id' => $paymentId,
                            'apportionment_id'=> $paymentId,
                            'link_trans_id' => $trans->trans_id,
                            'parent_trans_id' => $trans->trans_id,
                            'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                            'user_id' => $userId,
                            'trans_date' => $paymentDetails['date_of_payment'],
                            'amount' => $payments[$trans->trans_id],
                            'entry_type' => 1,
                            'soa_flag' => 1,
                            'trans_type' => $trans->trans_type,
                            'trans_mode' => 2,
                            'is_transaction'=>$trans->is_transaction
                        ];
                        $amtToSettle += $payments[$trans->trans_id];
                    }
                }

                $unAppliedAmt = round(($repaymentAmt-$amtToSettle),2);

                if((float) round($amtToSettle,2) > (float) round($repaymentAmt,2)){
                    Session::flash('error', trans('error_messages.apport_invalid_unapplied_amt'));
                    DB::rollback();
                    return redirect()->route('unsettled_payments')->withInput();
                }

                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                    /** Mark Payment Settled */
                    $payment = Payment::find($paymentId);
                    $payment->is_settled = Payment::PAYMENT_SETTLED;
                    $payment->save();
                }

                $transactionList = [];
                
                if($unAppliedAmt > 0){
                    $transactionList[] = [
                        'payment_id' => $paymentId,
                        'apportionment_id'=> $paymentId,
                        'link_trans_id' => null,
                        'parent_trans_id' => null,
                        'invoice_disbursed_id' => null,
                        'user_id' => $userId,
                        'trans_date' => $paymentDetails['date_of_payment'],
                        'amount' => $unAppliedAmt,
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.NON_FACTORED_AMT'),
                        'trans_mode' => 2,
                    ];
                }
                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                }
                foreach ($invoiceList as $invDisb) {
                    $date_of_payment = $invDisb['date_of_payment'];
                    if( (strtotime($invDisb['payment_due_date']) <= strtotime($invDisb['date_of_payment']) )  && ( strtotime($invDisb['date_of_payment']) <= strtotime($invDisb['payment_due_date'] . "+". $invDisb['grace_period']." days"))){
                        $date_of_payment = $invDisb['payment_due_date'];
                    }
                    
                    $Obj->intAccrual($invDisb['invoice_disbursed_id'], $date_of_payment);
                    $Obj->transactionPostingAdjustment($invDisb['invoice_disbursed_id'], $invDisb['date_of_payment'], $invDisb['payment_frequency'], $paymentId, $useApporCol = true);
                    $this->updateInvoiceRepaymentFlag([$invDisb['invoice_disbursed_id']]);
                    $Obj->refundProcess($invDisb['invoice_disbursed_id']);
                }
                
                if($paymentId){
                    InterestAccrualTemp::where('payment_id',$paymentId)->delete();
                }

                $whereActivi['activity_code'] = 'apport_mark_settle_save';
                $activity = $this->master->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Mark Settle Save (Manage Payment) '. null;
                    $arrActivity['app_id'] = null;
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['request'=>$request->all(), 'invoiceList'=>$invoiceList, 'transactionList'=>$transactionList]), $arrActivity);
                }                 
                
                $request->session()->forget('apportionment');
                DB::commit();
                return redirect()->route('apport_settled_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully marked settled']);
            }
        } catch (Exception $ex) {
            $payment = Payment::find($request->payment_id);
            if ($payment)
            $payment->update(['is_settled' => Payment::PAYMENT_SETTLED_PENDING]);

            DB::rollback();
            return redirect()->route('unsettled_payments')->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    
    /**
     * save reversal Detail
     * @param Request $request
     * @return array
     */
    public function saveRunningDetail(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'check' =>'required|array'
            ]);
            
            if ($validator->fails()) {
                Session::flash('error', $validator->messages()->first());
                return redirect()->back()->withInput();
            }

            $paymentAppor = PaymentApportionment::checkApportionmentHold($request->user_id);
            if ($paymentAppor) {
                \Session::flash('error', 'You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.');
                return back();
            }

            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $checks = ($request->has('check'))?$request->check:[];

            $transRunningIds = array_keys($checks);
            $requestedAmount = array_sum($checks);
            $unsettledAmt = round($this->getUnsettledAmt($userId),2);

            if($requestedAmount>$unsettledAmt){
                Session::flas('error', "Requested Amount: $requestedAmount is greater than Unsettled Amount: $unsettledAmt");
                return redirect()->back()->withInput();
            }

            $transactionsRunning = TransactionsRunning::where('user_id','=',$userId)
            //->where('is_posted','=',0)
            ->whereIn('trans_running_id',$transRunningIds)->get();
            
            $transactionList = [];

            foreach ($transactionsRunning as $key => $trans) {
                $transactionList[] = [
                    'payment_id' => null,
                    'link_trans_id' => null,
                    'parent_trans_id' => null,
                    'trans_running_id'=> $trans->trans_running_id,
                    'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                    'user_id' => $trans->user_id,
                    'trans_date' => $trans->trans_date,
                    'amount' => $trans->outstanding,
                    'entry_type' => $trans->entry_type,
                    'soa_flag' => 1,
                    'trans_type' => $trans->trans_type,
                    'trans_mode' => 2,
                ];
            }
            if(!empty($transactionList)){
                foreach ($transactionList as $key => $newTrans) {
                    $this->lmsRepo->saveTransaction($newTrans);
                }
            }

            $whereActivi['activity_code'] = 'apport_running_save';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Mark Posted Running Transsaction (Manage Sanction Cases) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($transactionList), $arrActivity);
            }             
            
            return redirect()->route('apport_unsettled_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully marked posted']);
        } catch (Exception $ex) {
             return redirect()->route('apport_settled_view',['payment_id' => $paymentId, 'user_id' =>$userId, 'sanctionPageView'=>$sanctionPageView])->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

     /* use function for the manage sention tabs */ 
    
    public  function  getUserLimitDetais($user_id){
        try {
            $totalLimit = 0;
            $totalCunsumeLimit = 0;
            $consumeLimit = 0;
            $transactions = 0;
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $application = $this->appRepo->getCustomerApplications($user_id);
            $anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);
            foreach ($application as $key => $app) {
                if (isset($app->prgmLimits)) {
                    foreach ($app->prgmLimits as $value) {
                        $totalLimit += $value->limit_amt;
                    }
                }
                if (isset($app->acceptedOffers)) {
                    foreach ($app->acceptedOffers as $value) {
                        $totalCunsumeLimit += $value->prgm_limit_amt;
                    }
                }
            }
            $userInfo->total_limit = number_format($totalLimit);
            $userInfo->consume_limit = number_format($totalCunsumeLimit);
            $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
            
            $data['userInfo'] = $userInfo;
            $data['application'] = $application;
            $data['anchors'] = $anchors;
            return $data;
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function updateInvoiceRepaymentFlag(array $invDisbId){
        $invDisbs = InvoiceDisbursed::whereIn('invoice_disbursed_id',$invDisbId)->get();
        foreach($invDisbs as $invd){
            // Update Invoice Disbursed Accrual Detail
            InvoiceDisbursedDetail::updateDailyInterestAccruedDetails($invd);
           //$flag = $this->lmsRepo->getInvoiceSettleStatus($invd->invoice_id);
            
            $transRunningOut = TransactionsRunning::where('invoice_disbursed_id',$invd)->get()->sum('outstanding');
            $transOut = Transactions::where('invoice_disbursed_id',$invd)->where('entry_type','0')->whereIn('trans_type',[9,16,33])->whereNull('parent_trans_id')->sum('outstanding');
            $transAmt = Transactions::where('invoice_disbursed_id',$invd)->where('entry_type','0')->whereIn('trans_type',[9,16,33])->whereNull('parent_trans_id')->sum('amount');
            $prinRepayAmt = Transactions::where('invoice_disbursed_id',$invd)->where('entry_type','1')->whereIn('trans_type',[16])->sum('settled_outstanding');
            $is_settled = false;

            if($transRunningOut+$transOut == 0){
                $is_settled = true;
            }
            $inv = BizInvoice::find($invd->invoice_id);
            if($is_settled){
                $inv->is_repayment = 1;
                $inv->status_id = 15;
                $inv->repayment_amt = ($transAmt - $transOut);
                $inv->principal_repayment_amt = $prinRepayAmt;
            }else{
                $inv->is_repayment = 0;
                $inv->status_id = 12;
                $inv->repayment_amt = ($transAmt - $transOut);
                $inv->principal_repayment_amt = $prinRepayAmt;
            }
            $inv->save();
        }
    }

    public function markWriteOffConfirmation(Request $request){
        DB::beginTransaction();
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $amtToSettle = 0;
            $unAppliedAmt = 0;
            $transIds = [];
            $transactions = [];
            $transactionList = [];
            $amtToWriteOff = 0;
            $userId = $request->user_id;
           
            $checks   = ($request->has('check'))?$request->check:[];

            $userDetails = $this->getUserDetails($userId); 

            foreach ($checks as $Ckey => $Cval) {
                if($Cval === 'on'){
                    array_push($transIds, $Ckey);
                }
            }

            if(!empty($transIds)){
                $transactions = Transactions::where('user_id','=',$userId)
                ->whereIn('trans_id',$transIds)
                ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                ->get();
            }

            foreach ($transactions as $trans){ 
                $transactionList[] = [
                    'trans_id' => $trans->trans_id,
                    'trans_date' => $trans->trans_date,
                    'value_date' => $trans->parenttransdate,
                    'invoice_no' =>($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                    'trans_type' => $trans->trans_type,
                    'trans_name' =>  $trans->transName,
                    'total_repay_amt' => (float)$trans->amount,
                    'outstanding_amt' => (float)$trans->outstanding,
                    'is_valid' => 1
                ];
                $amtToWriteOff += $trans->outstanding;
            }

            $request->session()->put('writeoff', [
                'user_id' => $userId,
                'check' => $checks,
            ]);
        
            DB::commit();
            return view('lms.apportionment.markWriteOffConfirm',[
                'userId' => $userId,
                'userDetails' => $userDetails,
                'transactions' => $transactionList,
                'sanctionPageView'=>$sanctionPageView
            ]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function markWriteOffSave(Request $request){
        DB::beginTransaction();
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            if($request->session()->has('writeoff')){
                $woAmount = 0; 
                $transIds = [];
                $userId = $request->session()->get('writeoff.user_id');
                $checks = $request->session()->get('writeoff.check');

                $transactionList = [];

                foreach ($checks as $Ckey => $Cval) {
                    if($Cval === 'on'){
                        array_push($transIds, $Ckey);
                    }
                }

                $transactions = [];
                if(!empty($transIds)){
                    $transactions = Transactions::where('user_id','=',$userId)
                    ->whereIn('trans_id',$transIds)
                    ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                    ->get();
                }

                foreach ($transactions as $trans){  
                    $transactionList[] = [
                        'payment_id' => null,
                        'link_trans_id' => $trans->trans_id,
                        'parent_trans_id' => $trans->trans_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $trans->user_id,
                        'trans_date' => date('Y-m-d H:i:s'),
                        'amount' => $trans->outstanding,
                        'settled_outstanding' => $trans->outstanding,
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.WRITE_OFF'),
                        'trans_mode' => 2,
                    ];
                    $woAmount += $trans->outstanding;
                }

                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                }

                if($userId){
                    $woData = $this->lmsRepo->getWriteOff($userId)->first();
                    $woLogData = [];
                    $woLogData['wo_req_id'] = $woData->wo_req_id;
                    $woLogData['status_id'] = config('lms.WRITE_OFF_STATUS.TRANSACTION_SETTLED');
                    $woStatusLogId = $this->lmsRepo->saveWriteOffReqLog($woLogData);
                    $updateData = [];
                    $updateData['wo_status_log_id'] = $woStatusLogId->wo_status_log_id;
                    $updateData['amount'] = $woAmount+$woData->amount;
                    $this->lmsRepo->updateWriteOffReqById((int) $woData->wo_req_id, $updateData);
                }
                
                $request->session()->forget('writeoff');
                DB::commit();
                return redirect()->route('apport_settled_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully marked Write Off']);
            }
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Refund Transaction marked Adjusted
     */
    public function markAdjustmentConfirmation(AdjustmentRequest $request){
        DB::beginTransaction();
        try {
            $paymentAppor = PaymentApportionment::checkApportionmentHold($request->user_id);
            if ($paymentAppor) {
                \Session::flash('error', 'You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.');
                DB::rollback();
                return back();
            }
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $amtToSettle = 0;
            $unAppliedAmt = 0;
            $transIds = [];
            $transactions = [];
            $transactionList = [];
            
            $userId = $request->user_id;
            $refunds = ($request->refund)?$request->refund:[];
            $checks   = ($request->has('check'))?$request->check:[];

            $userDetails = $this->getUserDetails($userId); 
           
            
            foreach ($checks as $Ckey => $Cval) {
                if($Cval === 'on' && $refunds[$Ckey] > 0){
                    array_push($transIds, $Ckey);
                }
            }

            if(!empty($transIds)){
                $transactions = Transactions::where('user_id','=',$userId)
                ->whereIn('trans_id',$transIds)
                ->orderBy("trans_date", 'asc')
                ->get();
            }

            foreach ($transactions as $trans){
                $invoiceList[$trans->invoice_disbursed_id] = [
                    'invoice_disbursed_id'=>$trans->invoice_disbursed_id
                ];     
                $transactionList[] = [
                    'trans_id' => $trans->trans_id,
                    'trans_date' => $trans->trans_date,
                    'value_date' => $trans->parenttransdate,
                    'invoice_no' => ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                    'trans_type' => $trans->trans_type,
                    'trans_name' =>  $trans->transName,
                    'total_repay_amt' => (float)$trans->amount,
                    'outstanding_amt' => (float)$trans->refundoutstanding,
                    'refund' => ($refunds[$trans->trans_id])?(float)$refunds[$trans->trans_id]:null,
                    'is_valid' => ((float)$refunds[$trans->trans_id] <= (float)$trans->refundoutstanding)?1:0
                ];
                $amtToSettle += $refunds[$trans->trans_id];
            }

            $request->session()->put('adjustment', [
                'user_id' => $userId,
                'refund' => $refunds,
                'check' => $checks,
            ]);
        
            DB::commit();
            return view('lms.apportionment.markAdjustmentConfirm',[
                'userId' => $userId,
                'userDetails' => $userDetails,
                'transactions' => $transactionList,
                'sanctionPageView'=>$sanctionPageView,
                'adjustableAmt'=>$amtToSettle
            ]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function markAdjustmentSave(Request $request){
        DB::beginTransaction();
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            if($request->session()->has('adjustment')){
                $amtToSettle = 0; 
                $transIds = [];
                $userId = $request->session()->get('adjustment.user_id');
                $refunds = $request->session()->get('adjustment.refund');
                $checks = $request->session()->get('adjustment.check');
 
                $invoiceList = [];
                $transactionList = [];

                foreach ($checks as $Ckey => $Cval) {
                    if($Cval === 'on' && $refunds[$Ckey] > 0){
                        array_push($transIds, $Ckey);
                    }
                }

                $transactions = [];
                if(!empty($transIds)){
                    $transactions = Transactions::where('user_id','=',$userId)
                    ->whereIn('trans_id',$transIds)
                    ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                    ->get();
                }

                $payments = [];
                foreach ($transactions as $trans){  
                    $transactionList[] = [
                        'payment_id' => NULL,
                        'link_trans_id' => $trans->trans_id,
                        'parent_trans_id' => $trans->parent_trans_id ?? $trans->trans_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $trans->user_id,
                        'trans_date' => date('Y-m-d H:i:s'),
                        'amount' => $refunds[$trans->trans_id],
                        'entry_type' => 0,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.ADJUSTMENT'),
                        'trans_mode' => 2,
                    ];
                    if(!isset($payments[$trans->trans_date]['amount'])){
                        $payments[$trans->trans_date]['amount'] = 0;
                    }
                    $payments[$trans->trans_date]['amount'] += $refunds[$trans->trans_id];
                    
                }

                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                }
                
                foreach ($payments as $transDate => $payment) {
                    $paymentData = [
                        'user_id' => $transactions[0]->user_id,
                        'biz_id' => $transactions[0]->payment->biz_id,
                        'virtual_acc' => $transactions[0]->payment->virtual_acc,
                        'action_type' => 5,
                        'trans_type' => config('lms.TRANS_TYPE.ADJUSTMENT'),
                        'amount' => $payment['amount'],
                        'date_of_payment' => $transDate,
                        'is_manual' => '1',
                        'generated_by' => 1,
                        'is_refundable' => 1
                    ];
                    $paymentId = Payment::insertPayments($paymentData);
                }
                $whereActivi['activity_code'] = 'apport_mark_adjustment_save';
                $activity = $this->master->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Adjustment Transaction Confirm (Manage Sanction Cases) '. null;
                    $arrActivity['app_id'] = null;
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['transactions'=>$transactions, 'payments'=>$payments]), $arrActivity);
                }   
                $request->session()->forget('apportionment');
                DB::commit();
                return redirect()->route('apport_refund_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully Mark Adjusted']);
            }
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
    
    private function processApportionmentUndoTrans($payment, $result)
    {
        
        ini_set('max_execution_time', -1);
        ini_set("memory_limit", -1);
        $userId             =   $payment->user_id;
        $paymentId          =   $payment->payment_id;

        $query              =   Transactions::where('apportionment_id', $paymentId)
                                            ->where('user_id', $userId);
        $query1             =   clone $query;
        $data               =   $query->whereNotNull('invoice_disbursed_id')
                                    ->distinct('invoice_disbursed_id')
                                    ->pluck('invoice_disbursed_id', 'invoice_disbursed_id')
                                    ->toArray();

        $uniqInvDisbIds     =   array_keys($data);
        $paymentDate        =   $payment->date_of_payment;
        $from               =   Carbon::parse($paymentDate)->format('Y-m-d');
        $to                 =   now()->format('Y-m-d');
        $interestAccruals   =   InterestAccrual::whereBetween('interest_date', [$from, $to])
                                            ->whereIn('invoice_disbursed_id', $uniqInvDisbIds)
                                            ->get();
        $interestAccruals->each->delete();

        $maxTransId         =   $query1->max('trans_id');
        $transactions       =   $query1->get();
        $transactions->each->delete();

        if ($maxTransId) {
            $trans          =   Transactions::whereIn('invoice_disbursed_id', $uniqInvDisbIds)
                                            ->where('user_id', $userId)
                                            ->where('trans_id', '>', $maxTransId)
                                            ->whereNotNull('invoice_disbursed_id')
                                            ->whereNull('payment_id')
                                            ->whereNull('apportionment_id')
                                            ->get();
            $trans->each->delete();
            $result = true;
        }
        if ($result) {
            //CustomerTransactionSOA::updateTransactionSOADetails($userId);
            $Obj  = new ManualApportionmentHelper($this->lmsRepo);
            foreach ($data as $invDisb) {
                $Obj->intAccrual($invDisb, $paymentDate);
                $Obj->transactionPostingAdjustment($invDisb, NULL, NULL, NULL);
                $this->updateInvoiceRepaymentFlag([$invDisb]);
                $Obj->refundProcess($invDisb);
            }
        }

        return $result;
    }

    private function apportionmentUndoProcess($payment){
        $result = false;
        $error  = null;
        $query  = Transactions::where('payment_id', $payment->payment_id);
        $transactions = $query->get();
        
        if($transactions){
            foreach($transactions as $trans){
                if($trans->refundReqTrans){
                    $error = "Apportionment Reversal has been blocked due to Refund Process.";
                }
                if($trans->entry_type == 0 && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.ADJUSTMENT'),config('lms.TRANS_TYPE.REFUND')])){
                    $error = "Apportionment Reversal has been blocked due to ".$trans->transName." of amount ".$trans->amount.".";
                }
            }
        }
        
        if(!$error){
            $result = $this->processApportionmentUndoTrans($payment, $result);
            return ['status' => $result];
        }else{
            return ['status' => $result, 'error' => $error];
        }        
    }

    public function undoApportionment(Request $request){
        DB::beginTransaction();
        try {
			$paymentId = $request->get('payment_id');
			if($paymentId){
				$payment = Payment::find($paymentId);
				if($payment){
                    if($payment->is_settled == '1' && (($payment->action_type == '1' && $payment->trans_type == '17') ||($payment->action_type == '3' && $payment->trans_type == '7')) && $payment->validRevertPayment ){
						$aporUndoPro = self::apportionmentUndoProcess($payment);
                        if($aporUndoPro['status']){
                            $payment->is_settled = '0';
                            $payment->save();

                            $whereActivi['activity_code'] = 'undo_apportionment';
                            $activity = $this->master->getActivity($whereActivi);
                            if(!empty($activity)) {
                                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                                $activity_desc = 'Undo Apportionment (Manage Payment)';
                                $arrActivity['app_id'] = null;
                                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
                            }                               
                            DB::commit();
                            return response()->json(['status' => 1,'message' => 'Successfully Apportionment Reverted']); 
                        }else{
                            return response()->json(['status' => 0,'message' => $aporUndoPro['error']]);
                        }
					}
					else{
						return response()->json(['status' => 0,'message' => 'Invalid Request: Apportionment cannot be reverted']);
					}
				}
				return response()->json(['status' => 0,'message' => 'Record Not Found / Apportionment already reverted']);
			}
			return response()->json(['status' => 0,'message' => 'Invalid Request: Payment details missing.']);
        } catch (Exception $ex) {
            DB::rollback();
			return response()->json(['status' => 0,'message' => Helpers::getExceptionMessage($ex)]); 
		}
    }

    public function viewUnsettledTDSTrans(Request $request){
        try {
            $oldData = [];
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $oldData['payment'] = (old('payment'))?old('payment'):[];
            $oldData['check'] = (old('check'))?old('check'):[];
            $userId = $request->user_id;
            $paymentId = null;
            $payment = null;
            $payment_amt = 0;
            $userDetails = $this->getUserDetails($userId); 
            if($request->has('payment_id') && $request->payment_id){
                $paymentId = $request->payment_id;
                $payment = $this->getPaymentDetails($paymentId,$userId); 
                $payment_amt = $payment['payment_amt']; 
               /* $Obj = new ManualApportionmentHelperTemp($this->lmsRepo);
                $Obj->setTempInterest($paymentId);*/
                if(!$payment['isApportPayValid']){
                    Session::flash('error', trans('Please select Valid Payment!'));
                    return redirect()->back()->withInput();
                }
            }

            $result = $this->getUserLimitDetais($userId);
            $paymentAppor = PaymentApportionment::checkApportionmentHold($userId);
            return view('lms.apportionment.unsettledTDSTransactions')
            ->with('paymentId', $paymentId)  
            ->with('userId', $userId)
            ->with('payment',$payment) 
            ->with('payment_amt', $payment_amt)
            ->with('userDetails', $userDetails)
            ->with('oldData',$oldData)
            ->with('sanctionPageView',$sanctionPageView)
            ->with(['userInfo' =>  $result['userInfo'],
                    'application' => $result['application'],
                    'anchors' =>  $result['anchors']
            ])
            ->with('paymentAppor', $paymentAppor);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    public function listUnsettledSettledTDSTrans(Request $request){
        $userId = $request->user_id;
        $paymentId = null;
        $payment_date = null;
        $payment = null;
        $transactions = null;
        if($request->has('payment_id')){
            $paymentId = $request->payment_id;
            $payment = $this->lmsRepo->getPaymentDetail($paymentId,$userId);    
        }
        if(!$transactions){
            $transactions = $this->lmsRepo->getUnsettledSettledTDSTrans($userId);
        }
        return $this->dataProvider->getUnsettledSettledTDSTrans($request,$transactions,$payment);
    }

    public function markSettleConfirmationTDS(ApportionmentRequest $request){
        //dd($request->all());
        try {
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $amtToSettle = 0;
            $unAppliedAmt = 0;
            $transIds = [];
            $transactions = [];
            $transactionList = [];
            
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $payments = ($request->payment)?$request->payment:[];
            $checks   = ($request->has('check'))?$request->check:[];
            
            $userDetails = $this->getUserDetails($userId); 
            $paymentDetails = $this->getPaymentDetails($paymentId,$userId);

            if(!$paymentDetails['isApportPayValid']){
                if(!$paymentDetails['isApportPayValid']['isValid']){
                    Session::flash('error', trans('Apportionment is not possible for the selected Payment. Please select valid payment for the unsettled payment screen.'));
                    return redirect()->back()->withInput();
                }
            }
            $repaymentAmt = (float) round($paymentDetails['amount'],2); 
            
            foreach ($checks as $Ckey => $Cval) {
                if($Cval === 'on' && $payments[$Ckey] > 0){
                    array_push($transIds, $Ckey);
                }
            }

            if(!empty($transIds)){
                $transactions = Transactions::where('user_id','=',$userId)
                ->whereIn('trans_id',$transIds)
                ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                ->get();
            }

            $totalOutstanding = 0;

            foreach ($transactions as $trans){
                $totalOutstanding += (float)$trans->TDSAmount;
                $invoiceList[$trans->invoice_disbursed_id] = [
                    'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                    'date_of_payment'=>$paymentDetails['date_of_payment']
                ];     
                $transactionList[] = [
                    'trans_id' => $trans->trans_id,
                    'trans_date' => $trans->trans_date,
                    'value_date' => $trans->parenttransdate,
                    'bill_type' => $trans->bill_type,
                    'invoice_no' => $trans->invoice_no,
                    'trans_type' => $trans->trans_type,
                    'trans_name' =>  $trans->transName,
                    'total_repay_amt' => (float)$trans->amount,
                    'outstanding_amt' => (float)$trans->TDSAmount,
                    'payment_date' =>  $paymentDetails['date_of_payment'],
                    'pay' => ($payments[$trans->trans_id])?(float)$payments[$trans->trans_id]:null,
                    'is_valid' => ((float)$payments[$trans->trans_id] <= (float)$trans->TDSAmount)?1:0
                ];
                $amtToSettle += $payments[$trans->trans_id];
            }

            $amtToSettle = (float) round($amtToSettle,2);
            $unAppliedAmt = (float) round(($repaymentAmt - $amtToSettle),2);

            $totalOutstanding = (float) round($totalOutstanding,2);
            //dd($paymentDetails);
            if($paymentDetails['action_type'] == '3' &&  $paymentDetails['trans_type'] == '7'){
                if($unAppliedAmt > 0 && $totalOutstanding > 0){
                    Session::flash('error', trans('You cannot settle partial TDS amount, please use full TDS amount for settlement.'));
                    return redirect()->back()->withInput();
                }
                if($unAppliedAmt == $repaymentAmt){
                    Session::flash('error', trans('You cannot settle TDS amount!'));
                    return redirect()->back()->withInput();
                }
            }

            $request->session()->put('apportionment', [
                'user_id' => $userId,
                'payment_id' => $paymentId,
                'payment' => $payments,
                'check' => $checks,
            ]);

            $payment = Payment::find($paymentId);

            if (!$this->verifyUnSettleTransInitiator($payment, $settleConfirmation = true))
                return redirect()->route('unsettled_payments')->withErrors('Someone is already trying to settle TDS transactions')->withInput();

            $payment->update(['is_settled' => Payment::PAYMENT_SETTLED_PROCESSING]);
        
            return view('lms.apportionment.markSettledConfirmTDS',[
                'paymentId' => $paymentId,
                'userId' => $userId,
                'payment' => $paymentDetails,
                'userDetails' => $userDetails,
                'transactions' => $transactionList,
                'unAppliedAmt' => $unAppliedAmt,
                'sanctionPageView'=>$sanctionPageView
            ]);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function TDSMarkSettleSave(Request $request){
        DB::beginTransaction();
        try {
            $payment = Payment::find($request->payment_id);

            if (!$this->verifyUnSettleTransInitiator($payment)) {
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Someone is already trying to settle transactions')->withInput();
            }

            if ($payment && $payment->is_settled == Payment::PAYMENT_SETTLED_PROCESSED) {
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Someone is already trying to settle transactions')->withInput();
            }

            if ($payment && $payment->is_settled == Payment::PAYMENT_SETTLED) {
                DB::rollback();
                return redirect()->route('unsettled_payments')->withErrors('Transactions already settled')->withInput();
            }

            $payment->update(['is_settled' => Payment::PAYMENT_SETTLED_PROCESSED]);

            $Obj = new ManualApportionmentHelper($this->lmsRepo);
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            if($request->session()->has('apportionment')){
                $amtToSettle = 0; 
                $transIds = [];
                $userId = $request->session()->get('apportionment.user_id');
                $paymentId = $request->session()->get('apportionment.payment_id');
                $payments = $request->session()->get('apportionment.payment');
                $checks = $request->session()->get('apportionment.check');

                $paymentDetails = $this->getPaymentDetails($paymentId,$userId);
                $repaymentAmt = (float) $paymentDetails['amount']; 
                
                $invoiceDisbursedList = [];
                $transactionList = [];

                foreach ($checks as $Ckey => $Cval) {
                    if($Cval === 'on' && $payments[$Ckey] > 0){
                        array_push($transIds, $Ckey);
                    }
                }

                $transactions = [];
                if(!empty($transIds)){
                    $transactions = Transactions::where('user_id','=',$userId)
                    ->whereIn('trans_id',$transIds)
                    ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                    ->get();
                }

                $totalOutstanding = 0;
                $refundTrans = [];
                foreach ($transactions as $trans){

                    $totalOutstanding += (float)$trans->outstanding; 
                    
                    $transactionList[] = [
                        'payment_id' => $paymentId,
                        'apportionment_id'=> $paymentId,
                        'link_trans_id' => $trans->trans_id,
                        'parent_trans_id' => $trans->trans_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $userId,
                        'trans_date' => $paymentDetails['date_of_payment'],
                        'amount' => $payments[$trans->trans_id],
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.TDS'),
                        'trans_mode' => 2,
                        'tds_per' => $trans->TDSRate,
                    ];
                    $amtToSettle += $payments[$trans->trans_id];
                }
                $unAppliedAmt = round(($repaymentAmt-$amtToSettle),2);

                if($paymentDetails['action_type'] == '6' &&  $paymentDetails['trans_type'] == '14' && $unAppliedAmt > 0 && $totalOutstanding > 0
                    || ($paymentDetails['action_type'] == 3 && $unAppliedAmt > 0)
                ){
                    Session::flash('error', trans('Please use whole unapplied amount.'));
                    DB::rollback();
                    return redirect()->back()->withInput();
                }

                if(round($amtToSettle, 2) > round($repaymentAmt, 2)){
                    Session::flash('error', trans('error_messages.apport_invalid_unapplied_amt'));
                    DB::rollback();
                    return redirect()->back()->withInput();
                }

                if($unAppliedAmt > 0){
                    $transactionList[] = [
                        'payment_id'           => $paymentId,
                        'apportionment_id'     => $paymentId,
                        'link_trans_id'        => null,
                        'parent_trans_id'      => null,
                        'invoice_disbursed_id' => null,
                        'user_id'              => $userId,
                        'trans_date'           => $paymentDetails['date_of_payment'],
                        'amount'               => $unAppliedAmt,
                        'entry_type'           => 1,
                        'soa_flag'             => 1,
                        'trans_type'           => config('lms.TRANS_TYPE.NON_FACTORED_AMT'),
                        'trans_mode'           => 2,
                    ];
                }
                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                        if($newTrans['invoice_disbursed_id']){
                            $invoiceDisbursedList[$newTrans['invoice_disbursed_id']] = $newTrans['invoice_disbursed_id'];
                        }
                    }
                    /** Mark Payment Settled */
                    $payment = Payment::find($paymentId);
                    $payment->is_settled = Payment::PAYMENT_SETTLED;
                    $payment->save();
                }

                /* Refund Process Start */
                if(!empty($invoiceDisbursedList)){
                    foreach($invoiceDisbursedList as $invDisbId){
                        $this->updateInvoiceRepaymentFlag([$invDisbId]);
                        $Obj->refundProcess($invDisbId);
                    }
                }
                /* Refund Process End */

                $request->session()->forget('apportionment');
                DB::commit();
                return redirect()->route('apport_settled_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully marked settled']);
            }
        } catch (Exception $ex) {
            $payment = Payment::find($request->payment_id);
            if ($payment)
                $payment->update(['is_settled' => Payment::PAYMENT_SETTLED_PROCESSING]);
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function downloadApportUnsettledTrans(Request $request){
        try {
            set_time_limit(0);
            $userId = $request->user_id;
            $paymentId = null;
            $payment_date = null;
            $payment = null;
            $transactions = null;
            $unInvCnt = BizInvoice::where('supplier_id', $userId)->whereHas('invoice_disbursed')->where('is_repayment', '0')->count();
            $showSuggestion = ($unInvCnt <= 50) ? true : false;
            //dd($showSuggestion);
            $date_of_payment = null;
            if ($request->has('payment_id')) {
                $paymentId = $request->payment_id;
                $payment = $this->lmsRepo->getPaymentDetail($paymentId, $userId);
                $date_of_payment = $payment->date_of_payment;
            }
            $datetime = \Carbon\Carbon::now("UTC");
            $token = $userId . '|' . $paymentId . '|' . $date_of_payment . '|' . $datetime;
            $transactions = $this->getUnsettledTrans($userId, $date_of_payment);
            $columns = ["Trans ID", "Trans Date", "Invoice No", "Trans Type", "Total Repay Amt", "Outstanding Amt", "Suggested Outstanding Amt", "Payment"];
            $unTransactions = [];
            foreach ($transactions as $trans) {
                $invoiceNo = '-';
                if ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id) {
                    $invoiceNo = $trans->invoiceDisbursed->invoice->invoice_no;
                }
                $accuredInterestR = '';
                if($showSuggestion && $payment && in_array($trans->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
                    $accuredInterest = $trans->tempInterest;
                    if(!is_null($accuredInterest) && !($trans->invoiceDisbursed->invoice->program_offer->payment_frequency == 1 && $trans->invoiceDisbursed->invoice->program->interest_borne_by == 1 && $trans->trans_type == config('lms.TRANS_TYPE.INTEREST'))){
                           $accuredInterestR = number_format($accuredInterest,2);
                    }
                }
                $totalPay = $trans->amount;
                $outStanding = $trans->outstanding;
                $unTransactions[] = [
                    'Trans ID' => Helpers::_encrypt($trans->trans_id, 'CAPAUT'),
                    'Trans Date' => Carbon::parse($trans->parenttransdate)->format('d-m-Y'),
                    'Invoice No' => $invoiceNo,
                    'Trans Type' =>  $trans->transName,
                    'Total Repay Amt' => $totalPay,
                    'Outstanding Amt' => $outStanding,
                    'Suggested Outstanding Amt' => $accuredInterestR,
                    'Payment' => ''
                ];
            }
            $fileHelper = new FileHelper($this->appRepo);
            $notes = 'Note: Token ID and Trans ID is required for updating data. Please do not change Trans ID.';
            $extraDataArray = ['TOKEN_ID' => Helpers::_encrypt($token, 'CAPAUT'), 'NOTE' => $notes];
            $now = now()->format('U');
            $fileName = 'UnsettledTransactions-' . $now . '.csv';
            $responseFile = $fileHelper->exportCsv($unTransactions, $columns, $fileName, $extraDataArray);
            if (is_array($responseFile) && isset($responseFile['status']) && $responseFile['status'] != 'success') {
                Session::flash('untrans_error', $fileHelper->validationMessage(1));
                return redirect()->back();
            }
            $uploadData = $fileHelper->uploadUnSettledTransCsv($unTransactions, $columns, $fileName, $extraDataArray, $paymentId, 'download');
            if ($uploadData['status'] != 'success' || empty($uploadData['data'])) {
                Session::flash('untrans_error', $fileHelper->validationMessage(2));
                return redirect()->back();
            }
            $uploadFileData = $uploadData['data'];
            $docRepo = \App::make('App\Inv\Repositories\Contracts\DocumentInterface');
            $userFile = $docRepo->saveFile($uploadFileData);
            if ($userFile) {
                $paymentApportionment  = [
                    'user_id'    => $userId,
                    'payment_id' => $paymentId,
                    'parent_id'   => 0,
                    'file_id' => $userFile->file_id,
                    'status' => 1,
                    'is_active' => 1
                ];
                $result = PaymentApportionment::creates($paymentApportionment, 'download');
            } else {
                Session::flash('untrans_error', $fileHelper->validationMessage(3));
                return redirect()->back();
            }
            return $responseFile;
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function uploadApportUnsettledTrans(Request $request){
        set_time_limit(0);
        $sanctionPageView = false;
        if ($request->has('sanctionPageView')) {
            $sanctionPageView = $request->get('sanctionPageView');
        }
        $userId = $request->user_id;
        $paymentId = $request->payment_id;
        $type = $request->type;
        if ($request->has('type') && $type == 'UploadForm') {
            try {
                $fileHelper = new FileHelper($this->appRepo);
                $validator = Validator::make($request->all(), [
                    'upload_unsettled_trans' => 'required'
                ], [
                    'upload_unsettled_trans.required' => 'This field is required.',
                ])->validate();
                $uploadedFile = $request->file('upload_unsettled_trans');
                $paymentApportionments  = ['payment_id' => $paymentId];
                $result = PaymentApportionment::getLastPaymentAportData($paymentApportionments);
                if ($result) {
                    $parentId = $result['payment_aporti_id'];
                    $paymentId = $result['payment_id'];
                    $fileName = $result['file_name'];
                    if ($fileName == $uploadedFile->getClientOriginalName()) {
                        $uploadData = $fileHelper->uploadUnSettledTransCsv($request->all(), null, null, null, $paymentId, 'upload');
                        if ($uploadData['status'] != 'success' || empty($uploadData['data'])) {
                            Session::flash('untrans_error', $fileHelper->validationMessage(4));
                            return redirect()->back();
                        }
                        $uploadFileData = $uploadData['data'];
                        $fullFilePath = storage_path('app') . '/public/' . $uploadFileData['file_path'];
                        if (file_exists($fullFilePath)) {
                            $fileArrayData = $fileHelper->csvToArray($fullFilePath, $delimiter = ',');
                            if ($fileArrayData['status'] != 'success') {
                                Session::flash('untrans_error', $fileHelper->validationMessage(5));
                                return redirect()->back();
                            }
                            $rowData = $fileArrayData['data'];
                            if (empty($rowData)) {
                                Session::flash('untrans_error', $fileHelper->validationMessage(6));
                                return redirect()->back();
                            }
                            if (empty($fileArrayData['TOKEN_ID'])) {
                                Session::flash('untrans_error', $fileHelper->validationMessage(7));
                                return redirect()->back();
                            }
                            $tokenId = Helpers::_decrypt($fileArrayData['TOKEN_ID'], 'CAPAUT');
                            $tokenData = [];
                            if ($tokenId) {
                                $tokenData = explode('|', $tokenId);
                                if (empty($tokenData[0]) || empty($tokenData[1]) || empty($tokenData[2])  || empty($tokenData[3]) ) {
                                    session::flash('untrans_error', $fileHelper->validationMessage(18));
                                    return redirect()->back();
                                }
                                $upload_date = Carbon::parse($tokenData[3])->format('Y-m-d');
                                $current_date = \Carbon\Carbon::now("UTC")->format('Y-m-d');
                            }
                            if (empty($tokenData)) {
                                Session::flash('untrans_error', $fileHelper->validationMessage(8));
                                return redirect()->back();
                            }
                            $date_of_payment = null;
                            if ($request->has('payment_id')) {
                                $paymentId = $request->payment_id;
                                $payment = $this->lmsRepo->getPaymentDetail($paymentId, $userId);
                                $date_of_payment = $payment->date_of_payment;
                            }
                            $transactions = $this->getUnsettledTrans($userId, $date_of_payment);
                            if ($tokenData[0] != $userId || $tokenData[1] != $paymentId || $tokenData[2] != $date_of_payment || $upload_date != $current_date) {
                                session::flash('untrans_error', $fileHelper->validationMessage(9));
                                return redirect()->back();
                            }
                            $checkV = 0;
                            foreach ($rowData as $key => $value) {
                                if ((empty($value['Trans ID']) || (empty($value['Trans Date']) || empty($value['Invoice No']) || empty($value['Trans Type']) || empty($value['Total Repay Amt']) || empty($value['Outstanding Amt'])))) {
                                    Session::flash('untrans_error', $fileHelper->validationMessage(10));
                                    return redirect()->back();
                                }
                                $selectedPayment = str_replace(",","",$value['Payment']);
                                $is_negative = $selectedPayment < 0 ? true : false;
                                if (!empty($selectedPayment)) {
                                    $checkV = 1;
                                }
                                if($is_negative){
                                    Session::flash("untrans_error", $fileHelper->validationMessage(13));
                                    return redirect()->back();
                                }
                                if (!is_numeric($selectedPayment) && !empty($selectedPayment)) {
                                    Session::flash("untrans_error", $fileHelper->validationMessage(13));
                                    return redirect()->back();
                                }
                            }
                            if ($checkV == 0) {
                                Session::flash('untrans_error', $fileHelper->validationMessage(11));
                                return redirect()->back();
                            }
                            $payment = $check = [];
                            foreach ($rowData as $key => $value) {
                                if ((empty($value['Trans ID']) || empty($value['Trans Date']) || empty($value['Invoice No']) || empty($value['Trans Type']) || empty($value['Total Repay Amt']) || empty($value['Outstanding Amt']))) {
                                    Session::flash('untrans_error', $fileHelper->validationMessage(10));
                                    return redirect()->back();
                                }
                                $Trans_ID = Helpers::_decrypt($value['Trans ID'], 'CAPAUT');
                                if($transactions[$key]['trans_id'] != $Trans_ID){
                                    Session::flash('untrans_error', $fileHelper->validationMessage(19));
                                    return redirect()->back();
                                }
                                if (!empty($value['Payment']) && $value['Payment'] != '') {
                                    $check[$Trans_ID] = 'on';
                                    $payment[$Trans_ID] = str_replace(",","",$value['Payment']);
                                }
                            }
                            $docRepo = \App::make('App\Inv\Repositories\Contracts\DocumentInterface');
                            $userFile = $docRepo->saveFile($uploadFileData);
                            if ($userFile) {
                                $paymentApportionment  = [
                                    'user_id'    => $userId,
                                    'payment_id' => $paymentId,
                                    'parent_id'   => $parentId,
                                    'file_id' => $userFile->file_id,
                                    'status' => 1,
                                    'is_active' => 1
                                ];
                                $result = PaymentApportionment::creates($paymentApportionment, 'upload');
                                Session::flash('is_accept', 1);
                                return redirect()->route('apport_mark_settle_confirmation', [
                                    'user_id' => $request->user_id,
                                    'payment_id' => $request->payment_id,
                                    'payment' => base64_encode(serialize($payment)),
                                    'check' => base64_encode(serialize($check)),
                                    'type' => 'uploadCsv'
                                ]);
                            } else {
                                Session::flash('untrans_error', $fileHelper->validationMessage(12));
                                return redirect()->back();
                            }
                        } else {
                            Session::flash('untrans_error', $fileHelper->validationMessage(14));
                            return redirect()->back();
                        }
                    } else {
                        Session::flash('untrans_error', $fileHelper->validationMessage(15));
                        return redirect()->back();
                    }
                } else {
                    Session::flash('untrans_error', $fileHelper->validationMessage(16));
                    return redirect()->back();
                }
            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
        } else {
            return view('lms.apportionment.uploadApportUnsettledTrans', [
                'paymentId' => $paymentId,
                'userId' => $userId,
                'sanctionPageView' => $sanctionPageView
            ]);
        }
    }

    public function deleteDownloadCsvApportUnsettledTrans(Request $request)
    {
        try {
            if ($request->has('user_id') && $request->user_id && $request->has('payment_id') && $request->payment_id) {
                if ($request->has('action_type') && $request->has('action_type') == 'checkDownloadCsvEntry') {
                    sleep(2);
                    $paymentApportionments  = ['payment_id' => $request->payment_id];
                    $result = PaymentApportionment::getLastPaymentAportData($paymentApportionments);
                    if ($result) {
                        return \Response::json(['status' => 1, 'message' =>  'Download Successfully!']);
                    } else {
                        return \Response::json(['status' => 2, 'message' =>  'Not Download Successfully!']);
                    }
                } else {
                    $paymentApportionment = PaymentApportionment::where('user_id', $request->user_id)
                        ->where('payment_id', $request->payment_id)
                        ->where('parent_id', 0)
                        ->where('payment_aporti_id', $request->payment_appor_id)
                        ->first();
                    if ($paymentApportionment) {
                        $paymentApportionment->update(['is_active' => 0]);
                    }
                    PaymentApportionment::where('payment_id', $request->payment_id)
                        ->update([
                            'is_active' => 0
                        ]);
                    //return redirect()->route('apport_unsettled_view', [ 'user_id' => $request->user_id , 'payment_id' => $request->payment_id, 'sanctionPageView' => $request->sanctionPageView ]);
                    return \Response::json(['status' => 1, 'message' =>  'Deleted Successfully!']);
                }
            }
        } catch (Exception $ex) {
            //return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            return \Response::json(['status' => 0, 'message' =>  Helpers::getExceptionMessage($ex)]);
        }
    }
}