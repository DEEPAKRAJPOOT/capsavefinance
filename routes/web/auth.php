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
   
    
});
