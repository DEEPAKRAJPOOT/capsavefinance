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
     //////////////////Get Promoter By Cin//////////////////////////
    Route::post(
        'get_promoter_details_by_cin',
        [
        'as' => 'get_promoter_details_by_cin',
        'uses' => 'Karza\KarzaController@getPromoterDetailsByCin'
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

    Route::post('getExcelSheet', [
        'as' => 'getExcelSheet',
        'uses' => 'Backend\CamController@getExcelSheet'
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
    
      Route::post('get_program_supplier', [
        'as' => 'get_program_supplier',
        'uses' => 'Backend\InvoiceController@getProgramSupplier'
    ]);
     Route::post('get_user_program_supplier', [
        'as' => 'get_user_program_supplier',
        'uses' => 'AjaxController@getUserProgramSupplier'
    ]);
      Route::post('get_biz_anchor', [
        'as' => 'get_biz_anchor',
        'uses' => 'AjaxController@getBizAnchor'
    ]);
   
      Route::post('get_user_biz_anchor', [
        'as' => 'get_user_biz_anchor',
        'uses' => 'AjaxController@getUserBizAnchor'
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

    Route::post('process_banking', [
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

    Route::post(
        'save_repayment',
        [
        'as' => 'save_repayment',
        'uses' => 'AjaxController@saveRepayment'
        ]
    );

    Route::post('get_to_settle_payments', [
        'as' => 'get_to_settle_payments',
        'uses' => 'AjaxController@getToSettlePayments'
        ]
    );

    Route::post('get_settled_payments', [
        'as' => 'get_settled_payments',
        'uses' => 'AjaxController@getSettledPayments'
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
        'get-assigned-user-lead',
        [
        'as' => 'get_assigned_user_lead',
        'uses' => 'AjaxController@getUserLead'
        ]
    );
    Route::post(
        'set-users-leads',
        [
          'as' => 'set_users_leads',
          'uses' => 'AjaxController@setUsersLeads'  
        ]
    ); 

    Route::post('set-user-application',
    [
        'as'=>'set_user_application',
        'uses'=> 'AjaxController@setUsersApplication'
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
        'get-assigned-application',
        [
        'as' => 'ajax_assigned_app_list',
        'uses' => 'AjaxController@getAssignedApplications'
        ]
    );

    Route::post(
        'get-backendusers',
        [
        'as' => 'get_backend_users',
        'uses' => 'AjaxController@getBackendUsers'
        ]
    );
    Route::post(
        'get-backendappusers',
        [
        'as' => 'get_backendapp_users',
        'uses' => 'AjaxController@getBackendUsers'
        ]
    );
    Route::post(
        'get-users-leads',
        [
            'as'=>'get_users_leads',
            'uses'=>'AjaxController@getUsersLeads'
        ]
    );
    
    Route::post(
        'get-usersapp-leads',
        [
            'as'=>'get_usersapp_leads',
            'uses'=>'AjaxController@getUsersLeads'
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
        'get_invoice_list',
        [
        'as' => 'get_invoice_list',
        'uses' => 'AjaxController@getInvoiceList'
        ]
    );
    
      Route::post(
        'backend_get_invoice_list',
        [
        'as' => 'backend_get_invoice_list',
        'uses' => 'AjaxController@getBackendInvoiceList'
        ]
    );
        Route::post(
        'frontend_get_invoice_list',
        [
        'as' => 'frontend_get_invoice_list',
        'uses' => 'AjaxController@getFrontendInvoiceList'
        ]
    );
       Route::post(
        'user_wise_invoice_list',
        [
        'as' => 'user_wise_invoice_list',
        'uses' => 'AjaxController@getUserWiseInvoiceList'
        ]
    );
        Route::post(
        'backend_get_invoice_list_approve',
        [
        'as' => 'backend_get_invoice_list_approve',
        'uses' => 'AjaxController@getBackendInvoiceListApprove'
        ]
    );
        
       Route::post(
        'backend_get_ep_list_approve',
        [
        'as' => 'backend_get_ep_list_approve',
        'uses' => 'AjaxController@getBackendEpList'
        ]
    );   
       Route::post(
        'backend_get_invoice_list_disbursed_que',
        [
        'as' => 'backend_get_invoice_list_disbursed_que',
        'uses' => 'AjaxController@getBackendInvoiceListDisbursedQue'
        ]
    );  
       
         
       Route::post(
        'backend_get_invoice_list_disbursed',
        [
        'as' => 'backend_get_invoice_list_disbursed',
        'uses' => 'AjaxController@getBackendInvoiceListDisbursed'
        ]
    );  
       
        Route::post('backend_get_bulk_transaction',[
            'as' => 'backend_get_bulk_transaction',
            'uses' => 'AjaxController@getBackendBulkTransaction'
        ]);  
       
         Route::post(
        'backend_get_invoice_list_repaid',
        [
        'as' => 'backend_get_invoice_list_repaid',
        'uses' => 'AjaxController@getBackendInvoiceListRepaid'
        ]
    );   
          Route::post('save_excel_payment', [
                 'as' => 'save_excel_payment',
                'uses' => 'AjaxController@saveExcelPayment'
            ]);  
          
          
           Route::post(
        'backend_get_invoice_list_reject',
        [
        'as' => 'backend_get_invoice_list_reject',
        'uses' => 'AjaxController@getBackendInvoiceListReject'
        ]
    );  
           
           Route::post(
        'get_customer_id',
        [
        'as' => 'get_customer_id',
        'uses' => 'AjaxController@getCustomerId'
        ]
    );       
          Route::post(
        'backend_activity_invoice_list',
        [
        'as' => 'backend_activity_invoice_list',
        'uses' => 'AjaxController@getBackendInvoiceActivityList'
        ]
    );   
             
             
       Route::post(
        'backend_get_invoice_list_bank',
        [
        'as' => 'backend_get_invoice_list_bank',
        'uses' => 'AjaxController@getBackendInvoiceListBank'
        ]
    );  
        Route::post(
        'frontend_get_invoice_list_bank',
        [
        'as' => 'frontend_get_invoice_list_bank',
        'uses' => 'AjaxController@getFrontendInvoiceListBank'
        ]
    );  
    Route::post(
        'get-ajax-bank-invoice',
        [
        'as' => 'get_ajax_bank_invoice',
        'uses' => 'AjaxController@getAjaxBankInvoice'
        ]
    );  
        
    Route::post(
        'get-ajax-bank-invoice-customers',
        [
        'as' => 'get_ajax_bank_invoice_customers',
        'uses' => 'AjaxController@getAjaxBankInvoiceCustomers'
        ]
    );  

    Route::post(
        'get-ajax-view-disburse-invoice',
        [
        'as' => 'get_ajax_view_disburse_invoice',
        'uses' => 'AjaxController@getAjaxViewDisburseInvoice'
        ]
    );  

       Route::post(
        'backend_get_invoice_list_failed_disbursed',
        [
        'as' => 'backend_get_invoice_list_failed_disbursed',
        'uses' => 'AjaxController@getBackendInvoiceListFailedDisbursed'
        ]
    );  
       
       
     Route::post(
        'invoice_document_save',
        [
        'as' => 'invoice_document_save',
        'uses' => 'AjaxController@saveInvoiceDoc'
        ]
    ); 
      Route::post(
        'update_invoice_approve_single_tab',
        [
        'as' => 'update_invoice_approve_single_tab',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    );   
      Route::post(
        'update_invoice_approve',
        [
        'as' => 'update_invoice_approve',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    );
       Route::post(
        'update_icon_invoice_approve',
        [
        'as' => 'update_icon_invoice_approve',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    );   
       Route::post(
        'update_invoice_approve_tab',
        [
        'as' => 'update_invoice_approve_tab',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    ); 
       
     Route::post(
        'update_invoice_disb_que_tab',
        [
        'as' => 'update_invoice_disb_que_tab',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    );   
      Route::post(
        'update_invoice_failed_disb_tab',
        [
        'as' => 'update_invoice_failed_disb_tab',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    );    
      Route::post(
        'update_invoice_reject_tab',
        [
        'as' => 'update_invoice_reject_tab',
        'uses' => 'AjaxController@updateInvoiceApprove'
        ]
    );     
      
     Route::post(
        'update_invoice_exception_tab',
        [
        'as' => 'update_invoice_exception_tab',
        'uses' => 'AjaxController@updateInvoiceApprove'
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
        'get-sub-industry',
        [
        'as' => 'get_sub_industry',
        'uses' => 'AjaxController@getSubIndustry'
        ]
    );
      
      Route::post(
        'get-program-list',
        [
        'as' => 'get_program_list',
        'uses' => 'AjaxController@getProgramList'
        ]
    );
      
      Route::post(
        'get-sub-program-list',
        [
        'as' => 'get_sub_program_list',
        'uses' => 'AjaxController@getSubProgramList'
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
    /*agency route*/
    Route::post(
        'get-agency-list',
        [
        'as' => 'get_ajax_agency_list',
        'uses' => 'AjaxController@getAgencyLists'
        ]
    );
    /*agency route*/
    Route::post(
        'get-charges-list',
        [
        'as' => 'get_ajax_charges_list',
        'uses' => 'AjaxController@getChargeLists'
        ]
    );
/*charges  route*/
    Route::post(
        'get-lms-charges-list',
        [
        'as' => 'get_lms_charges_list',
        'uses' => 'AjaxController@getLmsChargeLists'
        ]
    );
    
     /*Master Document route*/
    Route::post(
        'get-master-document-list',
        [
        'as' => 'get_ajax_master_document_list',
        'uses' => 'AjaxController@getDocLists'
        ]
    );

    /*Entities route*/
    Route::post(
        'get-entities-list',
        [
        'as' => 'get_ajax_entity_list',
        'uses' => 'AjaxController@getEntityLists'
        ]
    );
    
     /*Master Document route*/
    Route::post(
        'get-master-industries-list',
        [
        'as' => 'get_ajax_master_industry_list',
        'uses' => 'AjaxController@getIndustryLists'
        ]
    );

    /* GST Route */
    Route::post(
        'get-ajax-ajax-list',
        [
        'as' => 'get_ajax_gst_list',
        'uses' => 'AjaxController@getGstLists'
        ]
    );

    /* Segment Route */
    Route::post(
        'get-ajax-segment-list',
        [
        'as' => 'get_ajax_segment_list',
        'uses' => 'AjaxController@getSegmentLists'
        ]
    );

    /* Constitution Route */
    Route::post(
        'get-ajax-constitution-list',
        [
        'as' => 'get_ajax_constitution_list',
        'uses' => 'AjaxController@getConstitutionLists'
        ]
    );

    /* Equipment Route */
    Route::post(
        'get-ajax-equipment-list',
        [
        'as' => 'get_ajax_equipment_list',
        'uses' => 'AjaxController@getEquipmentLists'
        ]
    );

    Route::post(
        'get-agency-user-list',
        [
        'as' => 'get_ajax_agency_user_list',
        'uses' => 'AjaxController@getAgencyUserLists'
        ]
    );
    /*agency route*/
    
     Route::post(
        'get-charges-html',
        [
        'as' => 'get_charges_html',
        'uses' => 'AjaxController@getCharagesHtml'
        ]
    );
    
    Route::post(
        'get-backend-user-list',
        [
        'as' => 'ajax_get_backend_user_list',
        'uses' => 'AjaxController@getBackendUserList'
        ]
    );
    
    Route::post(
        'ajax-doa-levels-list', [
            'as' => 'ajax_doa_levels_list',
            'uses' => 'AjaxController@getDoaLevelsList'
        ]
    );
        
    Route::post(
        'ajax-get-city', [
            'as' => 'ajax_get_city',
            'uses' => 'AjaxController@getCityList'
        ]
    );

    Route::post(
        'get-anchors-by-product',
        [
        'as' => 'ajax_get_anchors_by_product',
        'uses' => 'AjaxController@getAnchorsByProduct'
        ]
    );

    Route::post(
        'get-programs-by-anchor',
        [
        'as' => 'ajax_get_programs_by_anchor',
        'uses' => 'AjaxController@getProgramsByAnchor'
        ]
    );

    Route::post(
        'get-program-balance-limit',
        [
        'as' => 'ajax_get_program_balance_limit',
        'uses' => 'AjaxController@getProgramBalanceLimit'
        ]
    );    

    Route::post(
        'change-program-status',
        [
        'as' => 'change_program_status',
        'uses' => 'AjaxController@changeProgramStatus'
        ]
    ); 

    Route::post(
        'change-doa-status',
        [
        'as' => 'change_doa_status',
        'uses' => 'AjaxController@changeDoaStatus'
        ]
    ); 

    /*lms route*/
    
    Route::post('lms-get-customer', [
        'as' => 'lms_get_customer',
        'uses' => 'AjaxController@lmsGetCustomer'
    ]);
     
    Route::get('get-customer',[
        'as' => 'get_customer',
        'uses' => 'AjaxController@getCustomer'
    ]);
      Route::post('search_business',[
        'as' => 'search_business',
        'uses' => 'AjaxController@searchBusiness'
    ]);
   Route::post('lms-get-disbursal-customer', [
        'as' => 'lms_get_disbursal_customer',
        'uses' => 'AjaxController@lmsGetDisbursalCustomer'
    ]);
    
     
    Route::post('lms-get-soa-list', [
        'as' => 'lms_get_soa_list',
        'uses' => 'AjaxController@lmsGetSoaList'
    ]);

    Route::post('lms-get-consolidated-soa-list', [
        'as' => 'lms_get_consolidated_soa_list',
        'uses' => 'AjaxController@lmsGetConsolidatedSoaList'
    ]);
    
 Route::post('lms_get_due_list', [
        'as' => 'lms_get_due_list',
        'uses' => 'AjaxController@getInvoiceDueList'
    ]);
  Route::post('lms_get_invoice_over_due_list', [
        'as' => 'lms_get_invoice_over_due_list',
        'uses' => 'AjaxController@getInvoiceOverDueList'
    ]);
   Route::post('lms_get_invoice_realisation_list', [
        'as' => 'lms_get_invoice_realisation_list',
        'uses' => 'AjaxController@getInvoiceRealisationList'
    ]);
    Route::post('get-colender-soa-list', [
        'as' => 'get_colender_soa_list',
        'uses' => 'AjaxController@getColenderSoaList'
    ]);
    
    Route::post('get-bank-account-list', [
        'as' => 'get_bank_account_list',
        'uses' => 'AjaxController@getBankAccountList'
    ]);
    
      Route::post('update_bulk_invoice', [
        'as' => 'update_bulk_invoice',
        'uses' => 'AjaxController@updateBulkInvoice'
    ]);
     Route::post('update_disburse_bulk_invoice', [
        'as' => 'update_disburse_bulk_invoice',
        'uses' => 'AjaxController@updateBulkInvoice'
    ]); 
    Route::post('set-default-account', [
        'as' => 'set_default_account',
        'uses' => 'AjaxController@setDefaultAccount'
    ]);
    
    
    
    
    Route::post('lms-get-disbursal-list', [
        'as' => 'lms_get_disbursal_list',
        'uses' => 'AjaxController@getDisbursalList'
    ]);
    
    
    // lms address
    Route::post('get-ajax-address-list', [
        'as' => 'get_ajax_address_list',
        'uses' => 'AjaxController@addressGetCustomer'
    ]);

    Route::post('set-default-address', [
        'as' => 'set_default_address',
        'uses' => 'AjaxController@setDefaultAddress'
    ]);
    Route::get('get-field-val', [
        'as' => 'get_field_val',
        'uses' => 'AjaxController@getTableValByField'
    ]);

    Route::post('lms-get-refund-customer', [
        'as' => 'lms_get_refund_customer',
        'uses' => 'AjaxController@lmsGetRefundList'
    ]);

    Route::post('lms-create-batch-ajax',[
        'as' => 'lms_create_batch_ajax',
        'uses' => 'AjaxController@lmsCreateBatch'
    ]);
    
    Route::post('lms-edit-batch-ajax',[
        'as' => 'lms_edit_batch_ajax',
        'uses' => 'AjaxController@lmsEditBatch'
    ]);
    /*lms route*/


    
    
    //////////////// ajax request for upload invoice///////////////////////
    Route::POST('front_program_list', [
        'as' => 'front_program_list',
        'uses' => 'AjaxController@getProgramSingleList'
    ]); 
      Route::POST('get_tenor', [
        'as' => 'get_tenor',
        'uses' => 'AjaxController@getTenor'
    ]); 
      Route::POST('get_adhoc', [
        'as' => 'get_adhoc',
        'uses' => 'AjaxController@getAdhoc'
    ]);  
     Route::POST('front_lms_program_list', [
        'as' => 'front_lms_program_list',
        'uses' => 'AjaxController@getProgramLmsSingleList'
    ]);
    Route::POST('front_supplier_list', [
        'as' => 'front_supplier_list',
        'uses' => 'AjaxController@getSupplierList'
    ]); 
    Route::POST('check_duplicate_invoice', [
        'as' => 'check_duplicate_invoice',
        'uses' => 'AjaxController@checkDuplicateInvoice'
    ]);    
  
    Route::POST('upload_invoice_csv', [
        'as' => 'upload_invoice_csv',
        'uses' => 'AjaxController@uploadInvoice'
    ]); 
    Route::POST('delete_temp_invoice', [
        'as' => 'delete_temp_invoice',
        'uses' => 'AjaxController@DeleteTempInvoice'
    ]);    
    Route::POST('get-ueser-by-role', [
        'as' => 'get_ueser_by_role',
        'uses' => 'AjaxController@getUserByRole'
    ]); 
    Route::POST('get-co-lender-list', [
        'as' => 'get_co_lender_list',
        'uses' => 'AjaxController@getColenderList'
    ]); 
    Route::get('get-group-company', [
        'as' => 'get_group_company',
        'uses' => 'AjaxController@getGroupCompany'
    ]); 

    Route::post('get-app-colender',[
         'as' => 'ajax_colender_app_list',
         'uses' => 'AjaxController@getColenderAppList'
         ]
    );  
          
   /////////// get transa name//////////
    
    Route::post('get_trans_name', [
        'as' => 'get_trans_name',
        'uses' => 'AjaxController@getTransName'
    ]);   
          
          
    //Financial 
    Route::post(
        'get-trans-type-list',
        [
        'as' => 'get_ajax_trans_type_list',
        'uses' => 'AjaxController@getTransTypeList'
        ]
    );    
    Route::post(
        'get-journal-list',
        [
        'as' => 'get_ajax_journal_list',
        'uses' => 'AjaxController@getJournalList'
        ]
    );
    Route::post(
        'get-account-list',
        [
        'as' => 'get_ajax_account_list',
        'uses' => 'AjaxController@getAccountList'
        ]
    );
    Route::post(
        'get-variable-list',
        [
        'as' => 'get_ajax_variable_list',
        'uses' => 'AjaxController@getVariableList'
        ]
    );
    Route::post(
        'get-jeconfig-list',
        [
        'as' => 'get_ajax_jeconfig_list',
        'uses' => 'AjaxController@getJeConfigList'
        ]
    );
    Route::post(
        'get-jiconfig-list',
        [
        'as' => 'get_ajax_jiconfig_list',
        'uses' => 'AjaxController@getJiConfigList'
        ]
    );
    Route::post(
        'get-transactions',
        [
        'as' => 'get_ajax_transactions',
        'uses' => 'AjaxController@getTransactions'
        ]
    );   
    Route::post('get-batches', [
        'as' => 'get_ajax_batches',
        'uses' => 'AjaxController@getBatches'
        ]
    );   

    Route::post('get-group-company-exposure', [
        'as' => 'get_group_company_exposure',
        'uses' => 'AjaxController@getGroupCompanyExposure'
    ]);      

    Route::post('update-group-company-exposure', [
        'as' => 'update_group_company_exposure',
        'uses' => 'AjaxController@updateGroupCompanyExposure'
    ]); 
    
    /*Master Base Rate route*/
    Route::post(
        'get-master-base-rate-list',
        [
        'as' => 'get_ajax_master_base_rate_list',
        'uses' => 'AjaxController@getAllBaseRateList'
        ]
    );

     Route::post('get_chrg_amount', [
        'as' => 'get_chrg_amount',
        'uses' => 'AjaxController@getChrgAmount'
    ]);
     Route::post('backend_get_payment_advice', [
        'as' => 'backend_get_payment_advice',
        'uses' => 'AjaxController@getPaymentAdvice'
    ]);
    
    Route::post('get_calculation_amount', [
        'as' => 'get_calculation_amount',
        'uses' => 'AjaxController@getCalculationAmount'
    ]);


    Route::post('lms-get-request-list',[
        'as' => 'lms_get_request_list',
        'uses' => 'AjaxController@lmsGetRequestList'
    ]);

    Route::post('lms_get_invoices', [
        'as' => 'lms_get_invoices',
        'uses' => 'AjaxController@lmsGetInvoiceByUser'
    ]); 
      
    Route::post('get-repayment-amount', [
        'as' => 'get_repayment_amount',
        'uses' => 'AjaxController@getRepaymentAmount'
    ]); 

    Route::post('get_remaining_charges', [
        'as' => 'get_remaining_charges',
        'uses' => 'AjaxController@getRemainingCharges'
    ]);  

    Route::post('get_interest_paid_amount', [
        'as' => 'get_interest_paid_amount',
        'uses' => 'AjaxController@getInterestPaidAmount'
    ]);  

    Route::post('check-unique-charge', [
        'as' => 'check_unique_charge',
        'uses' => 'AjaxController@checkUniqueCharge'
    ]);     

    //ajax route for check the email is exist or not
    Route::post('check-exist-email', [
        'as' => 'check_exist_email',
        'uses' => 'AjaxController@getExistEmailStatus'
    ]);        

    //ajax route for check the email is exist or not
    Route::post('check-exist-emails-anchor', [
        'as' => 'check_exist_email_anchor',
        'uses' => 'AjaxController@getExistEmailStatusAnchor'
    ]);

    Route::post('check_exist_user_email_anchor',[
        'as' => 'check_exist_user_email_anchor',
        'uses' => 'AjaxController@getExistUserEmailStatusAnchor'
    ]);

    Route::post('get-soa-client-details',[
        'as' => 'get_soa_client_details',
        'uses' => 'AjaxController@getSoaClientDetails'
    ]);

    Route::post('get_all_unsettled_trans_type',[
        'as' => 'get_all_unsettled_trans_type',
        'uses' => 'AjaxController@getAllUnsettledTransType'
    ]);

    Route::post('get-voucher-list', [
        'as' => 'get_ajax_voucher_list',
        'uses' => 'AjaxController@getVoucherLists'
    ]);
     
    Route::post('check-applied-charge', [
        'as' => 'check_applied_charge',
        'uses' => 'AjaxController@checkAppliedCharge'
    ]);       
    
    Route::post('check-eod-batch-process', [
        'as' => 'check_eod_batch_process',
        'uses' => 'AjaxController@checkEodProcess'
    ]); 
    
    Route::post('update-eod-batch-process', [
        'as' => 'update_eod_batch_process',
        'uses' => 'AjaxController@updateEodProcessStatus'
    ]);    

    Route::post('get_eod_list', [
        'as' => 'get_eod_list',
        'uses' => 'AjaxController@getEodList'
    ]);
    
    Route::post('get_eod_process_list', [
        'as' => 'get_eod_process_list',
        'uses' => 'AjaxController@getEodProcessList'
    ]);
    
    Route::post('start_system',[
        'as' => 'start_eod_system',
        'uses' => 'AjaxController@startEodSystem',
    ]);

    Route::post('check-bank-acc-exist',[
        'as' => 'check_bank_acc_exist',
        'uses' => 'AjaxController@checkBankAccExist'
    ]);
    
    Route::post('check-comp-add-exist',[
        'as' => 'check_comp_add_exist',
        'uses' => 'AjaxController@checkCompAddExist'
    ]);

    Route::post('get-user-invoice-list', [
        'as' => 'get_user_invoice_list',
        'uses' => 'AjaxController@getUserInvoiceList'
    ]);   

    Route::post('get_address_by_gst',[
        'as' => 'get_address_by_gst',
        'uses' => 'Karza\KarzaController@getAddressByGst'
        ]
    );

    Route::post('get_cust_and_cap_loca',[
        'as' => 'get_cust_and_cap_loca',
        'uses' => 'AjaxController@getCustAndCapsLoc'
        ]
    );

    Route::post('get_all_customers',[
        'as' => 'get_all_customers',
        'uses' => 'AjaxController@getAllCustomers'
        ]
    );    
    Route::post('get_all_lease_registers',[
        'as' => 'get_all_lease_registers',
        'uses' => 'AjaxController@leaseRegister'
        ]
    );
    Route::post('get_all_interest_breakups',[
        'as' => 'get_all_interest_breakups',
        'uses' => 'AjaxController@interestBreakup'
        ]
    );
    Route::post('get_all_charge_breakups',[
        'as' => 'get_all_charge_breakups',
        'uses' => 'AjaxController@chargeBreakup'
        ]
    );
    Route::post('get_all_tds_breakups',[
        'as' => 'get_all_tds_breakups',
        'uses' => 'AjaxController@tdsBreakup'
        ]
    );
    Route::post('backend_ajax_get_disbursal_batch_request',[
        'as' => 'backend_ajax_get_disbursal_batch_request',
        'uses' => 'AjaxController@getBackendDisbursalBatchRequest'
        ]
    );

    Route::get('get_unsettled_payments',[
        'as' => 'get_unsettled_payments',
        'uses' => 'AjaxController@unsettledPayments'
        ]
    );

    Route::post('check-exist-anchor-lead',[
        'as' => 'check_exist_anchor_lead',
        'uses' => 'AjaxController@checkExistAnchorLead'
        ]
    );  

    Route::post('check-bank-acc-ifsc-exist',[
        'as' => 'check_bank_acc_ifsc_exist',
        'uses' => 'AjaxController@checkBankAccWithIfscExist'
    ]);

    Route::post('/get_cibil_report_lms', [
        'as' => 'get_cibil_report_lms',
        'uses' => 'AjaxController@getCibilReportLms',
    ]);
    
    Route::post('get_all_tds',[
        'as' => 'get_all_tds',
        'uses' => 'AjaxController@tds'
        ]
    );

    Route::post('/change_agency_status', [
        'as' => 'change_agency_status',
        'uses' => 'AjaxController@changeAgencyStatus',
    ]);

    Route::post('/change_agency_user_status', [
        'as' => 'change_agency_user_status',
        'uses' => 'AjaxController@changeUsersAgencyStatus',
    ]);

    Route::post('/get_ajax_tds_list', [
        'as' => 'get_ajax_tds_list',
        'uses' => 'AjaxController@getTDSList',
    ]);

    Route::post('/get_ajax_limit_list', [
        'as' => 'get_ajax_limit_list',
        'uses' => 'AjaxController@getLimitList',
    ]);

    Route::post('backend_ajax_get_refund_batch_request',[
        'as' => 'backend_ajax_get_refund_batch_request',
        'uses' => 'AjaxController@getBackendRefundBatchRequest'
        ]
    );
    
    Route::post('get-all-nach',[
        'as' => 'get_all_nach',
        'uses' => 'AjaxController@getAllNach'
    ]);

    Route::post('front-ajax-user-nach-list',[
        'as' => 'front_ajax_user_nach_list',
        'uses' => 'AjaxController@frontAjaxUserNachList'
    ]);

    Route::post('front-ajax-user-nach-list',[
        'as' => 'front_ajax_user_nach_list',
        'uses' => 'AjaxController@frontAjaxUserNachList'
    ]);

    Route::post('front-ajax-user-soa-consolidated-list',[
        'as' => 'front_ajax_user_soa_consolidated_list',
        'uses' => 'AjaxController@frontAjaxUserSoaConsolidatedList'
    ]);

    Route::post('front-ajax-user-soa-list',[
        'as' => 'front_ajax_user_soa_list',
        'uses' => 'AjaxController@frontAjaxUserSoaList'
    ]);

    Route::post('anchor-ajax-user-nach-list',[
        'as' => 'anchor_ajax_user_nach_list',
        'uses' => 'AjaxController@anchorAjaxUserNachList'
    ]);

    Route::post('backend-ajax-user-nach-list',[
        'as' => 'backend_ajax_user_nach_list',
        'uses' => 'AjaxController@backendAjaxUserNachList'
    ]);

    Route::post('backend_ajax_nach_user',[
        'as' => 'backend_ajax_nach_user',
        'uses' => 'AjaxController@backendNachUserList'
        ]
    );

    Route::post('backend_ajax_nach_user_bank',[
        'as' => 'backend_ajax_nach_user_bank',
        'uses' => 'AjaxController@backendNachUserBankList'
        ]
    );

    Route::post('lms-get-nach-repayment-list',[
        'as' => 'lms_get_nach_repayment_list',
        'uses' => 'AjaxController@lmsGetNachRepaymentList'
    ]);
    Route::post('backend-ajax-nach-stb-list',[
        'as' => 'backend_ajax_nach_stb_list',
        'uses' => 'AjaxController@backendAjaxNachSTBList'
    ]);

    /*bank route*/
    Route::post('get-bank-list',[
        'as' => 'get_ajax_bank_list',
        'uses' => 'AjaxController@getAllBankList'
    ]);


    // Check frontend PAN and GST validation
    Route::match(['get', 'post'], '/check_anchor_pan_ajax', 'AjaxController@checkAnchorPanAjax');
    Route::match(['get', 'post'], '/check_anchor_gst_ajax', 'AjaxController@checkAnchorGstAjax');    
    
    Route::post('chk_anchor_phy_inv_req',[
        'as' => 'chk_anchor_phy_inv_req',
        'uses' => 'AjaxController@chkAnchorPhyInvReq'
    ]);

    Route::post('backend_get_invoice_processing_gst_amount',[
        'as' => 'backend_get_invoice_processing_gst_amount',
        'uses' => 'AjaxController@backendGetInvoiceProcessingGstAmount'
    ]);
    
    Route::post('unique-industry-url', [
        'as' => 'check_unique_industry_url',
        'uses' => 'AjaxController@checkUniqueIndustries'
    ]);
    
    Route::post('unique-industry-code', [
        'as' => 'check_unique_industry_code',
        'uses' => 'AjaxController@checkUniqueIndustriesCode'
    ]);
    
    Route::post('unique-voucher-url', [
        'as' => 'check_unique_voucher_url',
        'uses' => 'AjaxController@checkUniqueVoucher'
    ]);
    
    Route::post('unique-segment-url', [
        'as' => 'check_unique_segment_url',
        'uses' => 'AjaxController@checkUniqueSegment'
    ]);
    
    Route::post('unique-entity-url', [
        'as' => 'check_unique_entity_url',
        'uses' => 'AjaxController@checkUniqueEntity'
    ]);
    
    Route::post('unique-constitution-url', [
        'as' => 'check_unique_constitution_url',
        'uses' => 'AjaxController@checkUniqueConstitution'
    ]);
    
    Route::post('unique-constitution-code', [
        'as' => 'check_unique_constitution_code',
        'uses' => 'AjaxController@checkUniqueConstitutionCode'
    ]);
    
    Route::post('unique-equipment-url', [
        'as' => 'check_unique_equipment_url',
        'uses' => 'AjaxController@checkUniqueEquipment'
    ]);
    
    Route::post('unique-bank-master-url', [
        'as' => 'check_unique_bank_master_url',
        'uses' => 'AjaxController@checkUniqueBankMaster'
    ]);

    Route::post('get-tdsoutstanding-amount', [
        'as' => 'get_tdsoutstanding_amount',
        'uses' => 'AjaxController@getTDSOutstatingAmount'
    ]);
    
    Route::match(['get', 'post'], '/check_document_name_exist_ajax', 'AjaxController@checkDocumentNametAjax');
    Route::match(['get', 'post'], '/check_document_name_exist_edit_ajax', 'AjaxController@checkDocumentNameEdittAjax');
    Route::match(['get', 'post'], '/check_doa_name_exist_ajax', 'AjaxController@checkDOANametAjax');
    Route::match(['get', 'post'], '/check_doa_name_exist_edit_ajax', 'AjaxController@checkDOANametEditAjax');

    Route::post('unique-tds-certi-no', [
        'as' => 'check_unique_tds_certificate_no',
        'uses' => 'AjaxController@checkUniqueTdsCertificate'
    ]);

     /*Master Document route*/
    Route::post('get-master-locationtype-list', [
        'as' => 'get_ajax_master_locationtype_list',
        'uses' => 'AjaxController@getLocationTypeLists'
    ]); 
    
    Route::post('unique-location-url', [
        'as' => 'check_unique_location_url',
        'uses' => 'AjaxController@checkUniqueLocationType'
    ]);    
    
    Route::post('unique-location-code-url', [
        'as' => 'check_unique_location_code_url',
        'uses' => 'AjaxController@checkUniqueLocationCode'
    ]);    

    Route::post('lms_send_invoice_over_due', [
        'as' => 'lms_send_invoice_over_due',
        'uses' => 'AjaxController@sendInvoiceOverdueReportByMail'
    ]);

    Route::post('lms_req_for_chrg_deletion', [
        'as' => 'lms_req_for_chrg_deletion',
        'uses' => 'AjaxController@reqForChargeDeletion'
    ]);
    
    Route::post('lms_approve_chrg_deletion', [
        'as' => 'lms_approve_chrg_deletion',
        'uses' => 'AjaxController@approveChargeDeletion'
     ]);
    Route::post('delete_management_info', [
        'as' => 'delete_management_info',
        'uses' => 'AjaxController@deleteManagementInfo'
    ]);

    Route::post('get-non-anchor-leads', [
        'as' => 'get_non_anchor_leads',
        'uses' => 'AjaxController@getNonAnchorLeads'
    ]);

    Route::post('upload-approval-mail-copy', [
        'as' => 'upload_approval_mail_copy',
        'uses' => 'AjaxController@uploadApprovalMailCopy'
    ]);
});