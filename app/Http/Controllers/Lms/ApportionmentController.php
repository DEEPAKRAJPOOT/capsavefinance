<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use PHPExcel; 
use PHPExcel_IOFactory; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Contracts\Ui\DataProviderInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\ApportionmentRequest;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Models\Lms\Transactions;

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
            $oldData = [];
            $oldData['payment'] = (old('payment'))?old('payment'):[];
            $oldData['check'] = (old('check'))?old('check'):[];
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $userDetails = $this->getUserDetails($userId); 
            $payment = $this->getPaymentDetails($paymentId,$userId); 
            return view('lms.apportionment.unsettledTransactions')
            ->with('paymentId', $paymentId)  
            ->with('userId', $userId)
            ->with('payment',$payment) 
            ->with('userDetails', $userDetails)
            ->with('oldData',$oldData);
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
     * get Transaction Detail for waiveOff
     * @param Request $request
     * @return array
     */
    public function getTransDetailWaiveOff(Request $request){
        try {
            $transId = $request->get('trans_id');
            $payment_id = $request->get('payment_id');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            return view('lms.apportionment.waiveOffTransaction', ['TransDetail' => $TransDetail,'payment_id' => $payment_id]); 
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
            $transId = $request->get('trans_id');
            $payment_id = $request->get('payment_id');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            return view('lms.apportionment.reversalTransaction', ['TransDetail' => $TransDetail,'payment_id' => $payment_id]); 
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
                $commentData = [
                    'trans_id' => $resp->trans_id,
                    'comment' => $comment,
                ];
                $comment = $this->lmsRepo->saveTxnComment($commentData);
                return redirect()->route('apport_settled_list', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['message' => 'Amount successfully waived off']);
            }
        } catch (Exception $ex) {
             return redirect()->route('unsettled_payments')->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }
    /**
     * save reversal Detail
     * @param Request $request
     * @return array
     */
    public function saveReversalDetail(Request $request){
        try {
            $transId = $request->get('trans_id');
            $paymentId = $request->get('payment_id');
            $amount = $request->get('amount');
            $comment = $request->get('comment');
            $TransDetail = $this->lmsRepo->getTransDetail(['trans_id' => $transId]);
            if (empty($TransDetail)) {
                return redirect()->route('unsettled_payments')->with(['error' => 'Selected Transaction to be reversed is not valid']);
            }
            $outstandingAmount = $TransDetail->amount;
            if ($amount > $outstandingAmount)  {
                return redirect()->route('apport_settled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['error' => 'Amount to be reversed must be less than or equal to '. $outstandingAmount]);
            }
            if ($amount < 1)  {
                return redirect()->route('apport_settled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['error' => 'Amount to be reversed must have some values ']);
            }

            if (empty($comment))  {
                return redirect()->route('apport_settled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['error' => 'Comment / Remarks is required to reversed the amount.']);
            }
            $txnInsertData = [
                    'payment_id' => NULL,
                    'parent_trans_id' => $transId,
                    'invoice_disbursed_id' => $TransDetail->disburse->invoice_disbursed_id ?? NULL,
                    'user_id' => $TransDetail->user_id,
                    'trans_date' => date('Y-m-d H:i:s'),
                    'amount' => $amount,
                    'entry_type' => 0,
                    'trans_type' => 2,
                    'gl_flag' => 0,
                    'soa_flag' => 0,
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
                return redirect()->route('apport_settled_view', ['trans_id' => $transId, 'payment_id' => $paymentId, 'user_id' =>$TransDetail->user_id])->with(['message' => 'Amount successfully reversed']);
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
    }

    /**
     * Get Payment Details
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getPaymentDetails($paymentId, $userId){
        $payment = $this->lmsRepo->getPaymentDetail($paymentId, $userId);
        
        return [
            'payment_id' => $payment->payment_id,
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
    public function markSettleConfirmation(ApportionmentRequest $request){
        try {
            
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
                    'invoice_no' => ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                    'trans_type' =>  $trans->transName,
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
            ]);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function markSettleSave(Request $request){
        try {
            $validator = Validator::make($request->all(), 
                ["confirm" => 'required'],
                ['confirm.required'=> 'Please indicate that you accept the Terms and Conditions']
            );
            if ($validator->fails()) {
                Session::flash('error', $validator->messages()->first());
                return redirect()->back()->withInput();
            }

            if($request->session()->has('apportionment')){
                $amtToSettle = 0; 
                $transIds = [];
                $userId = $request->session()->get('apportionment.user_id');
                $paymentId = $request->session()->get('apportionment.payment_id');
                $payments = $request->session()->get('apportionment.payment');
                $checks = $request->session()->get('apportionment.check');

                $paymentDetails = $this->getPaymentDetails($paymentId,$userId);
                $repaymentAmt = $paymentDetails['amount']; 
                
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
                

                foreach ($transactions as $trans){  
                    $invoiceList[$trans->invoice_disbursed_id] = [
                        'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                        'date_of_payment'=>$paymentDetails['date_of_payment']
                    ];             
                    $transactionList[] = [
                        'payment_id' => $paymentId,
                        'parent_trans_id' => $trans->trans_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $trans->user_id,
                        'trans_date' => $paymentDetails['date_of_payment'],
                        'amount' => $payments[$trans->trans_id],
                        'entry_type' => 1,
                        'trans_type' => $trans->trans_type
                    ];
                    $amtToSettle += $payments[$trans->trans_id];
                }

                foreach ($invoiceList as $invDisb) {
                    $refundData = $this->lmsRepo->calInvoiceRefund($invDisb['invoice_disbursed_id'], $invDisb['date_of_payment']);
                    $refundParentTrans = $refundData->get('parent_transaction');
                    $refundAmt = $refundData->get('amount'); 
                    if($refundAmt > 0){
                        $transactionList[] = [
                            'payment_id' => $paymentId,
                            'parent_trans_id' => $refundParentTrans->trans_id,
                            'invoice_disbursed_id' => $refundParentTrans->invoice_disbursed_id,
                            'user_id' => $refundParentTrans->user_id,
                            'trans_date' => $invDisb['date_of_payment'],
                            'amount' => $refundAmt,
                            'entry_type' => 1,
                            'trans_type' => $refundParentTrans->trans_type
                        ];
                    }
                }
                

                $unAppliedAmt = $repaymentAmt-$amtToSettle;
    
                if($amtToSettle > $repaymentAmt){
                    Session::flash('error', trans('error_messages.apport_invalid_unapplied_amt'));
                    return redirect()->back()->withInput();
                }
                
                if(!empty($transactionList)){
                    foreach ($transactionList as $key => $newTrans) {
                        $this->lmsRepo->saveTransaction($newTrans);
                    }
                    $request->session()->forget('apportionment');
                    return redirect()->route('apport_settled_view', ['user_id' =>$userId])->with(['message' => 'Successfully marked settled']);
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    
}
