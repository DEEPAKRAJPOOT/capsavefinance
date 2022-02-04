<?php

namespace App\Inv\Repositories\Observers;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use App\Inv\Repositories\Models\Lms\CustomerTransactionSOA;

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