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

Route::group(
        ['middleware' => 'auth'],
        function () {
       
    

Route::get(
            'paypal',
            [
            'as' => 'pay_with_paypal',
            'uses' => 'PaymentController@showPayPalForm'
            ]
        );
          Route::post(
            'paymet-paypal',
            [
            'as' => 'paywithpaypal',
            'uses' => 'PaymentController@payWithpaypal'
            ]
        );
        Route::get(
            'paypal-payment',
            [
            'as' => 'paypal_payment',
            'uses' => 'Application\RightController@getPaymentPageonBuy'
            ]
        );  
        
        
        
    });


   
        
        
        
        
        
      
