<?php

namespace App\Inv\Repositories\Observers;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;

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
        InvoiceDisbursedDetail::createTransactionDetails($transaction);
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
    }

    /**
     * Handle the Transactions "deleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function deleted(Transactions $transaction)
    {
        InvoiceDisbursedDetail::deleteTransactionDetails($transaction);
    }

    /**
     * Handle the Transactions "forceDeleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\Transactions  $transaction
     * @return void
     */
    public function forceDeleted(Transactions $transaction)
    {
        InvoiceDisbursedDetail::forceDeletedTransactionDetails($transaction);
    }
}
?>