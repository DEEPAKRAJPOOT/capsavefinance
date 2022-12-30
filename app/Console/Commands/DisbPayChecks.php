<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Payment;
use DB;

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
        $this->eodDate = now()->parse('2022-12-12')->toDateString();
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
            //dd($this->eodDate);
            $dupPayments = $this->checkDuplicatePaymentRecords();
            $dupDisbursals = $this->checkDuplicateDisbursalRecords();
            $actualDisbursals = $this->checkActualDisbursalAmount();
            dd($actualDisbursals);
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
        DB::enableQueryLog();
        $dupPayments = Payment::withTrashed()->select(DB::raw('GROUP_CONCAT(payment_id) as payment_ids, user_id as customer_id, amount, CONCAT_WS("", utr_no, unr_no, cheque_no) AS com_utr_no, count(*) AS paymentCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->where('trans_type', config('lms.TRANS_TYPE.REPAYMENT'))
                            ->where('action_type', 1)
                            ->groupBy(['user_id', 'amount', 'utr_no', 'unr_no', 'cheque_no'])
                            ->havingRaw('paymentCount > 1')
                            ->get();
        //dd(DB::getQueryLog());                    
        return $dupPayments;
    }

    private function checkDuplicateDisbursalRecords()
    {
        DB::enableQueryLog();
        $dupDisbursals = Disbursal::select(DB::raw('GROUP_CONCAT(disbursal_id) as disbursal_ids, user_id as customer_id, disburse_amount as amount, count(*) AS disbCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->groupBy(['user_id', 'disburse_amount', 'disbursal_batch_id'])
                            ->havingRaw('disbCount > 1')
                            ->get();
            //dd(DB::getQueryLog());  
        return $dupDisbursals;
    }

    private function checkActualDisbursalAmount(){

        DB::enableQueryLog();

       $actualDisbursals =  Disbursal::select(DB::raw("rta_disbursal.disbursal_id,rta_disbursal.user_id as customer_id, rta_disbursal.disburse_amount as amount,count(*) AS disbCount,(rta_inv.invoice_amount - rta_inv_disb.total_interest - (rta_inv.invoice_amount*rta_inv_disb.margin/100)) AS actual_disbursed_amount,rta_inv_disb.*,rta_trans.*,rta_offer.*"))
        ->join('invoice_disbursed as inv_disb', 'disbursal.disbursal_id', '=', 'inv_disb.disbursal_id')
        ->leftJoin('transactions as trans', 'inv_disb.invoice_disbursed_id', '=', 'trans.invoice_disbursed_id')
        ->leftJoin('invoice as inv', 'inv_disb.invoice_id', '=', 'inv.invoice_id')
        ->leftJoin('app_prgm_offer as offer', 'inv.prgm_offer_id', '=', 'offer.prgm_offer_id')
        ->whereDate('disbursal.created_at', $this->eodDate)
        ->where('trans.trans_type' ,'=', 16)
        ->where('trans.entry_type', '=', 0)
        ->where('disbursal.disburse_amount','!=',DB::raw('(rta_inv.invoice_amount - rta_inv_disb.total_interest - (rta_inv.invoice_amount*rta_inv_disb.margin/100))'))->groupBy(['disbursal.user_id', 'disbursal.disburse_amount', 'disbursal.disbursal_batch_id'])
        ->get();
       // dd(DB::getQueryLog());
       return $actualDisbursals;
    }

}
