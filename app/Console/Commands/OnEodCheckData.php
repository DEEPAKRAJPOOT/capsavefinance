<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->eodDate = now()->toDateString();
        // $this->eodDate = '2022-10-01';
        $this->emailTo = 'pankaj.sharma@zuron.in';
        $this->emailCc = '';
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
        $tallyBatches = DB::table('tally')
                        ->whereNull('is_validated')
                        ->whereNotNull('start_date')
                        ->whereNotNull('end_date')
                        ->groupBy('start_date', 'end_date')
                        ->get();

        foreach($tallyBatches as $tallyBatch) {
            $this->processTallyBatch($tallyBatch);
        }

        if (count($this->tallyData)) {
            $emailData['disbursals'] = $dupDisbursals ?? [];
            $emailData['payments']   = $dupPayments ?? [];
            $emailData['tally_data'] = $this->tallyData;
            $emailData['to']         = $this->emailTo;
            $emailData['cc']         = $this->emailCc;
            $emailData['bcc']        = $this->emailBcc;
            \Event::dispatch("NOTIFY_EOD_CHECKS", serialize($emailData));
        }
    }

    public function processTallyBatch($tallyBatch)
    {
        $startDate = $tallyBatch->start_date;/*"$this->eodDate 00:00:00";*/
        $endDate = $tallyBatch->end_date;/*"$this->eodDate 23:59:59";*/
        $where = [/*['is_posted_in_tally', '=', '0'],*/ ['created_at', '>=', $startDate],['created_at', '<=', $endDate]];

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
                                ->where('entry_type', 1)
                                ->whereNotNull('payment_id')
                                ->whereNotIn('trans_type', [
                                    config('lms.TRANS_TYPE.REFUND'),
                                ])
                                ->pluck('trans_id')
                                ->toArray();

        $tally_trans_ids = array_unique(array_merge($journalData, $bankingData));
        $tally_data = DB::table('tally')
                        ->select(DB::raw('DISTINCT rta_tally_entry.transactions_id'))
                        ->where('start_date', $startDate)
                        ->where('end_date', $endDate)
                        ->join('tally_entry', 'tally_entry.batch_no', '=', 'tally.batch_no')
                        ->whereIn('tally_entry.transactions_id', $tally_trans_ids)
                        ->get()
                        ->toArray();
        $uniqTallyTransIds = array_column($tally_data, "transactions_id");
        $diffUniqTallyTransIds = array_diff($tally_trans_ids, $uniqTallyTransIds);

        if (count($diffUniqTallyTransIds)) {
            $updateQuery = ['is_validated' => 0]; 
            
            $tally_data = Transactions::select(DB::raw('COUNT(*) as transCount, trans_type, rta_mst_trans_type.trans_name as transaction_name, GROUP_CONCAT(trans_id SEPARATOR "|") as trans_ids'))
                                    ->join('mst_trans_type', 'mst_trans_type.id', '=', 'transactions.trans_type')
                                    ->whereIn('trans_id', $diffUniqTallyTransIds)
                                    ->groupBy('trans_type')
                                    ->get()
                                    ->toArray();
            $this->tallyData["$startDate|$endDate"] = $tally_data;
        }else {
            $updateQuery = ['is_validated' => 1];
        }

        // To update tally validate status on tally table
        DB::table('tally')
                ->where('start_date', $startDate)
                ->where('end_date', $endDate)
                ->update($updateQuery);
    }
}
