<?php
/**
 * FrontEnd routes
 * 
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.frontend_uri'))->group(function () {

    Route::get('/', 'Auth\LoginController@showLoginForm');
    
    Route::get('login',
        [
        'as' => 'login_open',
        'uses' => 'Auth\LoginController@showLoginForm'
    ]);

    Route::post('login',
        [
        'as' => 'login_open',
        'uses' => 'Auth\LoginController@login'
    ]);

    //Logout
    Route::post('logout',
        [
        'as' => 'frontend_logout',
        'uses' => 'Auth\LoginController@logout'
    ]);
    
     //Registration step 1
    Route::get('sign-up',
        [
        'as' => 'user_register_open',
        'uses' => 'Auth\RegisterController@showRegistrationForm'
    ]);

    Route::get('company-sign-up',
        [
        'as' => 'company_register_open',
        'uses' => 'Auth\RegisterController@showCompRegistrationForm'
    ]);

    Route::post('register',
        [
        'as' => 'user_register_open',
        'uses' => 'Auth\RegisterController@register'
    ]);

    Route::get('business-information',
        [
        'as' => 'business_information_open',
        'uses' => 'Backend\BusinessController@showBusinessInformationForm'
    ]);

    Route::post('business-information-save',
        [
        'as' => 'business_information_save',
        'uses' => 'Backend\BusinessController@saveBusinessInformation'
    ]);

    Route::get('authorized-signatory',
        [
        'as' => 'authorized_signatory_open',
        'uses' => 'Auth\RegisterController@showAuthorizedSignatoryForm'
    ]);
    
    Route::post('authorized-signatory-save',
        [
        'as' => 'authorized_signatory_save',
        'uses' => 'Auth\RegisterController@saveAuthorizedSignatoryForm'
    ]);
    
    Route::get('business-documents',
        [
        'as' => 'business-documents',
        'uses' => 'Auth\RegisterController@showBusinessDocument'
    ]);
    
    Route::post('business-documents-save',
        [
        'as' => 'business-documents-save',
        'uses' => 'Auth\RegisterController@saveBusinessDocument'
    ]);
    
    Route::get('associate-buyer',
        [
        'as' => 'associate-buyer',
        'uses' => 'Auth\RegisterController@showAssociateBuyer'
    ]);
    
    Route::post('associate-buyer-save',
        [
        'as' => 'associate-buyer-save',
        'uses' => 'Auth\RegisterController@saveAssociateBuyer'
    ]);
    
    Route::get('associate-logistics',
        [
        'as' => 'associate-logistics',
        'uses' => 'Auth\RegisterController@showAssociateLogistics'
    ]);
    
    Route::post('associate-logistics-save',
        [
        'as' => 'associate-logistics-save',
        'uses' => 'Auth\RegisterController@saveAssociateLogistics'
    ]);

    // for password
    
    




    Route::group(['prefix' => 'password'],
        function () {
        
        $this->get('email',
            [
            'as' => 'password.email',
            'uses' => 'Auth\ForgotPasswordController@showResetLinkEmail'
            ]
        );
        $this->post('email',
            [
            'as' => 'password.email',
            'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail'
            ]
        );
        $this->get('reset',
            [
            'as' => 'password.reset',
            'uses' => 'Auth\ResetPasswordController@showResetForm'
            ]
        );
        $this->post('reset',
            [
            'as' => 'password.reset',
            'uses' => 'Auth\ResetPasswordController@reset'
            ]
        );
        $this->get('change',
            [
            'as' => 'changepassword',
            'uses' => 'Auth\ChangePasswordController@showChangePasswordForm'
            ]
        );
        $this->post('change',
            [
            'as' => 'changepassword',
            'uses' => 'Auth\ChangePasswordController@changePassword'
            ]
        );
    });
    
    
    
   //// for verify opt and email
    
    
    Route::get('thanks',
        [
        'as' => 'thanks',
        'uses' => 'Auth\RegisterController@showThanksForm'
    ]);

    Route::get('otp/{token}',
        [
        'as' => 'otp',
        'uses' => 'Auth\RegisterController@otpForm'
    ]);
    //Registration step 2
    
    Route::get('verify-email/{token}',
        [
        'as' => 'verify_email',
        'uses' => 'Auth\RegisterController@verifyUser'
    ]);

    Route::post('verify-otp',
        [
        'as' => 'verify_otp',
        'uses' => 'Auth\RegisterController@verifyotpUser'
    ]);
    Route::get('resend-otp',
        [
        'as' => 'resend_otp',
        'uses' => 'Auth\RegisterController@resendotpUser'
    ]);
    
    
    Route::get('otp-thanks',
        [
        'as' => 'otp_thanks',
        'uses' => 'Auth\RegisterController@verifiedotpUser'
    ]);

   
    
});
