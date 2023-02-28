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
    protected $signature = 'note:generateDebitNote
    {user=all : The ID of the user};
    {invoice=null : Invoice type}';

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
        $userId = $this->argument('user');
        $invoiceType = $this->argument('invoice');
        $controller = \App::make('App\Http\Controllers\Lms\userInvoiceController');
        $billData = [];
        $curdate = Helpers::getSysStartDate();
        $transList = Transactions::whereNull('parent_trans_id')
        ->whereHas('transType', function($query){
            $query->where('chrg_master_id','>','0')
            ->orWhereIn('id',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
        })
        ->whereDate('created_at','>=','2023-01-01')
        ->where('entry_type','0')
        ->where('is_invoice_generated','0')
        ->get();
        $billData = [];
        foreach($transList as $trans){
            $billType = null;
            if($trans->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                $billType = 'I';
            }elseif($trans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $billType = 'I';
            }elseif($trans->trans_type >= 50){
                $billType = 'C';
            }

            $billData[$trans->user_id][$billType][$trans->gst][$trans->trans_id] = $trans->trans_id;
        }

        foreach($billData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstTypes){
                foreach ($gstTypes as $gst => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateDebitNote($transIds, $userId, $billType);
                    }
                }
            }
        }
        $this->info('Successfully completed.');
    }
}
