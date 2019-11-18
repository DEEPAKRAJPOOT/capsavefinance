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
        })
    });
});

