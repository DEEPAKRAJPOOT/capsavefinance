<?php

namespace App\Inv\Repositories\Observers;

use App\Inv\Repositories\Models\Lms\ChargesTransactions;


class ChargeTransactionObserver
{
    /**
     * Handle the ChargesTransactions "created" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\ChargesTransactions  $chrgTransaction
     * @return void
     */
    public function created(ChargesTransactions $chrgTransaction)
    {
        if($chrgTransaction->level_charges == 2){
            $invType = 'CC';
        }
        elseif($chrgTransaction->level_charges == 1){
            $invType = 'CA';
        }
        
        $appId = $chrgTransaction->app_id ?? NULL;
        $userId = $chrgTransaction->transaction->user_id ?? NULL;

        if($appId && $userId && $chrgTransaction->trans_id){
            $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
            $controller->generateDebitNote([$chrgTransaction->trans_id], $userId, $invType);
            // $controller->generateDebitNote([$chrgTransaction->trans_id], $userId, $invType, $appId);
        } 
    }

    /**
     * Handle the ChargesTransactions "updated" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\ChargesTransactions  $chrgTransaction
     * @return void
     */
    public function updated(ChargesTransactions $chrgTransaction)
    {
    }

    /**
     * Handle the ChargesTransactions "deleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\ChargesTransactions  $chrgTransaction
     * @return void
     */
    public function deleted(ChargesTransactions $chrgTransaction)
    {
    }

    /**
     * Handle the ChargesTransactions "forceDeleted" event.
     *
     * @param  \App\Inv\Repositories\Models\Lms\ChargesTransactions  $chrgTransaction
     * @return void
     */
    public function forceDeleted(ChargesTransactions $chrgTransaction)
    {
    }
}
?>