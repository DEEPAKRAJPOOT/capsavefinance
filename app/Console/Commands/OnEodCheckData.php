<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use Illuminate\Support\Facades\Storage;
use PHPExcel_IOFactory;
use Carbon\Carbon;
use PHPExcel;
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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->eodDate = now()->toDateString();
        // $this->eodDate = '2022-10-13';
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
        $dupPayments = $this->checkDuplicatePaymentRecords();
        $dupDisbursals = $this->checkDuplicateDisbursalRecords(); 
        $tally_data = $this->checkEODTallyRecords();

        $emailData['disbursals'] = $dupDisbursals;
        $emailData['payments']   = $dupPayments;
        $emailData['tally_data'] = $tally_data;
        $emailData['to']         = $this->emailTo;
        $emailData['cc']         = $this->emailCc;
        $emailData['bcc']        = $this->emailBcc;
        \Event::dispatch("NOTIFY_EOD_CHECKS", serialize($emailData));
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
        $where = [/*['is_posted_in_tally', '=', '0'],*/ ['created_at', '>=', "$this->eodDate 00:00:00"],['created_at', '<=', "$this->eodDate 23:59:59"]];
        $journalArray = Transactions::getJournalTxnTally($where)->toArray();
        $disbursalArray = Transactions::getDisbursalTxnTally($where)->toArray();
        $refundArray = Transactions::getRefundTxnTally($where)->toArray();

        $tally_data = array_merge($disbursalArray, $journalArray, $refundArray);
        $tally_trans_ids = array_column($tally_data, 'trans_id');
        $transactions = Transactions::whereIn('trans_id', $tally_trans_ids)
                                ->doesntHave('tallyEntry')
                                ->get();
        return $transactions;
    }
}
