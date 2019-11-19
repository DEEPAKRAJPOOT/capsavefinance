<?php
/**
 * All Ajax routes
 */
Route::group(
    ['middleware' => 'auth'],
    function () {


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
    
   Route::post(
        'get-lead',
        [
        'as' => 'get_lead',
        'uses' => 'AjaxController@getLeads'
        ]
    ); 
   Route::post(
        'get-case-pool',
        [
        'as' => 'get_case_pool',
        'uses' => 'AjaxController@getCasePools'
        ]
    ); 
      
   Route::post(
        'get-application',
        [
        'as' => 'ajax_app_list',
        'uses' => 'AjaxController@getApplications'
        ]
    );
    Route::post(
        'get-anchor-user-list',
        [
        'as' => 'get_anch_user_list',
        'uses' => 'AjaxController@getAnchorLists'
        ]
    ); 
});



