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
            
            Route::post('documents-save', [
                'as' => 'document_save',
                'uses' => 'Backend\ApplicationController@saveDocument'
            ]);
            
            Route::get('upload_bank_document', [
                'as' => 'upload_bank_document',
                'uses' => 'Backend\CamController@updateBankDocument'
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
            
            Route::post('save-app-note', [
                'as' => 'save_app_note',
                'uses' => 'Backend\ApplicationController@saveAppNote'
            ]); 
            
            Route::get('send-case-confirmBox', [
                'as' => 'send_case_confirmBox',
                'uses' => 'Backend\ApplicationController@sendCaseConfirmbox'
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
            
            Route::post('accept-offer', [
                'as' => 'accept_offer',
                'uses' => 'Backend\ApplicationController@acceptOffer'
            ]); 
            
            Route::get('sanction-letter', [
                'as' => 'gen_sanction_letter',
                'uses' => 'Backend\ApplicationController@genSanctionLetter'
            ]);
            
            Route::get('download-sanction-letter', [
                'as' => 'download_sanction_letter',
                'uses' => 'Backend\ApplicationController@downloadSanctionLetter'
            ]);

            Route::get('show-upload-sanction-letter', [
                'as' => 'show_upload_sanction_letter',
                'uses' => 'Backend\ApplicationController@showUploadSanctionLetter'
            ]);
            
            Route::post('upload-sanction-letter', [
                'as' => 'upload_sanction_letter',
                'uses' => 'Backend\ApplicationController@uploadSanctionLetter'
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

            Route::post('fircu/assign-rcu', [
                'as' => 'save_assign_rcu',
                'uses' => 'Backend\FiRcuController@saveAssignRcu'
            ]);
            

             Route::get('fircu/assign-rcu', [
                'as' => 'show_assign_rcu',
                'uses' => 'Backend\FiRcuController@showAssignRcu'
            ]);

            Route::get('pd-notes', [
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

            }); //end of cam   
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

            Route::get('/rcu', [
                'as' => 'backend_agency_rcu',
                'uses' => 'Backend\FiRcuController@listRCU'
            ]);   
        });
        
        Route::group(['prefix' => 'anchor'], function () {
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


           Route::group(['prefix' => 'invoice'], function () {
               Route::get('backend_upload_invoice', [
                 'as' => 'backend_upload_invoice',
                'uses' => 'Backend\InvoiceController@getInvoice'
            ]); 
               
         Route::get('backend_bulk_invoice', [
                 'as' => 'backend_bulk_invoice',
                'uses' => 'Backend\InvoiceController@getBulkInvoice'
            ]); 
            Route::get('backend_get_invoice', [
                 'as' => 'backend_get_invoice',
                'uses' => 'Backend\InvoiceController@viewInvoice'
            ]); 
          
           Route::get('backend_get_approve_invoice', [
                 'as' => 'backend_get_approve_invoice',
                'uses' => 'Backend\InvoiceController@viewApproveInvoice'
            ]); 
           
            Route::get('backend_get_disbursed_invoice', [
                 'as' => 'backend_get_disbursed_invoice',
                'uses' => 'Backend\InvoiceController@viewDisbursedInvoice'
            ]); 
            
            
            
             Route::get('backend_get_repaid_invoice', [
                 'as' => 'backend_get_repaid_invoice',
                'uses' => 'Backend\InvoiceController@viewRepaidInvoice'
            ]); 
             
              Route::get('backend_get_sent_to_bank', [
                 'as' => 'backend_get_sent_to_bank',
                'uses' => 'Backend\InvoiceController@viewSentToBankInvoice'
            ]); 
               Route::get('backend_get_failed_disbursment', [
                 'as' => 'backend_get_failed_disbursment',
                'uses' => 'Backend\InvoiceController@viewfailedDisbursment'
            ]); 
               
                Route::get('backend_get_disbursed', [
                 'as' => 'backend_get_disbursed',
                'uses' => 'Backend\InvoiceController@viewdisbursed'
            ]); 
                 Route::get('backend_get_reject_invoice', [
                 'as' => 'backend_get_reject_invoice',
                'uses' => 'Backend\InvoiceController@viewRejectInvoice'
            ]); 
              
             
        
           Route::POST('backend_save_invoice', [
                 'as' => 'backend_save_invoice',
                'uses' => 'Backend\InvoiceController@saveInvoice'
            ]); 
           
             Route::POST('backend_save_bulk_invoice', [
                 'as' => 'backend_save_bulk_invoice',
                'uses' => 'Backend\InvoiceController@saveBulkInvoice'
            ]); 
             
            Route::POST('update_invoice_amount', [
                 'as' => 'update_invoice_amount',
                'uses' => 'Backend\InvoiceController@saveInvoiceAmount'
            ]);    
             
             
         Route::get('backend_upload_all_invoice', [
                 'as' => 'backend_upload_all_invoice',
                'uses' => 'Backend\InvoiceController@getAllInvoice'
            ]);  
          Route::POST('backend_save_invoice', [
                 'as' => 'backend_save_invoice',
                'uses' => 'Backend\InvoiceController@saveInvoice'
            ]); 
          
            Route::get('invoice_failed_status', [
                 'as' => 'invoice_failed_status',
                'uses' => 'Backend\InvoiceController@invoiceFailedStatus'
            ]); 
            Route::get('invoice_success_status', [
                 'as' => 'invoice_success_status',
                'uses' => 'Backend\InvoiceController@invoiceSuccessStatus'
            ]); 
            
             Route::get('view_invoice_details', [
                 'as' => 'view_invoice_details',
                'uses' => 'Backend\InvoiceController@viewInvoiceDetails'
            ]);  
            
         });
         
        Route::group(['prefix' => 'document'], function () {
            Route::get('/list', [
                'as' => 'pp_document_list',
                'uses' => 'Backend\DocumentController@listDocument'
            ]);

            Route::get('/upload-document', [
                'as' => 'pp_upload_document',
                'uses' => 'Backend\DocumentController@uploadDocument'
            ]);

            Route::post('documents-save', [
                'as' => 'pp_document_save',
                'uses' => 'Backend\DocumentController@saveDocument'
            ]);
            
        });         
    });

  });

