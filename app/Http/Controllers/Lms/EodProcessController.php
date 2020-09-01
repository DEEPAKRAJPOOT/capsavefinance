<?php

namespace App\Http\Controllers\Lms;

use App\Helpers\Helper;
use App\Helpers\ManualApportionmentHelper;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\InvoiceInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use Auth;
use Carbon\Carbon;
use Helpers;
use Illuminate\Http\Request;

class EodProcessController extends Controller
{

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
    public function process($eod_process_id = null)
    {
        try {
            $response = false;
            $cLogDetails = Helper::cronLogBegin(2);

            if ($eod_process_id) {
                $eodDetails = $this->lmsRepo->getEodProcess(['eod_process_id' => $eod_process_id, 'status' => [config('lms.EOD_PROCESS_STATUS.FAILED'), config('lms.EOD_PROCESS_STATUS.RUNNING')]]);
            } else {
                $eodDetails = $this->lmsRepo->getEodProcess(['is_active' => 1, 'status' => [config('lms.EOD_PROCESS_STATUS.FAILED'), config('lms.EOD_PROCESS_STATUS.RUNNING')]]);
            }

            if ($eodDetails) {
                $eod_process_id = $eodDetails->eod_process_id;
                if ($eodDetails->status == config('lms.EOD_PROCESS_STATUS.RUNNING')) {
                    $this->startEodProcess($eod_process_id);
                }

                $whereCond = [];
                $whereCond['eod_process_id'] = $eod_process_id;
                $whereCond['status'] = [config('lms.EOD_PROCESS_STATUS.FAILED'), config('lms.EOD_PROCESS_STATUS.STOPPED')];
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
                // \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.is_running_trans_settled'), config('lms.EOD_PASS_STATUS'), $eod_process_id);

                $eodDetails = $this->lmsRepo->getEodProcess(['eod_process_id' => $eod_process_id]);
                if ($eodDetails && $eodDetails->status == config('lms.EOD_PROCESS_STATUS.COMPLETED')) {
                    $start = new \Carbon\Carbon(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Helpers::getSysStartDate()));
                    $sys_start_date = $start->addDays(1)->format('Y-m-d 00:00:00');

                    $sys_start_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sys_start_date, config('common.timezone'))
                        ->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');

                    $this->lmsRepo->updateEodProcess(['is_active' => 0], ['eod_process_id' => $eod_process_id]);
                    $data = [];
                    $data['status'] = config('lms.EOD_PROCESS_STATUS.WATING');
                    $data['sys_start_date'] = $sys_start_date;
                    $data['is_active'] = 1;
                    $eodProcess = $this->lmsRepo->saveEodProcess($data);
                    if ($eodProcess) {
                        $eod_process_id = $eodProcess->eod_process_id;
                        $logData = [];
                        $logData['eod_process_id'] = $eod_process_id;
                        $this->lmsRepo->saveEodProcessLog($logData);
                        $Obj = new ManualApportionmentHelper($this->lmsRepo);
                        $Obj->dailyIntAccrual();
                    }
                    $response = true;
                } else {
                    $response = false;
                }
            } else {
                $response = false;
            }

            if ($cLogDetails) {
                Helper::cronLogEnd('1', $cLogDetails->cron_log_id);
            }

            return $response;

        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    public function viewEodProcess(Request $request)
    {
        $enable_process_start = false;
        $whereCond = [];
        $whereCond['is_active'] = 1;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $status = $eodProcess ? $eodProcess->status : '';
        $disp_status = $eodProcess ? config('lms.EOD_PROCESS_STATUS_LIST')[$eodProcess->status] : '';
        $sys_start_date = $eodProcess->sys_start_date ? \Helpers::convertDateTimeFormat($eodProcess->sys_start_date, 'Y-m-d H:i:s', 'Y-m-d H:i:s') : '';
        $total_sec = $eodProcess ? $eodProcess->total_sec : '';
        $sys_end_date = $eodProcess->sys_end_date ? \Helpers::convertDateTimeFormat($eodProcess->sys_end_date, 'Y-m-d H:i:s', 'Y-m-d H:i:s') : null;
        $eod_process_id = $eodProcess ? $eodProcess->eod_process_id : null;
        $eod_status = $eodProcess ? $eodProcess->status : null;
        $created_at = $eodProcess->created_at ? \Helpers::convertDateTimeFormat($eodProcess->created_at, 'Y-m-d H:i:s', 'Y-m-d H:i:s') : null;

        if ($eod_process_id) {
            $enable_process_start = $eod_process_id && ($eodProcess->status == config('lms.EOD_PROCESS_STATUS.RUNNING') || $eodProcess->status == config('lms.EOD_PROCESS_STATUS.FAILED')) && !in_array($eodProcess->status, [config('lms.EOD_PROCESS_STATUS.STOPPED'), config('lms.EOD_PROCESS_STATUS.COMPLETED')]);
        }

        return view('lms.eod.eod_process')
            ->with('enable_process_start', $enable_process_start)
            ->with('eod_process_id', $eod_process_id)
            ->with('sys_start_date', $sys_start_date)
            ->with('sys_end_date', $sys_end_date)
            ->with('created_at', $created_at)
            ->with('status', $eod_status);
    }

