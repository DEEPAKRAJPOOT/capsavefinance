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
    
    //////////////////Pan card authontication//////////////////////////
    Route::post(
        'chk_user_pan_karza_add_more',
        [
        'as' => 'chk_user_pan_karza_add_more',
        'uses' => 'Karza\KarzaController@checkPanVerificationAddMore'
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

    Route::post('send_gst_otp', [
        'as' => 'send_gst_otp',
        'uses' => 'Application\ApplicationController@send_gst_otp'
    ]);
    
    Route::post('verify_gst_otp', [
        'as' => 'verify_gst_otp',
        'uses' => 'Application\ApplicationController@verify_gst_otp'
    ]);
  
    Route::post('sent_otp_mobile', [
        'as' => 'sent_otp_mobile',
        'uses' => 'Backend\ApplicationController@sentOtpmobile'
    ]);
  
    Route::post('verify_mobile', [
        'as' => 'verify_mobile',
        'uses' => 'Backend\ApplicationController@verify_mobile'
    ]);
    
    
    
    Route::post('verify_front_mobile', [
        'as' => 'verify_front_mobile',
        'uses' => 'Application\ApplicationController@verify_mobile'
    ]);
    
     Route::post('verify-otp-mobile', [
        'as' => 'verify_otp_mobile',
        'uses' => 'Backend\ApplicationController@verify_otp_mobile'
    ]);
    
     Route::post('verify-front-otp', [
        'as' => 'verify_front_otp',
        'uses' => 'Application\ApplicationController@verify_otp'
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
         'get-app-fircu',
         [
         'as' => 'ajax_fircu_app_list',
         'uses' => 'AjaxController@getFiRcuAppList'
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

    Route::post(
        'chk_biz_pan_to_gst',
        [
        'as' => 'chk_biz_pan_to_gst',
        'uses' => 'Karza\KarzaController@checkBizPanToGst'
        ]
    );

    Route::post(
        'chk_biz_gst_to_entity',
        [
        'as' => 'chk_biz_gst_to_entity',
        'uses' => 'Karza\KarzaController@checkBizGstToEntity'
        ]
    );

    Route::post(
        'chk_biz_entity_to_cin',
        [
        'as' => 'chk_biz_entity_to_cin',
        'uses' => 'Karza\KarzaController@checkBizEntityToCin'
        ]
    );

    Route::post(
        'change_agent_fi_status',
        [
        'as' => 'change_agent_fi_status',
        'uses' => 'AjaxController@changeAgentFiStatus'
        ]
    );

    Route::post(
        'change_cm_fi_status',
        [
        'as' => 'change_cm_fi_status',
        'uses' => 'AjaxController@changeCmFiStatus'
        ]
    );

    Route::post(
        'change_agent_rcu_status',
        [
        'as' => 'change_agent_rcu_status',
        'uses' => 'AjaxController@changeAgentRcuStatus'
        ]
    );

    Route::post(
        'change_cm_rcu_status',
        [
        'as' => 'change_cm_rcu_status',
        'uses' => 'AjaxController@changeCmRcuStatus'
        ]
    );

});