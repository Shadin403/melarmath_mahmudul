<?php

namespace SobkichuBazarPay\SobkichuBazarPay;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::query()
            ->whereIn("key", [
                "payment_sobkichubazarpay_name",
                "payment_sobkichubazarpay_description",
                "payment_sobkichubazarpay_brand_key",
                "payment_sobkichubazarpay_mode",
            ])
            ->delete();
    }
}

