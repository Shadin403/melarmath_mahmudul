@if ($product)
    <div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn"
        @if (isset($loop)) data-wow-delay="{{ ($loop->index + 1) / 10 }}s" @endif>

        <div class="product-img-action-wrap">
            <div class="product-img product-img-zoom">
                <a href="{{ $product->url }}">
                    <img class="default-img"
                        src="{{ RvMedia::getImageUrl($product->image, 'product-thumb', false, RvMedia::getDefaultImage()) }}"
                        alt="{{ $product->name }}">
                    <img class="hover-img"
                        src="{{ RvMedia::getImageUrl(Arr::get($product->images, 1, $product->image), 'product-thumb', false, RvMedia::getDefaultImage()) }}"
                        alt="{{ $product->name }}">
                </a>
            </div>
            <div class="product-action-1">
                @php
                    $actionMaxWidth = 40;
                    if (EcommerceHelper::isWishlistEnabled()) {
                        $actionMaxWidth += 36;
                    }
                    if (EcommerceHelper::isCompareEnabled()) {
                        $actionMaxWidth += 40;
                    }
                @endphp
                <div class="product-action-wrap" style="max-width: {{ $actionMaxWidth }}px !important;">
                    <a aria-label="{{ __('Quick View') }}" href="#"
                        class="action-btn hover-up js-quick-view-button"
                        data-url="{{ route('public.ajax.quick-view', $product->id) }}">
                        <i class="fi-rs-eye"></i>
                    </a>
                    @if (EcommerceHelper::isWishlistEnabled())
                        <a aria-label="{{ __('Add To Wishlist') }}" href="#"
                            class="action-btn hover-up js-add-to-wishlist-button"
                            data-url="{{ route('public.wishlist.add', $product->id) }}">
                            <i class="fi-rs-heart"></i>
                        </a>
                    @endif
                    @if (EcommerceHelper::isCompareEnabled())
                        <a aria-label="{{ __('Add To Compare') }}" href="#"
                            class="action-btn hover-up js-add-to-compare-button"
                            data-url="{{ route('public.compare.add', $product->id) }}">
                            <i class="fi-rs-shuffle"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="product-badges product-badges-position product-badges-mrg">
                @if ($product->isOutOfStock())
                    <span class="bg-dark" style="font-size: 11px;">{{ __('Out Of Stock') }}</span>
                @else
                    @if ($product->productLabels->isNotEmpty())
                        @foreach ($product->productLabels as $label)
                            <span {!! $label->css_styles !!}>{{ $label->name }}</span>
                        @endforeach
                    @else
                        @if (!EcommerceHelper::hideProductPrice() || EcommerceHelper::isCartEnabled())
                            @if ($product->front_sale_price !== $product->price)
                                <span
                                    class="hot">{{ get_sale_percentage($product->price, $product->front_sale_price) }}</span>
                            @endif
                        @endif
                    @endif
                @endif
            </div>
        </div>

        <div class="product-content-wrap">
            @if ($category = $product->categories->sortByDesc('id')->first())
                <div class="product-category">
                    <a href="{{ $category->url }}">{!! BaseHelper::clean($category->name) !!}</a>
                </div>
            @endif

            <h2 class="text-truncate"><a href="{{ $product->url }}"
                    title="{{ $product->name }}">{{ $product->name }}</a></h2>
            @if ($product?->sub_title)
                <small class="product-subtitle">{!! highlightProductText($product->sub_title) !!}</small>
            @endif

            @if (!$product?->sub_title)
                <div style="margin-top: 55px">

                </div>
            @endif

            @if (EcommerceHelper::isReviewEnabled() && $product->reviews_count)
                <div class="product-rate-cover">
                    <div class="product-rate d-inline-block">
                        <div class="product-rating" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                    </div>
                    <span class="font-small ml-5 text-muted">({{ $product->reviews_count }})</span>
                </div>
            @endif

            @if (is_plugin_active('marketplace') && $product->store->id)
                <div class="text-truncate">
                    <span class="font-small text-muted">{{ __('Sold By') }} <a
                            href="{{ $product->store->url }}">{!! BaseHelper::clean($product->store->name) !!}</a></span>
                </div>
            @endif

            <div class="product-card-bottom">
                <div class="product-price">
                    {!! apply_filters('ecommerce_before_product_price_in_listing', null, $product) !!}
                    @include(Theme::getThemeNamespace('views.ecommerce.includes.product-price'))
                    {!! apply_filters('ecommerce_after_product_price_in_listing', null, $product) !!}
                </div>

                @if (EcommerceHelper::isCartEnabled())
                    <div class="add-cart">
                        <a aria-label="{{ __('Add To Cart') }}" class="action-btn js-quick-view-button add"
                            data-url="{{ route('public.ajax.quick-view', $product->id) }}" href="#">
                            <i class="fi-rs-shopping-cart mr-5"></i>
                            <span class="d-inline-block">{{ __('Add') }}</span>
                        </a>
                    </div>

                    <div class="buy-now-wrap" style="width: 100%; margin-top: 2px;">
                        @if ($product->variations->isNotEmpty())
                            <a aria-label="{{ __('Buy Now') }}" class="action-btn buy-now-btn js-quick-view-button"
                                data-url="{{ route('public.ajax.quick-view', $product->id) }}" href="#"
                                style="width: 100%; background-color: #176131; color: #fff; font-weight: 700; border-radius: 5px; padding: 10px 0; border: none; font-size: 14px; transition: all .3s; display: flex; justify-content: center; align-items: center;">
                                <i class="fi-rs-bolt mr-5"></i> {{ __('Buy Now') }}
                            </a>
                        @else
                            <form class="buy-now-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="checkout" value="true">
                                <button type="submit" class="action-btn buy-now-btn"
                                    style="width: 100%; background-color: #176131; color: #fff; font-weight: 700; border-radius: 5px; padding: 10px 0; border: none; font-size: 14px; transition: all .3s; display: flex; justify-content: center; align-items: center;">
                                    <i class="fi-rs-bolt mr-5"></i> {{ __('Buy Now') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* highlight subtitle */
        .highlight-text {
            color: red;
            font-weight: bold;
            white-space: normal;
        }

        .product-subtitle {
            font-size: 16px;
            color: #5a97fa;
            font-weight: bold;
            white-space: normal;
        }

        /* Price & Button Layout - Modified to support full width button */
        .product-card-bottom {
            display: flex;
            flex-direction: column;
            /* Stack price and button */
            align-items: flex-start;
            margin-top: 15px;
            min-height: auto;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #3BB77E;
            margin-bottom: 10px;
            width: 100%;
        }

        /* Add to Cart Button (Yellow, Full Width) - KEPT FROM NEW DESIGN */
        .add-cart {
            width: 100%;
        }

        .add-cart .action-btn.add {
            width: 100%;

            /* Yellow background */
            color: #253D4E;
            /* Dark text */
            font-weight: 700;
            border-radius: 5px;
            padding: 10px 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            font-size: 14px;
            transition: all .3s;
        }

        .add-cart .action-btn.add:hover {
            background-color: #253D4E;
            color: #fff;
            transform: translateY(-2px);
        }

        .add-cart .action-btn.add i {
            margin-right: 8px;
            font-size: 16px;
        }

        /* Desktop Hover Logic for Button */
        @media (min-width: 577px) {

            .add-cart,
            .buy-now-wrap {
                opacity: 0;
                visibility: hidden;
                transform: translateY(20px);
                transition: all 0.3s ease;
                height: 0;
                margin-top: 0;
                overflow: hidden;
            }

            .product-cart-wrap:hover .add-cart {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                height: 45px;
                margin-top: 0;
            }

            .product-cart-wrap:hover .buy-now-wrap {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                height: 45px;
                margin-top: 10px;
            }
        }

        /* Mobile View */
        @media (max-width: 576px) {
            .product-card-bottom {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                min-height: 120px;
                /* Increased height for both buttons */
            }

            .add-cart,
            .buy-now-wrap {
                opacity: 1;
                visibility: visible;
                height: auto;
                margin-top: 0;
            }

            .buy-now-wrap {
                margin-top: 5px;
            }
        }

        /* row gap fix */
        .row {
            row-gap: 20px;
        }
    </style>
@endif
