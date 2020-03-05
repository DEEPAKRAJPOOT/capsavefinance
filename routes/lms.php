<?php

/**
 * This route have all lms auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {
    Route::group(['middleware' => 'auth'], function () {

        Route::group(['prefix' => 'lms'], function () {
        	
            Route::get('/upload-pf-df/{userId}/{appId}', [
                'as' => 'lms_get_customer_list',
                'uses' => 'Lms\DisbursalController@uploadPfDf'
            ]);

            Route::get('/', [
                'as' => 'lms_get_customer_list',
                'uses' => 'Lms\CustomerController@list'
            ]);

            Route::get('/applications', [
                'as' => 'lms_get_customer_applications',
                'uses' => 'Lms\CustomerController@listAppliction'
            ]);

            Route::get('/invoices', [
                'as' => 'lms_get_application_invoice',
                'uses' => 'Lms\CustomerController@listInvoice'
            ]);
            
            Route::get('/bank-account', [
                'as' => 'lms_get_bank_account',
                'uses' => 'Lms\BankAccountController@bankAccountList'
            ]);
            
            Route::get('/add-bank-account', [
                'as' => 'add_bank_account',
                'uses' => 'Lms\BankAccountController@addBankAccount'
            ]);
            
            Route::post('/save-bank-account', [
                'as' => 'save_bank_account',
                'uses' => 'Lms\BankAccountController@saveBankAccount'
            ]);
            
            // disbursal routes
            Route::get('/disbursal/request-list', [
                'as' => 'lms_disbursal_request_list',
                'uses' => 'Lms\DisbursalController@requestList'
            ]);


            Route::get('/disbursal/view-invoice', [
                'as' => 'lms_disbursal_invoice_view',
                'uses' => 'Lms\DisbursalController@viewInvoice'
            ]);

            Route::post('/send-to-bank', [
                'as' => 'send_to_bank',
                'uses' => 'Lms\DisbursalController@sendToBank'
            ]);

            Route::get('/confirm-disburse', [
                'as' => 'confirm_disburse',
                'uses' => 'Lms\DisbursalController@confirmDisburse'
            ]);
            
            Route::get('/interest-accrual', [
                'as' => 'lms_interest_accrual',
                'uses' => 'Lms\DisbursalController@processAccrualInterest'
            ]);
            
            Route::get('/disbursal/disbursed-list', [
                'as' => 'lms_disbursed_list',
                'uses' => 'Lms\DisbursalController@disbursedList'
            ]);

            Route::get('/soa/list', [
                'as' => 'lms_get_transaction',
                'uses' => 'Lms\SoaController@list'
            ]);
           Route::get('/charges/manage_charge', [
                'as' => 'manage_charge',
                'uses' => 'Lms\ChargeController@manageCharge'
            ]);
          
           
            Route::post('save_manual_charges', [
                'as' => 'save_manual_charges',
                'uses' => 'Lms\ChargeController@saveManualCharges'
            ]); 
           
            Route::get('/edit-lms-charges', [
                'as' => 'edit_lms_charges',
                'uses' => 'Lms\ChargeController@editLmsCharges'
            ]);
             Route::get('/list-lms-charges', [
                'as' => 'list_lms_charges',
                'uses' => 'Lms\ChargeController@listLmsCharges'
            ]);
             
             
            Route::get('view-interest-accrual', [
                'as' => 'view_interest_accrual',
                'uses' => 'Lms\DisbursalController@viewInterestAccrual'
            ]);
             
            Route::get('get-lms-charges-edit', [
                'as' => 'get_lms_charges_edit',
                'uses' => 'Lms\ChargeController@editLmsCharges'
            ]);  
        
            Route::get('payment-settlement',[
                'as' => 'lms-payment-settlement',
                'uses' => 'Lms\DisbursalController@processInvoiceSettlement' 
            ]);
            
            // manage refund routes 

            Route::get('/refund/refund-list', [
                'as' => 'lms_refund_list',
                'uses' => 'Lms\RefundController@refundList'
            ]);
                          
            Route::get('view-interest-accrual', [
                'as' => 'view_interest_accrual',
                'uses' => 'Lms\DisbursalController@viewInterestAccrual'
            ]);

            Route::get('/confirm-refund', [
                'as' => 'confirm_refund',
                'uses' => 'Lms\RefundController@confirmRefund'
            ]);
            
            Route::post('/send-refund', [
                'as' => 'lms_send_refund',
                'uses' => 'Lms\RefundController@sendRefund'
            ]);
        });//end of application

        // Business address
        Route::get('/address', [
            'as' => 'addr_get_customer_list',
            'uses' => 'Lms\AddressController@list'
        ]);

        Route::get('/add_addr', [
            'as' => 'add_addr',
            'uses' => 'Lms\AddressController@addAddress'
        ]);

        Route::post('/save_addr', [
            'as' => 'save_addr',
            'uses' => 'Lms\AddressController@saveAddress'
        ]);

        Route::get('/edit_addr', [
            'as' => 'edit_addr',
            'uses' => 'Lms\AddressController@editAddress'
        ]);
          Route::post('/copy_app', [
                'as' => 'copy_app',
                'uses' => 'Lms\CopyController@duplicateApp'
            ]); 
        // end address
	});

});

