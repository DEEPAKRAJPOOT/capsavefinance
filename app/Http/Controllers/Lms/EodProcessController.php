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
    public function process()
    {
        try {    
            
            $whereCond=[];        
            $whereCond['is_active'] = 1;
            $whereCond['status']    = config('lms.EOD_PROCESS_STATUS.STOPPED');
            $eodProcess = $this->lmsRepo->getEodProcess($whereCond);

            if ($eodProcess) {
                $transStartDate = $eodProcess->sys_start_date;                        
                $transEndDate = $eodProcess->sys_end_date;
                
                $this->checkDisbursal($transStartDate, $transEndDate);
                $this->checkInterestAccrual($transStartDate, $transEndDate);
                $this->checkRunningTransSettle($transStartDate, $transEndDate);
                $message = "Eod Process checks are done.";
            } else {
                $message = "Unable to process the checks, as system is not started or stopped yet.";
            }
            
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.TALLY_POSTING'), config('lms.EOD_PASS_STATUS'));        
            //\Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.INT_ACCRUAL'), config('lms.EOD_PASS_STATUS'));
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.REPAYMENT'), config('lms.EOD_PASS_STATUS'));
            //\Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL'), config('lms.EOD_PASS_STATUS'));
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.CHARGE_POST'), config('lms.EOD_PASS_STATUS'));
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.OVERDUE_INT_ACCRUAL'), config('lms.EOD_PASS_STATUS'));
            \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL_BLOCK'), config('lms.EOD_PASS_STATUS'));
            // \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.RUNNING_TRANS_POSTING_SETTLED'), config('lms.EOD_PASS_STATUS'));
        
            return $message;
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    public function viewEodProcess(Request $request)
    {                
        $today = \Carbon\Carbon::now();
        $sys_curr_date = $today->format('Y-m-d H:i:s');
        
        $whereCond=[];
        $eodCount = $this->lmsRepo->getEodDataCount();      
        $whereCond['is_active'] = $eodCount == 1 ? 1 : 0;
        $latestEod = $this->lmsRepo->getLatestEodProcess($whereCond);
        //if ($request->has('sys_curr_date') && !empty($request->get('sys_curr_date'))) {
        //    $sys_curr_date = $request->get('sys_curr_date');            
        //    $sys_start_date_eq = date('Y-m-d', strtotime($sys_curr_date));
        //} else 
        if ($latestEod) {
            $start = new \Carbon\Carbon($latestEod->sys_start_date);
            $start = $start->addDays(1);
            $sys_curr_date = $start->format('Y-m-d') . " " . date('H:i:s');
            $sys_start_date_eq = $start->toDateString();
        } else {
            $sys_start_date_eq = $today->format('Y-m-d');
        }
        
        
        
        //dd($sys_curr_date, $sys_start_date_eq, $current_date);
        
        $whereCond=[];
        //$whereCond['status'] = [config('lms.EOD_PROCESS_STATUS.RUNNING'), config('lms.EOD_PROCESS_STATUS.STOPPED'), config('lms.EOD_PROCESS_STATUS.FAILED')];
        //$whereCond['sys_start_date_eq'] = $sys_start_date_eq;        
        //$whereCond['sys_start_date_tz_eq'] = $sys_start_date_eq; 
        $whereCond['is_active'] = 1;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);             
        $eod_process_id = $eodProcess ? $eodProcess->eod_process_id : '';        
        $status = $eodProcess ? $eodProcess->status : '';
        $disp_status = $eodProcess ? config('lms.EOD_PROCESS_STATUS_LIST')[$eodProcess->status] : '';
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $total_hours = $eodProcess ? $eodProcess->total_hours : '';
        $sys_end_date = $eodProcess ? $eodProcess->sys_end_date : '';
        
        if ($eod_process_id) {
            $running_hours = round(abs(strtotime($sys_curr_date) - strtotime($sys_start_date))/3600, 1);                        
        } else {
            $running_hours = '';
        }
        
        $whereCond=[];
        $whereCond['eod_process_id'] = $eod_process_id;
        $statusLog = $this->lmsRepo->getEodProcessLog($whereCond);
        
        if ($eodProcess && $eodProcess->status == config('lms.EOD_PROCESS_STATUS.COMPLETED')) {
            $start = new \Carbon\Carbon($eodProcess->sys_start_date);
            $start = $start->addDays(1);
            $sys_curr_date = $start->format('Y-m-d') . " " . date('H:i:s');
        }
        
        $current_date = \Helpers::convertDateTimeFormat($sys_curr_date, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s');
        
        $statusArr = config('lms.EOD_PASS_FAIL_STATUS');
        
        $enable_sys_start = $eod_process_id && $status != 1 ? 0 : 1;
        
        $enable_process_start = !$eod_process_id || isset($eodProcess->status) && in_array($eodProcess->status,[config('lms.EOD_PROCESS_STATUS.STOPPED'), config('lms.EOD_PROCESS_STATUS.COMPLETED'), config('lms.EOD_PROCESS_STATUS.FAILED')])  ? 0 : 1;
        
        return view('lms.eod.eod_process')
                ->with('current_date', $current_date)
                ->with('sys_start_date', $sys_start_date)
                ->with('sys_end_date', $sys_end_date)
                ->with('running_hours', $running_hours)
                ->with('status', $disp_status)
                ->with('eodData', $eodProcess)
                ->with('statusLog', $statusLog)
                ->with('statusArr', $statusArr)
                ->with('eod_process_id', $eod_process_id)
                ->with('total_hours', $total_hours)
                ->with('enable_sys_start', $enable_sys_start)
                ->with('enable_process_start', $enable_process_start)
                ->with('sys_curr_date', $sys_curr_date);
                
                
    }
    
    public function saveEodProcess(Request $request)
    {
        $flag = $request->get('flag');
        $eod_process_id = $request->get('eod_process_id');
        $sys_curr_date  = $request->get('sys_curr_date');
        try {
            if ($flag == 2) {
                $addlData['sys_curr_date'] = $sys_curr_date;
                $this->startEodProcess($eod_process_id, $addlData);
                $message = 'Eod Process is started successfully';
            }
                        
            Session::flash('message', $message);
            return redirect()->route('eod_process', ['sys_curr_date' => $sys_curr_date]);
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    protected function startEodProcess($eod_process_id, $addlData)
    {
        //$current_datetime = \Carbon\Carbon::now()->toDateTimeString();
        $current_datetime = date('Y-m-d', strtotime($addlData['sys_curr_date'])) . " " . date('H:i:s');
        $current_user_id = \Auth::user() ? \Auth::user()->user_id : 1;

        $whereCond=[];        
        $whereCond['eod_process_id'] = $eod_process_id;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $running_hours = round(( abs(strtotime($current_datetime) - strtotime($sys_start_date)) )/3600, 1);

        $data=[];
        $data['status'] = config('lms.EOD_PROCESS_STATUS.STOPPED');
        $data['sys_end_date'] = $current_datetime;
        $data['eod_process_start'] = $current_datetime;
        $data['total_hours'] = $running_hours;
        $data['is_active'] = 1;
        //$data['created_by'] = $current_user_id;
        $data['updated_by'] = $current_user_id;
        $this->lmsRepo->saveEodProcess($data, $eod_process_id);        
    }
    
    protected function checkDisbursal($transStartDate, $transEndDate)
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
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL'), $status);           
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

    protected function checkInterestAccrual($transStartDate, $transEndDate)
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
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.INT_ACCRUAL'), $status);           
        return $result;
    }

    protected function checkRunningTransSettle($transStartDate, $transEndDate)
    {
        $transactions = $this->lmsRepo->checkRunningTrans($transStartDate, $transEndDate);
        $result = ($transactions->count() == 0)?true:false;
        if($result){                  
            $status = config('lms.EOD_PASS_STATUS'); 
        } else {
            $status = config('lms.EOD_FAIL_STATUS'); 
        }
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.RUNNING_TRANS_POSTING_SETTLED'), $status);
        return $result;
    }
}
