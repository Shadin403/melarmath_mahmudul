<?php

namespace Khairulkabir\Steadfast\Supports;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PDFHelper;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Botble\Ecommerce\Facades\EcommerceHelper as EcommerceHelperFacade;
use Botble\Ecommerce\Models\Order;
use Botble\Media\Facades\RvMedia;
use Illuminate\Support\Facades\DB;

use Khairulkabir\Steadfast\Models\SteadfastsConfig;

class PrintPDFHelper
{
   

   
    public function getDataForInvoiceTemplate(Order $order): array
    {
        // Fetch company details
        $companyDetails = $this->getCompanyDetails();

        // Fetch customer details from the order
        $customerDetails = $this->getCustomerDetails($order);

        // Fetch order details, including products and other information
        $orderDetails = $this->getOrderDetails($order);

        // Combine all the details into one array for the template
        return array_merge($companyDetails, $customerDetails, $orderDetails);
    }

    /**
     * Get company details.
     *
     * @return array
     */
    protected function getCompanyDetails(): array
    {
        // Fetch the Steadfast configuration from the database
        $steadfastConfig = SteadfastsConfig::first();

        // Check if Steadfast settings are enabled and configured to use as invoice info
        if ($steadfastConfig && $steadfastConfig->enable_disable && $steadfastConfig->use_as_invoice_info) {
            // Fetch details from SteadfastsConfig
            $logo = $steadfastConfig->business_logo;
            $companyName = $steadfastConfig->business_name;
            $companyAddress = $steadfastConfig->business_address;
            $companyPhone = $steadfastConfig->business_number;
            $companyEmail = $steadfastConfig->business_email;
            $terms_Conditions = $steadfastConfig->terms_conditions;
        } else {
            // Fallback to eCommerce settings if SteadfastsConfig is not enabled or not set for invoicing
            $logo = get_ecommerce_setting('company_logo_for_invoicing') ?: (theme_option('logo_in_invoices') ?: theme_option('logo'));
            $companyName = get_ecommerce_setting('company_name_for_invoicing') ?: get_ecommerce_setting('store_name');
            $companyAddress = $this->getCompanyAddress();
            $companyPhone = get_ecommerce_setting('company_phone_for_invoicing') ?: get_ecommerce_setting('store_phone');
            $companyEmail = get_ecommerce_setting('company_email_for_invoicing') ?: get_ecommerce_setting('store_email');
            $terms_Conditions = '';
        }

        // Tax ID is always fetched from eCommerce settings
        $companyTaxId = get_ecommerce_setting('company_tax_id_for_invoicing') ?: get_ecommerce_setting('store_vat_number');

        return [
            'company_logo' => $logo,
            'company_logo_full_path' => RvMedia::getRealPath($logo),
            'company_name' => $companyName,
            'company_address' => $companyAddress,
            'company_phone' => $companyPhone,
            'company_email' => $companyEmail,
            'company_tax_id' => $companyTaxId,
            'terms_Conditions'=> $terms_Conditions,
        ];
    }


    /** 
     * Get customer details.
     *
     * @param Order $order
     * @return array
     */
    protected function getCustomerDetails(Order $order): array
    {
        $address = $order->shippingAddress;

        // Use billing address if enabled
        if (EcommerceHelperFacade::isBillingAddressEnabled() && $order->billingAddress->id) {
            $address = $order->billingAddress;
        }

        return [
            'customer_name' => $address->name,
            'customer_email' => $address->email,
            'customer_phone' => $address->phone,
            'customer_address' => $address->address,
            'customer_city' => $address->city_name,
            'customer_state' => $address->state_name,
            'customer_country' => $address->country_name,
            'customer_zip_code' => $address->zip_code,
        ];
    }

