<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Helpers;
use App\Helpers\Helper;
use App\Inv\Repositories\Models\Lms\Transactions;



class GenerateCreditNote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'note:generateCreditNote
    {user=all : The ID of the user};
    {invoice=null : Invoice type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Credit Note';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * /var/www/html/rentalpha/app/Http/Controllers/Lms/userInvoiceController.php
     */
    public function handle()
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        $userId = $this->argument('user');
        $invoiceType = $this->argument('invoice');
        $controller = \App::make('App\Http\Controllers\Lms\userInvoiceController');
        $curdate = Helpers::getSysStartDate();
        $cDate = Carbon::parse($curdate)->format('Y-m-d');
        $cancelTransList = Transactions::whereNotNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.WAVED_OFF')])
        ->whereHas('userInvParentTrans.getUserInvoice')
        ->whereDate('created_at','=',$cDate)
        ->where('entry_type','1')
        ->where('is_invoice_generated','0')
        ->with('userInvParentTrans:trans_id,user_invoice_id','userInvParentTrans.getUserInvoice:user_invoice_id,user_invoice_rel_id','userInvParentTrans.trans:trans_id,invoice_disbursed_id','userInvParentTrans.trans.invoiceDisbursed:invoice_disbursed_id,invoice_id','userInvParentTrans.trans.invoiceDisbursed.invoice:invoice_id,program_id,invoice_no','userInvParentTrans.trans.invoiceDisbursed.invoice.program:prgm_id,interest_borne_by,overdue_interest_borne_by','userInvParentTrans.trans.ChargesTransactions:trans_id,prgm_id','userInvParentTrans.trans.ChargesTransactions.chargePrgm:prgm_id,interest_borne_by')
        ->get();

        $creditData = [];
        $userInvoices = [];
        foreach($cancelTransList as $trans){
            $billType = null;
            $userInvoiceTrans = $trans->userInvParentTrans;
            if(isset($userInvoiceTrans)){
                $userInvoices[$userInvoiceTrans->user_invoice_id] = isset($userInvoices[$userInvoiceTrans->user_invoice_id]) ? $userInvoices[$userInvoiceTrans->user_invoice_id] : $userInvoiceTrans->getUserInvoice;
            }

            if(isset($userInvoices[$userInvoiceTrans->user_invoice_id])){
                $invTypeName = $userInvoices[$userInvoiceTrans->user_invoice_id]->invoice_type_name == 1 ? 'C' : 'I';
                $invBorneBy = $userInvoices[$userInvoiceTrans->user_invoice_id]->invoice_borne_by == 1 ? 'A' : 'C';
                $billType = $invTypeName.$invBorneBy;
            }

            $creditData[$trans->user_id][$billType][$trans->gst.'_'.$userInvoices[$userInvoiceTrans->user_invoice_id]->user_invoice_rel_id][$trans->trans_id] = $trans->trans_id;
        }
        foreach($creditData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstRelation){
                foreach ($gstRelation as $gstRelCode => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateCreditNote($transIds, $userId, $billType, null, null, 1);
                    }
                }
            }
        }
         $this->info('successfully completed.');
    }
}
