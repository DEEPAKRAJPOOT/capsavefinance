<?php

/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {

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
            

        });
    });
    
//   Resource controller @Supplier,@buyer,@lender,@logistics    //

    Route::resource('supplier', 'Backend\SupplierController');
    Route::resource('buyer', 'Backend\BuyerController');
    Route::resource('lender', 'Backend\LenderController');
    Route::resource('logistics', 'Backend\LogisticsController');

    Route::get('/', [
        'as' => 'backend_login_open',
        'uses' => 'Backend\Auth\LoginController@showLoginForm'
    ]);
    Route::post(
            '/', [
        'as' => 'backend_login_open',
        'uses' => 'Backend\Auth\LoginController@login'
            ]
    );
    Route::post(
            'logout', [
        'as' => 'backend_logout',
        'uses' => 'Backend\Auth\LoginController@logout'
            ]
    );
});
