<?php

/*
  |--------------------------------------------------------------------------
  | Paystack Routes
  |--------------------------------------------------------------------------
  |
 */
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->namespace('Ujackson\AvoredPaystack\Http\Controllers')
    ->group(function () {
        Route::get('/paystack/pay', 'PaystackController@index')->name('paystack.pay');
        Route::get('/paystack/callback', 'PaystackController@callback')->name('paystack.callback');
        Route::post('/paystack/webhook', 'PaystackController@webhook')->name('paystack.webhook');

    });
