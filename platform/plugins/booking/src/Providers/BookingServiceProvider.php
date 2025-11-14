<?php

namespace Botble\Booking\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Setting\Events\SavingSettingsEvent;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File; 
use Botble\Base\Events\ActivatedPluginEvent; 

class BookingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/booking')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes(['web'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations();

        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::registerItem([
                'id'        => 'cms-plugins-booking',
                'priority'  => 5,
                'parent_id' => null,
                'name'      => 'plugins/booking::booking.name',
                'icon'      => 'ti ti-calendar',
            ]);

            DashboardMenu::registerItem([
                'id'          => 'cms-plugins-booking-list',
                'priority'    => 1,
                'parent_id'   => 'cms-plugins-booking',
                'name'        => 'Booking List',
                'url'         => route('booking.index'),
                'permissions' => ['booking.index'],
            ]);

            DashboardMenu::registerItem([
                'id'          => 'cms-plugins-booking-settings',
                'priority'    => 2,
                'parent_id'   => 'cms-plugins-booking',
                'name'        => 'Booking Settings',
                'url'         => route('booking.settings'),
                'permissions' => ['booking.index'],
            ]);

            DashboardMenu::registerItem([
                'id'          => 'cms-plugins-booking-blocked-dates',
                'priority'    => 3,
                'parent_id'   => 'cms-plugins-booking',
                'name'        => 'Closed Dates',
                'url'         => route('booking.blocked-dates'),
                'permissions' => ['booking.blocked-dates'],
            ]);

            DashboardMenu::registerItem([
                'id'          => 'cms-plugins-booking-blocked-times',
                'priority'    => 4,
                'parent_id'   => 'cms-plugins-booking',
                'name'        => 'Closed Times',
                'url'         => route('booking.blocked-times'),
                'permissions' => ['booking.blocked-times'],
            ]);
        });

        Event::listen(SavingSettingsEvent::class, function () {
            Setting::set('booking_slot_minutes', request('booking_slot_minutes', '30'));
            Setting::set('booking_open_time', request('booking_open_time', '09:00'));
            Setting::set('booking_close_time', request('booking_close_time', '18:00'));
            Setting::set('booking_week_start', request('booking_week_start', 'Mon'));
            Setting::save();
        });

         
    }

   
}
