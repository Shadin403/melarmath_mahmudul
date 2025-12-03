<?php

namespace App\Http\Controllers;

use App\Models\DhakaArea;
use App\Models\ProductVariation;
use Botble\Base\Facades\BaseHelper;
use Botble\Ecommerce\Facades\InvoiceHelper;
use Botble\Ecommerce\Models\Invoice;
use Botble\Ecommerce\Models\Order;
use Botble\Base\Supports\Language;
use Botble\Ecommerce\Supports\TwigExtension;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Theme\Facades\Theme;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Pdf;

use Botble\Ecommerce\Facades\EcommerceHelper as EcommerceHelperFacade;
use Illuminate\Http\Request;

class HomeController
{
    public function getTitleAndVariation($id, Request $request)
    {
        $variation = ProductVariation::findOrFail($id);

        $variationTitle = $variation->variation_title;
        $variationDesc = $variation->variation_desc;

        // If language parameter is provided and it's English, try to get translation
        if ($request->has('lang') && $request->input('lang') === 'en') {
            $translation = $variation->translations()->where('lang_code', 'en_US')->first();
            if ($translation) {
                $variationTitle = $translation->variation_title ?: $variationTitle;
                $variationDesc = $translation->variation_desc ?: $variationDesc;
            }
        }

        return response()->json([
            'variation_title' => $variationTitle,
            'variation_desc' => $variationDesc,
        ]);
    }

    public function getDhakaArea($id)
    {
        $areas = DhakaArea::where('thana_id', $id)->get();
        return Response()->json([
            'status' => 200,
            'areas' => $areas
        ]);
    }

    public function getOrderUsingToken($token)
    {
        $order = Order::with('invoice')->where('token', $token)->firstOrFail();
        return Response()->json($order);
    }


    public function getCompanyState(): ?string
    {
        return get_ecommerce_setting('company_state_for_invoicing', get_ecommerce_setting('store_state'));
    }

    public function getCompanyCity(): ?string
    {
        return get_ecommerce_setting('company_city_for_invoicing', get_ecommerce_setting('store_city'));
    }

    public function getCompanyCountry(): ?string
    {
        return get_ecommerce_setting('company_country_for_invoicing', get_ecommerce_setting('store_country'));
    }

