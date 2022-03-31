<?php

namespace App\Inv\Repositories\Observers;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use App\Inv\Repositories\Models\Lms\CustomerTransactionSOA;
use App\Http\Controllers\Lms\userInvoiceController;

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
            if($transaction->transType->chrg_master_id > 0 && !in_array($transaction->trans_type,[33])){
                $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
                $invType = 'C';
                $appId = $transaction->ChargesTransactions->app_id ?? null;
                $controller->generateCapsaveInvoice([$transaction->trans_id], $transaction->user_id, $invType, $appId);
            }
            // elseif(in_array($transaction->trans_type, [9])){
            //     $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
            //     $invType = 'I';
            //     $appId = $transaction->invoiceDisbursed->invoice->app_id ?? null;
            //     $controller->generateCapsaveInvoice([$transaction->trans_id], $transaction->user_id, $invType);
            // }
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
        InvoiceDisbursedDetail::updateTransactionDetails($transaction);
        CustomerTransactionSOA::updateTransactionSOADetails($transaction->user_id);
    }

    /**
     * Handle the Transactions "deleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function deleted(Transactions $transaction)
    {
        //$transaction->deleteAllChild();
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
        //$transaction->deleteAllChild();
        $transaction->calculateOutstandingsDelete();
        InvoiceDisbursedDetail::forceDeletedTransactionDetails($transaction);
        CustomerTransactionSOA::forceDeletedTransactionSOADetails($transaction);
    }
}
?>