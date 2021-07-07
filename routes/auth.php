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
    
    Route::post('login-lenevo',
        [
        'as' => 'login_open_lenevo',
        'uses' => 'Auth\LenevoLoginController@login'
    ]);
    
    Route::post('register-lenevo',
        [
        'as' => 'user_register_save_lenevo',
        'uses' => 'Auth\LenevoRegisterController@register'
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
        'as' => 'user_register_save',
        'uses' => 'Auth\RegisterController@register'
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

    Route::post('check-exist-user-pan',[
        'as' => 'check_exist_user_pan',
        'uses' => 'Auth\RegisterController@checkExistUserPan'
        ]
    );   
    
});


/**
 * Lenevo FrontEnd routes
 * 
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.lenevo_frontend_uri'))->group(function () {

    Route::get('/', 'Auth\LenevoRegisterController@showRegistrationForm');
    
    Route::get('lenevo-login',
        [
        'as' => 'lenevo_login_open',
        'uses' => 'Auth\LenevoLoginController@showLoginForm'
    ]);
    
     //Registration step 1
    Route::get('sign-up',
        [
        'as' => 'user_register_open',
        'uses' => 'Auth\LenevoRegisterController@showRegistrationForm'
    ]);
    
    // for password
   Route::group(['prefix' => 'password'],
        function () {
        
        $this->get('lenovo-email',
            [
            'as' => 'password.email',
            'uses' => 'Auth\LenovoForgotPasswordController@showResetLinkEmail'
            ]
        );
        $this->post('lenovo-email',
            [
            'as' => 'password.email',
            'uses' => 'Auth\LenovoForgotPasswordController@sendResetLinkEmail'
            ]
        );
        $this->get('reset',
            [
            'as' => 'password.lenevo-reset',
            'uses' => 'Auth\LenovoForgotPasswordController@showResetForm'
            ]
        );
        $this->post('reset-lenevo',
            [
            'as' => 'password.reset',
            'uses' => 'Auth\ResetPasswordController@reset'
            ]
        );
    });
    
});