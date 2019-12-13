<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


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