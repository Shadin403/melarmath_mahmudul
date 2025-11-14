<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\AdminHelper;
use function Laravel\Prompts\{confirm, note, progress, select};
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;

Route::group(['namespace' => 'Khairulkabir\Steadfast\Http\Controllers'], function () {

    Route::any('steadfast/webhook', 'SteadfastController@webhookHandler')->name('steadfast.webhook');

    Route::any('steadfast/license-check', 'SteadfastController@license_check')->name('steadfast.license-check');

   
    



    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'steadfasts', 'as' => 'steadfast.'], function () {
            Route::resource('', 'SteadfastController')->parameters(['' => 'steadfast']);
            Route::get('print-invoice/{id}', 'SteadfastController@printInvoice')->name('print-invoice');

            Route::get('check-amount', 'SteadfastController@check_balance')->name('check-amount');
            Route::any('bulk-print-invoice', 'SteadfastController@bulkPrintInvoice')->name('bulk-print-invoice');

            Route::any('bulk-status-update', 'SteadfastController@saveBulkChangeItem')->name('bulk-status-update');
            Route::any('create-order', 'SteadfastController@createOrder')->name('create-order');
            Route::post('delivery-status', 'SteadfastController@deliveryStatus')->name('delivery-status');
            Route::Post('show-error', 'SteadfastController@showError')->name('show-error');
            Route::any('fake-order-check', 'SteadfastController@fakeOrderCheck')->name('fake-order-check');

            Route::any('start-the-trail', 'SteadfastController@freeTrial')->name('start-trial');

        });
        Route::group(['prefix' => 'settings/steadfasts_configs', 'as' => 'steadfast.steadfasts_configs', 'permission' => 'steadfast.steadfasts_configs'], function () {
            Route::get('/', [
                'uses' => 'Settings\SteadfastController@edit',
            ]);

            Route::put('/', [
                'as' => '.update',
                'uses' => 'Settings\SteadfastController@update',
            ]);
        });
    });
});