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
        ['prefix' => 'dashboard'],
        function () {
        Route::group(
            ['middleware' => 'adminauth'],
            function () {
            Route::get(
                '/',
                [
                'as' => 'backend_dashboard',
                'uses' => 'Backend\DashboardController@index'
                ]
            );
        });
           
    });
    
      Route::group(
            ['middleware' => 'adminauth'],
            function () {
    
    
    Route::resource('lead', 'Backend\LeadController');
    Route::resource('supplier', 'Backend\SupplierController');
    Route::resource('buyer', 'Backend\BuyerController');
    Route::resource('lender', 'Backend\LenderController');
    Route::resource('logistics', 'Backend\LogisticsController');

    
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

    
});
