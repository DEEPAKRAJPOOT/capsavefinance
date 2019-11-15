<?php

/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.frontend_uri'))->group(function () {

    Route::group(
        ['prefix' => 'dashboard'],
        function () {
        Route::group(
            ['middleware' => 'auth'],
            function () {
            Route::get(
                '/',
                [
                'as' => 'front_dashboard',
                'uses' => 'Application\DashboardController@index'
                ]
            );
        });
    });
    
    
    Route::group(['prefix' => 'profile'],
        function () {
        Route::group(['middleware' => 'auth'],
            function () {

            Route::get('/',
                [
                'as' => 'profile',
                'uses' => 'Application\AccountController@index'
            ]);
           /* 
            Route::get('edit',
                [
                'as' => 'edit_profile',
                'uses' => 'Application\AccountController@editPersonalProfile'
            ]);*/
            
            Route::post('edit',
                [
                'as' => 'update_personal_profile',
                'uses' => 'Application\AccountController@savePersonalProfile'
            ]);
            

        });
    });
    
//   Resource controller @Supplier,@buyer,@lender,@logistics    //
    Route::get('cam/overview', 'Backend\CamController@index'); 


 
    
    
    
});


  
    