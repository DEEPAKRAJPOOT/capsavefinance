<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\InvoiceInterface;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;

class EodProcessController extends Controller {

    use LmsTrait;

    protected $lmsRepo;

    public function __construct(InvLmsRepoInterface $lmsRepo, InvoiceInterface $invRepo) 
    {
        $this->lmsRepo = $lmsRepo;
        $this->invRepo = $invRepo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function process($eod_process_id)
    {
        try {    
            
            $whereCond=[];        
            $whereCond['eod_process_id'] = $eod_process_id;
            $whereCond['status'] = [config('lms.EOD_PROCESS_STATUS.FAILED'),config('lms.EOD_PROCESS_STATUS.STOPPED')];
            $eodProcess = $this->lmsRepo->getEodProcess($whereCond);

            if ($eodProcess) {
                $transStartDate = $eodProcess->sys_start_date;                        
                $transEndDate = $eodProcess->sys_end_date;
                
                $this->checkDisbursal($transStartDate, $transEndDate, $eod_process_id);
                $this->checkInterestAccrual($transStartDate, $transEndDate, $eod_process_id);
                $this->checkRunningTransSettle($transStartDate, $transEndDate, $eod_process_id);
                $message = "Eod Process checks are done.";
            } else {
                $message = "Unable to process the checks, as system is not started or stopped yet.";
            }
            
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.TALLY_POSTING'), config('lms.EOD_PASS_STATUS'), $eod_process_id);        
            //\Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.INT_ACCRUAL'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.REPAYMENT'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
            //\Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.CHARGE_POST'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.OVERDUE_INT_ACCRUAL'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL_BLOCK'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
            // \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.RUNNING_TRANS_POSTING_SETTLED'), config('lms.EOD_PASS_STATUS'), $eod_process_id);
        
            return $message;
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    public function viewEodProcess(Request $request)
    {            
        $enable_process_start = false;

        $whereCond=[];
        $whereCond['is_active'] = 1;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $status = $eodProcess ? $eodProcess->status : '';
        $disp_status = $eodProcess ? config('lms.EOD_PROCESS_STATUS_LIST')[$eodProcess->status] : '';
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $total_min = $eodProcess ? $eodProcess->total_min : '';
        $sys_end_date = $eodProcess ? $eodProcess->sys_end_date : '';
        $eod_process_id = $eodProcess ? $eodProcess->eod_process_id : null;
        $eod_status = $eodProcess ? $eodProcess->status:null;

        if($eod_process_id){
            $enable_process_start = $eod_process_id && ($eodProcess->status == config('lms.EOD_PROCESS_STATUS.RUNNING') || $eodProcess->status == config('lms.EOD_PROCESS_STATUS.FAILED')) && !in_array($eodProcess->status,[config('lms.EOD_PROCESS_STATUS.STOPPED'), config('lms.EOD_PROCESS_STATUS.COMPLETED')]);
        }
        
        return view('lms.eod.eod_process')
        ->with('enable_process_start', $enable_process_start)
        ->with('eod_process_id', $eod_process_id)
        ->with('sys_start_date',$sys_start_date)
        ->with('status',$eod_status);
    }
    
    public function startEodProcess($eod_process_id)
    {
        $current_datetime = \Carbon\Carbon::now()->toDateTimeString();
        //$current_datetime = date('Y-m-d', strtotime($addlData['sys_curr_date'])) . " " . date('H:i:s');
        $current_user_id = \Auth::user() ? \Auth::user()->user_id : 1;

        $whereCond=[];        
        $whereCond['eod_process_id'] = $eod_process_id;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $running_min = round(( abs(strtotime($current_datetime) - strtotime($sys_start_date)) )/60, 1);

        $data=[];
        $data['status'] = config('lms.EOD_PROCESS_STATUS.STOPPED');
        $data['sys_end_date'] = $current_datetime;
        $data['eod_process_start'] = $current_datetime;
        $data['total_min'] = $running_min;
        $data['is_active'] = 1;
        $data['eod_process_mode'] = ($current_user_id)?2:1;
        $this->lmsRepo->saveEodProcess($data, $eod_process_id);        
    }
    
    protected function checkDisbursal($transStartDate, $transEndDate,$eod_process_id)
    {        
                        
        $transactions = $this->lmsRepo->checkDisbursalTrans($transStartDate, $transEndDate);
        $invoices = [];
        $users = [];
        $totalTransAmt = 0;
        $disbursedTransAmt = 0;
        $disbursalIds=[];
        foreach($transactions as $transaction) {

            //Upfront
            //$payFreq == '1'
                        
            //Monthly Case
            //$payFreq == '2'

            //Rear End Case
            //$payFreq == '3'
             //|| ($transaction->trans_type == config('lms.TRANS_TYPE.INTEREST') && !in_array($transaction->payment_frequency, [2,3])           
            if (in_array($transaction->trans_type, [config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),config('lms.TRANS_TYPE.MARGIN')] )                
               ) {
                $totalTransAmt += $transaction->amount;
            }
            
            if (in_array($transaction->trans_type, [config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]))
            {
                $disbursedTransAmt += $transaction->amount;
                $disbursalIds[] = $transaction->disbursal_id;
            }
            
            $invoices[] = $transaction->invoice_id;
            
        }
        
        $totInvApprAmt = $this->invRepo->getTotalInvApprAmt($invoices);
        $disbursedAmt = $this->lmsRepo->getTotalDisbursedAmt($disbursalIds);
        
        //dd($disbursedAmt, $disbursedTransAmt, $disbursalIds, $totInvApprAmt, $totalTransAmt);
        
        $result = $disbursedTransAmt == $disbursedAmt && $totInvApprAmt == $totalTransAmt;
        
        if ($result) {                  
            $status = config('lms.EOD_PASS_STATUS'); 
        } else {
            $status = config('lms.EOD_FAIL_STATUS'); 
        }
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL'), $status, $eod_process_id);           
        return $result;
    }


  public function checkTallyPosting() {
    $debitCredits = [];
    $debitAmt = 0;
    $creditAmt = 0;
    $txnRecords = $this->lmsRepo->postedTxnsInTally();
    $tallyAmounts = $this->lmsRepo->getActualTallyAmount();
    foreach ($txnRecords as $key => $txn) {
      $txnInvoiceAmount = $txn->amount;
      $waiveOffAmount = $txn->getWaiveOffAmount();
      $invoiceTrans = $txn->userinvoicetrans;
      if (!empty($invoiceTrans)) {
        $txnInvoiceAmount = $invoiceTrans->base_amount + $invoiceTrans->sgst_amount + $invoiceTrans->cgst_amount + $invoiceTrans->igst_amount;
      }
      if ($txn->amount != $txnInvoiceAmount && !empty($waiveOffAmount)) {
        $txn->amount = $txn->amount - $waiveOffAmount;
      }
      if ($txn->entry_type == 0) {
        $debitAmt += $txn->amount;
        // $debitCredits['debit'][] = $txn;
      }
      if ($txn->entry_type == 1) {
        $creditAmt += $txn->amount;
        // $debitCredits['credit'][] = $txn;
      }
    }
    $debitCredits['debit'] = $debitAmt;
    $debitCredits['credit'] = $creditAmt;
    $tallyAmountsData = [
        'debit' => 0,
        'credit' => 0,
    ];
    foreach ($tallyAmounts as $tallyAmt) {
      $tallyAmountsData[strtolower($tallyAmt->is_debit_credit)] = (float)$tallyAmt->amount; 
    }

    return [$debitCredits, $tallyAmountsData];
    // return ($debitCredits['debit'] == $tallyAmountsData['debit'] && $debitCredits['credit'] == $tallyAmountsData['credit'])
  }

    protected function checkInterestAccrual($transStartDate, $transEndDate, $eod_process_id)
    {
        $invoiceList = $this->lmsRepo->getUnsettledInvoices(['noNPAUser'=>true,'intAccrualStartDateLteSysDate'=>true]);

        $result = true;
        if(!empty($invoiceList)){
            foreach ($invoiceList as $invId => $trans) {
                $invDisbDetail = InvoiceDisbursed::find($invId);
                $maxAccrualDate = $invDisbDetail->interests->max('interest_date');
                $start = new \Carbon\Carbon(\Helpers::getSysStartDate());
                $sys_start_date = $start->format('Y-m-d');
                if(strtotime($maxAccrualDate) != strtotime($sys_start_date)){
                    $result = false;
                    break;
                }
            }
        }

        if ($result) {                  
            $status = config('lms.EOD_PASS_STATUS'); 
        } else {
            $status = config('lms.EOD_FAIL_STATUS'); 
        }
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.INT_ACCRUAL'), $status, $eod_process_id);           
        return $result;
    }

    protected function checkRunningTransSettle($transStartDate, $transEndDate, $eod_process_id)
    {
        $transactions = $this->lmsRepo->checkRunningTrans($transStartDate, $transEndDate);
        $result = ($transactions->count() == 0)?true:false;
        if($result){                  
            $status = config('lms.EOD_PASS_STATUS'); 
        } else {
            $status = config('lms.EOD_FAIL_STATUS'); 
        }
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.RUNNING_TRANS_POSTING_SETTLED'), $status, $eod_process_id);
        return $result;
    }
}
