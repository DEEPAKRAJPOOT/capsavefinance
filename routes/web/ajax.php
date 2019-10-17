<?php
/**
 * All Ajax routes
 */
Route::group(
    ['prefix' => 'ajax'],
    function () {
    Route::group(
        ['middleware' => 'checkAjax'],
        function () {
        Route::group(
            ['middleware' => 'auth'],
            function () {

           Route::post(
                'get-users-wci',
                [
                'as' => 'get_users_wci',
                'uses' => 'AjaxController@getUsersListAPI'
                ]
            ); 

            Route::post(
                'get-users-wci',
                [
                'as' => 'get_users_wci',
                'uses' => 'AjaxController@getUsersListAPIDummy'
                ]
            );
           
             Route::post(
                'get-users-wci-single',
                [
                'as' => 'get_users_wci_single',
                'uses' => 'AjaxController@getUsersDetailAPIDummy'
                ]
            );

           
           //
            Route::post(
                'shareholder-save-ajax',
                [
                'as' => 'shareholder_save_ajax',
                'uses' => 'AjaxController@saveShareholder'
                ]
            ); 
        });
    });
    Route::post(
                'without-login-right-ajax',
                [
                'as' => 'withought_login_right_ajax',
                'uses' => 'AjaxController@fetchRights'
                ]
            );
    Route::post(
                'without-login-global-search',
                [
                'as' => 'without_login_global_search',
                'uses' => 'AjaxController@globalSearch'
                ]
            );
});



