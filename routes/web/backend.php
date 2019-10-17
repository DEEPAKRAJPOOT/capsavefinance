<?php
/**
 * This route have all backend guest and auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {

    Route::get('/',
        [
        'as' => 'backend_login_open',
        'uses' => 'Backend\Auth\LoginController@showLoginForm'
    ]);
    Route::post(
        '/',
        [
        'as' => 'backend_login_open',
        'uses' => 'Backend\Auth\LoginController@login'
        ]
    );
    Route::post(
        'logout',
        [
        'as' => 'backend_logout',
        'uses' => 'Backend\Auth\LoginController@logout'
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



    Route::group(
        ['prefix' => 'dashboard'],
        function () {
        Route::group(
            ['middleware' => 'auth'],
            function () {
            Route::get(
                '/',
                [
                'as' => 'backend_dashboard',
                'uses' => 'Backend\DashboardController@index'
                ]
            );
            Route::get(
                'manage-users',
                [
                'as' => 'manage_users',
                'uses' => 'Backend\UserController@viewUserList'
                ]
            );
            Route::get(
                'user-detail',
                [
                'as' => 'user_detail',
                'uses' => 'Backend\UserController@viewUserDetail'
                ]
            );
            Route::get(
                'edit-user',
                [
                'as' => 'edit_backend_user',
                'uses' => 'Backend\UserController@editUser'
                ]
            );

            Route::get(
                'view-user',
                [
                'as' => 'view_user_detail',
                'uses' => 'Backend\UserController@viewUserDetail'
                ]
            );


            Route::post(
                'delete-user',
                [
                'as' => 'delete_users',
                'uses' => 'Backend\UserController@deleteUser'
                ]
            );
            Route::post(
                'save-user',
                [
                'as' => 'save_backend_user',
                'uses' => 'Backend\UserController@saveUser'
                ]
            );
            
            Route::get(
                'scout',
                [
                'as' => 'show_scout',
                'uses' => 'Backend\UserController@viewAllScout'
                ]
            );
            Route::get(
                'user',
                [
                'as' => 'show_user',
                'uses' => 'Backend\UserController@viewAllUser'
                ]
            );
            
            Route::get(
                'user_paginate',
                [
                'as' => 'user_paginate',
                'uses' => 'Backend\UserController@viewUserAjaxPaginate'
                ]
            );
            Route::post(
                'user-detail',
                [
                'as' => 'admin_approved',
                'uses' => 'Backend\UserController@updateUserDetail'
                ]
            );
            
        });
    });

    Route::group(
        ['prefix' => 'account'],
        function () {
        Route::group(
            ['middleware' => 'auth'],
            function () {
            Route::get(
                'view-profile',
                [
                'as' => 'view_profile',
                'uses' => 'Backend\UserController@viewProfile'
                ]
            );

            Route::get(
                'update-profile',
                [
                'as' => 'update_profile',
                'uses' => 'Backend\UserController@updateProfile'
                ]
            );

            Route::post(
                'update-profile',
                [
                'as' => 'update_profile',
                'uses' => 'Backend\UserController@updateUserProfile'
                ]
            );

            Route::post(
                'upload-image',
                [
                'as' => 'upload_image',
                'uses' => 'Backend\UserController@ajaxImageUpload'
                ]
            );


            Route::get(
                'change-password',
                [
                'as' => 'change_password',
                'uses' => 'Backend\UserController@changePassword'
                ]
            );

            Route::post(
                'change-password',
                [
                'as' => 'change_password',
                'uses' => 'Backend\UserController@updateChangePassword'
                ]
            );
        });
    });
});