    public function startSystem($eod_process_id = null){
        $eodDetails = null;
        $response = false;
        $current_datetime = \Carbon\Carbon::now()->toDateTimeString();
        if ($eod_process_id) {
            $eodDetails = $this->lmsRepo->getEodProcess(['eod_process_id' => $eod_process_id, 'status' => [config('lms.EOD_PROCESS_STATUS.WATING')]]);
        }

        if ($eodDetails) {
            $eod_process_id = $eodDetails->eod_process_id;
            if ($eodDetails->status == config('lms.EOD_PROCESS_STATUS.WATING') && strtotime($eodDetails->sys_start_date) < strtotime(Helper::getSysStartDate())) {
                $data = [];
                    $data['status'] = config('lms.EOD_PROCESS_STATUS.RUNNING');
                    $data['sys_start_date'] = $current_datetime;
                    $data['is_active'] = 1;
                    $eodProcess = $this->lmsRepo->saveEodProcess($data,$eod_process_id);
                    if ($eodProcess) {
                        $response = true;
                        // Calculate interest for new date
                        $Obj = new ManualApportionmentHelper($this->lmsRepo);
                        $Obj->dailyIntAccrual();
                    }
            }
        }
        return $response;
    }

    public function startEodProcess($eod_process_id)
    {
        $current_datetime = \Carbon\Carbon::now()->toDateTimeString();
        $current_user_id = \Auth::user() ? \Auth::user()->user_id : null;

        $whereCond = [];
        $whereCond['eod_process_id'] = $eod_process_id;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $running_sec = abs(strtotime($current_datetime) - strtotime($sys_start_date));

        $data = [];
        $data['status'] = config('lms.EOD_PROCESS_STATUS.STOPPED');
        $data['sys_end_date'] = $current_datetime;
        $data['eod_process_start'] = $current_datetime;
        $data['total_sec'] = $running_sec;
        $data['is_active'] = 1;
        $data['eod_process_mode'] = ($current_user_id) ? 2 : 1;
        $this->lmsRepo->saveEodProcess($data, $eod_process_id);
    }

    protected function checkDisbursal($transStartDate, $transEndDate, $eod_process_id)
    {

        $transactions = $this->lmsRepo->checkDisbursalTrans($transStartDate, $transEndDate);
        $invoices = [];
        $users = [];
        $totalTransAmt = 0;
        $disbursedTransAmt = 0;
        $disbursalIds = [];
        foreach ($transactions as $transaction) {

            //Upfront
            //$payFreq == '1'

            //Monthly Case
            //$payFreq == '2'

            //Rear End Case
            //$payFreq == '3'
            //|| ($transaction->trans_type == config('lms.TRANS_TYPE.INTEREST') && !in_array($transaction->payment_frequency, [2,3])
            if (in_array($transaction->trans_type, [config('lms.TRANS_TYPE.PAYMENT_DISBURSED'), config('lms.TRANS_TYPE.MARGIN')])
            ) {
                $totalTransAmt += $transaction->amount;
            }

            if (in_array($transaction->trans_type, [config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])) {
                $disbursedTransAmt += $transaction->amount;
                $disbursalIds[] = $transaction->disbursal_id;
            }

            $invoices[] = $transaction->invoice_id;

        }

        $totInvApprAmt = $this->invRepo->getTotalInvApprAmt($invoices);
        $disbursedAmt = $this->lmsRepo->getTotalDisbursedAmt($disbursalIds);

        //dd($disbursedAmt, $disbursedTransAmt, $disbursalIds, $totInvApprAmt, $totalTransAmt);

        $result = /*$disbursedTransAmt == $disbursedAmt &&*/$totInvApprAmt == $totalTransAmt;

        if ($result) {
            $status = config('lms.EOD_PASS_STATUS');
        } else {
            $status = config('lms.EOD_FAIL_STATUS');
        }
        $status = config('lms.EOD_PASS_STATUS');
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.DISBURSAL'), $status, $eod_process_id);
        return $result;
    }

    public function checkTallyPosting()
    {
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
            $tallyAmountsData[strtolower($tallyAmt->is_debit_credit)] = (float) $tallyAmt->amount;
        }

        return [$debitCredits, $tallyAmountsData];
        // return ($debitCredits['debit'] == $tallyAmountsData['debit'] && $debitCredits['credit'] == $tallyAmountsData['credit'])
    }

    protected function checkInterestAccrual($transStartDate, $transEndDate, $eod_process_id)
    {
        $invoiceList = $this->lmsRepo->getUnsettledInvoices(['noNPAUser' => true, 'intAccrualStartDateLteSysDate' => true]);

        $result = true;
        if (!empty($invoiceList)) {
            foreach ($invoiceList as $invId => $trans) {
                $invDisbDetail = InvoiceDisbursed::find($invId);
                $maxAccrualDate = $invDisbDetail->interests->max('interest_date');
                $start = new \Carbon\Carbon(\Helpers::getSysStartDate());
                $sys_start_date = $start->format('Y-m-d');
                if ((strtotime($maxAccrualDate) != strtotime($sys_start_date . "- 1 days")) && (strtotime($invDisbDetail->int_accrual_start_dt) > strtotime($sys_start_date))) {
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
        $status = config('lms.EOD_PASS_STATUS');
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.INT_ACCRUAL'), $status, $eod_process_id);
        return $result;
    }

    protected function checkRunningTransSettle($transStartDate, $transEndDate, $eod_process_id)
    {
        $transactions = $this->lmsRepo->checkRunningTrans($transStartDate, $transEndDate);
        $result = ($transactions->count() == 0) ? true : false;
        if ($result) {
            $status = config('lms.EOD_PASS_STATUS');
        } else {
            $status = config('lms.EOD_FAIL_STATUS');
        }
        $status = config('lms.EOD_PASS_STATUS');
        \Helpers::updateEodProcess(config('lms.EOD_PROCESS_CHECK_TYPE.is_running_trans_settled'), $status, $eod_process_id);
        return $result;
    }
}
