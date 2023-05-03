<?php

namespace App\Inv\Repositories\Observers;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use App\Inv\Repositories\Models\Lms\CustomerTransactionSOA;
use App\Http\Controllers\Lms\userInvoiceController;
use App\Inv\Repositories\Models\Master\Charges;

class TransactionsObserver
{
    /**
     * Handle the Transactions "created" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function created(Transactions $transaction)
    {
        $transaction->calculateOutstandingsCreate();
        InvoiceDisbursedDetail::createTransactionDetails($transaction);
        CustomerTransactionSOA::createTransactionSOADetails($transaction);
        if($transaction->entry_type == 0 &&  is_null($transaction->parent_trans_id)){
            // Temporarily prevented for overdue interest by sudesh
            $chrgMstId = $transaction->transType->chrg_master_id;
            if($chrgMstId > 0 && !in_array($transaction->trans_type,[config('lms.TRANS_TYPE.INTEREST_OVERDUE'), config('lms.TRANS_TYPE.INVOICE_PROCESSING_FEE')])){
                $levelBaseChrg = Charges::getChargeLevel($chrgMstId);
                if(isset($levelBaseChrg)){
                    $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
                    if($levelBaseChrg->level_charges == 2){
                        $invType = 'CC';
                    }else{
                        $invType = 'CA';
                    }
                    $appId = $transaction->ChargesTransactions->app_id ?? null;
                    $controller->generateDebitNote([$transaction->trans_id], $transaction->user_id, $invType, $appId);
                }
            }
        }
    }

    /**
     * Handle the Transactions "updated" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function updated(Transactions $transaction)
    {
    }

    /**
     * Handle the Transactions "deleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function deleted(Transactions $transaction)
    {
        $transaction->calculateOutstandingsDelete();
        InvoiceDisbursedDetail::deleteTransactionDetails($transaction);
        CustomerTransactionSOA::deleteTransactionSOADetails($transaction);
    }

    /**
     * Handle the Transactions "forceDeleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function forceDeleted(Transactions $transaction)
    {
        $transaction->calculateOutstandingsDelete();
        InvoiceDisbursedDetail::forceDeletedTransactionDetails($transaction);
        CustomerTransactionSOA::forceDeletedTransactionSOADetails($transaction);
    }
}
?>