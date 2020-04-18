<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Session;
use Helpers;
use PHPExcel; 
use PHPExcel_IOFactory;
use Illuminate\Http\Request;
use App\Http\Requests\MarkSettleInformationRequest as MarkSettleRequest;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Contracts\Ui\DataProviderInterface;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use  App\Inv\Repositories\Models\Lms\Transactions;
use App\Http\Requests\Lms\ApportionmentRequest;

class ApportionmentController extends Controller
{

    public function __construct(InvLmsRepoInterface $lms_repo ,DataProviderInterface $dataProvider, InvUserRepoInterface $user_repo){
        $this->lmsRepo = $lms_repo;
        $this->dataProvider = $dataProvider;
        $this->userRepo = $user_repo;
	}
    /**
     * View Unsettled Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewUnsettledTrans(Request $request){
        try {
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $userDetails = $this->getUserDetails($userId); 
            $payment = $this->getPaymentDetails($paymentId,$userId); 
            return view('lms.apportionment.unsettledTransactions')
            ->with('paymentId', $paymentId)  
            ->with('userId', $userId)
            ->with('payment',$payment) 
            ->with('userDetails', $userDetails);
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
            $userId = 385;
            //$userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 
            return view('lms.apportionment.settledTransactions')
                ->with('userDetails', $userDetails); 

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
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 

            return view('lms.apportionment.RefundTransactions')
                ->with('userDetails', $userDetails); 

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * get Transaction Detail
     * @param Request $request
     * @return array
     */
    public function getTransDetail(Request $request){
        try {
            $transId = $request->get('trans_id');
            $payment_id = $request->get('payment_id');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            return view('lms.apportionment.detailedTransaction', ['TransDetail' => $TransDetail,'payment_id' => $payment_id]); 
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
            $transId = $request->get('trans_id');
            $paymentId = $request->get('payment_id');
            $amount = $request->get('amount');
            $comment = $request->get('comment');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            if (empty($TransDetail)) {
                return redirect()->route('unsettled_payments')->with(['error' => 'Selected Transaction to be waived off is not valid']);
            }
            $outstandingAmount = $TransDetail->getOutstandingAttribute();
            if ($amount > $outstandingAmount)  {
                return redirect()->route('apport_unsettled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['error' => 'Amount to be Waived Off must be less than or equal to '. $outstandingAmount]);
            }
            if ($amount < 1)  {
                return redirect()->route('apport_unsettled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['error' => 'Amount to be Waived Off must have some values ']);
            }

            if (empty($comment))  {
                return redirect()->route('apport_unsettled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['error' => 'Comment / Remarks is required to waive off the amount.']);
            }
            $txnInsertData = [
                    'payment_id' => NULL,
                    'parent_trans_id' => $transId,
                    'invoice_disbursed_id' => $TransDetail->disburse->invoice_disbursed_id ?? NULL,
                    'user_id' => $TransDetail->user_id,
                    'trans_date' => date('Y-m-d H:i:s'),
                    'comment' => $comment ?? NULL,
                    'amount' => $amount,
                    'entry_type' => 1,
                    'trans_type' => 36,
                    'gl_flag' => 0,
                    'soa_flag' => 0,
                    'pay_from' => 1,
                    'is_settled' => 2,
            ];
            $resp = $this->lmsRepo->saveTransaction($txnInsertData);
            if (!empty($resp->trans_id)) {
                return redirect()->route('apport_unsettled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['message' => 'Amount successfully waived off']);
            }
        } catch (Exception $ex) {
             return redirect()->route('unsettled_payments')->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * Get Unsettled Transactions 
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getUnsettledTrans(int $userId){
        
      //  return $this->lmsRepo->getUnsettledTrans($userId);
        $invoiceList = $this->lmsRepo->getUnsettledInvoices(['user_id','=',$userId]);
        $transactionList = new Collection();
        foreach ($invoiceList as $invoice) {
            $invoiceTrans = $this->lmsRepo->getUnsettledInvoiceTransactions([
                'invoice_disbursed_id'=>$invoice->invoice_disbursed_id,
                'user_id'=>$userId,
                'trans_type'=>[9,16]
                ]);
            foreach($invoiceTrans as $trans){
                $transactionList->push($trans);
            }
        }

        $chargeTrans = $this->lmsRepo->getUnsettledChargeTransactions([
            'user_id'=>$userId,
            'trans_type_not_in'=>[9,16,10]
        ]);

        foreach ($chargeTrans as $key => $charge) {
            $transactionList->push($charge);
        }

        $marginTrans = $this->lmsRepo->getUnsettledInvoiceTransactions([
            'user_id'=>$userId,
            'trans_type'=>[10]
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
        $lmsUser = $this->userRepo->lmsGetCustomer($userId);
        $user = $this->userRepo->find($userId);
        
        return [
            'customer_id' => $lmsUser->customer_id,
            'customer_name' => $user->f_name.' '.$user->m_name.' '.$user->l_name,
            'address' => '',
            'limit_amt'=>  '',
        ];
    }

    /**
     * Get Payment Details
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getPaymentDetails($paymentId, $userId){
        $payment = $this->lmsRepo->getPaymentDetail($paymentId, $userId);
        
        return [
            'trans_id' => $payment->trans_id,
            'amount'=>$payment->amount,
            'date_of_payment'=> $payment->date_of_payment, 
            'paymentmode'=> $payment->paymentmode,
            'transactionno'=> $payment->transactionno,
            'payment_amt' => $payment->amount
        ];
    }

    /**
     * View Unsettled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listUnsettledTrans(Request $request){
        try {
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            
            $payment_date = null;
            $payment = $this->lmsRepo->getPaymentDetail($paymentId,$userId);    
            if(!empty($payment)){
                $transactions = $this->getUnsettledTrans($userId);
                return $this->dataProvider->getUnsettledTrans($request,$transactions,$payment);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    
    /**
     * View Settled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listSettledTrans(Request $request){
        try {
            $userId = $request->user_id;  
            $transactions = $this->getSettledTrans($userId);
            return $this->dataProvider->getSettledTrans($request,$transactions);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * View Refund Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listRefundTrans(Request $request){
        try {
            $userId = $request->user_id;
            $transactions = $this->getRefundTrans($userId);
            return $this->dataProvider->getRefundTrans($request,$transactions);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }


    /**
     * Unsettled Transaction marked Settled
     */
    public function markSettleConfirmation(MarkSettleRequest $request){

        dd($request->all());

        try {

            // $validator = Validator::make($request->all(), [
            //     "check.*" => 'required|min:1',
            //     'payment.*' => 'nullable|numeric|gt:0|regex:/[0-9 \,]/'
            // ]);
            // if ($validator->fails()) {
            //     Session::flash('error', $validator->messages()->first());
            //     return redirect()->back()->withInput();
            // }

            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $userDetails = $this->getUserDetails($userId); 
            $paymentDetails = $this->getPaymentDetails($paymentId,$userId);

            // if(!isset($userDetails['customer_id'])){
            //     Session::flash('error', trans('error_messages.apport_invalid_user_id'));
            //     return redirect()->back()->withInput();
            // }

            // if(!isset($paymentDetails['trans_id'])){
            //     Session::flash('error', trans('error_messages.apport_invalid_repayment_id'));
            //     return redirect()->back()->withInput();
            // }

            $repaymentAmt = $paymentDetails['amount']; 
            $amtToSettle = 0;
            $unAppliedAmt = 0;

            $transIds = [];
            $transactions = [];
            $transactionList = [];
            $payments = $request->payment;


            foreach ($request->check as $Ckey => $Cval) {
                if($Cval === 'on'){
                    if($payments[$Ckey] > 0){
                        array_push($transIds, $Ckey);
                    }
                }
            }

            // if(!empty($transIds)){
            //     $transactions = Transactions::whereIn('trans_id',$transIds)
            //     ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
            //     ->get();
            // }

            foreach ($transactions as $trans){
                $transactionList[] = [
                    'trans_id' => $trans->trans_id,
                    'trans_date' => $trans->trans_date,
                    'invoice_no' => ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                    'trans_type' =>  $trans->transName,
                    'total_repay_amt' => $trans->amount,
                    'outstanding_amt' => $trans->outstanding,
                    'payment_date' =>  $paymentDetails['date_of_payment'],
                    'pay' => $payments[$trans->trans_id],
                    'is_valid' => ((float)$payments[$trans->trans_id] <= (float)$trans->outstanding)?1:0
                ];
                $amtToSettle += $payments[$trans->trans_id];
            }

            $unAppliedAmt = $repaymentAmt-$amtToSettle;

            if($amtToSettle > $repaymentAmt){
                Session::flash('error', trans('error_messages.apport_invalid_unapplied_amt'));
                return redirect()->back()->withInput();
            }
            

            $request->session()->put('apportionment', [
                'payment_id' => $paymentId,
                'user_id' => $userId,
                'transactions' => $transactionList
            ]);
        
            return view('lms.apportionment.markSettledConfirm',[
                'paymentId' => $paymentId,
                'userId' => $userId,
                'payment' => $paymentDetails,
                'userDetails' => $userDetails,
                'transactions' => $transactionList,
                'unAppliedAmt' => $unAppliedAmt,
            ]);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function markSettledConfitm(Request $request){
        try {
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
}
