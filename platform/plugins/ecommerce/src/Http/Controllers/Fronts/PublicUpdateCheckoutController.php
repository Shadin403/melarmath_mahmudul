<?php



namespace Botble\Ecommerce\Http\Controllers\Fronts;



use App\Models\DhakaArea;
use Botble\Base\Http\Controllers\BaseController;

use Botble\Ecommerce\Facades\Cart;

use Botble\Ecommerce\Facades\EcommerceHelper;

use Botble\Ecommerce\Facades\OrderHelper;

use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Services\HandleCheckoutOrderData;

use Botble\Ecommerce\Services\HandleTaxService;

use Botble\Payment\Enums\PaymentMethodEnum;

use Botble\Payment\Facades\PaymentMethods;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Http\Request;



class PublicUpdateCheckoutController extends BaseController
{

    public function __invoke(Request $request, HandleCheckoutOrderData $handleCheckoutOrderData)
    {

        // return $request->all();

        $sessionCheckoutData = OrderHelper::getOrderSessionData(

            $token = OrderHelper::getOrderSessionToken()

        );



        /**

         * @var Collection $products

         */

        $products = Cart::instance('cart')->products();



        $checkoutOrderData = $handleCheckoutOrderData->execute(

            $request,

            $products,

            $token,

            $sessionCheckoutData

        );



        app(HandleTaxService::class)->execute($products, $sessionCheckoutData);



        add_filter('payment_order_total_amount', function () use ($checkoutOrderData) {

            return $checkoutOrderData->orderAmount - $checkoutOrderData->paymentFee;

        }, 120);



        $hideCODPayment = $this->cartContainsOnlyDigitalProducts($products);



        if ($hideCODPayment) {

            PaymentMethods::excludeMethod(PaymentMethodEnum::COD);

        }




        $currentShippingAmount = $checkoutOrderData->shippingAmount;

        if (array_key_exists('is_out_side_dhaka', $request->address)) {

            $currentShippingAmount = GlobalOptionValue::where('option_id', 8)->first()->affect_price;
        }

        if (array_key_exists('is_inside_of_dhaka', $request->address) && $request->address['is_inside_of_dhaka'] != (null || '')) {
            // return $request->address;
            if(isset($request->address['inside_dhaka'])){
                $currentShippingAmount = DhakaArea::where('id', $request->address['inside_dhaka'])?->first()?->price ?? $checkoutOrderData->shippingAmount;
            }else{
                $currentShippingAmount = $checkoutOrderData->shippingAmount;
            }
            
        }



        $orderAmount = ($checkoutOrderData->orderAmount + $currentShippingAmount) - $checkoutOrderData->shippingAmount;


        return $this
            ->httpResponse()
            ->setData([

                'amount' => view('plugins/ecommerce::orders.partials.amount', [

                    'products' => $products,

                    'rawTotal' => $checkoutOrderData->rawTotal,

                    'orderAmount' => $orderAmount,

                    'shipping' => $checkoutOrderData->shipping,

                    'sessionCheckoutData' => $sessionCheckoutData,

                    'shippingAmount' => $currentShippingAmount,

                    'promotionDiscountAmount' => $checkoutOrderData->promotionDiscountAmount,

                    'couponDiscountAmount' => $checkoutOrderData->couponDiscountAmount,

                    'paymentFee' => $checkoutOrderData->paymentFee,

                ])->render(),

                'payment_methods' => view('plugins/ecommerce::orders.partials.payment-methods', [

                    'orderAmount' => $orderAmount,

                ])->render(),

                'shipping_methods' => view('plugins/ecommerce::orders.partials.shipping-methods', [

                    'shipping' => $checkoutOrderData->shipping,

                    'defaultShippingOption' => $checkoutOrderData->defaultShippingOption,

                    'defaultShippingMethod' => $checkoutOrderData->defaultShippingMethod,

                ])->render(),

                'checkout_button' => view('plugins/ecommerce::orders.partials.checkout-button')->render(),

                'checkout_warnings' => apply_filters('ecommerce_checkout_form_before', '', $products),

            ]);

    }



    protected function cartContainsOnlyDigitalProducts(Collection $products): bool
    {

        if (!EcommerceHelper::isEnabledSupportDigitalProducts()) {

            return false;

        }



        if ($products->isEmpty()) {

            return false;

        }



        $digitalProductsCount = EcommerceHelper::countDigitalProducts($products);


        return $digitalProductsCount > 0 && $digitalProductsCount === $products->count();

    }

}

