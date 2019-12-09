<?php
/**
 * All Ajax routes
 */
Route::group(
    ['middleware' => 'auth'],
    function () {
    
    
   //////////////////Pan card authontication//////////////////////////
    Route::post(
        'chk_user_pan_karza',
        [
        'as' => 'chk_user_pan_karza',
        'uses' => 'Karza\KarzaController@checkPanVerification'
        ]
    );
   
    //////////////////Pan card status//////////////////////////
    Route::post(
        'chk-user-pan-status-karza',
        [
        'as' => 'chk_user_pan_status_karza',
        'uses' => 'Karza\KarzaController@checkPanStatusVerification'
        ]
    );
    //////Voter ID Verification
    
    Route::post(
        'chk-user-voterid-karza',
        [
        'as' => 'chk_user_voterid_karza',
        'uses' => 'Karza\KarzaController@checkVoterIdVerification'
        ]
    );


    Route::post(
        'chk_user_cibil',
        [
        'as' => 'chk_user_cibil',
        'uses' => 'Cibil\CibilController@getPromoterCibilRequest'
        ]
    );


    Route::post(
        'chk_commerical_cibil',
        [
        'as' => 'chk_commerical_cibil',
        'uses' => 'Cibil\CibilController@getCommercialCibilRequest'
        ]
    );






    Route::post('bank_analysys', [
        'as' => 'getAnalysis',
        'uses' => 'Backend\CamController@analyse_bank'
    ]);

    Route::post('finance_analysis', [
        'as' => 'financeAnalysis',
        'uses' => 'Backend\CamController@analyse_finance'
    ]);


    Route::post('gst_analysys', [
        'as' => 'gstAnalysis',
        'uses' => 'Application\ApplicationController@analyse_gst'
    ]);

    Route::post('verify_mobile', [
        'as' => 'verify_mobile',
        'uses' => 'Backend\ApplicationController@verify_mobile'
    ]);
    
    Route::post('verify_front_mobile', [
        'as' => 'verify_front_mobile',
        'uses' => 'Application\ApplicationController@verify_mobile'
    ]);

    Route::post('download_user_cibil',[
        'as' => 'download_user_cibil',
        'uses' => 'Cibil\CibilController@downloadPromoterCibil'
        ]
    );

    Route::post('process_finance', [
        'as' => 'process_financial_statement',
        'uses' => 'Backend\CamController@getFinanceReport'
    ]);

    Route::get('process_banking', [
        'as' => 'process_banking_statement',
        'uses' => 'Backend\CamController@getBankReport'
    ]);



    Route::post(
        'download_commerial_cibil',
        [
        'as' => 'download_commerial_cibil',
        'uses' => 'Cibil\CibilController@downloadCommercialCibil'
        ]
    );

     //////Voter DL Verification

    Route::post(
        'chk-user-dl-karza',
        [
        'as' => 'chk_user_dl_karza',
        'uses' => 'Karza\KarzaController@checkDlVerification'
        ]
    );
      //////Voter passport Verification
    
    Route::post(
        'chk-user-passport-karza',
        [
        'as' => 'chk_user_passport_karza',
        'uses' => 'Karza\KarzaController@checkPassportVerification'
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
    Route::post(
        'get-anchor-lead-list',
        [
        'as' => 'get_anch_lead_list',
        'uses' => 'AjaxController@getAnchorLeadLists'
        ]
    ); 
     
    /*Route::get(
        'check-exist-user',
        [
        'as' => 'check_exist_user',
        'uses' => 'AjaxController@checkExistUser'
        ]
    );*/

    Route::post(
        'get-user-application',
        [
        'as' => 'ajax_user_app_list',
        'uses' => 'AjaxController@getUserApplications'
        ]
    );
    
    Route::post(
        'get-role-list',
        [
        'as' => 'get_role_list',
        'uses' => 'AjaxController@getRoleLists'
        ]
    );
    
    Route::post(
        'get_user_role',
        [
        'as' => 'get_user_role_list',
        'uses' => 'AjaxController@getUserRoleLists'
        ]
    );

    Route::post(
        'get_fi_list',
        [
        'as' => 'get_fi_list',
        'uses' => 'AjaxController@getFiLists'
        ]
    );


});