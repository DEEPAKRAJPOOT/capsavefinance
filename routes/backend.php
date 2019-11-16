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
        ['middleware' => 'auth'],
        function () {
            Route::resource('lead', 'Backend\LeadController');
            Route::resource('supplier', 'Backend\SupplierController');
            Route::resource('buyer', 'Backend\BuyerController');
            Route::resource('lender', 'Backend\LenderController');
            Route::resource('logistics', 'Backend\LogisticsController');
        });
            Route::get('cam/overview', 'Backend\CamController@index');
            Route::get('lead-pool',
                    [
                    'as' => 'lead_leadspool',
                    'uses' => 'Backend\LeadController@leadspool'
            ]);
    
    
}); 
        
        
    Route::get('edit-backend-lead',
     [
        'as' => 'edit_backend_lead',
         'uses' => 'Backend\LeadController@editBackendLead'
    ]);

    Route::get('notes-from',
     [
        'as' => 'backend_notes_from',
         'uses' => 'Backend\NotesController@showNoteForm'
    ]);

    Route::get('notes', 'Backend\NotesController@index');
    //Route::get('notesForm', 'Backend\NotesController@showNoteForm');
    Route::post('notes', 'Backend\NotesController@store');
    
    
    
//    Route::group(['prefix' => 'profile'],
//        function () {
//        Route::group(['middleware' => 'adminauth'],
//            function () {
//
//            Route::get('/',
//                [
//                'as' => 'profile',
//                'uses' => 'Application\AccountController@index'
//            ]);
//           /* 
//            Route::get('edit',
//                [
//                'as' => 'edit_profile',
//                'uses' => 'Application\AccountController@editPersonalProfile'
//            ]);*/
//            
//            Route::post('edit',
//                [
//                'as' => 'update_personal_profile',
//                'uses' => 'Application\AccountController@savePersonalProfile'
//            ]);
//            
//
//        });
//    });

            Route::group(
                ['prefix' => 'dashboard'],
                function () {

                    Route::get(
                        '/',
                        [
                        'as' => 'backend_dashboard',
                        'uses' => 'Backend\DashboardController@index'
                        ]
                    );

            });

            Route::group(
                ['prefix' => 'application'],
                function () {
                    Route::get('/',
                        [
                        'as' => 'application_list',
                        'uses' => 'Backend\ApplicationController@index'
                    ]);

                    Route::get('/supplier',
                        [
                        'as' => 'supplier_list',
                        'uses' => 'Backend\SupplierController@index'
                    ]);

                    Route::get('cam/overview',
                        [
                        'as' => 'cam_overview',
                        'uses' => 'Backend\CamController@index'
                    ]);            
            });   


            Route::group(
                ['prefix' => 'lead'],
                function () {
                    Route::get('/',
                        [
                        'as' => 'lead_list',
                        'uses' => 'Backend\LeadController@index'
                    ]);

                    Route::get('edit-backend-lead',
                        [
                        'as' => 'edit_backend_lead',
                        'uses' => 'Backend\LeadController@editBackendLead'
                    ]);
            });

