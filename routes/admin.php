<?php
/**
 * FrontEnd routes
 * 
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */

/*if(config('app.env') == "production"){
    $proxy_url    = 'https://admin.capsavefinance.com';
   $proxy_schema = 'https';

//    dd(config('app.env'));
//    $proxy_url    = getenv('PROXY_URL');
//    $proxy_schema = getenv('PROXY_SCHEMA');
    if (!empty($proxy_url)) {
    \URL::forceRootUrl($proxy_url);
    }

    if (!empty($proxy_schema)) {
    \URL::forceScheme($proxy_schema);
    }
}*/

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

    Route::any('api/tally/entries',[
        'as' => 'api_tally_entries',
        'uses' => 'Auth\ApiController@tally_entry'
        ]
    );

    Route::any('api/tally/recover',[
        'as' => 'api_tally_recover',
        'uses' => 'Auth\ApiController@tally_recover'
        ]
    );

    Route::any('api/change/year',[
        'as' => 'api_change_year',
        'uses' => 'Auth\ApiController@changeFinancialYear'
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
        Route::get('reset-password',
            [
            'as' => 'password.do.reset',
            'uses' => 'Backend\Auth\ForgotPasswordController@showLinkRequestForm'
            ]
        );
        Route::post('email',
            [
            'as' => 'password.email',
            'uses' => 'Backend\Auth\ForgotPasswordController@sendResetLinkEmail'
            ]
        );
        Route::get('reset',
            [
            'as' => 'password.reset',
            'uses' => 'Backend\Auth\ResetPasswordController@showResetForm'
            ]
        );
        Route::post('reset',
            [
            'as' => 'password.reset',
            'uses' => 'Backend\Auth\ResetPasswordController@reset'
            ]
        );
        Route::get('change-password',
            [
            'as' => 'change_password',
            'uses' => 'Backend\Auth\ChangePasswordController@showChangePasswordForm'
            ]
        );
        Route::post('save-change-password',
            [
            'as' => 'save_change_password',
            'uses' => 'Backend\Auth\ChangePasswordController@changePassword'
            ]
        );
    });

});
});
