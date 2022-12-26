<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Payment;

class DisbPayChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disb_pays:checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check disbursements and payments of eod';

    protected $eodDate = '';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->eodDate = now()->toDateString();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);

        try {
            $dupPayments = $this->checkDuplicatePaymentRecords();
            $dupDisbursals = $this->checkDuplicateDisbursalRecords();

            if ($dupPayments || $dupDisbursals) {
                $emailData['disbursals'] = $dupDisbursals ?? [];
                $emailData['payments']   = $dupPayments ?? [];
                \Event::dispatch("NOTIFY_DISB_PAY_CHECKS", serialize($emailData));
            }
        } catch (\Exception $ex) {
            dd(\Helpers::getExceptionMessage($ex));
        }    

        // Disbursal::whereHas('invoice_disbursed', function($query) {
            
        // });
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
}
