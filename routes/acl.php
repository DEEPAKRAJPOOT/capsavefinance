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
            ['middleware' => 'auth'], function () {
        Route::group(
                ['prefix' => 'acl'], function () {

            Route::get(
                    '/', [
                'as' => 'get_role',
                'uses' => 'Backend\AclController@index'
                    ]
            );
            Route::get(
                    'add-role', [
                'as' => 'add_role',
                'uses' => 'Backend\AclController@addRole'
                    ]
            );
            Route::post(
                    'save-role', [
                'as' => 'save_add_role',
                'uses' => 'Backend\AclController@saveRole'
                    ]
            );
            Route::get(
                    'manage-role-permission', [
                'as' => 'manage_role_permission',
                'uses' => 'Backend\AclController@getRolePermission'
                    ]
            );
             Route::post(
                    'save_permission', [
                'as' => 'save_permission',
                'uses' => 'Backend\AclController@saveRolePermission'
                    ]
            );
            
            
            
        });


        
    });

});

