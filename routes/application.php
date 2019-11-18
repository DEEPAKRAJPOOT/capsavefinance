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
    
    Route::group(
        ['prefix' => 'application'],
        function () {
        Route::group(
            ['middleware' => 'auth'],
            function () {  
            Route::get('business-information',
                [
                'as' => 'business_information_open',
                'uses' => 'Backend\ApplicationController@showBusinessInformationForm'
            ]);

            Route::post('business-information-save',
                [
                'as' => 'business_information_save',
                'uses' => 'Backend\ApplicationController@saveBusinessInformation'
            ]);

            Route::get('promoter-detail',
                [
                'as' => 'promoter-detail',
                'uses' => 'Backend\ApplicationController@showPromoterDetail'
            ]);
            
            Route::post('promoter-detail-save',
                [
                    'as' => 'promoter_detail_save',
                    'uses' => 'Backend\ApplicationController@savePromoterDetail'
            ]);
            
            Route::get('document',
                [
                'as' => 'document',
                'uses' => 'Backend\ApplicationController@showDocument'
            ]);
            
            Route::post('document-save',
                [
                'as' => 'document-save',
                'uses' => 'Backend\ApplicationController@saveDocument'
            ]);
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
});    