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
            
            Route::post('documents-save', [
                'as' => 'document_save',
                'uses' => 'Backend\ApplicationController@saveDocument'
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

            Route::get('fircu/rcu', [
                'as' => 'backend_rcu',
                'uses' => 'Backend\FiRcuController@listRCU'
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

            
//            
            
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
        });


//////////////// For Promoter Iframe////////////////////////

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
        });
    });



            


            Route::get('mobile_verify', [
                'as' => 'mobile_verify',
                'uses' => 'Backend\ApplicationController@mobileModel'
            ]);


