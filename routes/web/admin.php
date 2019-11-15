<?php
/**
 * FrontEnd routes
 * 
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {
Route::get('/',
        [
        'as' => 'get_backend_login_open',
        'uses' => 'Backend\Auth\LoginController@showLoginForm'
    ]);
Route::any(
        'logout',
        [
        'as' => 'backend_logout',
        'uses' => 'Backend\Auth\LoginController@logout'
        ]
    );



    Route::group(['middleware' => []],
            function () {
    
    Route::post(
        '/',
        [
        'as' => 'backend_login_open',
        'uses' => 'Backend\Auth\LoginController@login'
        ]
    );
    

    Route::group(['prefix' => 'password'],
        function () {
        // Reset request email
        $this->get('reset-password',
            [
            'as' => 'password.do.reset',
            'uses' => 'Backend\Auth\ForgotPasswordController@showLinkRequestForm'
            ]
        );
        $this->post('email',
            [
            'as' => 'password.email',
            'uses' => 'Backend\Auth\ForgotPasswordController@sendResetLinkEmail'
            ]
        );
        $this->get('reset',
            [
            'as' => 'password.reset',
            'uses' => 'Backend\Auth\ResetPasswordController@showResetForm'
            ]
        );
        $this->post('reset',
            [
            'as' => 'password.reset',
            'uses' => 'Backend\Auth\ResetPasswordController@reset'
            ]
        );
    });

});
});