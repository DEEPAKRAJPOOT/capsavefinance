<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Helpers;
use App\Helpers\Helper;
use App\Inv\Repositories\Models\Lms\Transactions;



class GenerateDebitNote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'note:generateDebitNote';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Debit Note';

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
        $controller = \App::make('App\Http\Controllers\Lms\userInvoiceController');
        $billData = [];
        $transList = Transactions::with('invoiceDisbursed:invoice_disbursed_id,invoice_id',
        'invoiceDisbursed.invoice:invoice_id,program_id,invoice_no',
        'invoiceDisbursed.invoice.program:prgm_id,interest_borne_by,overdue_interest_borne_by',
        'ChargesTransactions:trans_id,prgm_id','ChargesTransactions.chargePrgm:prgm_id,interest_borne_by')
        ->whereNull('parent_trans_id')
        ->whereDate('created_at','>=','2023-01-01')
        ->where('entry_type','0')
        ->where('invoice_disbursed_id','!=','NULL')
        ->where('is_invoice_generated','0')
        ->whereHas('transType', function($query){
            $query->where('chrg_master_id','>','0')
            ->orWhereIn('id',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
        })
        ->get();
        $billData = [];
        foreach($transList as $trans){
            $billType = null;
            if($trans->trans_type == config('lms.TRANS_TYPE.INTEREST') ){
                if(isset($trans->invoiceDisbursed->invoice->program)){
                    if($trans->invoiceDisbursed->invoice->program->interest_borne_by == 2){
                        $billType = 'IC';
                    }else{
                        $billType = 'IA';
                    }
                }
            }elseif($trans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if(isset($trans->invoiceDisbursed->invoice->program)){
                    if($trans->invoiceDisbursed->invoice->program->overdue_interest_borne_by == 2){
                        $billType = 'IC';
                    }else{
                        $billType = 'IA';
                    }
                }
            }elseif($trans->trans_type >= 50){
                if(isset($trans->ChargesTransactions->chargePrgm)){
                    if($trans->ChargesTransactions->chargePrgm->interest_borne_by == 2){
                        $billType = 'CC';
                    }else{
                        $billType = 'CA';
                    }
                }
            }

            $billData[$trans->user_id][$billType][$trans->gst][$trans->trans_id] = $trans->trans_id;
        }

        foreach($billData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstTypes){
                foreach ($gstTypes as $gst => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateDebitNote($transIds, $userId, $billType, null, null, 1);
                    }
                }
            }
        }
        $this->info('Successfully completed.');
    }
}
