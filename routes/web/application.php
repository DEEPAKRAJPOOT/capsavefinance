<?php
/**
 * This route have user dashboard and all application routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.frontend_uri'))->middleware('web')->group(function () {
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
        ['middleware' => 'auth'],
        function () {
        Route::get(
            'add-right',
            [
            'as' => 'add_right',
            'uses' => 'Application\RightController@showAddRightForm'
            ]
        );
        Route::post(
            'add-right',
            [
            'as' => 'add_right',
            'uses' => 'Application\RightController@saveRightForm'
            ]
        );
        
        Route::get(
            'confirm-payment',
            [
            'as' => 'confirm_payment',
            'uses' => 'Application\RightController@confirmPaymentForm'
            ]
        );
        
        Route::post(
            'confirm-payment',
            [
            'as' => 'confirm_payment',
            'uses' => 'Application\RightController@confirmPayment'
            ]
        );
        
        
        Route::get(
            'edit-user-right',
            [
            'as' => 'edit_user_right',
            'uses' => 'Application\RightController@showEditRightForm'
            ]
        );
        
        Route::get(
            'status',
            [
            'as' => 'status',
            'uses' => 'Application\RightController@getPaymentStatus'
            ]
        );  
        
        Route::get(
            'download-zip-file',
            [
            'as' => 'download_zip_file',
            'uses' => 'Application\RightController@downloadZipFile'
            ]
        );        
        Route::get(
            'download-research-file/{user_id}/{file_id}',
            [
            'as' => 'download_research_file',
            'uses' => 'Application\AccountController@getAttachmentResearch'
            ]
        );
        
        Route::get(
            'download-zip-file-latest',
            [
            'as' => 'download_zip_file_latest',
            'uses' => 'Application\RightController@downloadZipFileLatest'
            ]
        );

        


        
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
            
            Route::get('family-information',
                [
                'as' => 'family_information',
                'uses' => 'Application\AccountController@editFamilyInformation'
            ]);
            
            Route::post('family-information',
                [
                'as' => 'family_information',
                'uses' => 'Application\AccountController@saveFamilyInformation'
            ]);
            
            Route::get('residential-information',
                [
                'as' => 'residential_information',
                'uses' => 'Application\AccountController@editResidentialInformation'
            ]);
            
            Route::post('residential-information',
                [
                'as' => 'residential_information',
                'uses' => 'Application\AccountController@saveResidentialInformation'
            ]);
            
            Route::get('professional-information',
                [
                    
                'as' => 'professional_information',
                'uses' => 'Application\AccountController@editProfessionalInformation'
            ]);
            
            Route::post('professional-information',
                [
                'as' => 'professional_information',
                'uses' => 'Application\AccountController@saveProfessionalInformation'
            ]);
            
            
            
            Route::get('commercial-information',
                [
                'as' => 'commercial_information',
                'uses' => 'Application\AccountController@editCommercialInformation'
            ]);
            
            Route::post('commercial-information',
                [
                'as' => 'commercial_information',
                'uses' => 'Application\AccountController@saveCommercialInformation'
            ]); 
            
            Route::get('financial-information',
                [
                'as' => 'financial_information',
                'uses' => 'Application\AccountController@editFinancialInformation'
            ]);
            
            Route::post('financial-information',
                [
                'as' => 'financial_information',
                'uses' => 'Application\AccountController@saveFinancialInformation'
            ]);
            
             Route::get('upload-document',
                [
                'as' => 'upload_document',
                'uses' => 'Application\AccountController@editDocuments'
            ]);
            
            Route::post('upload-document',
                [
                'as' => 'upload_document',
                'uses' => 'Application\AccountController@saveDocuments'
            ]);
            

            Route::get('import_document',
                
                ['as'=>'import_document',
                'uses'=>'Application\AccountController@IndivisualDocDownload'
            ]);
            
            Route::get('my-account',
                [
                'as' => 'my_account',
                'uses' => 'Application\AccountController@myAccoutPopup'
            ]);

            Route::get('public-profile',
                [
                'as' => 'public_profile',
                'uses' => 'Application\AccountController@publicProfile'
            ]);
            
            Route::get('update-notification',
            [
            'as' => 'update_notification',
            'uses' => 'Application\AccountController@updateNotifications'
            ]);

             Route::get('company-profile',
                [
                'as' => 'company_profile-show',
                'uses' => 'Application\CompanyController@index'
            ]);
            
            Route::post('company-profile',
                [
                'as' => 'company_profile',
                'uses' => 'Application\CompanyController@companyDetailsForm'
            ]);
            Route::get('company-address',
                [
                'as' => 'company-address-show',
                'uses' => 'Application\CompanyController@companyAddress'
            ]);
            Route::post('company-address',
                [
                'as' => 'company-address',
                'uses' => 'Application\CompanyController@companyAddressForm'
            ]);

            //shareholding-structure-show

            Route::get('shareholding-structure',
                [
                'as' => 'shareholding_structure',
                'uses' => 'Application\CompanyController@shareholdingStructure'
            ]);

            Route::post('shareholding-structure',
                [
                'as' => 'shareholding_structure',
                'uses' => 'Application\CompanyController@shareHoldingStructureForm'
            ]);
            
            Route::get('financial',
                [
                'as' => 'financial-show',
                'uses' => 'Application\CompanyController@financialInfo'
            ]);
             Route::post('financial',
                [
                'as' => 'financial',
                'uses' => 'Application\CompanyController@financialInfoForm'
            ]);

            Route::get('documents',
                [
                'as' => 'documents-show',
                'uses' => 'Application\CompanyController@documentDeclaration'
            ]);
            Route::post('documents',
                [
                'as' => 'documents',
                'uses' => 'Application\CompanyController@documentDeclarationForm'
              ]);
           
            Route::get('import-doc',['as'=>'import_doc','uses'=>'Application\CompanyController@docDownload']);

            Route::get('downloads/{user_id}',
                [
                'as' => 'downloads',
                'uses' => 'Application\CompanyController@docDownload'
              ]);

        });
    });

    Route::group(['prefix' => 'rights'],
        function () {
        Route::group(['middleware' => 'auth'],
            function () {
            Route::get('/',
                [
                'as' => 'all_rights',
                'uses' => 'Application\RightController@listAllRights'
            ]);
            
            Route::get('/detail',
                [
                'as' => 'right_details',
                'uses' => 'Application\RightController@rightDetails'
            ]);
            
            Route::get('/term-condition',
                [
                'as' => 'term_condition',
                'uses' => 'Application\RightController@termConditionPopup'
            ]);
            
            Route::get('open-report-popup',
                [
                'as' => 'open_report_popup',
                'uses' => 'Application\RightController@reportPopup'
            ]);
            
            Route::post('save-report',
                [
                'as' => 'save_report',
                'uses' => 'Application\RightController@saveReportPopup'
            ]);
            
            Route::post('accept-term-condition',
                [
                'as' => 'accept_term_condition',
                'uses' => 'Application\RightController@acceptCondition'
            ]);
            
            Route::get('/valid-claim',
                [
                'as' => 'scout_valid_claim',
                'uses' => 'Application\RightController@validClaimPopup'
            ]);
            
            Route::post('valid-and-claim',
                [
                'as' => 'valid_and_claim',
                'uses' => 'Application\RightController@postValidClaim'
            ]);
            
            Route::get('buy-right-popup',
                [
                'as' => 'buy_right_popup',
                'uses' => 'Application\RightController@buyRightPopup'
            ]);

            Route::post('save-buy-right-popup',
                [
                'as' => 'save_buy_right_popup',
                'uses' => 'Application\RightController@savebuyRightPopup'
            ]);

            Route::get('all-selling-rights',
                [
                'as' => 'selling_rights',
                'uses' => 'Application\RightController@listSellingRights'
            ]);
            Route::get('all-recomm-rights',
                [
                'as' => 'recomm_rights',
                'uses' => 'Application\RightController@listRecommRights'
            ]);
            Route::get('all-recently-rights',
                [
                'as' => 'recently_rights',
                'uses' => 'Application\RightController@listRecentlyRights'
            ]);


        });
    });
    Route::get('reg-inside-profile',
            [
            'as' => 'view_inside_register_profile',
            'uses' => 'Application\DefaultController@accessStorageImages'
            ]
        );
});
