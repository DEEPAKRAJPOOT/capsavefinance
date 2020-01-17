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
            Route::get('/disbursal-request/list', [
                'as' => 'lms_disbursal_request_list',
                'uses' => 'Lms\DisbursalController@requestList'
            ]);

            Route::get('/disbursal-request/view-invoice', [
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
            
        });//end of application
        
	});

});

