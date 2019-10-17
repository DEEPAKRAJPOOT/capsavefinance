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
            ['prefix' => 'master'],
            function () {
        Route::group(
                ['middleware' => 'auth'],
                function () {
            Route::get(
                'manage-country',
                [
                'as' => 'manage_country',
                'uses' => 'Backend\MasterController@viewCountryList'
                ]
            );
            Route::get(
                    'edit-countries',
                    [
                        'as' => 'edit_countries',
                        'uses' => 'Backend\MasterController@showCountriesForm'
                    ]
            );
            Route::post(
                    'add-edit-countries',
                    [
                        'as' => 'add_edit_countries',
                        'uses' => 'Backend\MasterController@addEditCountries'
                    ]
            );
            Route::get(
                'manage-state',
                [
                'as' => 'manage_state',
                'uses' => 'Backend\MasterController@viewStateList'
                ]
            );
            Route::get(
                    'edit-state',
                    [
                        'as' => 'edit_state',
                        'uses' => 'Backend\MasterController@showStateForm'
                    ]
            );
            Route::post(
                    'add-edit-state',
                    [
                        'as' => 'add_edit_state',
                        'uses' => 'Backend\MasterController@addEditState'
                    ]
            );
            Route::get(
                'manage-cluster',
                [
                'as' => 'manage_cluster',
                'uses' => 'Backend\MasterController@viewClusterList'
                ]
            );
            Route::get(
                    'edit-cluster',
                    [
                        'as' => 'edit_cluster',
                        'uses' => 'Backend\MasterController@showClusterForm'
                    ]
            );
            Route::post(
                    'add-edit-cluster',
                    [
                        'as' => 'add_edit_cluster',
                        'uses' => 'Backend\MasterController@addEditCluster'
                    ]
            );
            Route::get(
                'manage-right-type',
                [
                'as' => 'manage_right_type',
                'uses' => 'Backend\MasterController@viewRightTypeList'
                ]
            );
            Route::get(
                    'edit-right-type',
                    [
                        'as' => 'edit_right_type',
                        'uses' => 'Backend\MasterController@showRightTypeForm'
                    ]
            );
            Route::post(
                    'add-edit-right-type',
                    [
                        'as' => 'add_edit_right_type',
                        'uses' => 'Backend\MasterController@addEditRightType'
                    ]
            );
            Route::get(
                'manage-source',
                [
                'as' => 'manage_source',
                'uses' => 'Backend\MasterController@viewSourceList'
                ]
            );
            Route::get(
                    'edit-source',
                    [
                        'as' => 'edit_source',
                        'uses' => 'Backend\MasterController@showSourceForm'
                    ]
            );
            Route::post(
                    'add-edit-source',
                    [
                        'as' => 'add_edit_source',
                        'uses' => 'Backend\MasterController@addEditSource'
                    ]
            );

            Route::get(
                'manage-rights',
                [
                'as' => 'manage_rights',
                'uses' => 'Backend\MasterController@viewRightList'
                ]
            );
            Route::get(
                    'edit-right',
                    [
                        'as' => 'edit_right',
                        'uses' => 'Backend\MasterController@showRightForm'
                    ]
            );
            Route::post(
                    'add-edit-right',
                    [
                        'as' => 'add_edit_right',
                        'uses' => 'Backend\MasterController@addEditRight'
                    ]
            );
            Route::get(
                    'view-right',
                    [
                        'as' => 'view_right',
                        'uses' => 'Backend\MasterController@viewSingleRight'
                    ]
            );



        });
    });
});
