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
            ['middleware' => ['auth','CheckWorkflow']],
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
                'uses' => 'Application\ApplicationController@showBusinessInformationForm'
            ]);

            Route::post('business-information-save',
                [
                'as' => 'business_information_save',
                'uses' => 'Application\ApplicationController@saveBusinessInformation'
            ]);

            Route::get('promoter-detail',
                [
                'as' => 'promoter-detail',
                'uses' => 'Application\ApplicationController@showPromoterDetail'
            ]);
            
            Route::post('promoter-detail-save',
                [
                    'as' => 'front_promoter_detail_save',
                    'uses' => 'Application\ApplicationController@updatePromoterDetail'
            ]); 
            
            Route::post('promoter-save',
                [
                    'as' => 'promoter_save',
                    'uses' => 'Application\ApplicationController@savePromoter'
            ]);
            Route::get('document',
                [
                'as' => 'document',
                'uses' => 'Application\ApplicationController@showDocument'
            ]);
            
            Route::post('document-save',
                [
                'as' => 'document-save',
                'uses' => 'Application\ApplicationController@saveDocument'
            ]);
            
            Route::get('document-delete/{appDocFileId}',
                [
                'as' => 'document-delete',
                'uses' => 'Application\ApplicationController@documentDelete'
            ]);
            
            Route::get('document-view',
                [
                'as' => 'document-view',
                'uses' => 'Application\ApplicationController@documentView'
            ]);
            
            Route::get('document-download',
                [
                'as' => 'document-download',
                'uses' => 'Application\ApplicationController@documentDownload'
            ]);
            
            Route::post('application-save',
                [
                'as' => 'front_application_save',
                'uses' => 'Application\ApplicationController@applicationSave'
            ]);
        });
    });
     
});    