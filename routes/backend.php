<?php

/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {

    Route::group(
            ['middleware' => 'auth'], function () {
        Route::group(
                ['prefix' => 'dashboard'], function () {

            Route::get(
                    '/', [
                'as' => 'backend_dashboard',
                'uses' => 'Backend\DashboardController@index'
                    ]
            );
        });

        Route::group(
                ['prefix' => 'application'], function () {
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

            Route::get('company-details/{id}', [
                'as' => 'company_details',
                'uses' => 'Backend\ApplicationController@showCompanyDetails'
            ]);
            
           Route::get('promoter-details/{id}', [
                'as' => 'promoter_details',
                'uses' => 'Backend\ApplicationController@showPromoterDetails'
            ]);

            Route::get('company-details',
                [
                'as' => 'company_details',
                'uses' => 'Backend\ApplicationController@showCompanyDetails'
            ]);

            Route::post('cam/cam-information-save', [
                'as' => 'cam/cam-information-save',
                'uses' => 'Backend\CamController@camInformationSave'
            ]);

            Route::get('cam/finance', [
                'as' => 'cam_finance',
                'uses' => 'Backend\CamController@finance'
            ]);

            Route::post('cam/finance_store', [
                'as' => 'cam_finance_store',
                'uses' => 'Backend\CamController@finance_store'
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
        });

        Route::group(
                ['prefix' => 'lead'], function () {
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


