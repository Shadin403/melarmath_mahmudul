<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use App\Models\DhakaArea;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Rules\EmailRule;
use Botble\Ecommerce\AdsTracking\FacebookPixel;
use Botble\Ecommerce\AdsTracking\GoogleTagManager;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Enums\OrderHistoryActionEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Events\OrderProductCreatedEvent;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\Discount;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Forms\Fronts\CheckoutForm;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount as DiscountModel;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Models\Invoice;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleCheckoutOrderData;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Ecommerce\Services\HandleTaxService;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentFeeHelper;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class PublicCheckoutController extends BaseController
{
    public function __construct()
    {
        if (class_exists(OptimizerHelper::class)) {
            OptimizerHelper::disable();
        }
    }

    public function getCheckout(
        string $token,
        Request $request,
        HandleTaxService $handleTaxService,
        HandleCheckoutOrderData $handleCheckoutOrderData,
    ) {



        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = Order::query()->where(['token' => $token, 'is_finished' => false])->first();

            if (!$order) {
                return $this
                    ->httpResponse()
                    ->setNextUrl(BaseHelper::getHomepageUrl());
            }
        }

        if (
            !$request->session()->has('error_msg') &&
            $request->input('error') == 1 &&
            $request->input('error_type') == 'payment'
        ) {
            $message = $request->input('error_message') ?: __('Payment failed! Something wrong with your payment. Please try again.');

            $request->session()->flash('error_msg', $message);

            return redirect()->to(route('public.checkout.information', $token))->with('error_msg', $message);
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

        /**
         * @var Collection $products
         */
        $products = Cart::instance('cart')->products();
        if ($products->isEmpty()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('public.cart'));
        }

        foreach ($products as $product) {
            /**
             * @var Product $product
             */
            if ($product->isOutOfStock()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setNextUrl(route('public.cart'))
                    ->setMessage(
                        __('Product :product is out of stock!', ['product' => $product->original_product->name])
                    );
            }
        }

        if (
            EcommerceHelper::isEnabledSupportDigitalProducts()
            && !EcommerceHelper::canCheckoutForDigitalProducts($products)
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $handleTaxService->execute($products, $sessionCheckoutData);

        $sessionCheckoutData = $this->processOrderData($token, $sessionCheckoutData, $request);

        $isShowAddressForm = EcommerceHelper::isSaveOrderShippingAddress($products);

        $checkoutOrderData = $handleCheckoutOrderData->execute(
            $request,
            $products,
            $token,
            $sessionCheckoutData
        );

        $shipping = $checkoutOrderData->shipping;
        $defaultShippingMethod = $checkoutOrderData->defaultShippingMethod;
        $defaultShippingOption = $checkoutOrderData->defaultShippingOption;
        $promotionDiscountAmount = $checkoutOrderData->promotionDiscountAmount;
        $couponDiscountAmount = $checkoutOrderData->couponDiscountAmount;
        $shippingAmount = $checkoutOrderData->shippingAmount;
        $paymentFee = $checkoutOrderData->paymentFee;

        $data = compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData',
            'products',
            'isShowAddressForm',
            'paymentFee',
        );

        if (auth('customer')->check()) {
            $addresses = auth('customer')->user()->addresses;
            $isAvailableAddress = !$addresses->isEmpty();

            if (Arr::get($sessionCheckoutData, 'is_new_address')) {
                $sessionAddressId = 'new';
            } else {
                $sessionAddressId = Arr::get(
                    $sessionCheckoutData,
                    'address_id',
                    $isAvailableAddress ? $addresses->first()->id : null
                );
                if (!$sessionAddressId && $isAvailableAddress) {
                    $address = $addresses->firstWhere('is_default') ?: $addresses->first();
                    $sessionAddressId = $address->id;
                }
            }

            $data = array_merge($data, compact('addresses', 'isAvailableAddress', 'sessionAddressId'));
        }

        // @phpstan-ignore-next-line
        $discountsQuery = DiscountModel::query()
            ->where('type', DiscountTypeEnum::COUPON)
            ->where('display_at_checkout', true)
            ->active()
            ->available();

        $discounts = apply_filters('ecommerce_checkout_discounts_query', $discountsQuery, $products)->get();

        $rawTotal = $checkoutOrderData->rawTotal;
        $orderAmount = $checkoutOrderData->orderAmount;

        $data = [...$data, 'discounts' => $discounts, 'rawTotal' => $rawTotal, 'orderAmount' => $orderAmount];

        $productsArray = $products->all();

        app(GoogleTagManager::class)->beginCheckout($productsArray, $orderAmount);
        app(FacebookPixel::class)->checkout($productsArray, $orderAmount);

        $checkoutView = Theme::getThemeNamespace('views.ecommerce.orders.checkout');

        if (view()->exists($checkoutView)) {
            return view($checkoutView, $data);
        }

        add_filter('payment_order_total_amount', function () use ($orderAmount, $paymentFee) {
            return $orderAmount - $paymentFee;
        }, 120);

        return view(
            'plugins/ecommerce::orders.checkout',
            ['orderAmount' => $orderAmount, 'checkoutForm' => CheckoutForm::createFromArray($data)]
        );
    }

    protected function processOrderData(
        string $token,
        array $sessionData,
        Request $request,
        bool $finished = false
    ): array {

        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->boolean(
                'billing_address_same_as_shipping_address'
            );
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->has('address.address_id')) {
            $sessionData['is_new_address'] = $request->input('address.address_id') == 'new';
        }






        if ($request->input('address', [])) {
            // ব্যবহারকারী লগইন করা না থাকলেই কেবল এই প্রক্রিয়াটি চলবে
            if (!auth('customer')->check()) {
                // ধাপ ১: ইনপুট ডেটা ভ্যালিডেট এবং প্রস্তুত করা
                $addressData = $request->input('address');
                $email = !empty($addressData['email']) ? $addressData['email'] : 'demo@gmail.com';
                $phone = $addressData['phone'] ?? null;

                // ন্যূনতম প্রয়োজনীয় ডেটা ভ্যালিডেশন
                $validator = Validator::make($addressData, [
                    'name' => ['required', 'min:3', 'max:120'],
                    'phone' => ['required'],
                ]);

                if ($validator->passes()) {
                    // ধাপ ২: ইমেল বা ফোন নম্বর দিয়ে বিদ্যমান গ্রাহক খোঁজা
                    $customer = null;
                    if ($phone) {
                        $customer = Customer::query()
                            ->where('email', $email)
                            ->orWhere('phone', $phone)
                            ->first();
                    }

                    // ধাপ ৩: যদি গ্রাহক বিদ্যমান থাকে, তাকে লগইন করানো
                    if ($customer) {
                        auth('customer')->login($customer);
                    } else {
                        // ধাপ ৪: যদি গ্রাহক বিদ্যমান না থাকে, নতুন অ্যাকাউন্ট তৈরি করা
                        try {
                            // পাসওয়ার্ড নির্ধারণ: ব্যবহারকারী দিলে সেটি, না দিলে ফোন নম্বর
                            $password = ($request->input('create_account') == 1 && $request->filled('password'))
                                ? $request->input('password')
                                : $phone;

                            // পাসওয়ার্ড ভ্যালিডেশন (যদি create_account চেক করা থাকে)
                            if ($request->input('create_account') == 1) {
                                $passwordValidator = Validator::make($request->input(), [
                                    'password' => ['required', 'min:6'],
                                    'password_confirmation' => ['required', 'same:password'],
                                ]);

                                if ($passwordValidator->fails()) {
                                    // এখানে আপনি ভ্যালিডেশন ত্রুটি হ্যান্ডেল করতে পারেন, যেমন একটি exception throw করা বা redirect করা
                                    // আপাতত আমরা পরবর্তী ধাপে যাওয়া থেকে বিরত থাকব
                                    return back()->withErrors($passwordValidator)->withInput();
                                }
                            }

                            $newCustomer = Customer::query()->create([
                                'name' => $addressData['name'],
                                'email' => $email,
                                'phone' => $phone,
                                'password' => Hash::make($password),
                            ]);

                            // নতুন গ্রাহককে লগইন করানো
                            auth('customer')->login($newCustomer);

                            // Registration event ফায়ার করা
                            event(new Registered($newCustomer));

                            $sessionData['created_account'] = true;
                        } catch (Throwable $exception) {
                            BaseHelper::logError($exception);
                        }
                    }
                }
            }

            // ধাপ ৫: ঠিকানা তৈরি করা (লগইন করা ব্যবহারকারীর জন্য)
            // এই অংশটি নতুন বা বিদ্যমান, উভয় গ্রাহকের জন্য কাজ করবে যারা এইমাত্র লগইন করেছে
            if (auth('customer')->check()) {
                $customerId = auth('customer')->id();
                $customer = auth('customer')->user();

                // যদি গ্রাহকের কোনো ঠিকানা না থাকে বা নতুন ঠিকানা যোগ করতে চায়
                if ($customer->addresses->count() == 0 || $request->input('address.address_id') === 'new' || !$request->input('address.address_id')) {
                    $customerAddress = $request->address;
                    $is_inside_dhaka = null;
                    $inside_dhaka = null;
                    if (isset($customerAddress['is_inside_of_dhaka'])) {
                        // $is_inside_dhaka = GlobalOptionValue::where('id', $customerAddress['is_inside_of_dhaka'])?->first()?->option_value;
                        $is_inside_dhaka = $customerAddress['is_inside_of_dhaka'];
                    }

                    if (isset($customerAddress['inside_dhaka'])) {
                        // $inside_dhaka = DhakaArea::where('id', $customerAddress['inside_dhaka'])?->first()?->name;
                        $inside_dhaka = $customerAddress['inside_dhaka'];
                    }

                    // Convert checkbox value to proper boolean/integer
                    $isOutsideDhaka = isset($customerAddress['is_out_side_dhaka']) &&
                        ($customerAddress['is_out_side_dhaka'] === 'on' ||
                            $customerAddress['is_out_side_dhaka'] === '1' ||
                            $customerAddress['is_out_side_dhaka'] === 1) ? 1 : 0;
                    $address = Address::query()->create([
                        // array_merge($request->input('address'), [
                        //     'customer_id' => $customerId,
                        //     'is_default' => true, // প্রথম ঠিকানা সবসময় ডিফল্ট হবে
                        // ])
                        'name' => $customerAddress['name'],
                        'email' => isset($customerAddress['email']) ? $customerAddress['email'] : null,
                        'phone' => isset($customerAddress['phone']) ? $customerAddress['phone'] : null,
                        'country' => isset($customerAddress['country']) ? $customerAddress['country'] : null,
                        'state' => isset($customerAddress['state']) ? $customerAddress['state'] : null,
                        'city' => isset($customerAddress['city']) ? $customerAddress['city'] : null,
                        'address' => isset($customerAddress['address']) ? $customerAddress['address'] : null,
                        'customer_id' => $customerId,
                        'is_default' => $customer->addresses->count() == 0,
                        'zip_code' => isset($customerAddress['zip_code']) ? $customerAddress['zip_code'] : null,
                        'is_inside_dhaka' => $is_inside_dhaka,
                        'inside_dhaka' => $inside_dhaka,
                        'is_out_side_dhaka' => $isOutsideDhaka,
                        'courier_option' => isset($customerAddress['courier_option']) ? $customerAddress['courier_option'] : null,
                    ]);

                    // রিকোয়েস্ট এবং সেশনে নতুন ঠিকানা আইডি যোগ করা
                    $request->merge(['address.address_id' => $address->getKey()]);
                    $sessionData['address_id'] = $address->getKey();
                }
            }
        }






        // if ($request->input('address', [])) {
        //     // ব্যবহারকারী লগইন করা না থাকলেই কেবল এই প্রক্রিয়াটি চলবে
        //     if (!auth('customer')->check()) {
        //         $checkoutAddressData = $request->input('address');
        //         $checkoutEmail = !empty($checkoutAddressData['email']) ? $checkoutAddressData['email'] :  $checkoutAddressData['phone'].'$request->getHost()';
        //         $checkoutPhone = $checkoutAddressData['phone'] ?? null;

        //         // ন্যূনতম প্রয়োজনীয় ডেটা ভ্যালিডেশন
        //         $validator = Validator::make($checkoutAddressData, [
        //             'name' => ['required', 'min:3', 'max:120'],
        //             'phone' => ['required'],
        //         ]);

        //         if ($validator->passes()) {
        //             // ধাপ ১: ইমেল বা ফোন নম্বর দিয়ে বিদ্যমান গ্রাহক খোঁজা
        //             $customer = null;
        //             if ($checkoutPhone) {
        //                 // যদি প্রদত্ত ইমেল অথবা ফোন নম্বরের কোনো একটি মিলে যায়
        //                 $customer = Customer::query()
        //                     ->where('email', $checkoutEmail)
        //                     ->orWhere('phone', $checkoutPhone) // ফোন নম্বর দিয়েও খুঁজবে
        //                     ->first();
        //             }

        //             // ধাপ ২: যদি গ্রাহক বিদ্যমান থাকে, তাকে লগইন করানো
        //             if ($customer) {
        //                 auth('customer')->login($customer);
        //             } else {
        //                 // ধাপ ৩: যদি গ্রাহক বিদ্যমান না থাকে, নতুন অ্যাকাউন্ট তৈরি করা
        //                 try {
        //                     $password = ($request->input('create_account') == 1 && $request->filled('password'))
        //                         ? $request->input('password')
        //                         : $checkoutPhone;

        //                     $newCustomer = Customer::query()->create([
        //                         'name'     => $checkoutAddressData['name'],
        //                         'email'    => $checkoutEmail,
        //                         'phone'    => $checkoutPhone,
        //                         'password' => Hash::make($password),
        //                     ]);

        //                     auth('customer')->login($newCustomer);
        //                     event(new Registered($newCustomer));
        //                     $sessionData['created_account'] = true;

        //                 } catch (Throwable $exception) {
        //                     BaseHelper::logError($exception);
        //                 }
        //             }
        //         }
        //     }

        //     // ধাপ ৪: ঠিকানা তৈরি করা (লগইন করা ব্যবহারকারীর জন্য)
        //     // এই অংশটি নতুন বা বিদ্যমান, উভয় গ্রাহকের জন্য কাজ করবে
        //     if (auth('customer')->check()) {
        //         $customer = auth('customer')->user();

        //         // সর্বদা চেকআউট পেজে দেওয়া তথ্য দিয়ে একটি নতুন ঠিকানা তৈরি করুন
        //         // এটি নিশ্চিত করে যে অর্ডারের জন্য ব্যবহৃত ঠিকানাটি সর্বদা আপ-টু-ডেট
        //         $address = Address::query()->create(
        //             array_merge($request->input('address'), [
        //                 'customer_id' => $customer->getKey(),
        //                 'is_default'  => $customer->addresses->count() == 0, // প্রথম ঠিকানা হলে ডিফল্ট হবে
        //             ])
        //         );

        //         $request->merge(['address.address_id' => $address->getKey()]);
        //         $sessionData['address_id'] = $address->getKey();
        //     }
        // }

        $address = null;

        if (($addressId = $request->input('address.address_id')) && $addressId !== 'new') {
            $address = Address::query()->find($addressId);
            if ($address) {
                $sessionData['address_id'] = $address->getKey();
            }
        } elseif (auth('customer')->check() && !Arr::get($sessionData, 'address_id')) {
            $address = Address::query()->where([
                'customer_id' => auth('customer')->id(),
                'is_default' => true,
            ])->first();

            if ($address) {
                $sessionData['address_id'] = $address->id;
            }
        }

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get(
                $sessionData,
                'billing_address_same_as_shipping_address',
                true
            ),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (!empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'address' => $address->address,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ($addressFromInput = (array) $request->input('address', [])) {
            $addressData = $addressFromInput;
        }

        $addressData = OrderHelper::cleanData($addressData);

        $sessionData = array_merge($sessionData, $addressData);

        Cart::instance('cart')->refresh();

        $products = Cart::instance('cart')->products();

        if (is_plugin_active('marketplace')) {
            $sessionData = apply_filters(
                HANDLE_PROCESS_ORDER_DATA_ECOMMERCE,
                $products,
                $token,
                $sessionData,
                $request
            );

            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }

        if (!isset($sessionData['created_order'])) {
            $currentUserId = 0;
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->id();
            }

            $request->merge([
                'amount' => Cart::instance('cart')->rawTotal(),
                'user_id' => $currentUserId,
                'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
                'shipping_option' => $request->input('shipping_option'),
                'shipping_amount' => 0,
                'tax_amount' => Cart::instance('cart')->rawTax(),
                'sub_total' => Cart::instance('cart')->rawSubTotal(),
                'coupon_code' => session('applied_coupon_code'),
                'discount_amount' => 0,
                'status' => OrderStatusEnum::PENDING,
                'is_finished' => false,
                'token' => $token,
            ]);

            /**
             * @var Order $order
             */
            $order = Order::query()->where(compact('token'))->first();

            $order = $this->createOrderFromData($request->input(), $order);

            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->getKey();
        }

        if (!empty($address)) {
            $addressData['order_id'] = $sessionData['created_order_id'];
        } elseif ((array) $request->input('address', [])) {
            $addressData = array_merge(
                ['order_id' => $sessionData['created_order_id']],
                (array) $request->input('address', [])
            );
        }

        $sessionData['is_save_order_shipping_address'] = EcommerceHelper::isSaveOrderShippingAddress($products);

        $sessionData = OrderHelper::checkAndCreateOrderAddress($addressData, $sessionData);

        if (!isset($sessionData['created_order_product'])) {
            $weight = Cart::instance('cart')->weight();

            OrderProduct::query()->where(['order_id' => $sessionData['created_order_id']])->delete();

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = Product::query()->find($cartItem->id);

                if (!$product) {
                    continue;
                }

                if ($product->is_variation && $product->variationInfo) {
                    $subtitle = $product->variationInfo->variation_title;
                } else {
                    $subtitle = $cartItem->options['sub_title'] ?? $product->original_product->sub_title;
                }
                $data = [
                    'order_id' => $sessionData['created_order_id'],
                    'product_id' => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'product_image' => $cartItem->options['image'],
                    'product_sub_title' => $subtitle,
                    'qty' => $cartItem->qty,
                    'weight' => $weight,
                    'price' => $cartItem->price,
                    'tax_amount' => $cartItem->tax,
                    'options' => $cartItem->options,
                    'product_type' => $product->product_type,
                ];

                if (isset($cartItem->options['options'])) {
                    $data['product_options'] = $cartItem->options['options'];
                }

                OrderProduct::query()->create($data);
            }

            $sessionData['created_order_product'] = Cart::instance('cart')->getLastUpdatedAt();
        }

        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    public function postSaveInformation(
        string $token,
        SaveCheckoutInformationRequest $request,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService
    ) {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if ($token !== session('tracked_start_checkout')) {
            $order = Order::query()->where(['token' => $token, 'is_finished' => false])->first();

            if (!$order) {
                return $this
                    ->httpResponse()
                    ->setNextUrl(BaseHelper::getHomepageUrl());
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

            $sessionData = apply_filters(
                PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE,
                $sessionData,
                $request,
                $token
            );

            foreach ($sessionData['marketplace'] as $storeData) {
                if (!empty($storeData['created_order_id'])) {
                    $order = Order::query()
                        ->where('id', $storeData['created_order_id'])
                        ->first();

                    if ($order && $order->shipping_amount != Arr::get($storeData, 'shipping_amount', 0)) {
                        $order->update(['shipping_amount' => Arr::get($storeData, 'shipping_amount', 0)]);
                    }
                }
            }
        } else {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));
            OrderHelper::setOrderSessionData($token, $sessionData);
            if (session()->has('applied_coupon_code')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
                if (!$discount) {
                    $removeCouponService->execute();
                }
            }

            if (!empty($sessionData['created_order_id'])) {
                $order = Order::query()
                    ->where('id', $sessionData['created_order_id'])
                    ->first();

                if ($order && $order->shipping_amount != Arr::get($sessionData, 'shipping_amount', 0)) {
                    $order->update(['shipping_amount' => Arr::get($sessionData, 'shipping_amount', 0)]);
                }
            }
        }

        $sessionData = $this->processOrderData($token, $sessionData, $request);

        return $this
            ->httpResponse()
            ->setData($sessionData);
    }

    public function postCheckout(
        string $token,
        CheckoutRequest $request,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {

        if (!($request->input('address.is_inside_of_dhaka') || $request->input('address.is_out_side_dhaka'))) {
            $request->validate([
                'address.is_inside_of_dhaka' => 'required',
                'address.is_out_side_dhaka' => 'required'
            ], [
                'required' => 'All Field is Required'
            ]);
        }

        // $request->validate([
        //     'address.is_inside_of_dhaka' => 'required',
        //     'is_out_side_dhaka' => 'required'
        // ]);

        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if (Cart::instance('cart')->isEmpty()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('No products in cart'));
        }

        $products = Cart::instance('cart')->products();


        if (
            EcommerceHelper::isEnabledSupportDigitalProducts() &&
            !EcommerceHelper::canCheckoutForDigitalProducts($products)
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $totalQuality = Cart::instance('cart')->rawTotalQuantity();

        if (
            ($minimumQuantity = EcommerceHelper::getMinimumOrderQuantity()) > 0
            && $totalQuality < $minimumQuantity
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Minimum order quantity is :qty, you need to buy more :more to place an order!', [
                        'qty' => $totalQuality,
                        'more' => $minimumQuantity - $totalQuality,
                    ])
                );
        }

        if (
            ($maximumQuantity = EcommerceHelper::getMaximumOrderQuantity()) > 0
            && $totalQuality > $maximumQuantity
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Maximum order quantity is :qty, please check your cart and retry again!', [
                        'qty' => $maximumQuantity,
                    ])
                );
        }

        if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Minimum order amount is :amount, you need to buy more :more to place an order!', [
                        'amount' => format_price(EcommerceHelper::getMinimumOrderAmount()),
                        'more' => format_price(
                            EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal()
                        ),
                    ])
                );
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $sessionData = $this->processOrderData($token, $sessionData, $request, true);

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Product :product is out of stock!', ['product' => $product->original_product->name])
                    );
            }

            $quantityOfProduct = Cart::instance('cart')->rawQuantityByItemId($product->id);

            if ($product->minimum_order_quantity > 0 && $quantityOfProduct < $product->minimum_order_quantity) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Minimum order quantity of product :product is :quantity, you need to buy more :more to place an order! ', [
                            'product' => BaseHelper::clean($product->original_product->name),
                            'quantity' => $product->minimum_order_quantity,
                            'more' => $product->minimum_order_quantity - $quantityOfProduct,
                        ])
                    );
            }

            if ($product->maximum_order_quantity > 0 && $quantityOfProduct > $product->maximum_order_quantity) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Maximum order quantity of product :product is :quantity! ', [
                            'product' => $product->original_product->name,
                            'quantity' => $product->maximum_order_quantity,
                        ])
                    );
            }
        }

        $paymentMethod = $request->input('payment_method', session('selected_payment_method'));

        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        try {
            do_action('ecommerce_post_checkout', $products, $request, $token, $sessionData);
        } catch (Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage());
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(
                HANDLE_PROCESS_POST_CHECKOUT_ORDER_DATA_ECOMMERCE,
                $products,
                $request,
                $token,
                $sessionData,
                $this->httpResponse()
            );
        }

        $promotionDiscountAmount = $handleApplyPromotionsService->execute($token);
        $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');
        $rawTotal = Cart::instance('cart')->rawTotal();
        $orderAmount = max($rawTotal - $promotionDiscountAmount - $couponDiscountAmount, 0);

        $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);
        $shippingMethodInput = $request->input('shipping_method', ShippingMethodEnum::DEFAULT);

        $shippingAmount = 0;
        $shippingData = [];
        if ($isAvailableShipping) {
            $origin = EcommerceHelper::getOriginAddress();
            $shippingData = EcommerceHelper::getShippingData(
                $products,
                $sessionData,
                $origin,
                $orderAmount,
                $paymentMethod
            );

            $shippingMethodData = $shippingFeeService->execute(
                $shippingData,
                $shippingMethodInput,
                $request->input('shipping_option')
            );

            $shippingMethod = Arr::first($shippingMethodData);
            if (!$shippingMethod) {
                throw ValidationException::withMessages([
                    'shipping_method' => trans(
                        'validation.exists',
                        ['attribute' => trans('plugins/ecommerce::shipping.shipping_method')]
                    ),
                ]);
            }

            $shippingAmount = Arr::get($shippingMethod, 'price', 0);

            if (get_shipping_setting('free_ship', $shippingMethodInput)) {
                $shippingAmount = 0;
            }
        }

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute();
            } else {
                $shippingAmount = Arr::get($sessionData, 'is_free_shipping') ? 0 : $shippingAmount;
            }
        }

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }

        $orderAmount += (float) $shippingAmount;

        // Add payment fee if applicable
        $paymentFee = 0;
        if ($paymentMethod && is_plugin_active('payment')) {
            $paymentFee = PaymentFeeHelper::calculateFee($paymentMethod, $orderAmount);
            $orderAmount += $paymentFee;
        }

        // Store payment fee in request
        $request->merge(['payment_fee' => $paymentFee]);

        $request->merge([
            'amount' => $orderAmount ?: 0,
            'currency' => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id' => $currentUserId,
            'shipping_method' => $isAvailableShipping ? $shippingMethodInput : '',
            'shipping_option' => $isAvailableShipping ? $request->input('shipping_option') : null,
            'shipping_amount' => (float) $shippingAmount,
            'payment_fee' => (float) $paymentFee,
            'tax_amount' => Cart::instance('cart')->rawTax(),
            'sub_total' => Cart::instance('cart')->rawSubTotal(),
            'coupon_code' => session('applied_coupon_code'),
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status' => OrderStatusEnum::PENDING,
            'token' => $token,
        ]);

        /**
         * @var Order $order
         */
        $order = Order::query()->where(compact('token'))->first();

        $order = $this->createOrderFromData($request->all(), $order);

        OrderHistory::query()->create([
            'action' => OrderHistoryActionEnum::CREATE_ORDER_FROM_PAYMENT_PAGE,
            'description' => __('Order was created from checkout page'),
            'order_id' => $order->getKey(),
        ]);

        if ($isAvailableShipping && !Shipment::query()->where(['order_id' => $order->getKey()])->exists()) {
            Shipment::query()->create([
                'order_id' => $order->getKey(),
                'user_id' => 0,
                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                'cod_amount' => (is_plugin_active(
                    'payment'
                ) && $order->payment->id && $order->payment->status != PaymentStatusEnum::COMPLETED) ? $order->amount : 0,
                'cod_status' => ShippingCodStatusEnum::PENDING,
                'type' => $order->shipping_method,
                'status' => ShippingStatusEnum::PENDING,
                'price' => $order->shipping_amount,
                'rate_id' => $shippingData ? Arr::get($shippingMethod, 'id', '') : '',
                'shipment_id' => $shippingData ? Arr::get($shippingMethod, 'shipment_id', '') : '',
                'shipping_company_name' => $shippingData ? Arr::get($shippingMethod, 'company_name', '') : '',
            ]);
        }

        if (
            EcommerceHelper::isDisplayTaxFieldsAtCheckoutPage() &&
            $request->boolean('with_tax_information')
        ) {
            $order->taxInformation()->create($request->input('tax_information'));
        }

        if ($appliedCouponCode = session('applied_coupon_code')) {
            Discount::getFacadeRoot()->afterOrderPlaced($appliedCouponCode);
        }

        OrderProduct::query()->where(['order_id' => $order->getKey()])->delete();

        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = Product::query()->find($cartItem->id);

            if (!$product) {
                continue;
            }

            if ($product->is_variation && $product->variationInfo) {
                $subtitle = $product->variationInfo->variation_title;
            } else {
                $subtitle = $cartItem->options['sub_title'] ?? $product->original_product->sub_title;
            }
            $data = [
                'order_id' => $order->getKey(),
                'product_id' => $cartItem->id,
                'product_name' => $cartItem->name,
                'product_image' => $cartItem->options['image'],
                'product_sub_title' => $subtitle,
                'qty' => $cartItem->qty,
                'weight' => Arr::get($cartItem->options, 'weight', 0),
                'price' => $cartItem->price,
                'tax_amount' => $cartItem->tax,
                'options' => $cartItem->options,
                'product_type' => $product->product_type,
            ];

            if (isset($cartItem->options['options'])) {
                $data['product_options'] = $cartItem->options['options'];
            }

            /**
             * @var OrderProduct $orderProduct
             */
            $orderProduct = OrderProduct::query()->create($data);

            OrderProductCreatedEvent::dispatch($orderProduct);
            do_action('ecommerce_after_each_order_product_created', $orderProduct);
        }

        $request->merge(['order_id' => $order->getKey()]);

        do_action('ecommerce_before_processing_payment', $products, $request, $token, $sessionData);

        if (!is_plugin_active('payment') || !$orderAmount) {
            OrderHelper::processOrder($order->getKey());

            return redirect()->to(route('public.checkout.success', OrderHelper::getOrderSessionToken()));
        }

        $paymentData = [
            'error' => false,
            'message' => false,
            'amount' => (float) format_price($order->amount, null, true),
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        $paymentData = apply_filters(FILTER_ECOMMERCE_PROCESS_PAYMENT, $paymentData, $request);

        if ($checkoutUrl = Arr::get($paymentData, 'checkoutUrl')) {
            return $this
                ->httpResponse()
                ->setError($paymentData['error'])
                ->setNextUrl($checkoutUrl)
                ->setData(['checkoutUrl' => $checkoutUrl])
                ->withInput()
                ->setMessage($paymentData['message']);
        }

        if ($paymentData['error'] || !$paymentData['charge_id']) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL($token))
                ->withInput()
                ->setMessage($paymentData['message'] ?: __('Checkout error!'));
        }

        return $this
            ->httpResponse()
            ->setNextUrl(PaymentHelper::getRedirectURL($token))
            ->setMessage(__('Checkout successfully!'));
    }

    public function getCheckoutSuccess(Request $request, string $token)
    {
        $customer = auth('customer')->user();
        $customerAddress = Address::query()->where('customer_id', $customer->id)->latest()->first();
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        /**
         * @var Order $order
         */
        $order = Order::query()
            ->where('token', $token)
            ->with(['address', 'products', 'taxInformation'])
            ->latest('id')
            ->firstOrFail();
        $invoice = Invoice::query()->where('reference_id', operator: $order->id)->firstOrFail();
        $orderAddress = OrderAddress::where('order_id', $order->id)->firstOrFail();
        $order->delivery_charge_status = $orderAddress->is_out_side_dhaka;
        $invoice->is_out_side_dhaka = $orderAddress->is_out_side_dhaka ? 1 : 0;
        if ($orderAddress->is_inside_of_dhaka != null) {
            $invoice->is_inside_dhaka = GlobalOptionValue::where('id', $orderAddress->is_inside_of_dhaka)->first()->option_value;
            $invoice->inside_dhaka = DhakaArea::where('id', $orderAddress->inside_dhaka)->first()->name;
        }
        if ($customerAddress) {
            $customerAddress->qr_code = $orderAddress->qr_code;
            $customerAddress->update();
        }
        $invoice->qr_code = $orderAddress->qr_code;
        $invoice->order_code = str_replace('#', '', $order->code);

        // Process payment data from request
        if ($request->query('payment') && $request->query('payment') == 'success') {
            // Handle payment success
            if (is_plugin_active('payment')) {
                // Get payment data from request
                $paymentMethod = $request->query('paymentMethod', 'sobkichubazarpay');
                $transactionId = $request->query('transactionId');
                $paymentAmount = $request->query('paymentAmount', $order->amount);

                // Check if there's already a payment record for this order
                $existingPayment = Payment::where('order_id', $order->id)->first();

                if ($existingPayment) {
                    // Update existing payment record
                    $existingPayment->amount = $paymentAmount;
                    $existingPayment->currency = 'BDT';
                    $existingPayment->charge_id = $transactionId;
                    $existingPayment->payment_channel = $paymentMethod;
                    $existingPayment->status = PaymentStatusEnum::COMPLETED;
                    $existingPayment->save();

                    // Update order payment ID
                    $order->payment_id = $existingPayment->id;
                    $order->save();
                } else {
                    // Store payment information
                    $paymentData = [
                        'amount' => $paymentAmount,
                        'currency' => 'BDT', // Assuming BDT currency
                        'charge_id' => $transactionId,
                        'order_id' => $order->id,
                        'customer_id' => $customer->id,
                        'customer_type' => get_class($customer),
                        'payment_channel' => $paymentMethod,
                        'status' => PaymentStatusEnum::COMPLETED,
                    ];

                    // Use PaymentHelper to store payment
                    $payment = PaymentHelper::storeLocalPayment($paymentData);

                    // Update order payment ID if payment was created
                    if ($payment) {
                        $order->payment_id = $payment->id;
                        $order->save();
                    }
                }
            }

            // Handle delivery charge payment
            if ($orderAddress->is_out_side_dhaka != null) {
                $invoice->is_paid_delivery_charge = 1;
                $payment = Payment::where('order_id', $orderAddress->order_id)->first();
                if ($payment) {
                    if ($orderAddress->pay_delevery_charge == 'helf_payment') {
                        $payment->advance_payment = GlobalOptionValue::where('option_id', 8)->first()->affect_price;
                    } else if ($orderAddress->pay_delevery_charge == 'full_payment') {
                        $payment->status = PaymentStatusEnum::COMPLETED;
                    }
                    $payment->update();
                }
            }
        }

        $invoice->update();
        $order->update();
        if (session('tracked_start_checkout')) {
            app(GoogleTagManager::class)->purchase($order);
            app(FacebookPixel::class)->purchase($order);
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_SUCCESS_IN_ORDER, $token, $this->httpResponse());
        }

        $products = $order->getOrderProducts();

        OrderHelper::clearSessions($token);

        return view('plugins/ecommerce::orders.thank-you', compact('order', 'products'));
    }

    public function postApplyCoupon(ApplyCouponRequest $request, HandleApplyCouponService $handleApplyCouponService)
    {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        $result = [
            'error' => false,
            'message' => '',
        ];

        if (is_plugin_active('marketplace')) {
            $result = apply_filters(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, $result, $request);
        } else {
            $result = $handleApplyCouponService->execute($request->input('coupon_code'));
        }

        if ($result['error']) {
            return $this
                ->httpResponse()
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $this
            ->httpResponse()
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function postRemoveCoupon(Request $request, HandleRemoveCouponService $removeCouponService)
    {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();
            $result = apply_filters(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, $products, $request);
        } else {
            $result = $removeCouponService->execute();
        }

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }

            return $this
                ->httpResponse()
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function getCheckoutRecover(string $token, Request $request)
    {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, $token, $request);
        }

        $order = Order::query()
            ->where([
                'token' => $token,
                'is_finished' => false,
            ])
            ->with(['products', 'address'])
            ->firstOrFail();

        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            session(['tracked_start_checkout' => $token]);
            $sessionCheckoutData = [
                'name' => $order->address->name,
                'email' => $order->address->email,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'country' => $order->address->country,
                'state' => $order->address->state,
                'city' => $order->address->city,
                'zip_code' => $order->address->zip_code,
                'shipping_method' => $order->shipping_method,
                'shipping_option' => $order->shipping_option,
                'shipping_amount' => $order->shipping_amount,
            ];
        }

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $request->merge(['qty' => $orderProduct->qty]);

            /**
             * @var Product $product
             */
            $product = Product::query()->find($orderProduct->product_id);

            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    protected function createOrderFromData(array $data, ?Order $order): Order|null|false
    {
        return OrderHelper::createOrUpdateIncompleteOrder($data, $order);
    }
}
