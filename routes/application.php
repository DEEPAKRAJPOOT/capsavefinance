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
            
            Route::get('document-delete/{appDocFileId}',
                [
                'as' => 'document-delete',
                'uses' => 'Backend\ApplicationController@documentDelete'
            ]);
            
            Route::get('document-view',
                [
                'as' => 'document-view',
                'uses' => 'Backend\ApplicationController@documentView'
            ]);
            
            Route::get('document-download',
                [
                'as' => 'document-download',
                'uses' => 'Backend\ApplicationController@documentDownload'
            ]);
            
            Route::post('application-save',
                [
                'as' => 'application_save',
                'uses' => 'Backend\ApplicationController@applicationSave'
            ]);
        });
    });
     
});    