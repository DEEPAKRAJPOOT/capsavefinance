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
use App\Http\Requests\Lms\ApportionmentRequest;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\TransactionsRunning;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class ApportionmentController extends Controller
{

    public function __construct(InvLmsRepoInterface $lms_repo ,DataProviderInterface $dataProvider, InvUserRepoInterface $user_repo,InvAppRepoInterface $app_repo){
        $this->lmsRepo = $lms_repo;
        $this->dataProvider = $dataProvider;
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
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
            }
            $result = $this->getUserLimitDetais($userId);
            return view('lms.apportionment.unsettledTransactions')
            ->with('paymentId', $paymentId)  
            ->with('userId', $userId)
            ->with('payment',$payment) 
            ->with('payment_amt', $payment_amt)
            ->with('userDetails', $userDetails)
            ->with('oldData',$oldData)
            ->with('sanctionPageView',$sanctionPageView)
            ->with(['userInfo' =>  $result['userInfo'],
                            'application' => $result['application'],
                            'anchors' =>  $result['anchors']]);
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
            return view('lms.apportionment.settledTransactions')
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
            return view('lms.apportionment.waiveOffTransaction', ['TransDetail' => $TransDetail,'payment_id' => $payment_id, 'sanctionPageView'=>$sanctionPageView]); 
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
            $amount = $request->get('amount');
            $comment = $request->get('comment');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            if (empty($TransDetail)) {
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Selected Transaction to be waived off is not valid']);
            }
            $is_interest_charges = ($TransDetail->transType->chrg_master_id > 0 || $TransDetail->transType->id == 9);
            if(!$is_interest_charges){
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Waived off is possible only Interest and Charges.']);
            }
            $outstandingAmount = $TransDetail->getOutstandingAttribute();
            if ($amount > $outstandingAmount)  {
                return redirect()->route('apport_unsettled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be Waived Off must be less than or equal to '. $outstandingAmount]);
            }
            if ($amount < 1)  {
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
                    'trans_date' => date('Y-m-d H:i:s'),
                    'amount' => $amount,
                    'entry_type' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.WAVED_OFF'),
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
                $comment = $this->lmsRepo->saveTxnComment($commentData);
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
            $sanctionPageView = false;
            if($request->has('sanctionPageView')){
                $sanctionPageView = $request->get('sanctionPageView');
            }
            $transId = $request->get('trans_id');
            $paymentId = $request->get('payment_id');
            $amount = $request->get('amount');
            $comment = $request->get('comment');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            
            if (empty($TransDetail)) {
                return redirect()->route('apport_settled_view', ['payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Selected Transaction to be reversed is not valid']);
            }
            $outstandingAmount = $TransDetail->amount;
            
            if ($amount > $outstandingAmount)  {
                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be reversed must be less than or equal to '. $outstandingAmount]);
            }
            if ($amount < 1)  {
                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Amount to be reversed must have some values ']);
            }
            
            if (empty($comment))  {
                return redirect()->route('apport_settled_view', [ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Comment / Remarks is required to reversed the amount.']);
            }
            
            $paymentDetails = Payment::find($TransDetail->payment_id);
            if (empty($TransDetail)) {
                return redirect()->route('apport_settled_view',[ 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id, 'sanctionPageView'=>$sanctionPageView])->with(['error' => 'Payment Detail missing for this transaction!']);
            }

            $transDateTime = date('Y-m-d H:i:s');
            $txnInsertData = [
                    'payment_id' => NULL,
                    'link_trans_id'=> $transId,
                    'parent_trans_id' => $TransDetail->parent_trans_id,
                    'invoice_disbursed_id' => $TransDetail->disburse->invoice_disbursed_id ?? NULL,
                    'user_id' => $TransDetail->user_id,
                    'trans_date' => $transDateTime,
                    'amount' => $amount,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                    'gl_flag' => 0,
                    'soa_flag' => 1,
                    'pay_from' => 1,
                    'is_settled' => 2,
            ];
            $resp = $this->lmsRepo->saveTransaction($txnInsertData);
            
            $paymentData = [
                'user_id' => $paymentDetails->user_id,
                'biz_id' => $paymentDetails->biz_id,
                'virtual_acc' => $paymentDetails->virtual_acc,
                'action_type' => $paymentDetails->action_type,
                'trans_type' => $paymentDetails->trans_type,
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
                'created_at' => $paymentDetails->created_at,
                'created_by' => $paymentDetails->created_by,
                'generated_by' => 1,
                'is_refundable' => 1
            ];
            $paymentId = Payment::insertPayments($paymentData);
            if (!empty($resp->trans_id) && is_int($paymentId)) {
                $commentData = [
                    'trans_id' => $resp->trans_id,
                    'comment' => $comment,
                ];
                $comment = $this->lmsRepo->saveTxnComment($commentData);
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
    private function getUnsettledTrans(int $userId){
        
        $invoiceList = $this->lmsRepo->getUnsettledInvoices(['user_id','=',$userId]);

        $transactionList = new Collection();
        foreach ($invoiceList as $invId => $invoice) {
            $invoiceTrans = $this->lmsRepo->getUnsettledInvoiceTransactions([
                'invoice_disbursed_id'=>$invId,
                'user_id'=>$userId,
                'trans_type'=>[
                    config('lms.TRANS_TYPE.INTEREST'),
                    config('lms.TRANS_TYPE.PAYMENT_DISBURSED')
                    ]
                ]);
            foreach($invoiceTrans as $trans){
                $transactionList->push($trans);
            }
        }

        $chargeTrans = $this->lmsRepo->getUnsettledChargeTransactions([
            'user_id'=>$userId,
            'trans_type_not_in'=>[
                config('lms.TRANS_TYPE.INTEREST'),
                config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),
                config('lms.TRANS_TYPE.MARGIN')
            ]
        ]);

        foreach ($chargeTrans as $key => $charge) {
            $transactionList->push($charge);
        }

        $marginTrans = $this->lmsRepo->getUnsettledInvoiceTransactions([
            'user_id'=>$userId,
            'trans_type'=>[config('lms.TRANS_TYPE.MARGIN')]
        ]);

        foreach ($marginTrans as $key => $margin) {
            $transactionList->push($margin);
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
        $userId = $request->user_id;
        $paymentId = null;
        $payment_date = null;
        $payment = null;

        if($request->has('payment_id')){
            $paymentId = $request->payment_id;
            $payment = $this->lmsRepo->getPaymentDetail($paymentId,$userId);    
        }
        
        $transactions = $this->getUnsettledTrans($userId);
        return $this->dataProvider->getUnsettledTrans($request,$transactions,$payment);
    }
    
    /**
     * View Settled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listSettledTrans(Request $request){
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
                $invoiceList[$trans->invoice_disbursed_id] = [
                    'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                    'date_of_payment'=>$paymentDetails['date_of_payment']
                ];     
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
                    'is_valid' => ((float)$payments[$trans->trans_id] <= (float)$trans->outstanding)?1:0
                ];
                $amtToSettle += $payments[$trans->trans_id];
            }

            $unAppliedAmt = $repaymentAmt-$amtToSettle;


            $request->session()->put('apportionment', [
                'user_id' => $userId,
                'payment_id' => $paymentId,
                'payment' => $payments,
                'check' => $checks,
            ]);
        
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
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function markSettleSave(Request $request){
        try {
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
                    'link_trans_id' => null,
                    'parent_trans_id' => null,
                    'invoice_disbursed_id' => null,
                    'user_id' => $userId,
                    'trans_date' => $paymentDetails['date_of_payment'],
                    'amount' => 0,
                    'entry_type' => 1,
                    'soa_flag' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.REPAYMENT')
                ];

                foreach ($transactions as $trans){  
                    if($trans->invoice_disbursed_id){

                        $invoiceList[$trans->invoice_disbursed_id] = [
                            'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                            'date_of_payment'=>$paymentDetails['date_of_payment']
                        ];             
                    }
                    $transactionList[] = [
                        'payment_id' => $paymentId,
                        'link_trans_id' => $trans->trans_id,
                        'parent_trans_id' => $trans->trans_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $trans->user_id,
                        'trans_date' => $paymentDetails['date_of_payment'],
                        'amount' => $payments[$trans->trans_id],
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => $trans->trans_type
                    ];
                    $amtToSettle += $payments[$trans->trans_id];
                }

                $unAppliedAmt = round(($repaymentAmt-$amtToSettle),2);

                if($amtToSettle > $repaymentAmt){
                    Session::flash('error', trans('error_messages.apport_invalid_unapplied_amt'));
                    return redirect()->back()->withInput();
                }

                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                    /** Mark Payment Settled */
                    $payment = Payment::find($paymentId);
                    $payment->is_settled = 1;
                    $payment->save();
                }

                $transactionList = [];
                foreach ($invoiceList as $invDisb) {
                    $refundData = $this->lmsRepo->calInvoiceRefund($invDisb['invoice_disbursed_id'], $invDisb['date_of_payment']);
                    $refundParentTrans = $refundData->get('parent_transaction');
                    $refundAmt = $refundData->get('amount');
                    if($refundAmt > 0 && $refundParentTrans){
                        $transactionList[] = [
                            'payment_id' => $paymentId,
                            'link_trans_id' => $refundParentTrans->trans_id,
                            'parent_trans_id' => $refundParentTrans->trans_id,
                            'invoice_disbursed_id' => $refundParentTrans->invoice_disbursed_id,
                            'user_id' => $refundParentTrans->user_id,
                            'trans_date' => $invDisb['date_of_payment'],
                            'amount' => $refundAmt,
                            'soa_flag' => 1,
                            'entry_type' => 1,
                            'trans_type' => config('lms.TRANS_TYPE.REFUND')
                        ];
                    }
                }
                
                if($unAppliedAmt > 0){
                    $transactionList[] = [
                        'payment_id' => $paymentId,
                        'link_trans_id' => null,
                        'parent_trans_id' => null,
                        'invoice_disbursed_id' => null,
                        'user_id' => $trans->user_id,
                        'trans_date' => $paymentDetails['date_of_payment'],
                        'amount' => $unAppliedAmt,
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.NON_FACTORED_AMT')
                    ];
                }
                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                }
                foreach ($invoiceList as $invDisb) {
                    $Obj = new ManualApportionmentHelper($this->lmsRepo);
                    $Obj->intAccrual($invDisb['invoice_disbursed_id'], $invDisb['date_of_payment']);
                }
                $this->updateInvoiceRepaymentFlag(array_keys($invoiceList));
                $request->session()->forget('apportionment');
                return redirect()->route('apport_settled_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully marked settled']);
            }
        } catch (Exception $ex) {
            return redirect()->back('unsettled_payments', [ 'payment_id' => $paymentId, 'user_id' =>$userId])->withErrors(Helpers::getExceptionMessage($ex))->withInput();
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
                    'amount' => $trans->amount,
                    'entry_type' => $trans->entry_type,
                    'soa_flag' => 1,
                    'trans_type' => $trans->trans_type,
                ];
            }
            if(!empty($transactionList)){
                foreach ($transactionList as $key => $newTrans) {
                    $this->lmsRepo->saveTransaction($newTrans);
                }
            }
            return redirect()->route('apport_unsettled_view', ['user_id' =>$userId,'sanctionPageView'=>$sanctionPageView])->with(['message' => 'Successfully marked posted']);
        } catch (Exception $ex) {
             return redirect()->route('apport_settled_view',['payment_id' => $paymentId, 'user_id' =>$userId, 'sanctionPageView'=>$sanctionPageView])->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    // public function getAccruedInterest($invDisbId, $fromDate = null, $toDate = null){

    //     $accuredIntAmount = 0; 
    //     $invoiceDetails = InvoiceDisbursed::find($invDisbId);
    //     $invDueDate = $invoiceDetails->inv_due_date;
    //     $intRate = $invoiceDetails->interest_rate; 
    //     $gracePeriod = $invoiceDetails->grace_period;

    //     $fromDate = $fromDate ?? $invoiceDetails->int_accrual_start_dt;
    //     $toDate =  $toDate ?? date('Y-m-d H:i:s');

    //     $graceStartDate = ($gracePeriod > 0)?$this->addDays($invDueDate, 1):$invDueDate;
    //     $graceEndDate = addDays($invDueDate, $gracePeriod);

    //     $accuredIntAmount += InterestAccrual::where('invoice_disbursed_id',$invDisbId)->whereBetween('interest_date', [$fromDate, $invDueDate])->sum('accrued_interest');

    //     if($gracePeriod > 0 && strtotime($toDate) <= strtotime($graceDueDate)){
            
    //         $interestData = InterestAccrual::whereBetween('interest_date', [$graceStartDate, $toDate])
    //         ->select(DB::row("sum((principal_amount*($intRate/360))100) as total"))->get();
    //         if($interestData){
    //             $accuredIntAmount += $interestData->total;
    //         }
    //     }

    //     if(strtotime($toDate) > strtotime($graceDueDate)){
    //         $accuredIntAmount += InterestAccrual::where('invoice_disbursed_id',$invDisbId)->whereBetween('interest_date', [$fromDate, $invDueDate])->sum('accrued_interest');
    //     }

    // }

    // protected function addDays($currentDate, $noOfDays)
    // {
    //     $calDate = date('Y-m-d', strtotime($currentDate . "+ $noOfDays days"));
    //     return $calDate;
    // }

    // protected function subDays($currentDate, $noOfDays)
    // {
    //     $calDate = date('Y-m-d', strtotime($currentDate . "- $noOfDays days"));
    //     return $calDate;
    // }
    
    // public function manualUpfrontPostCalculation(int $transId, int $paymentId){
       
    //     $transactionList = [];
    //     $finalReversalAmt = 0;
    //     $finalRendedAmt = 0;

    //     $currentRequesteAmt = 0;
    //     $currentRefundedAmt = 0;
    //     $currentUnpaidAmt = 0;
    //     $currentPaidAmt = 0;
        
    //     $interest = Transactions::find($transId);
    //     $invoiceDisbursedId = $interest->invoice_disbursed_id;
    //     $userId = $interest->user_id;
        
    //     $currentRequesteAmt = $interest->amount;
    //     $currentRefundedAmt =  $interest->refundableamt;  
    //     $currentUnpaidAmt = $interest->outstanding;
    //     $currentPaidAmt = $interest - $totalUnpaidAmt;

    //     $payment = $this->lmsRepo->getPaymentDetail($paymentId, $userId);
    //     $paymentDate = $payment->date_of_payment;

    //     $currentOverdueRequested = Transactions::where('invoice_disbursed_id','=',$invoiceDisbursedId)
    //                 ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
    //                 ->where('entry_type','=','0')->sum('amount');

    //     $currentOverduePaid =  Transactions::where('invoice_disbursed_id','=',$invoiceDisbursedId)
    //                 ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
    //                 ->where('entry_type','=','1')->sum('amount');

    //     $payDateRequesteAmt = $this->getAccruedInterest($invoiceDisbursedId, null, $paymentDate);
    //     $payOverdueAmt = ($payDateRequesteAmt>$currentRequesteAmt)?($payDateRequesteAmt-$currentRequesteAmt):0;

    //     if($payOverdueAmt < $currentOverduePaid){
    //         $finalRendedAmt += $currentOverduePaid-$payOverdueAmt;
    //     }

    //     if($payOverdueAmt > 0 && $payOverdueAmt <= $currentOverduePaid && $currentOverdueRequested > $payDateRequesteAmt){
    //         $finalReversalAmt += $currentOverdueRequested-$payDateRequesteAmt;
    //     }
        
    //     if($payDateRequesteAmt < $currentPaidAmt){
    //         $finalRendedAmt += $currentOverduePaid-$payOverdueAmt;
    //     }
        
    //     if($payDateRequesteAmt <= $currentPaidAmt && ($payDateRequesteAmt+$currentRefundedAmt) < $currentRequesteAmt){
    //         $finalReversalAmt += ($payDateRequesteAmt+$currentRefundedAmt)-$currentRequesteAmt;
    //     }

    //     if($finalRendedAmt > 0){
    //         $transactionList[] = [
    //             'payment_id' => $paymentId,  
    //             'link_trans_id' => null,  
    //             'parent_trans_id' => null,  
    //             'invoice_disbursed_id' => $interest->invoice_disbursed_id,  
    //             'user_id' => $interest->user_id,  
    //             'trans_date' => $paymentDate,           
    //             'trans_type' => config('lms.TRANS_TYPE.REFUND'),   
    //             'amount' => $finalRendedAmt,  
    //             'entry_type' => '0',
    //             'soa_flag' => '1'    
    //         ];
    //     }
    //     if($finalReversalAmt > 0){
    //         $transactionList[] = [
    //             'payment_id' => $paymentId,  
    //             'link_trans_id' => null,  
    //             'parent_trans_id' => null,  
    //             'invoice_disbursed_id' => $interest->invoice_disbursed_id,  
    //             'user_id' => $interest->user_id,  
    //             'trans_date' => $paymentDate,           
    //             'trans_type' => config('lms.TRANS_TYPE.REVERSE'),   
    //             'amount' => $finalReversalAmt,  
    //             'entry_type' => '1',
    //             'soa_flag' => '1'     
    //         ];
    //     }

    //     foreach ($transactionList as $key => $newTrans) {
    //         $this->lmsRepo->saveTransaction($newTrans);
    //     }
    // }
    
    // public function manualRearendPostCalculation(int $transId, int $paymentId){

    // }
    
    
     /* use function for the manage sention tabs */ 
    
    public  function  getUserLimitDetais($user_id) 
   {
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
            $flag = $this->lmsRepo->getInvoiceSettleStatus($invd->invoice_id, true);
            $inv = BizInvoice::find($invd->invoice_id);
            if($flag){
                $inv->is_repayment = 1;
                $inv->status_id = 13;
            }else{
                if($inv->is_repayment == 1)
                $inv->is_repayment = 0;
                if($inv->status_id == 13)
                $inv->status_id = 12;
            }
            $inv->save();
        }
    }
   
}
