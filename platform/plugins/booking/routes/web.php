<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Botble\Booking\Http\Controllers\BookingController;
use Botble\Base\Facades\AdminHelper;
use Botble\Setting\Facades\Setting;
use Botble\Booking\Http\Controllers\BlockedController;

// -----------------------------
// -----------------------------
if (!function_exists('ensureBookingView')) {
    function ensureBookingView(): void
    {
        $theme = Theme::getThemeName();
        $path = base_path("platform/themes/{$theme}/views/booking-page.blade.php");

        if (!File::exists($path)) {
            File::ensureDirectoryExists(dirname($path));

            File::put($path, <<<BLADE
@php
    Theme::layout('full-width');
    Theme::set('pageTitle', 'Booking');
@endphp

@include('plugins/booking::frontend.widget')
BLADE
            );
        }
    }
}

// -----------------------------
// -----------------------------
AdminHelper::registerRoutes(function () {
    Route::get('booking/blocked-dates', [BlockedController::class, 'dates'])->name('booking.blocked-dates');
    Route::post('booking/blocked-dates', [BlockedController::class, 'saveDate'])->name('booking.blocked-dates.save');
    Route::delete('booking/blocked-dates/{date}', [BlockedController::class, 'deleteDate'])->name('booking.blocked-dates.delete');

    // Blocked Times
    Route::get('booking/blocked-times', [BlockedController::class, 'times'])->name('booking.blocked-times');
    Route::post('booking/blocked-times', [BlockedController::class, 'saveTime'])->name('booking.blocked-times.save');
    Route::delete('booking/blocked-times/{time}', [BlockedController::class, 'deleteTime'])->name('booking.blocked-times.delete'); 
    
    Route::resource('booking', BookingController::class)->parameters(['booking' => 'booking']);

    Route::get('booking/settings', function () {
        return view('plugins/booking::settings', [
            'slot'  => Setting::get('booking_slot_minutes', '30'),
            'open'  => Setting::get('booking_open_time', '09:00'),
            'close' => Setting::get('booking_close_time', '18:00'),
            'week'  => Setting::get('booking_week_start', 'Mon'),
        ]);
    })->name('booking.settings');

    Route::post('booking/settings', function (Request $request) {
        Setting::set('booking_slot_minutes', $request->input('booking_slot_minutes', 30));
        Setting::set('booking_open_time', $request->input('booking_open_time', '09:00'));
        Setting::set('booking_close_time', $request->input('booking_close_time', '18:00'));
        Setting::set('booking_week_start', $request->input('booking_week_start', 'Mon'));
        Setting::save();

        return back()->with('status', 'تنظیمات ذخیره شد!');
    })->name('booking.settings.save');
});

// -----------------------------
// -----------------------------
Route::get('booking', function (Request $request) {
    ensureBookingView();
    return app(BookingController::class)->widget($request);
})->name('booking.widget');

Route::get('booking/slots', function (Request $request) {
    ensureBookingView();
    return app(BookingController::class)->slots($request);
})->name('booking.slots');

Route::post('booking/reserve', function (Request $request) {
    ensureBookingView();
    return app(BookingController::class)->reserve($request);
})->name('booking.reserve');

Route::get('booking-test', function (Request $request) {
    ensureBookingView();
    return app(BookingController::class)->test($request);
});
