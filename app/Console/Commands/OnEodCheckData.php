<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\TallyEntry;
use DB;

class OnEodCheckData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eod:check-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check eod data for mismatch records';

    protected $eodDate = '';
    protected $emailTo = '';
    protected $tallyData = [];
    protected $tallyErrorData = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->eodDate = now()->toDateString();
        // $this->eodDate = '2022-10-01';
        $this->emailTo = 'hirdesh@zuron.in';
        $this->emailCc = 'amit.suman@zuron.in,gaurav.agarwal@zuron.in,sudesh.kumar@zuron.in';
        $this->emailBcc = '';
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $dupPayments = $this->checkDuplicatePaymentRecords();
        // $dupDisbursals = $this->checkDuplicateDisbursalRecords(); 
        $this->checkEODTallyRecords();
    }

    private function checkDuplicatePaymentRecords()
    {
        $dupPayments = Payment::withTrashed()->select(DB::raw('GROUP_CONCAT(payment_id) as payment_ids, user_id as customer_id, amount, CONCAT_WS("", utr_no, unr_no, cheque_no) AS com_utr_no, count(*) AS paymentCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->where('trans_type', config('lms.TRANS_TYPE.REPAYMENT'))
                            ->where('action_type', 1)
                            ->groupBy(['user_id', 'amount', 'utr_no', 'unr_no', 'cheque_no'])
                            ->havingRaw('paymentCount > 1')
                            ->get();
        return $dupPayments;
    }

    private function checkDuplicateDisbursalRecords()
    {
        $dupDisbursals = Disbursal::select(DB::raw('GROUP_CONCAT(disbursal_id) as disbursal_ids, user_id as customer_id, disburse_amount as amount, count(*) AS disbCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->groupBy(['user_id', 'disburse_amount', 'disbursal_batch_id'])
                            ->havingRaw('disbCount > 1')
                            ->get();
        return $dupDisbursals;
    }

    private function checkEODTallyRecords()
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        try {
            DB::beginTransaction();
            $tallyBatches = DB::table('tally')
                            ->whereNull('is_validated')
                            ->whereNotNull('start_date')
                            ->whereNotNull('end_date')
                            ->orderByDesc('id')
                            ->get();

            foreach($tallyBatches as $tallyBatch) {
                $this->processTallyBatch($tallyBatch);
            }
            
            if (count($this->tallyData) && count($this->tallyErrorData)) {
                $emailData['disbursals'] = $dupDisbursals ?? [];
                $emailData['payments']   = $dupPayments ?? [];
                $emailData['tally_data'] = $this->tallyData;
                $emailData['tally_error_data'] = $this->tallyErrorData;
                $emailData['to']         = $this->emailTo;
                $emailData['cc']         = $this->emailCc;
                $emailData['bcc']        = $this->emailBcc;
                \Event::dispatch("NOTIFY_EOD_CHECKS", serialize($emailData));
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            dd(\Helpers::getExceptionMessage($ex));
        }        
    }

    public function processTallyBatch($tallyBatch)
    {
        $startDate = $tallyBatch->start_date;
        $endDate = $tallyBatch->end_date;
        $where = [['created_at', '>=', $startDate],['created_at', '<=', $endDate]];

        $journalData = Transactions::where($where)
                                    ->where(function($query){
                                    $query->where('entry_type', 0)
                                        ->whereNotIn('trans_type', [config('lms.TRANS_TYPE.MARGIN')]);
                                    $query->where(function($query1) {
                                        $query1->whereIn('trans_type', [
                                            config('lms.TRANS_TYPE.INTEREST'),
                                            config('lms.TRANS_TYPE.INTEREST_OVERDUE'),
                                            config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),
                                        ]);
                                        $query1->orWhere('trans_type', '>', 49);
                                    });

                                    $query->orWhere(function ($query1) {
                                        $query1->where('entry_type', 1)
                                            ->whereIn('trans_type', [
                                                config('lms.TRANS_TYPE.CANCEL'),
                                                config('lms.TRANS_TYPE.REVERSE'), 
                                                config('lms.TRANS_TYPE.TDS'), 
                                                config('lms.TRANS_TYPE.REFUND'), 
                                                config('lms.TRANS_TYPE.WAVED_OFF'),
                                                config('lms.TRANS_TYPE.WRITE_OFF'),
                                            ]);
                                    });
                                })
                                ->pluck('trans_id')
                                ->toArray();

        $bankingData = Transactions::where($where)
                                ->where(function($query){
                                    $query->where(function($query1){
                                        $query1->where('entry_type', 1)
                                            ->whereNotNull('payment_id')
                                            ->whereNotIn('trans_type', [
                                                config('lms.TRANS_TYPE.REFUND'),
                                                config('lms.TRANS_TYPE.REPAYMENT'),
                                            ])
                                            ->whereHas('payment', function ($query2) {
                                                $query2->whereNotIn('action_type', [5]);
                                            });
                                    })
                                    ->orWhere(function($query3){
                                        $query3->where('entry_type', 0)
                                              ->where('trans_type', 32);
                                    })
                                    ->orWhere(function($query4){
                                        $query4->where('entry_type', 1)
                                            ->where('trans_type', 9)
                                            ->whereNull('payment_id')
                                            ->whereNull('trans_running_id');
                                    });
                                })
                                ->pluck('trans_id')
                                ->toArray();
    
        $trans_ids = array_unique(array_merge($journalData, $bankingData));    
        $uniqTallyTransIds = TallyEntry::where('batch_no', $tallyBatch->batch_no)
                                ->whereIn('transactions_id', $trans_ids)
                                ->distinct('transactions_id')
                                ->pluck('transactions_id')
                                ->toArray();
        $diffUniqTallyTransIds = array_diff($trans_ids, $uniqTallyTransIds);

        $totalTransAmt = Transactions::whereIn('trans_id', $trans_ids)->sum('amount');
        $totalTallyAmtArray = TallyEntry::select(DB::raw('SUM(rta_tally_entry.amount) as amount'))
                                ->where('batch_no', $tallyBatch->batch_no)
                                ->whereNotNull('transactions_id')
                                ->groupBy('transactions_id', 'trans_type')
                                ->get()
                                ->toArray();
        $totalTallyAmt = array_sum(array_column($totalTallyAmtArray, 'amount'));
        $totalDiffAmt  = (round($totalTransAmt, 2) - round($totalTallyAmt, 2));
        
        if ((count($diffUniqTallyTransIds) && $totalDiffAmt > 0) || ($totalDiffAmt > 0)) {
            $updateQuery = ['is_validated' => 0];
        } else {
            $updateQuery = ['is_validated' => 1];
        }
        $tallyRecords = TallyEntry::select(DB::raw('concat(if(rta_transactions.entry_type = 0,"Debit - ","Credit - "),rta_customer_transaction_soa.trans_name) as transaction_name, COUNT(DISTINCT rta_tally_entry.transactions_id) as tallyCount, SUM(rta_tally_entry.amount) as tallyAmt'))
                            ->join('transactions', 'transactions.trans_id', '=', 'tally_entry.transactions_id')
                            ->join('customer_transaction_soa', 'customer_transaction_soa.trans_id', '=', 'transactions.trans_id')
                            ->where('tally_entry.batch_no', $tallyBatch->batch_no)
                            ->groupBy('tally_entry.transactions_id')
                            ->orderBy('transactions.trans_type', 'ASC')
                            ->get()
                            ->toArray();
                            
        $tallyTransRecords = Transactions::select(DB::raw('concat(if(rta_transactions.entry_type = 0,"Debit - ","Credit - "),rta_customer_transaction_soa.trans_name) as transaction_name, COUNT(*) as transCount, SUM(rta_transactions.amount) as transAmt'))
                                ->join('customer_transaction_soa', 'customer_transaction_soa.trans_id', '=', 'transactions.trans_id')
                                ->leftJoin('payments', 'transactions.payment_id', '=', 'payments.payment_id')
                                ->whereIn('transactions.trans_id', $trans_ids)
                                ->groupBy('transactions.trans_id')
                                ->get()
                                ->toArray();

        $this->tallyData[$tallyBatch->batch_no] = [
            'start_date' => \Helpers::convertDateTimeFormat($tallyBatch->start_date, 'Y-m-d H:i:s', 'd-m-Y h:i A'),
            'end_date' => \Helpers::convertDateTimeFormat($tallyBatch->end_date, 'Y-m-d H:i:s', 'd-m-Y h:i A'),
            'total_record' => array_sum(array_column($tallyTransRecords, 'transCount')),
            'matched_record' => array_sum(array_column($tallyRecords, 'tallyCount')),
            'trans_wise_data' => $this->formatData(array_merge($tallyRecords, $tallyTransRecords)),
        ];
        
        $transGstRecords = Transactions::whereIn('trans_id', $trans_ids)
                        ->whereHas('userInvTrans')
                        ->where('trans_type', '>', 49)
                        ->where('gst', 1)
                        ->get();

        $tallyGstRecords = TallyEntry::select('tally_entry.trans_type as trans_name', DB::raw('sum(rta_tally_entry.amount) as tally_gst_amount'))
                    ->where('tally_entry.batch_no', $tallyBatch->batch_no)
                    ->whereNotNull('tally_entry.transactions_id')
                    ->where('tally_entry.trans_type', 'like', '%gst%')
                    ->groupBy('tally_entry.transactions_id', 'tally_entry.trans_type')
                    ->get()
                    ->toArray();
        $gstData = $this->formatGstData($transGstRecords, $tallyGstRecords);
        $this->tallyData[$tallyBatch->batch_no]['trans_wise_data']['SGST + CGST'] = $gstData['gst_type_1'];
        $this->tallyData[$tallyBatch->batch_no]['trans_wise_data']['IGST'] = $gstData['gst_type_2'];

        $tally_data = Transactions::select(DB::raw('COUNT(*) as transCount, rta_customer_transaction_soa.trans_name as transaction_name, GROUP_CONCAT(rta_transactions.trans_id) as trans_ids'))
                                ->join('customer_transaction_soa', 'customer_transaction_soa.trans_id', '=', 'transactions.trans_id')
                                ->whereIn('transactions.trans_id', $diffUniqTallyTransIds)
                                ->groupBy('transactions.trans_type')
                                ->get()
                                ->toArray();

        $this->tallyErrorData[$tallyBatch->batch_no] = $tally_data;

        // To update tally validate status on tally table
        DB::table('tally')
            ->where('batch_no', $tallyBatch->batch_no)
            ->update($updateQuery);
    }

    private function formatData($allData)
    {
        $newData = [];
        foreach($allData as $data) {
            if (isset($newData[$data['transaction_name']])) {
                $oldData = $newData[$data['transaction_name']];
                if (isset($data['tallyCount'])) {
                    if (isset($oldData['tallyCount'])) {
                        $newData[$data['transaction_name']]['tallyCount'] = $oldData['tallyCount'] + $data['tallyCount'];
                    }else {
                        $newData[$data['transaction_name']]['tallyCount'] = $data['tallyCount'];
                    }
                }
                if (isset($data['tallyAmt'])) {
                    if (isset($oldData['tallyAmt'])) {
                        $newData[$data['transaction_name']]['tallyAmt'] = $oldData['tallyAmt'] + $data['tallyAmt'];
                    }else {
                        $newData[$data['transaction_name']]['tallyAmt'] = $data['tallyAmt'];
                    }
                }
                if (isset($data['transCount'])) {
                    if (isset($oldData['transCount'])) {
                        $newData[$data['transaction_name']]['transCount'] = $oldData['transCount'] + $data['transCount'];
                    }else {
                        $newData[$data['transaction_name']]['transCount'] = $data['transCount'];
                    }
                }
                if (isset($data['transAmt'])) {
                    if (isset($oldData['transAmt'])) {
                        $newData[$data['transaction_name']]['transAmt'] = $oldData['transAmt'] + $data['transAmt'];
                    }else {
                        $newData[$data['transaction_name']]['transAmt'] = $data['transAmt'];
                    }
                }
            }else {
                $newData[$data['transaction_name']] = $data;
            }
        }
        return $newData;
    }

    private function formatGstData($transGstRecords, $tallyGstRecords)
    {
        $transGstRecordsArray = [];
        $tallyGstRecordsArray = [];
        if(count($transGstRecords)) {
            foreach($transGstRecords as $transGstRecord) {
                $userInvTrans = $transGstRecord->userInvTrans;
                if ($userInvTrans->sgst_amount > 0 && $userInvTrans->cgst_amount > 0) {
                   $data['trans_gst1_amount'] = round(($userInvTrans->sgst_amount + $userInvTrans->cgst_amount), 2);
                   array_push($transGstRecordsArray, $data);
                }elseif($userInvTrans->igst_amount > 0) {
                   $data1['trans_gst2_amount'] = round($userInvTrans->igst_amount, 2);
                   array_push($transGstRecordsArray, $data1);
                }
            }
        }
        if(count($tallyGstRecords)) {
            foreach($tallyGstRecords as $tallyGstRecord) {
                if ($tallyGstRecord['trans_name'] == 'SGST + CGST') {
                   $data2['tally_gst1_amount'] = round($tallyGstRecord['tally_gst_amount'], 2);
                   array_push($tallyGstRecordsArray, $data2);
                }elseif($tallyGstRecord['trans_name'] == 'IGST') {
                   $data3['tally_gst2_amount'] = round($tallyGstRecord['tally_gst_amount'], 2);
                   array_push($tallyGstRecordsArray, $data3);
                }
            }
        }
        $totalTransGst1Amt = array_sum(array_column($transGstRecordsArray, 'trans_gst1_amount'));
        $totalTransGst2Amt = array_sum(array_column($transGstRecordsArray, 'trans_gst2_amount'));
        $totalTallyGst1Amt = array_sum(array_column($tallyGstRecordsArray, 'tally_gst1_amount'));
        $totalTallyGst2Amt = array_sum(array_column($tallyGstRecordsArray, 'tally_gst2_amount'));
        return [
            'gst_type_1' => [
                'transCount' => count(array_column($transGstRecordsArray, 'trans_gst1_amount')),
                'transAmt' => $totalTransGst1Amt,
                'tallyCount' => count(array_column($tallyGstRecordsArray, 'tally_gst1_amount')),
                'tallyAmt' => $totalTallyGst1Amt,
            ],
            'gst_type_2' => [
                'transCount' => count(array_column($transGstRecordsArray, 'trans_gst2_amount')),
                'transAmt' => $totalTransGst2Amt,
                'tallyCount' => count(array_column($tallyGstRecordsArray, 'tally_gst2_amount')),
                'tallyAmt' => $totalTallyGst2Amt,
            ],
        ];
    }
}
