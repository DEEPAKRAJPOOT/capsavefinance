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
                'uses' => 'Lms\DisbursalController@uploadPfDf',
            ]);

            Route::get('/cibil_report', [
                'as' => 'cibil_report',
                'uses' => 'Lms\CibilReportController@index',
            ]);

            Route::get('/download_cibil', [
                'as' => 'download_lms_cibil_reports',
                'uses' => 'Lms\CibilReportController@downloadCibilReport'
            ]);

            Route::get('/', [
                'as' => 'lms_get_customer_list',
                'uses' => 'Lms\CustomerController@list',
            ]);

            Route::get('/applications', [
                'as' => 'lms_get_customer_applications',
                'uses' => 'Lms\CustomerController@listAppliction',
            ]);

            Route::get('/invoices', [
                'as' => 'lms_get_application_invoice',
                'uses' => 'Lms\CustomerController@listInvoice',
            ]);

            Route::get('/bank-account', [
                'as' => 'lms_get_bank_account',
                'uses' => 'Lms\BankAccountController@bankAccountList',
            ]);

            Route::get('/add-bank-account', [
                'as' => 'add_bank_account',
                'uses' => 'Lms\BankAccountController@addBankAccount',
            ]);

            Route::get('/limit_management', [
                'as' => 'limit_management',
                'uses' => 'Lms\CustomerController@limitManagement',
            ]);

            Route::post('/save-bank-account', [
                'as' => 'save_bank_account',
                'uses' => 'Lms\BankAccountController@saveBankAccount',
            ]);

            Route::get('/see-upload-bank-detail', [
                'as' => 'see_upload_bank_detail',
                'uses' => 'Lms\BankAccountController@seeUploadFile',
            ]);

            Route::get('/download-bank-detail', [
                'as' => 'download_bank_detail',
                'uses' => 'Lms\BankAccountController@downloadUploadFile',
            ]);

            Route::get('/add-adhoc-limit', [
                'as' => 'add_adhoc_limit',
                'uses' => 'Lms\CustomerController@addAdhocLimit',
            ]);

            Route::post('/save-adhoc-limit', [
                'as' => 'save_adhoc_limit',
                'uses' => 'Lms\CustomerController@saveAdhocLimit',
            ]);

            Route::get('approve-adhoc-limit', [
                'as' => 'approve_adhoc_limit',
                'uses' => 'Lms\CustomerController@openApproveAdhocLimit',
            ]);

            Route::post('save-approve-adhoc-limit', [
                'as' => 'save_approve_adhoc_limit',
                'uses' => 'Lms\CustomerController@approveAdhocLimit',
            ]);
            // User Invoice
            Route::get('/view-user-invoice', [
                'as' => 'view_user_invoice',
                'uses' => 'Lms\userInvoiceController@listUserInvoice',
            ]);

            Route::post('/get_invoice_transaction', [
                'as' => 'get_invoice_transaction',
                'uses' => 'Lms\userInvoiceController@getUserInvoiceTxns',
            ]);

            Route::get('/create-user-invoice', [
                'as' => 'create_user_invoice',
                'uses' => 'Lms\userInvoiceController@createUserInvoice',
            ]);

            Route::post('/save_user_invoice', [
                'as' => 'save_user_invoice',
                'uses' => 'Lms\userInvoiceController@saveUserInvoice',
            ]);

            Route::post('get-user-state-code', [
                'as' => 'get_user_state_code',
                'uses' => 'Lms\userInvoiceController@getUserStateCode',
            ]);

            Route::post('preview-user-invoice', [
                'as' => 'preview_user_invoice',
                'uses' => 'Lms\userInvoiceController@previewUserInvoice',
            ]);

            Route::get('download_user_invoice', [
                'as' => 'download_user_invoice',
                'uses' => 'Lms\userInvoiceController@downloadUserInvoice',
            ]);

            Route::get('user-invoice-location', [
                'as' => 'user_invoice_location',
                'uses' => 'Lms\userInvoiceController@userInvoiceLocation',
            ]);
            Route::post('save-user-invoice-location', [
                'as' => 'save_user_invoice_location',
                'uses' => 'Lms\userInvoiceController@saveUserInvoiceLocation',
            ]);

            // user_invoice relation get state id for user
            Route::get('get-user-invoice-unpublished', [
                'as' => 'get_user_invoice_unpublished',
                'uses' => 'Lms\userInvoiceController@unpublishUsereAddr',
            ]);
            

            Route::group(['prefix' => 'charges'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/manage', [
                        'as' => 'manage_charge',
                        'uses' => 'Lms\ChargeController@manageCharge',
                    ]);

                    Route::post('save', [
                        'as' => 'save_manual_charges',
                        'uses' => 'Lms\ChargeController@saveManualCharges',
                    ]);

                    Route::get('/list', [
                        'as' => 'list_lms_charges',
                        'uses' => 'Lms\ChargeController@listLmsCharges',
                    ]);

                    Route::get('edit', [
                        'as' => 'get_lms_charges_edit',
                        'uses' => 'Lms\ChargeController@editLmsCharges',
                    ]);
                }
            });

            Route::group(['prefix' => 'address'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/view', [
                        'as' => 'addr_get_customer_list',
                        'uses' => 'Lms\AddressController@list',
                    ]);

                    Route::get('/add', [
                        'as' => 'add_addr',
                        'uses' => 'Lms\AddressController@addAddress',
                    ]);

                    Route::post('/save', [
                        'as' => 'save_addr',
                        'uses' => 'Lms\AddressController@saveAddress',
                    ]);

                    Route::get('/edit', [
                        'as' => 'edit_addr',
                        'uses' => 'Lms\AddressController@editAddress',
                    ]);

                    Route::post('/copy', [
                        'as' => 'copy_app',
                        'uses' => 'Lms\CopyController@duplicateApp',
                    ]);
                }
            });

            Route::group(['prefix' => 'payment'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('payment_list', [
                        'as' => 'payment_list',
                        'uses' => 'Backend\PaymentController@paymentList',
                    ]);

                    Route::get('unsettled_payments', [
                        'as' => 'unsettled_payments',
                        'uses' => 'Backend\PaymentController@unsettledPayment',
                    ]);

                    Route::get('settled_payments', [
                        'as' => 'settled_payments',
                        'uses' => 'Backend\PaymentController@settledPayment',
                    ]);

                    Route::get('excel_payment_list', [
                        'as' => 'excel_payment_list',
                        'uses' => 'Backend\PaymentController@excelPaymentList',
                    ]);

                    Route::get('add_payment', [
                        'as' => 'add_payment',
                        'uses' => 'Backend\PaymentController@addPayment',
                    ]);

                    Route::post('save_payment', [
                        'as' => 'save_payment',
                        'uses' => 'Backend\PaymentController@savePayment',
                    ]);

                    Route::get('edit_payment', [
                        'as' => 'edit_payment',
                        'uses' => 'Backend\PaymentController@EditPayment',
                    ]);

                    Route::get('view-file', [
                        'as' => 'see_repayment_file',
                        'uses' => 'Backend\PaymentController@viewUploadedFile',
                    ]);

                    Route::post('update_payment', [
                        'as' => 'update_payment',
                        'uses' => 'Backend\PaymentController@updatePayment',
                    ]);

                    Route::get('excel_bulk_payment', [
                        'as' => 'excel_bulk_payment',
                        'uses' => 'Backend\PaymentController@excelBulkPayment',
                    ]);

                    Route::POST('backend_save_excel_payment', [
                        'as' => 'backend_save_excel_payment',
                        'uses' => 'Backend\PaymentController@saveExcelPayment',
                    ]);

                    Route::get('payment_advice', [
                        'as' => 'payment_advice',
                        'uses' => 'Backend\PaymentController@paymentAdviceList',
                    ]);

                    Route::get('payment_advice_excel', [
                        'as' => 'payment_advice_excel',
                        'uses' => 'Backend\PaymentController@paymentAdviceExcel',
                    ]);

                    Route::get('payment_refund_index', [
                        'as' => 'payment_refund_index',
                        'uses' => 'Backend\PaymentController@paymentInvoiceList',
                    ]);

                    Route::post('create-payment-refund', [
                        'as' => 'create_payment_refund',
                        'uses' => 'Backend\PaymentController@createPaymentRefund',
                    ]);
                                        
                    Route::post('download-cheque', [
                        'as' => 'download_cheque',
                        'uses' => 'Backend\PaymentController@downloadCheque',
                    ]);   
                    
                    Route::delete('delete-payment', [
                        'as' => 'delete_payment',
                        'uses' => 'Backend\PaymentController@deletePayment',
                    ]);
                }
            });

            Route::group(['prefix' => 'invoice'], function () {
                if (config('lms.LMS_STATUS')) {

                    Route::get('backend_upload_invoice', [
                        'as' => 'backend_upload_invoice',
                        'uses' => 'Backend\InvoiceController@getInvoice',
                    ]);

                    Route::get('backend_bulk_invoice', [
                        'as' => 'backend_bulk_invoice',
                        'uses' => 'Backend\InvoiceController@getBulkInvoice',
                    ]);

                    Route::get('backend_get_invoice', [
                        'as' => 'backend_get_invoice',
                        'uses' => 'Backend\InvoiceController@viewInvoice',
                    ]);

                    Route::get('view-invoice-file', [
                        'as' => 'see_invoice_file',
                        'uses' => 'Backend\InvoiceController@viewUploadedFile',
                    ]);

                    Route::get('user_wise_invoice', [
                        'as' => 'user_wise_invoice',
                        'uses' => 'Backend\InvoiceController@UserWiseInvoice',
                    ]);

                    Route::get('backend_get_approve_invoice', [
                        'as' => 'backend_get_approve_invoice',
                        'uses' => 'Backend\InvoiceController@viewApproveInvoice',
                    ]);

                    Route::get('backend_get_disbursed_invoice', [
                        'as' => 'backend_get_disbursed_invoice',
                        'uses' => 'Backend\InvoiceController@viewDisbursedInvoice',
                    ]);

                    Route::get('/disburse-confirm', [
                        'as' => 'disburse_confirm',
                        'uses' => 'Backend\InvoiceController@disburseConfirm',
                    ]);

                    Route::post('/disburse-online', [
                        'as' => 'disburse_online',
                        'uses' => 'Backend\InvoiceController@disburseOnline',
                    ]);

                    Route::post('/disburse-offline', [
                        'as' => 'disburse_offline',
                        'uses' => 'Backend\InvoiceController@disburseOffline',
                    ]);

                    Route::get('backend_get_repaid_invoice', [
                        'as' => 'backend_get_repaid_invoice',
                        'uses' => 'Backend\InvoiceController@viewRepaidInvoice',
                    ]);

                    Route::get('backend_get_sent_to_bank', [
                        'as' => 'backend_get_sent_to_bank',
                        'uses' => 'Backend\InvoiceController@viewSentToBankInvoice',
                    ]);

                    Route::post('/download-batch-data', [
                        'as' => 'download_batch_data',
                        'uses' => 'Backend\InvoiceController@downloadBatchData',
                    ]);

                    Route::get('/view-batch-user-invoice', [
                        'as' => 'view_batch_user_invoice',
                        'uses' => 'Backend\InvoiceController@viewBatchUserInvoice',
                    ]);

                    Route::get('/invoice-update-disbursal', [
                        'as' => 'invoice_udpate_disbursal',
                        'uses' => 'Backend\InvoiceController@invoiceUpdateDisbursal',
                    ]);

                    Route::post('/update-disburse-invoice', [
                        'as' => 'updateDisburseInvoice',
                        'uses' => 'Backend\InvoiceController@updateDisburseInvoice',
                    ]);

                    Route::get('backend_get_failed_disbursment', [
                        'as' => 'backend_get_failed_disbursment',
                        'uses' => 'Backend\InvoiceController@viewfailedDisbursment',
                    ]);

                    Route::get('backend_get_disbursed', [
                        'as' => 'backend_get_disbursed',
                        'uses' => 'Backend\InvoiceController@viewdisbursed',
                    ]);

                    Route::get('backend_get_reject_invoice', [
                        'as' => 'backend_get_reject_invoice',
                        'uses' => 'Backend\InvoiceController@viewRejectInvoice',
                    ]);

                    Route::POST('backend_save_invoice', [
                        'as' => 'backend_save_invoice',
                        'uses' => 'Backend\InvoiceController@saveInvoice',
                    ]);

                    Route::POST('backend_save_bulk_invoice', [
                        'as' => 'backend_save_bulk_invoice',
                        'uses' => 'Backend\InvoiceController@saveBulkInvoice',
                    ]);

                    Route::POST('update_invoice_amount', [
                        'as' => 'update_invoice_amount',
                        'uses' => 'Backend\InvoiceController@saveInvoiceAmount',
                    ]);

                    Route::get('backend_upload_all_invoice', [
                        'as' => 'backend_upload_all_invoice',
                        'uses' => 'Backend\InvoiceController@getAllInvoice',
                    ]);

                    Route::POST('backend_save_invoice', [
                        'as' => 'backend_save_invoice',
                        'uses' => 'Backend\InvoiceController@saveInvoice',
                    ]);

                    Route::get('invoice_failed_status', [
                        'as' => 'invoice_failed_status',
                        'uses' => 'Backend\InvoiceController@invoiceFailedStatus',
                    ]);
                    Route::get('invoice_success_status', [
                        'as' => 'invoice_success_status',
                        'uses' => 'Backend\InvoiceController@invoiceSuccessStatus',
                    ]);

                    Route::get('view_invoice_details', [
                        'as' => 'view_invoice_details',
                        'uses' => 'Backend\InvoiceController@viewInvoiceDetails',
                    ]);

                    Route::get('backend_get_exception_cases', [
                        'as' => 'backend_get_exception_cases',
                        'uses' => 'Backend\InvoiceController@exceptionCases',
                    ]);

                    Route::get('bank-invoice', [
                        'as' => 'backend_get_bank_invoice',
                        'uses' => 'Backend\InvoiceController@viewBankInvoice',
                    ]);

                    Route::get('bank-invoice-customers', [
                        'as' => 'backend_get_bank_invoice_customers',
                        'uses' => 'Backend\InvoiceController@viewBankInvoiceCustomers',
                    ]);

                    Route::POST('upload_bulk_csv_Invoice', [
                        'as' => 'upload_bulk_csv_Invoice',
                        'uses' => 'Backend\InvoiceController@uploadBulkCsvInvoice',
                    ]);

                    Route::get('view-disburse-invoice', [
                        'as' => 'backend_view_disburse_invoice',
                        'uses' => 'Backend\InvoiceController@viewDisburseInvoice',
                    ]);

                    Route::POST('account_closure', [
                        'as' => 'account_closure',
                        'uses' => 'Backend\InvoiceController@accountClosure',
                    ]);

                    Route::get('disbursal-batch-request', [
                        'as' => 'backend_get_disbursal_batch_request',
                        'uses' => 'Backend\InvoiceController@disbursalBatchRequest',
                    ]);

                    Route::get('disbursal-payment-enquiry', [
                        'as' => 'disbursal_payment_enquiry',
                        'uses' => 'Backend\InvoiceController@disbursalPaymentEnquiry',
                    ]);

                    Route::post('delete-disbursal-batch-request', [
                        'as' => 'rollback_disbursal_batch_request',
                        'uses' => 'Backend\InvoiceController@rollbackDisbursalBatchRequest',
                    ]);

                    Route::get('online-disbursal-rollback', [
                        'as' => 'online_disbursal_rollback',
                        'uses' => 'Backend\InvoiceController@onlineDisbursalRollback',
                    ]);
                }
            });

            Route::group(['prefix' => 'disbursal'], function () {
                if (config('lms.LMS_STATUS')) {

                    Route::get('/request-list', [
                        'as' => 'lms_disbursal_request_list',
                        'uses' => 'Lms\DisbursalController@requestList',
                    ]);

                    Route::get('/view-invoice', [
                        'as' => 'lms_disbursal_invoice_view',
                        'uses' => 'Lms\DisbursalController@viewInvoice',
                    ]);

                    Route::post('/send-to-bank', [
                        'as' => 'send_to_bank',
                        'uses' => 'Lms\DisbursalController@sendToBank',
                    ]);

                    Route::get('/confirm-disburse', [
                        'as' => 'confirm_disburse',
                        'uses' => 'Lms\DisbursalController@confirmDisburse',
                    ]);

                    Route::get('/disbursed-list', [
                        'as' => 'lms_disbursed_list',
                        'uses' => 'Lms\DisbursalController@disbursedList',
                    ]);
                    Route::get('view-interest-accrual', [
                        'as' => 'view_interest_accrual',
                        'uses' => 'Lms\DisbursalController@viewInterestAccrual',
                    ]);
                    Route::get('payment-settlement', [
                        'as' => 'lms-payment-settlement',
                        'uses' => 'Lms\DisbursalController@processInvoiceSettlement',
                    ]);

                    Route::get('/interest-accrual', [
                        'as' => 'lms_interest_accrual',
                        'uses' => 'Lms\DisbursalController@processAccrualInterest',
                    ]);
                }
            });

            Route::group(['prefix' => 'soa'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/customer', [
                        'as' => 'soa_customer_view',
                        'uses' => 'Lms\SoaController@soa_customer_view',
                    ]);

                    Route::match(array('GET', 'POST'), '/consolidated', [
                        'as' => 'soa_consolidated_view',
                        'uses' => 'Lms\SoaController@soa_consolidated_view',
                    ]);

                    Route::get('/pdf/download', [
                        'as' => 'soa_pdf_download',
                        'uses' => 'Lms\SoaController@soaPdfDownload',
                    ]);

                    Route::get('/excel/download', [
                        'as' => 'soa_excel_download',
                        'uses' => 'Lms\SoaController@soaExcelDownload',
                    ]);
                }
            });

            Route::group(['prefix' => 'refund'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/request/advise', [
                        'as' => 'lms_refund_payment_advise',
                        'uses' => 'Lms\RefundController@paymentAdvise',
                    ]);

                    Route::post('/request/create', [
                        'as' => 'lms_refund_request_create',
                        'uses' => 'Lms\RefundController@createRefundRequest',
                    ]);

                    Route::get('/list', [
                        'as' => 'lms_refund_new',
                        'uses' => 'Lms\RefundController@refundListNew',
                    ]);

                    Route::get('/pending', [
                        'as' => 'lms_refund_pending',
                        'uses' => 'Lms\RefundController@refundListPending',
                    ]);

                    Route::get('/approved', [
                        'as' => 'lms_refund_approved',
                        'uses' => 'Lms\RefundController@refundListApproved',
                    ]);

                    Route::get('/queue', [
                        'as' => 'request_list',
                        'uses' => 'Lms\RefundController@refundListQueue',
                    ]);

                    Route::get('/refund-request', [
                        'as' => 'refund_request',
                        'uses' => 'Lms\RefundController@refundRequest',
                    ]);

                    Route::get('/sentbank', [
                        'as' => 'lms_refund_sentbank',
                        'uses' => 'Lms\RefundController@refundListSentBank',
                    ]);

                    Route::get('/refunded', [
                        'as' => 'lms_refund_refunded',
                        'uses' => 'Lms\RefundController@refundListRefunded',
                    ]);

                    Route::get('/request/view', [
                        'as' => 'lms_refund_request_view',
                        'uses' => 'Lms\RefundController@viewRefundRequest',
                    ]);

                    Route::post('/request/update', [
                        'as' => 'lms_refund_request_udate',
                        'uses' => 'Lms\RefundController@updateRequestStatus',
                    ]);

                    Route::get('/confirm', [
                        'as' => 'refund_confirm',
                        'uses' => 'Lms\RefundController@refundConfirm',
                    ]);

                    Route::post('/refund-offline', [
                        'as' => 'refund_offline',
                        'uses' => 'Lms\RefundController@refundOffline',
                    ]);

                    Route::post('/refund-online', [
                        'as' => 'refund_online',
                        'uses' => 'Lms\RefundController@refundOnline',
                    ]);

                    Route::get('/refund-update-disbursal', [
                        'as' => 'refund_udpate_disbursal',
                        'uses' => 'Lms\RefundController@refundUpdateDisbursal',
                    ]);

                    Route::post('/update-disburse-refund', [
                        'as' => 'updateDisburseRefund',
                        'uses' => 'Lms\RefundController@updateDisburseRefund',
                    ]);

                    Route::get('/refund/refund-list', [
                        'as' => 'lms_refund_list',
                        'uses' => 'Lms\RefundController@customerList',
                    ]);

                    Route::get('/confirm-refund', [
                        'as' => 'confirm_refund',
                        'uses' => 'Lms\RefundController@confirmRefund',
                    ]);

                    Route::post('/send-refund', [
                        'as' => 'lms_send_refund',
                        'uses' => 'Lms\RefundController@sendRefund',
                    ]);

                    Route::get('/lms-create-batch', [
                        'as' => 'lms_create_batch',
                        'uses' => 'Lms\RefundController@createBatch',
                    ]);

                    Route::get('/lms-edit-batch', [
                        'as' => 'lms_edit_batch',
                        'uses' => 'Lms\RefundController@editBatch',
                    ]);

                    Route::get('refund/download-sentbank-data', [
                        'as' => 'download_sentbank',
                        'uses' => 'Lms\RefundController@downloadSentBank',
                    ]);

                    Route::get('req-move-next-stage', [
                        'as' => 'lms_req_move_next_stage',
                        'uses' => 'Lms\RefundController@moveReqToNextStage',
                    ]);
                    Route::get('req-move-prev-stage', [
                        'as' => 'lms_req_move_prev_stage',
                        'uses' => 'Lms\RefundController@moveReqToNextStage',
                    ]);

                    Route::post('accept-request-stage', [
                        'as' => 'lms_accept_next_stage',
                        'uses' => 'Lms\RefundController@acceptReqStage',
                    ]);

                    Route::get('update-request-status', [
                        'as' => 'lms_update_request_status',
                        'uses' => 'Lms\RefundController@updateRequestStatus',
                    ]);

                    Route::post('save-request-status', [
                        'as' => 'lms_save_request_status',
                        'uses' => 'Lms\RefundController@saveRequestStatus',
                    ]);

                    Route::get('view-process-refund', [
                        'as' => 'lms_view_process_refund',
                        'uses' => 'Lms\RefundController@viewProcessRefund',
                    ]);

                    Route::post('process-refund', [
                        'as' => 'lms_process_refund',
                        'uses' => 'Lms\RefundController@processRefund',
                    ]);

                    Route::get('refund-payment-enquiry', [
                        'as' => 'refund_payment_enquiry',
                        'uses' => 'Lms\RefundController@refundPaymentEnquiry',
                    ]);
                }
            });

            Route::group(['prefix' => 'writeOff'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/view', [
                        'as' => 'write_off_customer_list',
                        'uses' => 'Lms\WriteOffController@index',
                    ]);

                    Route::get('/generate', [
                        'as' => 'generate_write_off',
                        'uses' => 'Lms\WriteOffController@generateWriteOff',
                    ]);

                    Route::get('/approve', [
                        'as' => 'wo_approve_dissapprove',
                        'uses' => 'Lms\WriteOffController@getWriteOffPopUP',
                    ]);

                    Route::post('/comment', [
                        'as' => 'wo_save_appr_dissappr',
                        'uses' => 'Lms\WriteOffController@saveWriteOffComment',
                    ]);
                }
            });

            Route::group(['prefix' => 'apportionment'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/running/view', [
                        'as' => 'apport_running_view',
                        'uses' => 'Lms\ApportionmentController@viewRunningTrans',
                    ]);

                    Route::post('/running/list', [
                        'as' => 'apport_running_list',
                        'uses' => 'Lms\ApportionmentController@listrunningTrans',
                    ]);

                    Route::post('/running/save', [
                        'as' => 'apport_running_save',
                        'uses' => 'Lms\ApportionmentController@saveRunningDetail',
                    ]);

                    Route::get('/unsettled/view', [
                        'as' => 'apport_unsettled_view',
                        'uses' => 'Lms\ApportionmentController@viewUnsettledTrans',
                    ]);

                    Route::post('/unsettled/list', [
                        'as' => 'apport_unsettled_list',
                        'uses' => 'Lms\ApportionmentController@listUnsettledTrans',
                    ]);

                    Route::get('/settled/view', [
                        'as' => 'apport_settled_view',
                        'uses' => 'Lms\ApportionmentController@viewSettledTrans',
                    ]);

                    Route::post('/settled/list', [
                        'as' => 'apport_settled_list',
                        'uses' => 'Lms\ApportionmentController@listSettledTrans',
                    ]);

                    Route::post('/settled/save', [
                        'as' => 'apport_settled_save',
                        'uses' => 'Lms\ApportionmentController@saveSettledTrans',
                    ]);

                    Route::post('/mark/settle/confirmation', [
                        'as' => 'apport_mark_settle_confirmation',
                        'uses' => 'Lms\ApportionmentController@markSettleConfirmation',
                    ]);

                    Route::post('/mark/settle/save', [
                        'as' => 'apport_mark_settle_save',
                        'uses' => 'Lms\ApportionmentController@markSettleSave',
                    ]);

                    Route::get('/refund/view', [
                        'as' => 'apport_refund_view',
                        'uses' => 'Lms\ApportionmentController@viewRefundTrans',
                    ]);

                    Route::post('/refund/list', [
                        'as' => 'apport_refund_list',
                        'uses' => 'Lms\ApportionmentController@listRefundTrans',
                    ]);

                    Route::get('/txn/waiveoff', [
                        'as' => 'apport_trans_waiveoff',
                        'uses' => 'Lms\ApportionmentController@getTransDetailWaiveOff',
                    ]);

                    Route::get('/txn/reversal', [
                        'as' => 'apport_trans_reversal',
                        'uses' => 'Lms\ApportionmentController@getTransDetailReversal',
                    ]);

                    Route::post('/waiveoff/save', [
                        'as' => 'apport_waiveoff_save',
                        'uses' => 'Lms\ApportionmentController@saveWaiveOffDetail',
                    ]);

                    Route::post('/reversal/save', [
                        'as' => 'apport_reversal_save',
                        'uses' => 'Lms\ApportionmentController@saveReversalDetail',
                    ]);

                    Route::post('/mark/adjustment/confirmation', [
                        'as' => 'apport_mark_adjustment_confirmation',
                        'uses' => 'Lms\ApportionmentController@markAdjustmentConfirmation',
                    ]);

                    Route::post('/mark/adjustment/save', [
                        'as' => 'apport_mark_adjustment_save',
                        'uses' => 'Lms\ApportionmentController@markAdjustmentSave',
                    ]);

                    Route::post('/mark/writeOff/confirmation', [
                        'as' => 'apport_mark_writeOff_confirmation',
                        'uses' => 'Lms\ApportionmentController@markWriteOffConfirmation',
                    ]);

                    Route::post('/mark/writeOff/save', [
                        'as' => 'apport_mark_writeOff_save',
                        'uses' => 'Lms\ApportionmentController@markWriteOffSave',
                    ]);

                    Route::delete('/revert',[
                        'as' => 'undo_apportionment',
                        'uses' => 'Lms\ApportionmentController@undoApportionment',
                    ]);
                }
            });

            Route::group(['prefix' => 'reports'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('/', [
                        'as' => 'report_summary',
                        'uses' => 'Backend\ReportController@index',
                    ]);
                    Route::get('/customer', [
                        'as' => 'report_customer',
                        'uses' => 'Backend\ReportController@customer',
                    ]);
                    Route::get('/lease-register', [
                        'as' => 'lease_register',
                        'uses' => 'Backend\ReportController@leaseRegister',
                    ]);
                    Route::get('/download', [
                        'as' => 'download_reports',
                        'uses' => 'Backend\ReportController@downloadLeaseReport',
                    ]);
                    Route::get('/duereport', [
                        'as' => 'report_duereport',
                        'uses' => 'Backend\ReportController@duereport',
                    ]);
                    Route::get('/overduereport', [
                        'as' => 'report_overduereport',
                        'uses' => 'Backend\ReportController@overduereport',
                    ]);

                    Route::get('/realisationreport', [
                        'as' => 'report_realisationreport',
                        'uses' => 'Backend\ReportController@realisationreport',
                    ]);
                    Route::get('/pdf_invoice_due_url', [

                        'as' => 'pdf_invoice_due_url',
                        'uses' => 'Backend\ReportController@pdfInvoiceDue',
                    ]);
                    Route::get('/pdf_invoice_over_due_url', [
                        'as' => 'pdf_invoice_over_due_url',
                        'uses' => 'Backend\ReportController@pdfInvoiceOverDue',
                    ]);

                    Route::get('/pdf_invoice_realisation_url', [
                        'as' => 'pdf_invoice_realisation_url',
                        'uses' => 'Backend\ReportController@pdfInvoiceRealisation',
                    ]);

                    Route::get('/test', [
                        'as' => 'test',
                        'uses' => 'Backend\ReportController@maturityReport',
                    ]);
                }
            });

            Route::group(['prefix' => 'eod'], function () {
                if (config('lms.LMS_STATUS')) {
                    Route::get('view', [
                        'as' => 'eod_process',
                        'uses' => 'Lms\EodProcessController@viewEodProcess',
                    ]);

                    Route::post('save', [
                        'as' => 'save_process',
                        'uses' => 'Lms\EodProcessController@saveEodProcess',
                    ]);

                    Route::get('process', [
                        'as' => 'do_process',
                        'uses' => 'Lms\EodProcessController@process',
                    ]);
                }
            });

                    
            Route::group(['prefix' => 'nach'], function () {
                if (config('lms.LMS_STATUS')) {
                    
                    Route::get('/users-nach-list', [
                        'as' => 'users_nach_list',
                        'uses' => 'Lms\NachController@getNachList'
                    ]);
                    Route::post('/nach-download_sheet', [
                        'as' => 'nach_download_reports_sheet',
                        'uses' => 'Lms\NachController@downloadNachReport'
                    ]);
                    
                    Route::get('/upload-nach-response', [
                        'as' => 'upload_nach_response',
                        'uses' => 'Lms\NachController@uploadNachResponse'
                    ]);
                    
                    Route::post('/import-nach-response', [
                        'as' => 'import_nach_response',
                        'uses' => 'Lms\NachController@importNachResponse'
                    ]);
                }
            });
        });
    });
    //end of application
});
