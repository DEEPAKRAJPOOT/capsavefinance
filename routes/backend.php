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

            Route::get('cam/overview', [
                'as' => 'cam_overview',
                'uses' => 'Backend\CamController@index'
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
            
            Route::post('promoter-document-save', [
                'as' => 'promoter_document_save',
                'uses' => 'Backend\ApplicationController@promoterDocumentSave'
            ]); 
            
            Route::post('application-save',
                [
                'as' => 'application_save',
                'uses' => 'Backend\ApplicationController@applicationSave'
            ]);
            
            Route::post('cam/cam-information-save', [
                'as' => 'cam/cam-information-save',
                'uses' => 'Backend\CamController@camInformationSave'
            ]);

            Route::get('cam/bank', [
                'as' => 'cam_bank',
                'uses' => 'Backend\CamController@banking'
            ]);

            Route::get('cam/finance', [
                'as' => 'cam_finance',
                'uses' => 'Backend\CamController@finance'
            ]);

            Route::post('cam/finance_store', [
                'as' => 'cam_finance_store',
                'uses' => 'Backend\CamController@finance_store'
            ]);
            
            Route::get('fircu/index', [
                'as' => 'backend_fircu_index',
                'uses' => 'Backend\FiRcuController@index'
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

            Route::get('cam/cibil', [
                'as' => 'cam_cibil',
                'uses' => 'Backend\CamController@showCibilForm'
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


            Route::get('cam/promoter', [
                'as' => 'cam_promoter',
                'uses' => 'Backend\CamController@showPromoter'
            ]);


        });

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
});

            Route::get('bank_statement', [
                'as' => 'bank_statement',
                'uses' => 'Backend\CamController@uploadBankStatement'
            ]);
            
            Route::get('financial_statement', [
                'as' => 'financial_statement',
                'uses' => 'Backend\CamController@uploadFinancialStatement'
            ]);

            Route::get('bank_report', [
                'as' => 'bank_statement',
                'uses' => 'Backend\CamController@getBankReport'
            ]);
            
            Route::get('financial_report', [
                'as' => 'financial_statement',
                'uses' => 'Backend\CamController@getFinanceReport'
            ]);


