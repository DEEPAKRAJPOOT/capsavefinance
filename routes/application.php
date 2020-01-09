<?php

/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.frontend_uri'))->group(function () {


    Route::any('api/perfios/fsa-callback',[
        'as' => 'api_perfios_fsa_callback',
        'uses' => 'Auth\ApiController@fsa_callback'
        ]
    );

    Route::any('api/perfios/bsa-callback',[
        'as' => 'api_perfios_bsa_callback',
        'uses' => 'Auth\ApiController@bsa_callback'
        ]
    );

    Route::any('api/karza/webhook',[
        'as' => 'api_karza_webhook',
        'uses' => 'Auth\ApiController@karza_webhook'
        ]
    );



     Route::group(
        ['prefix' => 'dashboard'],
        function () {
        Route::group(
            ['middleware' => ['auth']],   //,'CheckWorkflow'
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
            
            Route::post('front_promoter_detail_update',
                [
                    'as' => 'front_promoter_detail_update',
                    'uses' => 'Application\ApplicationController@updatePromoterDetail'
            ]); 

            Route::get('verify_mobile_front', [
                'as' => 'verify_mobile_front',
                'uses' => 'Application\ApplicationController@mobileModel'
            ]);
            
            Route::post('front-promoter-save',
                [
                    'as' => 'front_promoter_save',
                    'uses' => 'Application\ApplicationController@savePromoter'
            ]);
            
            
//////////////// For Promoter Iframe////////////////////////
            Route::get('front_show-pan-verify-data', [
                'as' => 'front_show_pan_verify_data',
                'uses' => 'Application\ApplicationController@showPanVerifyResponseData'
            ]);

            Route::get('front_show-pan-data', [
                'as' => 'front_show_pan_data',
                'uses' => 'Application\ApplicationController@showPanResponseData'
            ]);

            Route::get('front_show-dl-data', [
                'as' => 'front_show_dl_data',
                'uses' => 'Application\ApplicationController@showDlResponseData'
            ]);
            Route::get('front_show-voter-data', [
                'as' => 'front_show_voter_data',
                'uses' => 'Application\ApplicationController@showVoterResponseData'
            ]);
            Route::get('front_show-pass-data', [
                'as' => 'front_show_pass_data',
                'uses' => 'Application\ApplicationController@showPassResponseData'
            ]);
             Route::get('front_verify_mobile', [
                'as' => 'front_verify_mobile',
                'uses' => 'Application\ApplicationController@mobileModel'
            ]);
              Route::get('front_mobile_otp_view', [
                'as' => 'front_mobile_otp_view',
                'uses' => 'Application\ApplicationController@mobileOtpModel'
            ]);
            
               Route::post('front_promoter_document_save', [
                'as' => 'front_promoter_document_save',
                'uses' => 'Application\ApplicationController@promoterDocumentSave'
            ]); 
            
            Route::get('document',
                [
                'as' => 'document',
                'uses' => 'Application\ApplicationController@showDocument'
            ]);


            Route::get('documents/upload-document', [
                'as' => 'front_show_upload_document',
                'uses' => 'Application\ApplicationController@uploadDocument'
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

            Route::get('/', [
                'as' => 'front_application_list',
                'uses' => 'Application\ApplicationController@index'
            ]);

            Route::get('/gstin', [
                'as' => 'front_gstin',
                'uses' => 'Application\ApplicationController@gstinForm'
            ]);
        });
    });
    
    
     ///////////////////////// Route for invoice controller///////////////////////
           Route::group(['prefix' => 'invoice'], function () {
               Route::get('front_upload_invoice', [
                 'as' => 'front_upload_invoice',
                'uses' => 'Application\InvoiceController@getInvoice'
            ]); 
            Route::get('front_upload_all_invoice', [
                 'as' => 'front_upload_all_invoice',
                'uses' => 'Application\InvoiceController@getAllInvoice'
            ]); 
            
          Route::get('get_invoice', [
                 'as' => 'get_invoice',
                'uses' => 'Application\InvoiceController@viewInvoice'
            ]); 
          
           Route::get('get_approve_invoice', [
                 'as' => 'get_approve_invoice',
                'uses' => 'Application\InvoiceController@viewApproveInvoice'
            ]); 
           
            Route::get('get_disbursed_invoice', [
                 'as' => 'get_disbursed_invoice',
                'uses' => 'Application\InvoiceController@viewDisbursedInvoice'
            ]); 
            
             Route::get('get_repaid_invoice', [
                 'as' => 'get_repaid_invoice',
                'uses' => 'Application\InvoiceController@viewRepaidInvoice'
            ]); 
     
           Route::POST('front_save_invoice', [
                 'as' => 'front_save_invoice',
                'uses' => 'Application\InvoiceController@saveInvoice'
            ]); 
           
             Route::get('frontend_bulk_invoice', [
                 'as' => 'frontend_bulk_invoice',
                'uses' => 'Application\InvoiceController@getBulkInvoice'
            ]); 
             Route::POST('frontend_save_bulk_invoice', [
                 'as' => 'frontend_save_bulk_invoice',
                'uses' => 'Application\InvoiceController@saveBulkInvoice'
            ]); 
             
          });
          
     
});    