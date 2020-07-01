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
                'uses' => 'Backend\DashboardController@index'
            ]);
            Route::get('/idfc', [
                'as' => 'idfc',
                'uses' => 'Backend\DashboardController@idfc'
            ]);
        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/lease-register', [
                'as' => 'lease_register',
                'uses' => 'Backend\ReportController@leaseRegister'
            ]);
            Route::get('/download', [
                'as' => 'download_reports',
                'uses' => 'Backend\ReportController@downloadLeaseReport'
            ]);
            Route::get('/duereport', [
                'as' => 'report_duereport',
                'uses' => 'Backend\ReportController@duereport'
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
            
            
            Route::get('document-delete/{appDocFileId}',
                [
                'as' => 'document_delete',
                'uses' => 'Application\ApplicationController@documentDelete'
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
            
            Route::get('get-city-list', [
                'as' => 'get-city-list',
                'uses' => 'Backend\LeadController@getCityList'
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
        });

        Route::post('ckeditor/image_upload', 'CKEditorController@upload')->name('upload_ckeditor_image');
    });

  });

