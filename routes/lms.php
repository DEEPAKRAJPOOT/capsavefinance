<?php

/**
 * This route have all lms auth routes
 *
 * @since 1.0
 *
 * @author Prolitus Dev Team
 */
Route::domain(config('proin.backend_uri'))->group(function () {
    Route::group(['middleware' => 'auth'], function () {

        Route::group(['prefix' => 'lms'], function () {
            Route::get('/', [
                'as' => 'lms.get_customer_list',
                'uses' => 'Lms\CustomerController@list'
            ]);

        });//end of application
        
	});

});