    /**
     * Get order details, including products and payment information.
     *
     * @param Order $order
     * @return array
     */
    protected function getOrderDetails(Order $order): array
    {
        // Fetch products from ec_order_product and join with ec_products for description
        $orderProducts = DB::table('ec_order_product')
            ->where('order_id', $order->id)
            ->join('ec_products', 'ec_order_product.product_id', '=', 'ec_products.id')
            ->select('ec_order_product.qty', 'ec_order_product.weight', 'ec_products.name as product_name', 'ec_products.description', 'ec_order_product.price')
            ->get();

        $totalQuantity = $orderProducts->sum('qty');
        $totalAmount = $orderProducts->sum(function ($product) {
            return $product->qty * $product->price;
        });

        if(!empty($order->tax_amount) ||$order->tax_amount != 0 || $order->tax_amount != null ){
            $totalAmount =  $totalAmount +$order->tax_amount;
        }



        // Fetch SteadFast consignment details from the related SteadFast model
        $steadfastDetails = DB::table('steadfasts')->where('order_id', $order->id)->first();

        $qrCode = '';
        $barcodeImage = '';

        if (!empty($steadfastDetails->steadfast_consignment_id) || $steadfastDetails->steadfast_consignment_id != null || $steadfastDetails->steadfast_consignment_id != ''){
           // Generate QR Code
           $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($steadfastDetails->steadfast_consignment_id);
            // $qrCodeImage = $qrWriter->writeString($steadfastDetails->steadfast_consignment_id);
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $barcode = base64_encode($barcodeGenerator->getBarcode($steadfastDetails->steadfast_consignment_id, $barcodeGenerator::TYPE_CODE_128));
            $barcodeImage = '<img src="data:image/png;base64,' . $barcode . '" alt="Barcode" />';
        }

       





        return [
            'order_id' => $order->id,
            'order_code' => $order->code,
            'order_date' => $order->created_at->format('Y-m-d'),
            'order_status' => $order->status,
            'order_payment_method' => $order->payment->payment_channel->label(),
            'order_payment_status' => $order->payment->status->label(),
            'order_products' => $orderProducts, // Pass products data to the view
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
            'tax_amount' => $order->tax_amount,
            'shipping_amount' => $order->shipping_amount,
            'order_note' => $order->description,
            'sub_total' => $order->sub_total,
            'steadfast_consignment_id' => $steadfastDetails->steadfast_consignment_id ?? 'N/A', 
            'created_at' => $steadfastDetails->created_at ,
            'steadfast_amount' => $steadfastDetails->steadfast_amount ?? 'N/A',
            'steadfast_is_sent' => $steadfastDetails->steadfast_is_sent ,
            'tracking_code' => $steadfastDetails->tracking_code ,
            'qr_code_image' =>  $qrCode,  // SVG QR code
            'barcode_image' => $barcodeImage // Base64-encoded barcode image
        ];
    }

    /**
     * Get the company address for invoicing.
     *
     * @return string
     */
    public function getCompanyAddress(): string
    {
        $country = EcommerceHelperFacade::getCountryNameById($this->getCompanyCountry());
        $state = $this->getCompanyState();
        $city = $this->getCompanyCity();
        $companyAddress = get_ecommerce_setting('company_address_for_invoicing');

        if (! $companyAddress) {
            $companyAddress = implode(', ', array_filter([
                get_ecommerce_setting('company_address_for_invoicing', get_ecommerce_setting('store_address')),
                $city,
                $state,
                $country,
            ]));
        }

        return $companyAddress;
    }

    // Helper methods for company details

    public function getCompanyCountry(): ?string
    {
        return get_ecommerce_setting('company_country_for_invoicing', get_ecommerce_setting('store_country'));
    }

    public function getCompanyState(): ?string
    {
        return get_ecommerce_setting('company_state_for_invoicing', get_ecommerce_setting('store_state'));
    }

    public function getCompanyCity(): ?string
    {
        return get_ecommerce_setting('company_city_for_invoicing', get_ecommerce_setting('store_city'));
    }
}
