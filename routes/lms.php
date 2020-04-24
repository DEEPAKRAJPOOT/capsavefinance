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
              Route::get('/limit_management', [
                'as' => 'limit_management',
                'uses' => 'Lms\CustomerController@limitManagement'
            ]);
              
            Route::post('/save-bank-account', [
                'as' => 'save_bank_account',
                'uses' => 'Lms\BankAccountController@saveBankAccount'
            ]);

            Route::get('/see-upload-bank-detail', [
                'as' => 'see_upload_bank_detail',
                'uses' => 'Lms\BankAccountController@seeUploadFile'
            ]);

            Route::get('/download-bank-detail', [
                'as' => 'download_bank_detail',
                'uses' => 'Lms\BankAccountController@downloadUploadFile'
            ]);

            // User Invoice
            Route::get('/view-user-invoice', [
                'as' => 'view_user_invoice',
                'uses' => 'Lms\userInvoiceController@getUserInvoice'
            ]);
            
            Route::get('/create-user-invoice', [
                'as' => 'create_user_invoice',
                'uses' => 'Lms\userInvoiceController@createUserInvoice'
            ]);
            
            Route::post('/save_user_invoice', [
                'as' => 'save_user_invoice',
                'uses' => 'Lms\userInvoiceController@saveUserInvoice'
            ]);

            // get bissuness address in user invoice 
            Route::post('get-biz-add-user-invoice', [
                'as' => 'get_biz_add_user_invoice',
                'uses' => 'Lms\userInvoiceController@getBizUserInvoiceAddr'
            ]);

            // get gstins in user invoice 
            Route::post('get_app_gstin', [
                'as' => 'get_app_gstin',
                'uses' => 'Lms\userInvoiceController@getGstinOfApp'
            ]);

            // get state code for user invoice 
            Route::post('get-user-state-code', [
                'as' => 'get_user_state_code',
                'uses' => 'Lms\userInvoiceController@getUserStateCode'
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
                'uses' => 'Lms\RefundController@customerList'
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


            Route::get('/lms-create-batch', [
                'as' => 'lms_create_batch',
                'uses' => 'Lms\RefundController@createBatch'
            ]);

            Route::get('/lms-edit-batch', [
                'as' => 'lms_edit_batch',
                'uses' => 'Lms\RefundController@editBatch'
            ]);

            Route::get('/refund/list',[
                'as' => 'lms_refund_new',
                'uses' => 'Lms\RefundController@refundListNew'
            ]);

            Route::get('/refund/pending',[
                'as' => 'lms_refund_pending',
                'uses' => 'Lms\RefundController@refundListPending'
            ]);

            Route::get('/refund/approved',[
                'as' => 'lms_refund_approved',
                'uses' => 'Lms\RefundController@refundListApproved'
            ]);

            Route::get('/refund/request',[
                'as' => 'request_list',
                'uses' => 'Lms\RefundController@refundListRequest'
            ]);

            Route::get('/refund/confirm',[
                'as' => 'refund_confirm',
                'uses' => 'Lms\RefundController@refundConfirm'
            ]);
            
            Route::post('/refund-offline', [
                'as' => 'refund_offline',
                'uses' => 'Lms\RefundController@refundOffline'
            ]);

            Route::get('refund/download-sentbank-data', [
                'as' => 'download_sentbank',
                'uses' => 'Lms\RefundController@downloadSentBank'
            ]);

            Route::get('/refund/sentbank',[
                'as' => 'lms_refund_sentbank',
                'uses' => 'Lms\RefundController@refundListSentBank'
            ]);

            Route::get('/refund/refunded',[
                'as' => 'lms_refund_refunded',
                'uses' => 'Lms\RefundController@refundListRefunded'
            ]);

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
            
            Route::get('req-move-next-stage',[
                'as' => 'lms_req_move_next_stage',
                'uses' => 'Lms\RefundController@moveReqToNextStage'
            ]);
            Route::get('req-move-prev-stage',[
                'as' => 'lms_req_move_prev_stage',
                'uses' => 'Lms\RefundController@moveReqToNextStage'
            ]);    
            
            Route::post('accept-request-stage',[
                'as' => 'lms_accept_next_stage',
                'uses' => 'Lms\RefundController@acceptReqStage'
            ]); 
            
            Route::get('update-request-status',[
                'as' => 'lms_update_request_status',
                'uses' => 'Lms\RefundController@updateRequestStatus'
            ]);    
            
            Route::post('save-request-status',[
                'as' => 'lms_save_request_status',
                'uses' => 'Lms\RefundController@saveRequestStatus'
            ]); 
              
            Route::get('view-process-refund',[
                'as' => 'lms_view_process_refund',
                'uses' => 'Lms\RefundController@viewProcessRefund'
            ]);   
            
            Route::post('process-refund',[
                'as' => 'lms_process_refund',
                'uses' => 'Lms\RefundController@processRefund'
            ]); 
            
        });
        
        //end of application
});

