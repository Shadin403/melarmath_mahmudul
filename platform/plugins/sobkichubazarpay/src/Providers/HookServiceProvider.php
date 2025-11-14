<?php

namespace SobkichuBazarPay\SobkichuBazarPay\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use SobkichuBazarPay\SobkichuBazarPay\Forms\SobkichuBazarPayPaymentMethodForm;
use SobkichuBazarPay\SobkichuBazarPay\Services\Gateways\SobkichuBazarPayPaymentService;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerSobkichuBazarPayMethod'], 2, 2);

        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithSobkichuBazarPay'], 2, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 2);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['SOBKICHUBAZARPAY'] = SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME) {
                $value = 'SobkichuBazarPay';
            }

            return $value;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 2, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME) {
                $data = SobkichuBazarPayPaymentService::class;
            }

            return $data;
        }, 2, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . SobkichuBazarPayPaymentMethodForm::create()->renderForm();
    }

    public function registerSobkichuBazarPayMethod(?string $html, array $data): string
    {
        PaymentMethods::method(SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/sobkichubazarpay::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithSobkichuBazarPay(array $data, Request $request): array
    {
        if ($request->input('payment_method') == SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME) {
            $currentCurrency = get_application_currency();
            $currencyModel = $currentCurrency->replicate();

            $sobkichuBazarPayService = $this->app->make(SobkichuBazarPayPaymentService::class);
            $supportedCurrencies = $sobkichuBazarPayService->supportedCurrencyCodes();
            $currency = strtoupper($currentCurrency->title);
            $notSupportCurrency = false;

            if (! in_array($currency, $supportedCurrencies)) {
                $notSupportCurrency = true;

                if (! $currencyModel->where('title', 'BDT')->exists()) {
                    $data['error'] = true;
                    $data['message'] = __(":name doesn't support :currency. List of currencies supported by :name: :currencies.", [
                        'name' => 'SobkichuBazarPay',
                        'currency' => $currency,
                        'currencies' => implode(', ', $supportedCurrencies),
                    ]);

                    return $data;
                }
            }

            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

            if ($notSupportCurrency) {
                $bdtCurrency = $currencyModel->where('title', 'BDT')->first();
                $paymentData['currency'] = 'BDT';
                
                if ($currentCurrency->is_default) {
                    $paymentData['amount'] = $paymentData['amount'] * $bdtCurrency->exchange_rate;
                } else {
                    $paymentData['amount'] = format_price($paymentData['amount'], $currentCurrency, true);
                }
            }

            $checkoutUrl = $sobkichuBazarPayService->makePayment($paymentData);

            if (isset($checkoutUrl['error'])) {
                $data['error'] = true;
                $data['message'] = $checkoutUrl['message'] ?? __('Something went wrong. Please try again later.');
            } else {
                if ($checkoutUrl) {
                    $data['checkoutUrl'] = $checkoutUrl;
                } else {
                    $data['error'] = true;
                    $data['message'] = __('Something went wrong. Please try again later.');
                }
            }

            return $data;
        }

        return $data;
    }
}

