<?php

use Illuminate\Support\Facades\Route;
use SobkichuBazarPay\SobkichuBazarPay\Http\Controllers\SobkichuBazarPayController;

Route::group(['controller' => SobkichuBazarPayController::class, 'middleware' => ['web', 'core']], function () {
    Route::get('payment/sobkichubazarpay/callback', 'getCallback')->name('payments.sobkichubazarpay.callback');
});

