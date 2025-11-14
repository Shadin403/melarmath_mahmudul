<?php

namespace SobkichuBazarPay\SobkichuBazarPay\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Forms\PaymentMethodForm;

class SobkichuBazarPayPaymentMethodForm extends PaymentMethodForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME)
            ->paymentName("SobkichuBazarPay")
            ->paymentDescription(__("Customer can buy product and pay with :name", ["name" => "SobkichuBazarPay"]))
            ->paymentLogo(url("vendor/core/plugins/sobkichubazarpay/images/sobkichubazarpay.png"))
            ->paymentUrl("https://pay.sobkichubazar.com.bd")
            ->paymentInstructions(view("plugins/sobkichubazarpay::settings")->render())
            ->add(
                sprintf("payment_%s_brand_key", SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME),
                TextField::class,
                TextFieldOption::make()
                    ->label(__("Brand Key"))
                    ->attributes(["data-counter" => 400])
                    ->value(BaseHelper::hasDemoModeEnabled() ? "*******************************" : get_payment_setting("brand_key", SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME))
                    ->toArray()
            )
            ->add(
                sprintf("payment_%s_mode", SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME),
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans("plugins/payment::payment.live_mode"))
                    ->value(get_payment_setting("mode", SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME, false))
                    ->toArray(),
            );
    }
}

