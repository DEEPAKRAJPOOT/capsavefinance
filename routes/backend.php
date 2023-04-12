<?php

/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {
    Route::group(['middleware' => 'auth'], function () {

        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', [
                'as' => 'backend_dashboard',
                'uses' => 'Backend\DashboardController@dashboardindex'
            ]);
            Route::get('/idfc', [
                'as' => 'idfc',
                'uses' => 'Backend\DashboardController@idfc'
            ]);
            Route::get('/download-file', [
                'as' => 'download_storage_file',
                'uses' => 'Backend\DocumentController@downloadStorageFile'
            ]);
            Route::get('/download-s3-file', [
                'as' => 'download_s3_file',
                'uses' => 'Backend\DocumentController@downloadAWSS3File'
            ]);

        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/lease-register', [
                'as' => 'lease_register',
                'uses' => 'Backend\ReportController@leaseRegister'
            ]);
            Route::get('/interest-breakup', [
                'as' => 'interest_breakup',
                'uses' => 'Backend\ReportController@interestBreakup'
            ]);
            Route::get('/download/interest-breakup', [
                'as' => 'download_interest_breakup',
                'uses' => 'Backend\ReportController@downloadInterestBreakup'
            ]);
            Route::get('/charge-breakup', [
                'as' => 'charge_breakup',
                'uses' => 'Backend\ReportController@chargeBreakup'
            ]);

            Route::get('/download/charge-breakup', [
                'as' => 'download_charge_breakup',
                'uses' => 'Backend\ReportController@downloadChargeBreakup'
            ]);

            Route::get('/tds-breakup', [
                'as' => 'tds_breakup',
                'uses' => 'Backend\ReportController@tdsBreakup'
            ]);
            
            Route::get('/download/tds-breakup', [
                'as' => 'download_tds_breakup',
                'uses' => 'Backend\ReportController@downloadTdsBreakup'
            ]);
            
            Route::get('/download', [
                'as' => 'download_reports',
                'uses' => 'Backend\ReportController@downloadLeaseReport'
            ]);
            Route::get('/duereport', [
                'as' => 'report_duereport',
                'uses' => 'Backend\ReportController@duereport'
            ]);

            Route::get('/outstandingreport', [
                'as' => 'report_outstandingreport',
                'uses' => 'Backend\ReportController@outstandingreport'
            ]);

            Route::get('/overduereport', [
                'as' => 'report_overduereport',
                'uses' => 'Backend\ReportController@overduereport'
            ]);

               Route::get('/realisationreport', [
                'as' => 'report_realisationreport',
                'uses' => 'Backend\ReportController@realisationreport'
            ]);
               Route::get('/pdf_invoice_due_url', [

                'as' => 'pdf_invoice_due_url',
                'uses' => 'Backend\ReportController@pdfInvoiceDue'
            ]);
            Route::get('/pdf_invoice_over_due_url', [
                'as' => 'pdf_invoice_over_due_url',
                'uses' => 'Backend\ReportController@pdfInvoiceOverDue'
            ]);
                
             Route::get('/pdf_invoice_realisation_url', [
                'as' => 'pdf_invoice_realisation_url',
                'uses' => 'Backend\ReportController@pdfInvoiceRealisation'
            ]);
             
            Route::get('/tds', [
                'as' => 'tds',
                'uses' => 'Backend\ReportController@tdsReport'
            ]);
            Route::get('/tds-download', [
                'as' => 'tds_download_reports',
                'uses' => 'Backend\ReportController@downloadTdsReport'
            ]);
            Route::get('/outstandingreportManual', [
                'as' => 'outstanding_report_manual',
                'uses' => 'Backend\ReportController@outstandingReportManual'
            ]);
            Route::get('/reconReport', [
                'as' => 'recon_report',
                'uses' => 'Backend\ReportController@reconReport'
            ]);
        });

        Route::group(['prefix' => 'application'], function () {
            Route::get('/', [
                'as' => 'application_list',
                'uses' => 'Backend\ApplicationController@index'
            ]);

            Route::get('/supplier', [
                'as' => 'supplier_list',
                'uses' => 'Backend\SupplierController@index'
            ]);

          
            Route::get('company-details', [
                'as' => 'company_details',
                'uses' => 'Backend\ApplicationController@showCompanyDetails'
            ]);

            Route::post('company-details', [
                'as' => 'company_details_save',
                'uses' => 'Backend\ApplicationController@updateCompanyDetail'
            ]);
            
            Route::get('promoter-details', [
                'as' => 'promoter_details',
                'uses' => 'Backend\ApplicationController@showPromoterDetails'
            ]);

            Route::get('view-uploaded-doc',[
                'as' => 'view_uploaded_doc',
                'uses' => 'Backend\DocumentController@seeUploadFile'
            ]);

            Route::post('promoter-save',
                [
                    'as' => 'promoter_save',
                    'uses' => 'Backend\ApplicationController@savePromoter'
            ]);
             
            Route::post('promoter-detail-save',
                [
                    'as' => 'promoter_detail_save',
                    'uses' => 'Backend\ApplicationController@updatePromoterDetail'
            ]); 
             
            Route::post('get-user-pan-response-karza', [
                'as' => 'get_user_pan_response_karza',
                'uses' => 'Backend\ApplicationController@getKarzaApiRes'
            ]);
            
            Route::get('documents', [
                'as' => 'documents',
                'uses' => 'Backend\ApplicationController@showDocuments'
            ]);
            
            
            Route::get('documents/upload-document', [
                'as' => 'show_upload_document',
                'uses' => 'Backend\ApplicationController@uploadDocument'
            ]);

            Route::get('/see-onboarding-file', [
                'as' => 'view_onboarding_documents',
                'uses' => 'Backend\DocumentController@seeUploadFile'
            ]);

            Route::get('documents/edit-upload-document', [
                'as' => 'edit_upload_document',
                'uses' => 'Backend\ApplicationController@editUploadDocument'
            ]);

            Route::post('documents/update-edit-upload-document', [
                'as' => 'update_edit_upload_document',
                'uses' => 'Backend\ApplicationController@updateEditUploadDocument'
            ]);
            
            Route::post('documents-save', [
                'as' => 'document_save',
                'uses' => 'Backend\ApplicationController@saveDocument'
            ]);
            
            Route::get('upload_bank_document', [
                'as' => 'upload_bank_document',
                'uses' => 'Backend\CamController@updateBankDocument'
            ]);
            
            Route::get('upload_xlsx_document', [
                'as' => 'upload_xlsx_document',
                'uses' => 'Backend\CamController@uploadBankXLSX'
            ]);
            
            Route::get('upload_xlsx_document_finance', [
                'as' => 'upload_xlsx_document_finance',
                'uses' => 'Backend\CamController@uploadFinanceXLSX'
            ]);
            
            Route::post('save_xlsx_document', [
                'as' => 'save_xlsx_document',
                'uses' => 'Backend\CamController@saveBankXLSX'
            ]);
            
            Route::post('bank_document_save', [
                'as' => 'bank_document_save',
                'uses' => 'Backend\CamController@saveBankDocument'
            ]);
            
            Route::get('document-delete',
                [
                'as' => 'document_delete',
                'uses' => 'Backend\ApplicationController@documentDelete'
            ]);
            
            Route::post('promoter-document-save', [
                'as' => 'promoter_document_save',
                'uses' => 'Backend\ApplicationController@promoterDocumentSave'
            ]); 
            
            Route::post('promoter-document-delete', [
                'as' => 'promoter_document_delete',
                'uses' => 'Backend\ApplicationController@promoterDocumentDelete'
            ]); 
            
            Route::post('application-save',
                [
                'as' => 'application_save',
                'uses' => 'Backend\ApplicationController@applicationSave'
            ]);                 

            Route::get('fircu/fi', [
                'as' => 'backend_fi',
                'uses' => 'Backend\FiRcuController@listFI'
            ]);

            Route::get('fircu/fiupload', [
                'as' => 'fi_upload',
                'uses' => 'Backend\FiRcuController@FiUpload'
            ]);

            Route::get('fircu/fi-download', [
                'as' => 'download_fi_doc',
                'uses' => 'Backend\DocumentController@downloadStorageFile'
            ]);

            Route::post('fircu/fiupload', [
                'as' => 'save_fi_upload',
                'uses' => 'Backend\FiRcuController@saveFiUpload'
            ]);

            Route::get('fircu/inspection', [
                'as' => 'backend_inspection',
                'uses' => 'Backend\FiRcuController@listInspection'
            ]);

            Route::get('fircu/inspectionupload', [
                'as' => 'inspection_upload',
                'uses' => 'Backend\FiRcuController@InspectionUpload'
            ]);

            Route::post('fircu/inspectionupload', [
                'as' => 'save_inspection_upload',
                'uses' => 'Backend\FiRcuController@saveInspectionUpload'
            ]);

            Route::get('fircu/rcu', [
                'as' => 'backend_rcu',
                'uses' => 'Backend\FiRcuController@listRCU'
            ]);
            
            Route::get('fircu/rcuupload', [
                'as' => 'rcu_upload',
                'uses' => 'Backend\FiRcuController@RcuUpload'
            ]);

            Route::post('fircu/rcuupload', [
                'as' => 'save_rcu_upload',
                'uses' => 'Backend\FiRcuController@saveRcuUpload'
            ]);

            Route::get('notes-from', [
                'as' => 'backend_notes_from',
                'uses' => 'Backend\NotesController@showNoteForm'
            ]);

            Route::get('notes', [
                'as' => 'notes_list',
                'uses' => 'Backend\NotesController@index'
            ]);

            Route::post('notes', [
                'as' => 'note_save',
                'uses' => 'Backend\NotesController@store'
            ]);
                        
            Route::get('change-status', [
                'as' => 'change_app_status',
                'uses' => 'Backend\ApplicationController@changeAppStatus'
            ]);
            
            Route::get('assign-case', [
                'as' => 'assign_case',
                'uses' => 'Backend\ApplicationController@assignCase'
            ]);
            
            Route::post('update-app-status', [
                'as' => 'update_app_status',
                'uses' => 'Backend\ApplicationController@updateAppStatus'
            ]);
            
            Route::post('save-assign-case', [
                'as' => 'save_assign_case',
                'uses' => 'Backend\ApplicationController@updateAssignee'
            ]);

            Route::get('add-app-note', [
                'as' => 'add_app_note',
                'uses' => 'Backend\ApplicationController@addAppNote'
            ]);
              Route::get('add-app-copy', [
                'as' => 'add_app_copy',
                'uses' => 'Backend\ApplicationController@addAppCopy'
            ]);
            Route::post('save-app-note', [
                'as' => 'save_app_note',
                'uses' => 'Backend\ApplicationController@saveAppNote'
            ]); 

            Route::get('reject-app', [
                'as' => 'reject_app',
                'uses' => 'Backend\ApplicationController@rejectApp'
            ]);

            Route::post('save-app-rejection', [
                'as' => 'save_app_rejection',
                'uses' => 'Backend\ApplicationController@saveAppRejection'
            ]);

            Route::get('view-app-status-list', [
                'as' => 'view_app_status_list',
                'uses' => 'Backend\ApplicationController@getAppStatusList'
            ]);

            Route::get('send-case-confirmBox', [
                'as' => 'send_case_confirmBox',
                'uses' => 'Backend\ApplicationController@sendCaseConfirmbox'
            ]); 
           
            Route::get('view-approvers', [
                'as' => 'view_approvers',
                'uses' => 'Backend\ApplicationController@viewApprovers'
            ]); 

            Route::get('view-shared-details', [
                'as' => 'view_shared_details',
                'uses' => 'Backend\ApplicationController@viewSharedDetails'
            ]); 
            
            Route::post('accept-next-stage', [
                'as' => 'accept_next_stage',
                'uses' => 'Backend\ApplicationController@AcceptNextStage'
            ]); 

            Route::get('application-pool', [
                'as' => 'application_pool',
                'uses' => 'Backend\LeadController@showApplicationPool'
            ]);
            
            Route::get('confirm-box', [
                'as' => 'confirm_box',
                'uses' => 'Backend\LeadController@confirmBox'
            ]);
            
            Route::post('accept-application-pool', [
                'as' => 'accept_application_pool',
                'uses' => 'Backend\LeadController@acceptApplicationPool'
            ]);

            Route::get('business-information', [
                'as' => 'create_application',
                'uses' => 'Backend\ApplicationController@showBusinessInformation'
            ]);

            Route::post('business-information', [
                'as' => 'save_new_application',
                'uses' => 'Backend\ApplicationController@saveBusinessInformation'
            ]);        
            
            Route::get('view-offer', [
                'as' => 'view_offer',
                'uses' => 'Backend\ApplicationController@showOffer'
            ]);

            Route::get('accept-offer', [
                'as' => 'accept_offer_form',
                'uses' => 'Backend\ApplicationController@acceptOfferForm'
            ]);
            
            Route::post('accept-offer', [
                'as' => 'accept_offer',
                'uses' => 'Backend\ApplicationController@acceptOffer'
            ]); 
            
            Route::get('sanction-letter', [
                'as' => 'gen_sanction_letter',
                'uses' => 'Backend\ApplicationController@genSanctionLetter'
            ]);
            
            Route::get('download-sanction-letter', [
                'as' => 'send_sanction_letter',
                'uses' => 'Backend\ApplicationController@sendSanctionLetter'
            ]); 

            Route::get('send_sanction_letter_supplychain', [
                'as' => 'send_sanction_letter_supplychain',
                'uses' => 'Backend\ApplicationController@sendSanctionLetterSupplyChain'
            ]);

            Route::get('show-upload-sanction-letter', [
                'as' => 'show_upload_sanction_letter',
                'uses' => 'Backend\ApplicationController@showUploadSanctionLetter'
            ]);
            
            Route::post('upload-sanction-letter', [
                'as' => 'upload_sanction_letter',
                'uses' => 'Backend\ApplicationController@uploadSanctionLetter'
            ]); 
            
            Route::post('save-sanction-letter', [
                'as' => 'save_sanction_letter',
                'uses' => 'Backend\ApplicationController@saveSanctionLetter'
            ]);
               
            Route::post('save_sanction_letter_supplychain', [
                'as' => 'save_sanction_letter_supplychain',
                'uses' => 'Backend\ApplicationController@saveSanctionLetterSupplychain'
            ]); 

             Route::get('preview_supply_chain_sanction_letter', [
                'as' => 'preview_supply_chain_sanction_letter',
                'uses' => 'Backend\ApplicationController@previewSanctionLetterSupplychain'
            ]); 

            Route::get('preview-sanction-letter',[
                'as' => 'preview_sanction_letter',
                'uses' => 'Backend\ApplicationController@previewSanctionLetter'
            ]);

            //////////////// For Promoter Iframe///////////////////
             Route::get('show-pan-verify-data', [
                'as' => 'show_pan_verify_data',
                'uses' => 'Backend\ApplicationController@showPanVerifyResponseData'
            ]);


            Route::get('show-pan-data', [
                'as' => 'show_pan_data',
                'uses' => 'Backend\ApplicationController@showPanResponseData'
            ]);

            Route::get('show-dl-data', [
                'as' => 'show_dl_data',
                'uses' => 'Backend\ApplicationController@showDlResponseData'
            ]);
            Route::get('show-voter-data', [
                'as' => 'show_voter_data',
                'uses' => 'Backend\ApplicationController@showVoterResponseData'
            ]);
            Route::get('show-pass-data', [
                'as' => 'show_pass_data',
                'uses' => 'Backend\ApplicationController@showPassResponseData'
            ]);
             Route::get('mobile_verify', [
                'as' => 'mobile_verify',
                'uses' => 'Backend\ApplicationController@mobileModel'
            ]);
              Route::get('mobile_otp_view', [
                'as' => 'mobile_otp_view',
                'uses' => 'Backend\ApplicationController@mobileOtpModel'
            ]);
            Route::get('app-status-disbursed', [
                         'as' => 'app_status_disbursed',
                  'uses' => 'Backend\ApplicationController@showAppStatusForm'
                ]);
                Route::post('app-status-disbursed', [
                   'as' => 'app_status_disbursed',
                 'uses' => 'Backend\ApplicationController@saveShowAppStatusForm'
                ]);
            //////////////for cibil Iframe//////////////////////// 
             Route::get('pull-cibil-commercial', [
                'as' => 'pull_cibil_commercial',
                'uses' => 'Backend\CamController@pullCibilCommercial'
            ]);
              Route::get('pull-cibil-promoter', [
                'as' => 'pull_cibil_promoter',
                'uses' => 'Backend\CamController@pullCibilPromoter'
            ]);
               Route::get('view-cibil-report', [
                'as' => 'view_cibil_report',
                'uses' => 'Backend\CamController@viewCibilReport'
            ]);

            //////////////for Assign FI RCU Iframe////////////////////
            Route::get('fircu/assign-fi', [
                'as' => 'show_assign_fi',
                'uses' => 'Backend\FiRcuController@showAssignFi'
            ]);

            Route::post('fircu/assign-fi', [
                'as' => 'save_assign_fi',
                'uses' => 'Backend\FiRcuController@saveAssignFi'
            ]);

            Route::get('fircu/assign-inspection', [
                'as' => 'show_assign_inspection',
                'uses' => 'Backend\FiRcuController@showAssignInspection'
            ]);

            Route::post('fircu/assign-inspection', [
                'as' => 'save_assign_inspection',
                'uses' => 'Backend\FiRcuController@saveAssignInspection'
            ]);

            Route::post('fircu/assign-rcu', [
                'as' => 'save_assign_rcu',
                'uses' => 'Backend\FiRcuController@saveAssignRcu'
            ]);
            
            Route::get('fircu/assign-rcu', [
                'as' => 'show_assign_rcu',
                'uses' => 'Backend\FiRcuController@showAssignRcu'
            ]);

            Route::get('fircu/pd-notes', [
                'as' => 'pd_notes_list',
                'uses' => 'Backend\NotesController@pdNotesList'
            ]);
            
            Route::get('pd-notes-from', [
                'as' => 'backend_pd_notes_from',
                'uses' => 'Backend\NotesController@showPdNotesForm'
            ]);

            Route::post('save-pd-notes', [
                'as' => 'save_pd_notes',
                'uses' => 'Backend\NotesController@savePdNotes'
            ]);
       
            Route::get('query-management', [
                'as' => 'query_management_list',
                'uses' => 'Backend\QmsController@index'
            ]);

            Route::get('query-management-from', [
                'as' => 'query_management_from',
                'uses' => 'Backend\QmsController@showQueryForm'
            ]);

            
            Route::post('save-query-management', [
                'as' => 'save_query_management',
                'uses' => 'Backend\QmsController@saveQueryManagement'
            ]);

            Route::get('show-qms-details', [
                'as' => 'show_qms_details',
                'uses' => 'Backend\QmsController@showQmsDetails'

            ]);
            
            Route::get('see-qms-file', [
                'as' => 'view_qms_doc',
                'uses' => 'Backend\DocumentController@seeUploadFile'

            ]);
            //New Sanction Letter
            Route::get('new-sanction-letter', [
                'as' => 'list_new_sanction_letter',
                'uses' => 'Backend\ApplicationController@ListNewSanctionLetter'
            ]);

            Route::get('create-new-sanction-letter', [
                'as' => 'create_new_sanction_letter',
                'uses' => 'Backend\ApplicationController@createNewSanctionLetter',
            ]);

            Route::get('app-pull-back-confirmBox', [
                'as' => 'app_pull_back_confirmBox',
                'uses' => 'Backend\ApplicationController@sendCaseConfirmbox'
            ]); 
            Route::get('download-approval-file-copy', [
                'as' => 'download_approval_file_copy',
                'uses' => 'Backend\DocumentController@downloadStorageFile'
            ]);

            Route::post('save-new-sanction-letter', [
                'as' => 'save_new_sanction_letter',
                'uses' => 'Backend\ApplicationController@saveNewSanctionLetterSupplyChain',
            ]);
            
            Route::get('view-new-sanction-letter', [
                'as' => 'view_new_sanction_letter',
                'uses' => 'Backend\ApplicationController@viewNewSanctionLetterSupplyChain',
            ]);

            Route::get('download-new-sanction-letter', [
                'as' => 'download_new_sanction_letter',
                'uses' => 'Backend\ApplicationController@downloadNewSanctionLetterSupplyChain',
            ]);

            Route::post('send-new-sanction-letter-on-mail', [
                'as' => 'send_new_sanction_letter_on_mail',
                'uses' => 'Backend\ApplicationController@sendNewSanctionLetterSupplyChainMail',
            ]);

            Route::get('reactivate-status-app', [
                'as' => 'reactivate_status_app',
                'uses' => 'Backend\ApplicationController@reactivateStatusApp'
            ]);
            Route::post('post-reactivate-status-app', [
                'as' => 'reactivate_status_app_save',
                'uses' => 'Backend\ApplicationController@reactivateStatusAppSave'
            ]); 
           
            Route::get('app-pull-back-confirmBox', [
                'as' => 'app_pull_back_confirmBox',
                'uses' => 'Backend\ApplicationController@sendCaseConfirmbox'
            ]); 
            Route::get('download-approval-file-copy', [
                'as' => 'download_approval_file_copy',
                'uses' => 'Backend\DocumentController@downloadStorageFile'
            ]);

            //start section cam
             Route::group(['prefix' => 'cam'], function () {

                Route::get('promoter', [
                    'as' => 'cam_promoter',
                    'uses' => 'Backend\CamController@showPromoter'
                ]);
                Route::get('overview', [
                    'as' => 'cam_overview',
                    'uses' => 'Backend\CamController@index'
                ]);
                Route::get('cibil', [
                    'as' => 'cam_cibil',
                    'uses' => 'Backend\CamController@showCibilForm'
                ]);
                Route::post('cam-information-save', [
                    'as' => 'cam_information_save',
                    'uses' => 'Backend\CamController@camInformationSave'
                ]);

                Route::get('bank', [
                    'as' => 'cam_bank',
                    'uses' => 'Backend\CamController@banking'
                ]);

                Route::get('finance', [
                    'as' => 'cam_finance',
                    'uses' => 'Backend\CamController@finance'
                ]);

                Route::post('save-finance-detail', [
                    'as' => 'save_finance_detail',
                    'uses' => 'Backend\CamController@saveFinanceDetail'
                ]);

                Route::get('reviewer-summary', [
                    'as' => 'reviewer_summary',
                    'uses' => 'Backend\CamController@reviewerSummary'
                ]);

                Route::post('save-reviewer-summary', [
                    'as' => 'save_reviewer_summary',
                    'uses' => 'Backend\CamController@saveReviewerSummary'
                ]);

                Route::get('mail-reviewer-summary', [
                    'as' => 'mail_reviewer_summary',
                    'uses' => 'Backend\CamController@mailReviewerSummary'
                ]);

                Route::get('gstin', [
                    'as' => 'cam_gstin',
                    'uses' => 'Backend\CamController@gstin'
                ]);
                
                Route::post('finance_store', [
                    'as' => 'cam_finance_store',
                    'uses' => 'Backend\CamController@finance_store'
                ]);
                
                Route::get('limit-assessment', [
                    'as' => 'limit_assessment',
                    'uses' => 'Backend\CamController@showLimitAssessment'
                ]);  
            
                Route::post('save-limit-assessment', [
                    'as' => 'save_limit_assessment',
                    'uses' => 'Backend\CamController@saveLimitAssessment'
                ]);

                Route::get('edit-total-limit-amnt', [
                    'as' => 'edit_total_limit_amnt',
                    'uses' => 'Backend\CamController@totalCreditAssessed'
                ]); 

                Route::post('update-total-limit-amnt', [
                    'as' => 'update_total_limit_amnt',
                    'uses' => 'Backend\CamController@updateTotalCreditAssessed'
                ]);

                Route::get('anchor-view', [
                    'as' => 'anchor_view',
                    'uses' => 'Backend\CamController@anchorViewForm'
                ]);
                
                Route::post('save-anchor-view', [
                    'as' => 'save_anchor_view',
                    'uses' => 'Backend\CamController@SaveAnchorForm'
                ]);
                
                Route::post('cam-hygiene-save', [
                    'as' => 'cam_hygiene_save',
                    'uses' => 'Backend\CamController@camHygieneSave'
                ]);


                Route::post('cam-promoter-comment-save', [
                    'as' => 'cam_promoter_comment_save',
                    'uses' => 'Backend\CamController@promoterCommentSave'
                ]);

                Route::get('show-limit-offer', [
                    'as' => 'show_limit_offer',
                    'uses' => 'Backend\CamController@showLimitOffer'
                ]);
                Route::get('delete-limit-offer', [
                    'as' => 'delete_limit_offer',
                    'uses' => 'Backend\CamController@deleteLimitOffer'
                ]);

                Route::get('delete-prgm-limit', [
                    'as' => 'delete_prgm_limit',
                    'uses' => 'Backend\CamController@deletePrgmLimit'
                ]);

                Route::get('approve-adhoc-limit', [
                    'as' => 'approve_adhoc_limit',
                    'uses' => 'Backend\CamController@showLimitOffer'
                ]);

                Route::post('update-limit-offer', [
                    'as' => 'update_limit_offer',
                    'uses' => 'Backend\CamController@updateLimitOffer'
                ]);

                Route::get('show-limit', [
                    'as' => 'show_limit',
                    'uses' => 'Backend\CamController@showLimit'
                ]);

                Route::post('update-limit', [
                    'as' => 'update_limit',
                    'uses' => 'Backend\CamController@updateLimit'
                ]);

                Route::post('approve-offer', [
                    'as' => 'approve_offer',
                    'uses' => 'Backend\CamController@approveOffer'
                ]);

                Route::get('cam-report', [
                    'as' => 'cam_report',
                    'uses' => 'Backend\CamController@viewCamReport'
                ]);

                Route::get('generate-cam-report', [
                    'as' => 'generate_cam_report',
                    'uses' => 'Backend\CamController@generateCamReport'
                ]);

                Route::post('save-bank-detail', [
                    'as' => 'save_bank_detail',
                    'uses' => 'Backend\CamController@saveBankDetail'
                ]);
                Route::get('/reject-offer-form', [
                    'as' => 'reject_offer_form',
                    'uses' => 'Backend\CamController@rejectOfferForm'
                ]);
                Route::post('reject-offer', [
                    'as' => 'reject_offer',
                    'uses' => 'Backend\CamController@rejectOffer'
                ]);
                Route::get('/approve-limit-form', [
                    'as' => 'approve_limit_form',
                    'uses' => 'Backend\CamController@approveLimitForm'
                ]);
                Route::get('/finstmt-download', [
                'as' => 'download_fin_stmt_doc',
                'uses' => 'Backend\DocumentController@downloadStorageFile'
                ]);
                Route::get('security-deposit', [
                    'as' => 'security_deposit',
                    'uses' => 'Backend\CamController@securityDeposit'
                ]);
                Route::post('save-security-deposit', [
                    'as' => 'save_security_deposit',
                    'uses' => 'Backend\CamController@saveSecurityDeposit'
                ]);
            }); //end of cam   
                        
            Route::get('copy-app-confirmBox', [
                'as' => 'copy_app_confirmbox',
                'uses' => 'Backend\RenewalController@copyAppConfirmbox'
            ]);
            
            Route::post('renew-application', [
                'as' => 'renew_application',
                'uses' => 'Backend\RenewalController@renewApplication'
            ]);

            Route::get('renewal-application-list', [
                'as' => 'renewal_application_list',
                'uses' => 'Backend\RenewalController@renewalAppList'
            ]); 
            
            Route::get('check-renewal-application', [
                'as' => 'check_renewal_application',
                'uses' => 'Backend\RenewalController@checkRenewalApps'
            ]); 
                        
            Route::get('enhance-limit-confirmBox', [
                'as' => 'enhance_limit_confirmbox',
                'uses' => 'Backend\RenewalController@copyAppConfirmbox'
            ]);
            
            Route::post('create-enhanced-limit-app', [
                'as' => 'create_enhanced_limit_app',
                'uses' => 'Backend\RenewalController@renewApplication'
            ]);

            Route::get('reduce-limit-confirmBox', [
                'as' => 'reduce_limit_confirmBox',
                'uses' => 'Backend\RenewalController@copyAppConfirmbox'
            ]);
            
            Route::post('create_reduced_limit_app', [
                'as' => 'create_reduced_limit_app',
                'uses' => 'Backend\RenewalController@renewApplication'
            ]);  
            
            Route::get('api/change/year',[
                'as' => 'api_change_year',
                'uses' => 'Auth\ApiController@changeFinancialYear'
            ]);

            Route::post('api/change/year',[
                'as' => 'api_change_year',
                'uses' => 'Auth\ApiController@changeFinancialYear'
            ]);   
            
            Route::get('user-invoice-location-app', [
                'as' => 'user_invoice_location_app',
                'uses' => 'Backend\ApplicationController@userInvoiceLocationApp',
            ]);

            Route::post('save-user-invoice-location-app', [
                'as' => 'save_user_invoice_location_app',
                'uses' => 'Backend\ApplicationController@saveUserInvoiceLocationApp',
            ]);
            
        });//end of application

        Route::group(['prefix' => 'lead'], function () {
            Route::get('/', [
                'as' => 'lead_list',
                'uses' => 'Backend\LeadController@index'
            ]);


            Route::get('edit-backend-lead', [
                'as' => 'edit_backend_lead',
                'uses' => 'Backend\LeadController@editBackendLead'
            ]);
            Route::get('lead-detail', [
                'as' => 'lead_detail',
                'uses' => 'Backend\LeadController@leadDetail'
            ]); 
            Route::get('create-lead', [
                'as' => 'create_backend_lead',
                'uses' => 'Backend\LeadController@createBackendLead'
            ]);   
            Route::post('save-create-lead', [
                'as' => 'save_backend_lead',
                'uses' => 'Backend\LeadController@saveBackendLead'
            ]); 

            Route::post('update-backend-lead', [
                'as' => 'update_backend_lead',
                'uses' => 'Backend\LeadController@updateBackendLead'
            ]);  
                        
            Route::get('download-sample-lead-csv', [
                'as' => 'download_sample_lead_csv',
                'uses' => 'Backend\LeadController@downloadSample'
            ]);
        });


        Route::group(['prefix' => 'non-anchor-leads'], function () {
            Route::get('/', [
                'as' => 'non_anchor_lead_list',
                'uses' => 'Backend\LeadController@getNonAnchorLeads'
            ]);
        });
        
        Route::group(['prefix' => 'fircu'], function () {
            Route::get('/applications', [
                'as' => 'applicaiton_list',
                'uses' => 'Backend\FiRcuController@appList'
            ]);
            
            Route::get('/fi', [
                'as' => 'backend_agency_fi',
                'uses' => 'Backend\FiRcuController@listFI'
            ]);

            Route::get('/inspection', [
                'as' => 'backend_agency_inspection',
                'uses' => 'Backend\FiRcuController@listInspection'
            ]);

            Route::get('/rcu', [
                'as' => 'backend_agency_rcu',
                'uses' => 'Backend\FiRcuController@listRCU'
            ]);   
        });
        Route::group(['prefix' => 'transfer-lead'], function(){

            Route::get('/', [
                'as' => 'transfer_lead',
                'uses' => 'Backend\DashboardController@assignedLead'
            ]);

            Route::get('lead-assign', [
                'as' => 'assign_lead',
                'uses' => 'Backend\LeadController@assignedLead'
            ]);

            Route::get('case-assign', [
                'as' => 'assign_cases',
                'uses' => 'Backend\LeadController@assignedCases'
            ]);
        });

        Route::group(['prefix' => 'anchor'], function(){
            Route::get('/', [
                'as' => 'get_anchor_list',
                'uses' => 'Backend\LeadController@allAnchorList'
            ]);
            Route::get('add-anchor', [
                'as' => 'add_anchor_reg',
                'uses' => 'Backend\LeadController@addAnchorReg'
            ]);

            Route::post('add-anchor', [
                'as' => 'add_anchor_reg',
                'uses' => 'Backend\LeadController@saveaddAnchorReg'
            ]);

            Route::get('/add-anchor-lead',
                [
                'as' => 'add_anchor_lead',
                'uses' => 'Backend\LeadController@uploadAnchorlead'
            ]);
            Route::post('/add-anchor-lead',
                [
                'as' => 'add_anchor_lead',
                'uses' => 'Backend\LeadController@saveUploadAnchorlead'
            ]);
            Route::get('update-anchor', [
                'as' => 'edit_anchor_reg',
                'uses' => 'Backend\LeadController@editAnchorReg'
            ]);

            Route::get('view-uploaded-file', [
                'as' => 'view_uploaded_file',
                'uses' => 'Backend\LeadController@viewUploadedFile'
            ]);

            Route::post('update-anchor', [
                'as' => 'update_anchor_reg',
                'uses' => 'Backend\LeadController@updateAnchorReg'
            ]); 
            Route::get('manage-anchor-lead', [
                'as' => 'get_anchor_lead_list',
                'uses' => 'Backend\LeadController@getAnchorLeadList'
            ]); 
            Route::get('add-manual-anchor-lead', [
                'as' => 'add_manual_anchor_lead',
                'uses' => 'Backend\LeadController@addManualAnchorLead'
            ]);

            Route::post('add-manual-anchor-lead', [
               'as' => 'add_manual_anchor_lead',
               'uses' => 'Backend\LeadController@saveManualAnchorLead'
            ]);         
            
            Route::post('accept-application-pool', [
                'as' => 'accept_application_pool',
                'uses' => 'Backend\LeadController@acceptApplicationPool'
            ]); 
            
            Route::post('get-city-list', [
                'as' => 'get-city-list',
                'uses' => 'Backend\LeadController@getCityList'
            ]);
            
            Route::get('assign-user-lead', [
                'as' => 'assign_user_leads',
                'uses' => 'Backend\LeadController@assignUserLeads'
            ]);

            Route::post('assign-user-lead', [
                'as' => 'assign_user_leads',
                'uses' => 'Backend\LeadController@saveassignUserLeads'
            ]);

            Route::get('assign-user-application',[
                'as' => 'assign_user_application',
                'uses' => 'Backend\ApplicationController@assignUserApplication'
                ]);

            Route::post('assign-user-application',[
                'as' => 'assign_user_application',
                'uses' => 'Backend\ApplicationController@saveassignUserApplication'
                ]);
            //add anchor bank details
            
            Route::get('add-anchor-bank', [
                'as' => 'add_anchor_bank_account',
                'uses' => 'Backend\LeadController@addAnchorBank'
            ]);
            
            Route::post('/save-anchor-bank-account', [
                'as' => 'save_anchor_bank_account',
                'uses' => 'Backend\LeadController@saveAnchorBankAccount'
            ]);
            
            Route::get('manage-program', [
                'as' => 'manage_program',
                'uses' => 'Backend\ProgramController@mangeProgramList'
            ]);
            
             Route::get('add-program', [
                'as' => 'add_program',
                'uses' => 'Backend\ProgramController@addProgram'
            ]);
             
             
            Route::post('save-program', [
            'as' => 'save_program',
            'uses' => 'Backend\ProgramController@saveProgram'
           ]);
            
            
            Route::get('add-sub-program', [
            'as' => 'add_sub_program',
            'uses' => 'Backend\ProgramController@addSubProgram'
           ]);
            
            Route::get('manage-sub-program', [
            'as' => 'manage_sub_program',
            'uses' => 'Backend\ProgramController@mangeSubProgram'
           ]);
            
             Route::post('save-sub-program', [
            'as' => 'save_sub_program',
            'uses' => 'Backend\ProgramController@saveSubProgram'
           ]);
             
            Route::get('confirm-end-program', [
            'as' => 'confirm_end_program',
            'uses' => 'Backend\ProgramController@confirmEndProgram'
           ]); 
            
            Route::post('save-end-program', [
            'as' => 'save_end_program',
            'uses' => 'Backend\ProgramController@saveEndProgram'
           ]); 
            
            Route::get('view-sub-program', [
            'as' => 'view_sub_program',
            'uses' => 'Backend\ProgramController@addSubProgram'
           ]); 
            Route::get('view-end-program-reason', [
            'as' => 'view_end_program_reason',
            'uses' => 'Backend\ProgramController@viewEndPrgmReason'
           ]); 

             Route::get('edit-program', [
                'as' => 'edit_program',
                'uses' => 'Backend\ProgramController@addProgram'
            ]);

            Route::get('nach-list', [
                 'as' => 'anchor_nach_list',
                'uses' => 'Backend\AnchorNACHController@nachList'
            ]);
            Route::get('create-nach', [
                'as' => 'anchor_create_nach',
                'uses' => 'Backend\AnchorNACHController@createNACH',
            ]);
            Route::post('/add-nach-detail', [
                'as' => 'anchor_add_nach_detail',
                'uses' => 'Backend\AnchorNACHController@addNachDetail',
            ]);
            
            Route::get('/edit-nach-detail', [
                'as' => 'anchor_edit_nach_detail',
                'uses' => 'Backend\AnchorNACHController@EditNachDetail',
            ]);
            
            Route::post('/save-nach-detail', [
                'as' => 'anchor_save_nach_detail',
                'uses' => 'Backend\AnchorNACHController@saveNachDetail',
            ]);

            Route::get('/nach-detail-preview', [
                'as' => 'anchor_nach_detail_preview',
                'uses' => 'Backend\AnchorNACHController@nachDetailPreview',
            ]); 
                    
            Route::get('generate-nach', [
                'as' => 'anchor_generate_nach',
                'uses' => 'Backend\AnchorNACHController@generateNach'
            ]);
            
            Route::get('/upload-nach-document', [
                'as' => 'anchor_upload_nach_document',
                'uses' => 'Backend\AnchorNACHController@uploadNachDocument'
            ]);

            Route::post('/nach_document-save', [
                'as' => 'anchor_nach_document_save',
                'uses' => 'Backend\AnchorNACHController@saveNachDocument'
            ]);
             
        });
            // All master routes
        Route::group(['prefix' => 'manage'], function () {

            Route::get('/charges', [
                'as' => 'get_charges_list',
                'uses' => 'Master\ChargeController@index'
            ]);
            Route::get('/add_charges', [
                'as' => 'add_charges',
                'uses' => 'Master\ChargeController@addCharges'
            ]); 
            Route::get('/edit_charges', [
                'as' => 'edit_charges',
                'uses' => 'Master\ChargeController@editCharges'
            ]);

            Route::post('/save_charges', [
                'as' => 'save_charges',
                'uses' => 'Master\ChargeController@saveCharges'
            ]); 

            Route::get('/documents', [
                'as' => 'get_documents_list',
                'uses' => 'Master\DocumentController@index'
            ]);

            Route::get('/vouchers', [
                'as' => 'get_vouchers_list',
                'uses' => 'Master\VoucherController@index'
            ]);

            Route::get('/add_voucher', [
                'as' => 'add_voucher',
                'uses' => 'Master\VoucherController@addVoucher'
            ]);

            Route::post('/save_voucher', [
                'as' => 'save_voucher',
                'uses' => 'Master\VoucherController@saveVoucher'
            ]);

            Route::get('/add_documents', [
                'as' => 'add_documents',
                'uses' => 'Master\DocumentController@addDocument'
            ]); 
            Route::get('/edit_documents', [
                'as' => 'edit_documents',
                'uses' => 'Master\DocumentController@editDocument'
            ]);

            Route::post('/save_documents', [
                'as' => 'save_documents',
                'uses' => 'Master\DocumentController@saveDocuments'
            ]);
            
             // business
             Route::get('/business', [
                'as' => 'get_entity_list',
                'uses' => 'Master\EntityController@index'
            ]);
            Route::get('/add_entity', [
                'as' => 'add_entity',
                'uses' => 'Master\EntityController@addEntity'
            ]);
            Route::post('/save_entity', [
                'as' => 'save_entity',
                'uses' => 'Master\EntityController@saveEntity'
            ]);
            Route::get('/edit_entity', [
                'as' => 'edit_entity',
                'uses' => 'Master\EntityController@editEntity'
            ]);

            Route::get('/industries', [
                'as' => 'get_industries_list',
                'uses' => 'Master\IndustryController@index'
            ]);
            Route::get('/add_industries', [
                'as' => 'add_industries',
                'uses' => 'Master\IndustryController@addIndustry'
            ]); 
            Route::get('/edit_industries', [
                'as' => 'edit_industries',
                'uses' => 'Master\IndustryController@editIndustry'
            ]);

            Route::post('/save_industries', [
                'as' => 'save_industries',
                'uses' => 'Master\IndustryController@saveIndustries'
            ]); 
            
            Route::get('/doa-levels', [
                'as' => 'manage_doa',
                'uses' => 'Master\DoaController@index'
            ]);

            Route::get('/add-doa-level', [
                'as' => 'add_doa_level',
                'uses' => 'Master\DoaController@addDoaLevel'
            ]);
            
            Route::get('/edit-doa-level', [
                'as' => 'edit_doa_level',
                'uses' => 'Master\DoaController@addDoaLevel'
            ]);            
            
            Route::post('/save_doa-level', [
                'as' => 'save_doa_level',
                'uses' => 'Master\DoaController@saveDoaLevel'
            ]);
                        
            Route::get('/assign-role-level', [
                'as' => 'assign_role_level',
                'uses' => 'Master\DoaController@assignRoleLevel'
            ]);
            
            Route::post('/save-assign-role-level', [
                'as' => 'save_assign_role_level',
                'uses' => 'Master\DoaController@saveAssignRoleLevel'
            ]); 
            
            
            
            
            
            
            
            Route::get('/get-co-lenders', [
                'as' => 'get_co_lenders',
                'uses' => 'Master\CoLenderControllers@getColenders'
            ]);  
            Route::get('/add-co-lender', [
                'as' => 'add_co_lender',
                'uses' => 'Master\CoLenderControllers@addCoLender'
            ]);  
            Route::post('/save-co-lender', [
                'as' => 'save_co_lenders',
                'uses' => 'Master\CoLenderControllers@saveCoLender'
            ]);
            Route::get('/share-to-colender', [
                'as' => 'share_to_colender',
                'uses' => 'Master\CoLenderControllers@shareToColender'
            ]);  
            Route::post('/share-to-colender', [
                'as' => 'save_share_to_colender',
                'uses' => 'Master\CoLenderControllers@saveShareToColender'
            ]);
            Route::get('/view-shared-colender', [
                'as' => 'view_shared_colender',
                'uses' => 'Master\CoLenderControllers@viewSharedColender'
            ]);
            Route::get('/get-colender-soa', [
                'as' => 'view_colander_soa',
                'uses' => 'Master\CoLenderControllers@viewColenderSoa'
            ]);  
            
            
            //Company
            
            Route::get('company', [
                'as' => 'get_companies_list',
                'uses' => 'Master\CompanyController@index'
            ]);
            
            Route::get('add-company', [
                'as' => 'add_companies',
                'uses' => 'Master\CompanyController@addCompanyForm'
            ]);
            
            Route::post('save-company', [
                'as' => 'save_companies',
                'uses' => 'Master\CompanyController@saveCompanies'
            ]);
            
            Route::get('edit-company', [
                'as' => 'edit_companies',
                'uses' => 'Master\CompanyController@addCompanyForm'
            ]);
            
            Route::get('/add-company-bank-account', [
                'as' => 'add_company_bank_account',
                'uses' => 'Master\CompanyController@addCompanyBankAccount'
            ]);
            
            Route::post('/save-company-bank-account', [
                'as' => 'save_company_bank_account',
                'uses' => 'Master\CompanyController@saveCompanyBankAccount'
            ]);
            
             // GST
             Route::get('/gst', [
                'as' => 'get_gst_list',
                'uses' => 'Master\GstController@index'
            ]);
            Route::get('/add_Gst', [
                'as' => 'add_Gst',
                'uses' => 'Master\GstController@addGst'
            ]);
            Route::post('/save_Gst', [
                'as' => 'save_Gst',
                'uses' => 'Master\GstController@saveGst'
            ]);
            Route::get('/edit_Gst', [
                'as' => 'edit_Gst',
                'uses' => 'Master\GstController@editGst'
            ]);

            // Segment
            Route::get('/segment', [
                'as' => 'get_segment_list',
                'uses' => 'Master\SegmentController@index'
            ]);
            Route::get('/add_segment', [
                'as' => 'add_segment',
                'uses' => 'Master\SegmentController@addSegment'
            ]);
            Route::get('/edit_segment', [
                'as' => 'edit_segment',
                'uses' => 'Master\SegmentController@editSegment'
            ]);
            Route::post('/save_segment', [
                'as' => 'save_segment',
                'uses' => 'Master\SegmentController@saveSegment'
            ]);
    
            // constitutions
            Route::get('/constitutions', [
                'as' => 'get_constitutions_list',
                'uses' => 'Master\ConstiController@index'
            ]);
            Route::get('/add_constitution', [
                'as' => 'add_constitution',
                'uses' => 'Master\ConstiController@addConstitution'
            ]);
            Route::get('/edit_constitution', [
                'as' => 'edit_constitution',
                'uses' => 'Master\ConstiController@editConstitution'
            ]);
            Route::post('/save_constitution', [
                'as' => 'save_constitution',
                'uses' => 'Master\ConstiController@saveConstitution'
            ]);
            
            // equipment
            Route::get('/equipment', [
                'as' => 'get_equipment_list',
                'uses' => 'Master\EquipmentController@index'
            ]);
            Route::get('/add_equipment', [
                'as' => 'add_equipment',
                'uses' => 'Master\EquipmentController@addEquipment'
            ]);
            Route::get('/edit_equipment', [
                'as' => 'edit_equipment',
                'uses' => 'Master\EquipmentController@editEquipment'
            ]);
            Route::post('/save_equipment', [
                'as' => 'save_equipment',
                'uses' => 'Master\EquipmentController@saveEquipment'
            ]);
            
            //Base Rate
            Route::get('/bank-base-rate', [
                'as' => 'get_baserate_list',
                'uses' => 'Master\BaseRateController@index'
            ]);
            
            Route::get('/add-base-rate', [
                'as' => 'add_base_rate',
                'uses' => 'Master\BaseRateController@addBaseRate'
            ]); 
            Route::get('/edit-base-rate', [
                'as' => 'edit_base_rate',
                'uses' => 'Master\BaseRateController@editBaseRate'
            ]);

            Route::post('/save-base-rate', [
                'as' => 'save_base_rate',
                'uses' => 'Master\BaseRateController@saveBaseRate'
            ]);

            // Manage TDS
            Route::get('/get-tds', [
                'as' => 'get_tds_list',
                'uses' => 'Master\TdsController@list'
            ]);

            Route::get('/add-tds', [
                'as' => 'add_tds',
                'uses' => 'Master\TdsController@addTds'
            ]);

            Route::get('/edit_tds', [
                'as' => 'edit_tds',
                'uses' => 'Master\TdsController@editTds'
            ]);

            Route::post('/save-tds', [
                'as' => 'save_tds',
                'uses' => 'Master\TdsController@saveTds'
            ]);

            // Manage Borrower Limit 
            Route::get('/get-borrower-limit', [
                'as' => 'get_borrower_limit',
                'uses' => 'Master\LimitController@list'
            ]);

            Route::get('/add-borrowe-limit',[
                'as'=>'add_borrower_limit',
                'uses' => 'Master\LimitController@addLimit'
            ]);

            Route::get('/edit_borrower_limit',[
                'as'=>'edit_borrower_limit',
                'uses' => 'Master\LimitController@editLimit'
            ]);

            Route::post('/save-borrowe-limit', [
                'as' => 'save_borrowe_limit',
                'uses' => 'Master\LimitController@saveLimit'
            ]);
            // END Manage TDS

            //Manage Bank Name
            Route::get('/get-bank-list', [
                'as' => 'get_bank_list',
                'uses' => 'Master\BankController@index'
            ]);

            Route::get('/add-new-bank', [
                'as' => 'add_new_bank',
                'uses' => 'Master\BankController@addBank'
            ]);

            Route::post('/save-new-bank', [
                'as' => 'save_new_bank',
                'uses' => 'Master\BankController@saveNewBank'
            ]);

            Route::get('/list-location', [
                'as' => 'list_location_type',
                'uses' => 'Master\LocationTypeController@index'
            ]);
            
            Route::get('/add-location', [
                'as' => 'add_location_type',
                'uses' => 'Master\LocationTypeController@addLocationType'
            ]);

            Route::post('/add-location', [
                'as' => 'add_location_type',
                'uses' => 'Master\LocationTypeController@saveLocationType'
            ]);

            Route::get('/edit-location', [
                'as' => 'edit_location_type',
                'uses' => 'Master\LocationTypeController@editLocationType'
            ]);

            Route::post('/edit-location', [
                'as' => 'edit_location_type',
                'uses' => 'Master\LocationTypeController@saveLocationType'
            ]);
            //Start Security Document
            Route::get('/list-security-document', [
                'as' => 'list_security_document',
                'uses' => 'Master\SecurityDocumentController@index'
            ]);

            Route::get('/add-security-document', [
                'as' => 'add_security_document',
                'uses' => 'Master\SecurityDocumentController@addSecurityDoc'
            ]);

            Route::post('/add-security-document', [
                'as' => 'add_security_document',
                'uses' => 'Master\SecurityDocumentController@saveSecurityDoc'
            ]);

            Route::get('/edit-security-document', [
                'as' => 'edit_security_document',
                'uses' => 'Master\SecurityDocumentController@editSecurityDoc'
            ]);

            Route::post('/edit-security-document', [
                'as' => 'edit_security_document',
                'uses' => 'Master\SecurityDocumentController@saveSecurityDoc'
            ]);
            //END Security Document
            
        });

        Route::group(['prefix' => 'agency'], function () {
            Route::get('/', [
                'as' => 'get_agency_list',
                'uses' => 'Backend\AgencyController@allAgencyList'
            ]);
            Route::get('manage-agency-user', [
                'as' => 'get_agency_user_list',
                'uses' => 'Backend\AgencyController@getAgencyUserList'
            ]);
            Route::get('add-agency', [
                'as' => 'add_agency_reg',
                'uses' => 'Backend\AgencyController@addAgencyReg'
            ]);

            Route::post('add-agency', [
                'as' => 'save_agency_reg',
                'uses' => 'Backend\AgencyController@saveAgencyReg'
            ]);

            Route::get('update-agency', [
                'as' => 'edit_agency_reg',
                'uses' => 'Backend\AgencyController@editAgencyReg'
            ]);

            Route::post('update-agency', [
                'as' => 'update_agency_reg',
                'uses' => 'Backend\AgencyController@updateAgencyReg'
            ]);

            Route::get('/users', [
                'as' => 'get_agency_user_list',
                'uses' => 'Backend\AgencyController@getAgencyUserList'
            ]);

            Route::get('add-agency-user', [
                'as' => 'add_agency_user_reg',
                'uses' => 'Backend\AgencyController@addAgencyUserReg'
            ]);

            Route::post('add-agency-user', [
                'as' => 'save_agency_user_reg',
                'uses' => 'Backend\AgencyController@saveAgencyUserReg'
            ]);

            Route::get('update-agency-user', [
                'as' => 'edit_agency_user_reg',
                'uses' => 'Backend\AgencyController@editAgencyUserReg'
            ]);

            Route::post('update-agency-user', [
                'as' => 'update_agency_user_reg',
                'uses' => 'Backend\AgencyController@updateAgencyUserReg'
            ]);        
        });
        
          ///////////////////////// Route for invoice controller///////////////////////


        Route::group(['prefix' => 'document'], function () {
            Route::get('/list', [
                'as' => 'pp_document_list',
                'uses' => 'Backend\DocumentController@listDocument'
            ]);

            Route::get('/upload-document', [
                'as' => 'pp_upload_document',
                'uses' => 'Backend\DocumentController@uploadDocument'
            ]);
            Route::get('/see-prepost-file',[
                'as' => 'view_prepost_documents',
                'uses' => 'Backend\DocumentController@seeUploadFile'
            ]);

            Route::post('/documents-save', [
                'as' => 'pp_document_save',
                'uses' => 'Backend\DocumentController@saveDocument'
            ]);

            Route::get('/edit-upload-document', [
                'as' => 'pp_edit_upload_document',
                'uses' => 'Backend\ApplicationController@ppEditUploadDocument'
            ]);

            Route::post('/update-edit-upload-document', [
                'as' => 'pp_update_edit_upload_document',
                'uses' => 'Backend\ApplicationController@ppUpdateEditUploadDocument'
            ]);
            
        });   
        
        

        //colender route 
         Route::group(['prefix' => 'colender'], function () {
            Route::get('application', [
                'as' => 'colender_application_list',
                'uses' => 'Master\CoLenderControllers@appList'
            ]); 
            
            Route::get('application/view-offer', [
                'as' => 'colender_view_offer',
                'uses' => 'Master\CoLenderControllers@showOffer'
            ]);

            Route::post('accept-offer', [
                'as' => 'accept_offer_by_colender',
                'uses' => 'Master\CoLenderControllers@acceptOffer'
            ]);
            // Payment Advice Excel Download
             
         }); 
        //colender route 

         Route::group(['prefix' => 'finance'], function () {
            Route::get('/', [
                'as' => 'get_fin_trans_list',
                'uses' => 'Backend\FinanceController@getFinTransList'
            ]);
            Route::get('fin-journal', [
                'as' => 'get_fin_journal',
                'uses' => 'Backend\FinanceController@getFinJournal'
            ]);
            Route::post('save-journal', [
                'as' => 'save_journal',
                'uses' => 'Backend\FinanceController@saveJournal'
            ]);
            Route::get('fin-account', [
                'as' => 'get_fin_account',
                'uses' => 'Backend\FinanceController@getFinAccount'
            ]);
            Route::post('save-account', [
                'as' => 'save_account',
                'uses' => 'Backend\FinanceController@saveAccount'
            ]);
            Route::get('fin-variable', [
                'as' => 'get_fin_variable',
                'uses' => 'Backend\FinanceController@getFinVariable'
            ]);
            Route::get('create-je-config', [
                'as' => 'create_je_config',
                'uses' => 'Backend\FinanceController@crateJeConfig'
            ]);
            Route::post('save-je-config', [
                'as' => 'save_je_config',
                'uses' => 'Backend\FinanceController@saveJeConfig'
            ]);
            Route::get('add-ji-config', [
                'as' => 'add_ji_config',
                'uses' => 'Backend\FinanceController@addJiConfig'
            ]);
            Route::post('save-ji-config', [
                'as' => 'save_ji_config',
                'uses' => 'Backend\FinanceController@saveJiConfig'
            ]);
            Route::get('fin-transactions', [
                'as' => 'get_fin_transactions',
                'uses' => 'Backend\FinanceController@getFinTransactions'
            ]);
            Route::get('fin-batches', [
                'as' => 'get_tally_batches',
                'uses' => 'Backend\FinanceController@getFinBatches'
            ]);
            Route::get('export_txns', [
                'as' => 'export_txns',
                'uses' => 'Backend\FinanceController@exportTransactions'
            ]);                        
            Route::get('export_fact_payment_txns', [
                'as' => 'export_fact_payment_txns',
                'uses' => 'Backend\FinanceController@exportFactPaymentTransactions'
            ]);                        
            Route::get('export_fact_journal_txns', [
                'as' => 'export_fact_journal_txns',
                'uses' => 'Backend\FinanceController@exportFactJournalTransactions'
            ]);
            Route::get('download_fact_payment_txns', [
                'as' => 'download_fact_payment_txns',
                'uses' => 'Backend\FinanceController@downloadFactPaymentTransactions'
            ]);                        
            Route::get('download_fact_journal_txns', [
                'as' => 'download_fact_journal_txns',
                'uses' => 'Backend\FinanceController@downloadFactJournalTransactions'
            ]);                         
        });

        Route::group(['prefix' => 'nach'], function () {
            
            Route::get('nach-list', [
                 'as' => 'backend_nach_list',
                'uses' => 'Backend\NACHController@nachList'
            ]);
            Route::get('create-nach', [
                'as' => 'backend_create_nach',
                'uses' => 'Backend\NACHController@createNACH',
            ]);
            Route::post('/add-nach-detail', [
                'as' => 'backend_add_nach_detail',
                'uses' => 'Backend\NACHController@addNachDetail',
            ]);
            
            Route::get('/edit-nach-detail', [
                'as' => 'backend_edit_nach_detail',
                'uses' => 'Backend\NACHController@EditNachDetail',
            ]);
            
            Route::post('/save-nach-detail', [
                'as' => 'backend_save_nach_detail',
                'uses' => 'Backend\NACHController@saveNachDetail',
            ]);

            Route::get('/nach-detail-preview', [
                'as' => 'backend_nach_detail_preview',
                'uses' => 'Backend\NACHController@nachDetailPreview',
            ]); 
                    
            Route::get('generate-nach', [
                'as' => 'backend_generate_nach',
                'uses' => 'Backend\NACHController@generateNach'
            ]);
            
            Route::get('/upload-nach-document', [
                'as' => 'backend_upload_nach_document',
                'uses' => 'Backend\NACHController@uploadNachDocument'
            ]);

            Route::post('/nach_document-save', [
                'as' => 'backend_nach_document_save',
                'uses' => 'Backend\NACHController@saveNachDocument'
            ]); 
            Route::group(['prefix' => 'repayment'], function () {
                
                Route::get('list', [
                    'as' => 'nach_repayment_list',
                    'uses' => 'Backend\NACHController@repaymentList'
                ]);
                
                Route::post('create-nach-repayment-req', [
                    'as' => 'create_nach_repayment_req',
                    'uses' => 'Backend\NACHController@createNachRepaymentReq'
                ]);
                
                Route::get('repayment_trans_list', [
                    'as' => 'nach_repayment_trans_list',
                    'uses' => 'Backend\NACHController@repaymentTransList'
                ]);
                
                Route::get('/upload-nach-trans-response', [
                        'as' => 'upload_nach_trans_response',
                        'uses' => 'Backend\NACHController@uploadNachTransResponse'
                    ]);
                    
                Route::post('/import-nach-trans-response', [
                    'as' => 'import_nach_trans_response',
                    'uses' => 'Backend\NACHController@importNachTransResponse'
                ]);
                
            });                     
        });

        Route::post('ckeditor/image_upload', 'CKEditorController@upload')->name('upload_ckeditor_image');
    });

    Route::get('/cron-test', [
        'as' => 'cron_test',
        'uses' => 'Backend\InvoiceController@disbursalPaymentEnquiryCron'
    ]);

  });

