<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Helpers;
use App\Helpers\Helper;
use App\Inv\Repositories\Models\Lms\Transactions;



class GenerateCreditNoteReversal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'note:generateCreditNoteReversal
    {user=all : The ID of the user};
    {invoice=null : Invoice type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Credit Note Reversal';

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
        $curdate = Helpers::getSysStartDate();
        $cancelTransList = Transactions::whereNotNull('link_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE')])
            ->whereHas('userInvLinkTrans.getUserInvoice')
            ->where('entry_type','0')
            ->where('is_invoice_generated','0');

        if($userId){
            $cancelTransList->where('user_id',$userId);
        }

        $cancelTransList = $cancelTransList->with('userInvLinkTrans:trans_id,user_invoice_id','userInvLinkTrans.getUserInvoice:user_invoice_id,user_invoice_rel_id')->get();

        $creditData = [];
        foreach($cancelTransList as $trans){
            $billType = null;
            if($trans->parentTransactions->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                $billType = 'I';
            }elseif($trans->parentTransactions->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $billType = 'I';
            }elseif($trans->parentTransactions->trans_type >= 50){
                $billType = 'C';
            }else{
                $billType = $trans->parentTransactions->trans_type;
            }

            $creditData[$trans->user_id][$billType][$trans->gst.'_'.$trans->userInvLinkTrans->getUserInvoice->user_invoice_rel_id][$trans->trans_id] = $trans->trans_id;
        }
        foreach($creditData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstRelation){
                foreach ($gstRelation as $gstRelCode => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateCreditNoteReversal($transIds, $userId, $billType);
                    }
                }
            }
        }
         $this->info('successfully completed.');
    }
}
