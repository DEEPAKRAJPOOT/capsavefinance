<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Session;
use Helpers;
use PHPExcel; 
use PHPExcel_IOFactory;
use Illuminate\Http\Request;
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
    public function markSettleConfirmation(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                "check.*" => 'required|string|min:1',
                'payment.*' => 'nullable|numeric|gt:0|regex:/[0-9 \,]/'
            ]);
            if ($validator->fails()) {
                Session::flash('error', $validator->messages()->first());
                return redirect()->back()->withInput();
            }

            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            $userDetails = $this->getUserDetails($userId); 
            $paymentDetails = $this->getPaymentDetails($paymentId,$userId);

            if(!isset($userDetails['customer_id'])){
                Session::flash('error', trans('error_messages.apport_invalid_user_id'));
                return redirect()->back()->withInput();
            }

            if(!isset($paymentDetails['payment_id'])){
                Session::flash('error', trans('error_messages.apport_invalid_repayment_id'));
                return redirect()->back()->withInput();
            }

            $repaymentAmt = $paymentDetails['amount']; 
            $amtToSettle = 0;
            $unAppliedAmt = 0;
            $checkedCount = 0;
            $transIds = [];
            $transactions = [];
            $transactionList = [];
            $payments = $request->payment;

            if($request->has('check')){
                foreach ($request->check as $Ckey => $Cval) {
                    if($Cval === 'on'){
                        $checkedCount++;
                        if($payments[$Ckey] > 0){
                            array_push($transIds, $Ckey);
                        }
                    }
                }
            }
            if($checkedCount <= 0){
                Session::flash('error', "Please select at least one record");
                return redirect()->back()->withInput();
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
                    'invoice_no' => ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                    'trans_type' =>  $trans->transName,
                    'total_repay_amt' => (float)$trans->amount,
                    'outstanding_amt' => (float)$trans->outstanding,
                    'payment_date' =>  $paymentDetails['date_of_payment'],
                    'pay' => (float)$payments[$trans->trans_id],
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
                'user_id' => $userId,
                'payment_id' => $paymentId,
                'payments' => $payments
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
            if($request->session()->has('apportionment')){
                $amtToSettle = 0; 
                $userId = $request->session()->get('apportionment.user_id');
                $paymentId = $request->session()->get('apportionment.payment_id');
                $payments = $request->session()->get('apportionment.payments');
                $paymentDetails = $this->getPaymentDetails($paymentId,$userId);
                $repaymentAmt = $paymentDetails['amount']; 
            

                $transIds = array_keys($payments);
                if(!empty($transIds)){
                    $transactions = Transactions::where('user_id','=',$userId)
                    ->whereIn('trans_id',$transIds)
                    ->orderByRaw("FIELD(trans_id, ".implode(',',$transIds).")")
                    ->get();
                }
    
                foreach ($transactions as $trans){
                    if(((float)$payments[$trans->trans_id] > (float)$trans->outstanding)){
                        Session::flash('error', 'Please re confirm');
                        return redirect()->back()->withInput();
                    }
                    /*$transactionList[] = [
                        'trans_id' => $trans->trans_id,
                        'trans_date' => $trans->trans_date,
                        'invoice_no' => ($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id)?$trans->invoiceDisbursed->invoice->invoice_no:'',
                        'trans_type' =>  $trans->transName,
                        'total_repay_amt' => (float)$trans->amount,
                        'outstanding_amt' => (float)$trans->outstanding,
                        'payment_date' =>  $paymentDetails['date_of_payment'],
                        'pay' => (float)$payments[$trans->trans_id],
                        'is_valid' => ((float)$payments[$trans->trans_id] <= (float)$trans->outstanding)?1:0
                    ];*/
                    $amtToSettle += $payments[$trans->trans_id];
                }
    
                $unAppliedAmt = $repaymentAmt-$amtToSettle;
    
                if($amtToSettle > $repaymentAmt){
                    Session::flash('error', trans('error_messages.apport_invalid_unapplied_amt'));
                    return redirect()->back()->withInput();
                }
                
                dd( $userId, $paymentId, $transactions );
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    
}
