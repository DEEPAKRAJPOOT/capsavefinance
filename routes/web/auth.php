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

<<<<<<< HEAD



=======
>>>>>>> 5c15199bd5e1fe13c98591b61ceccafbc74a339b
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