    public function generateOrderPDF($id)
    {
        $invoice = Invoice::findOrFail($id);
        $logo = get_ecommerce_setting('company_logo_for_invoicing') ?: (theme_option(
            'logo_in_invoices'
        ) ?: Theme::getLogo());

        $paymentDescription = null;

        if (
            is_plugin_active('payment') &&
            $invoice->payment->payment_channel == PaymentMethodEnum::BANK_TRANSFER &&
            $invoice->payment->status == PaymentStatusEnum::PENDING
        ) {
            $paymentDescription = BaseHelper::clean(
                get_payment_setting('description', $invoice->payment->payment_channel)
            );
        }

        $companyName = get_ecommerce_setting('company_name_for_invoicing') ?: get_ecommerce_setting('store_name');

        $companyAddress = get_ecommerce_setting('company_address_for_invoicing');

        $country = EcommerceHelperFacade::getCountryNameById(get_ecommerce_setting('company_country_for_invoicing', get_ecommerce_setting('store_country')));
        $state = get_ecommerce_setting('company_state_for_invoicing', get_ecommerce_setting('store_state'));
        $city = get_ecommerce_setting('company_city_for_invoicing', get_ecommerce_setting('store_city'));

        if (EcommerceHelperFacade::loadCountriesStatesCitiesFromPluginLocation()) {
            if (is_numeric($state)) {
                $state = State::query()->wherePublished()->where('id', $state)->value('name');
            }

            if (is_numeric($city)) {
                $city = City::query()->wherePublished()->where('id', $city)->value('name');
            }
        }

        if (!$companyAddress) {
            $companyAddress = implode(', ', array_filter([
                get_ecommerce_setting('company_address_for_invoicing', get_ecommerce_setting('store_address')),
                $city,
                $state,
                $country,
            ]));
        }

        $companyPhone = get_ecommerce_setting('company_phone_for_invoicing') ?: get_ecommerce_setting('store_phone');
        $companyEmail = get_ecommerce_setting('company_email_for_invoicing') ?: get_ecommerce_setting('store_email');
        $companyTaxId = get_ecommerce_setting('company_tax_id_for_invoicing') ?: get_ecommerce_setting(
            'store_vat_number'
        );

        $invoice->loadMissing(['items', 'reference']);

        $invoice->items = $invoice->items->map(function ($item) {
            $item->product_options_implode = (string) $item->product_options_implode;

            return $item;
        });

        // $invoice->items = $invoice->items->map(function ($item) {
        //     $item->product_options_implode = (string) $item->product_options_implode;

        //     // options decode
        //     $decodedOptions = [];
        //     if (!empty($item->options) && is_array($item->options)) {
        //         if (isset($item->options['attributes'])) {
        //             $decodedOptions[] = $item->options['attributes'];
        //         }

        //         if (isset($item->options['product_options'])) {
        //             $decodedOptions[] = $item->options['product_options'];
        //         }

        //         if (isset($item->options['license_code'])) {
        //             $decodedOptions[] = 'License: ' . $item->options['license_code'];
        //         }
        //     }

        //     // JSON encoded string থাকলে handle করা
        //     if (is_string($item->options)) {
        //         $optionsArr = json_decode($item->options, true);
        //         if (json_last_error() === JSON_ERROR_NONE && is_array($optionsArr)) {
        //             if (isset($optionsArr['attributes'])) {
        //                 $decodedOptions[] = $optionsArr['attributes'];
        //             }
        //             if (isset($optionsArr['product_options'])) {
        //                 $decodedOptions[] = $optionsArr['product_options'];
        //             }
        //             if (isset($optionsArr['license_code'])) {
        //                 $decodedOptions[] = 'License: ' . $optionsArr['license_code'];
        //             }
        //         }
        //     }

        //     // 🆕 decoded_options property add
        //     $item->decoded_options = implode(' | ', $decodedOptions);

        //     return $item;
        // });


        $data = [
            'invoice' => $invoice->toArray(),
            'logo' => $logo,
            'logo_full_path' => RvMedia::getRealPath($logo),
            'site_title' => Theme::getSiteTitle(),
            'company_logo_full_path' => RvMedia::getRealPath($logo),
            'company_name' => $companyName,
            'company_address' => $companyAddress,
            'company_country' => $country,
            'company_state' => $state,
            'company_city' => $city,
            'company_zipcode' => get_ecommerce_setting('company_zipcode_for_invoicing') ?: get_ecommerce_setting(
                'store_zip_code'
            ),
            'company_phone' => $companyPhone,
            'company_email' => $companyEmail,
            'company_tax_id' => $companyTaxId,
            'total_quantity' => $invoice->items->sum('qty'),
            'payment_description' => $paymentDescription,
            'is_tax_enabled' => EcommerceHelperFacade::isTaxEnabled(),
            'is_payment_plugin_active' => is_plugin_active('payment'),
            'settings' => [
                'using_custom_font_for_invoice' => (bool) get_ecommerce_setting('using_custom_font_for_invoice'),
                'custom_font_family' => get_ecommerce_setting('invoice_font_family', 'DejaVu Sans'),
                'font_family' => (int) get_ecommerce_setting('using_custom_font_for_invoice', 0) == 1
                    ? get_ecommerce_setting('invoice_font_family', 'DejaVu Sans')
                    : 'DejaVu Sans',
                'enable_invoice_stamp' => get_ecommerce_setting('enable_invoice_stamp'),
                'date_format' => get_ecommerce_setting('invoice_date_format', 'F d, Y'),
            ],
            'invoice_header_filter' => apply_filters('ecommerce_invoice_header', null, $invoice),
            'invoice_body_filter' => apply_filters('ecommerce_invoice_body', null, $invoice),
            'ecommerce_invoice_footer' => apply_filters('ecommerce_invoice_footer', null, $invoice),
            'invoice_payment_info_filter' => apply_filters('invoice_payment_info_filter', null, $invoice),
            'tax_classes_name' => $invoice->taxClassesName,
        ];

        $data['settings']['font_css'] = null;

        if ($data['settings']['using_custom_font_for_invoice'] && $data['settings']['font_family']) {
            $data['settings']['font_css'] = BaseHelper::googleFonts(
                'https://fonts.googleapis.com/css2?family=' .
                    urlencode($data['settings']['font_family']) .
                    ':wght@400;600;700&display=swap'
            );
        }

        $data['settings']['extra_css'] = apply_filters('ecommerce_invoice_extra_css', null, $invoice);

        $data['settings']['header_html'] = apply_filters('ecommerce_invoice_header_html', null, $invoice);

        $language = Language::getCurrentLocale();

        $data['html_attributes'] = trim(Html::attributes([
            'lang' => $language['locale'],
        ]));

        $data['body_attributes'] = trim(Html::attributes([
            'dir' => $language['is_rtl'] ? 'rtl' : 'ltr',
        ]));

        $order = $invoice->reference;

        if ($order) {
            $address = $order->shippingAddress;

            if (EcommerceHelperFacade::isBillingAddressEnabled() && $order->billingAddress->id) {
                $address = $order->billingAddress;
            }

            $data['customer_country'] = $address->country_name;
            $data['customer_state'] = $address->state_name;
            $data['customer_city'] = $address->city_name;
            $data['customer_zip_code'] = $address->zip_code;
        }

        if (is_plugin_active('payment')) {
            $invoice->loadMissing(['payment']);

            $data['payment_method'] = $invoice->payment->payment_channel->label();
            $data['payment_status'] = $invoice->payment->status->getValue();
            $data['payment_status_label'] = $invoice->payment->status->label();
        }

        // QR Code Generator for  order Tracking
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);

        $orderCode = $invoice->order_code ?: $invoice->order->code;

        if ($orderCode && $invoice->customer_email) {
            // Remove # symbol from order code for QR tracking link
            $cleanOrderCode = ltrim($orderCode, '#');

            $trackingLink = sprintf(
                'https://melarmath.com/orders/tracking?order_id=%s&email=%s',
                $cleanOrderCode,
                $invoice->customer_email
            );

            $qrCode = $writer->writeString($trackingLink);
            $data['qr_code'] = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        }

        // return $data;
        return (new Pdf())
            ->templatePath(plugin_path('ecommerce/resources/templates/order-invoice.tpl')) // path to your tpl file
            ->destinationPath(storage_path('ecommerce/resources/templates/order-invoice.tpl'))
            ->supportLanguage(InvoiceHelper::getLanguageSupport())  // crucial for Bangla font support
            ->paperSizeA4()
            ->data($data)
            ->twigExtensions([new TwigExtension()])
            ->setProcessingLibrary('mpdf')
            ->stream('order-invoice.pdf');
    }
}
