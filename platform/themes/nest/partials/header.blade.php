@php
    $headerStyle = theme_option('header_style') ?: '';
    $page = Theme::get('page');
    if ($page) {
        $headerStyle = $page->getMetaData('header_style', true) ?: $headerStyle;
    }
    $headerStyle = $headerStyle && in_array($headerStyle, array_keys(get_layout_header_styles())) ? $headerStyle : '';
@endphp

<header class="header-area header-style-1 header-height-2 {{ $headerStyle }}">
    @if (theme_option('mobile-header-message'))
        <div class="mobile-promotion">
            {!! BaseHelper::clean(theme_option('mobile-header-message')) !!}
        </div>
    @endif
    <div class="header-top header-top-ptb-1 d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-3 col-lg-6">
                    <div class="header-info">
                        {!! Menu::renderMenuLocation('header-navigation', [
                            'view' => 'header-menu',
                        ]) !!}
                    </div>
                </div>
                <div class="col-xl-5 d-none d-xl-block">
                    <div class="text-center">
                        @if (theme_option('header_messages') && ($headerMessages = json_decode(theme_option('header_messages'), true)))
                            <div id="news-flash" class="d-inline-block">
                                <ul>
                                    @foreach ($headerMessages as $headerMessage)
                                        @if (count($headerMessage) == 4)
                                            <li @if (!$loop->first) style="display: none" @endif>
                                                @if ($headerMessage[0]['value'])
                                                    {!! BaseHelper::renderIcon($headerMessage[0]['value'], null, ['class' => 'd-inline-block mr-5']) !!}
                                                @endif

                                                @if ($headerMessage[1]['value'])
                                                    <span class="d-inline-block">
                                                        {!! BaseHelper::clean($headerMessage[1]['value']) !!}
                                                    </span>
                                                @endif
                                                @if ($headerMessage[2]['value'] && $headerMessage[3]['value'])
                                                    <a class="active d-inline-block"
                                                        href="{{ url($headerMessage[2]['value']) }}">&nbsp;{!! BaseHelper::clean($headerMessage[3]['value']) !!}</a>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                @php $currencies = is_plugin_active('ecommerce') ? get_all_currencies() : []; @endphp
                <div class="col-xl-4 col-lg-6">
                    <div class="header-info header-info-right">
                        <ul>
                            @if ($hotline = theme_option('hotline'))
                                <li>{{ __('Need help? Call Us:') }} &nbsp;<strong class="text-brand">
                                        {{ $hotline }}</strong></li>
                            @endif

                            @if (is_plugin_active('language'))
                                {!! Theme::partial('language-switcher') !!}
                            @endif

                            @if (count($currencies) > 1)
                                <li>
                                    <a class="language-dropdown-active"
                                        href="javascript:void(0)">{{ get_application_currency()->title }} <i
                                            class="fi-rs-angle-small-down"></i></a>
                                    <ul class="language-dropdown">
                                        @foreach ($currencies as $currency)
                                            <li><a
                                                    href="{{ route('public.change-currency', $currency->title) }}">{{ $currency->title }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div @class([
        'header-middle header-middle-ptb-1 d-none d-lg-block',
        'sticky-bar' =>
            theme_option('enabled_sticky_header', 'yes') == 'yes' &&
            theme_option('sticky_header_content_position', 'creative_header') ==
                ('middle' || 'creative_header'),
    ])>
        <div class="container">
            <div class="header-wrap">
                <div class="logo logo-width-1">
                    <a href="{{ BaseHelper::getHomepageUrl() }}">
                        {!! Theme::getLogoImage(['style' => 'max-height: 55px']) !!}
                    </a>
                </div>

                <div class="header-right" style="flex-grow: 1; justify-content: flex-end;">
                    <div class="delivery-location-info mr-30 d-none d-lg-block"
                        style="margin-right: 116px !important; position: relative; left: 88px;">
                        @if (Session::has('user_selected_location'))
                            <a href="javascript:void(0)" onclick="openLocationModal()" class="location-badge">
                                <div class="icon-box" style="position: relative; left: 34px;">
                                    <i class="fi-rs-marker"></i>
                                </div>
                                <div class="text-box">
                                    <span class="label" style="text-align: center;">{{ __('Delivery:') }}</span>
                                    <span class="value"
                                        style="text-align: center;">{{ Session::get('user_selected_location')['area_name'] }}</span>
                                </div>
                                {{-- <i class="fi-rs-angle-small-down ms-auto"></i> --}}
                            </a>
                        @else
                            <a href="javascript:void(0)" onclick="openLocationModal()" class="location-badge">
                                <div class="icon-box" style="position: relative; left: 34px;">
                                    <i class="fi-rs-marker"></i>
                                </div>
                                <div class="text-box">
                                    <span class="label" style="text-align: center;">{{ __('Delivery:') }}</span>
                                    <span class="value" style="text-align: center;">{{ __('Select Location') }}</span>
                                </div>
                            </a>
                        @endif
                    </div>

                    <div class="search-container d-flex align-items-center" style="flex-grow: 1; max-width: 900px;">
                        @if (is_plugin_active('ecommerce'))
                            <div class="search-style-2 flex-grow-1 me-2">
                                <form action="{{ route('public.products') }}" class="form--quick-search"
                                    data-ajax-url="{{ route('public.ajax.search-products') }}" method="GET"
                                    style="background: #fff; border: 1px solid #BCE3C9; border-radius: 5px; height: 50px; display: flex; align-items: center;">
                                    @if (theme_option('enabled_product_categories_on_search_keyword_box', 'yes') !== 'no')
                                        <div class="form-group--icon position-relative"
                                            style="border-right: 1px solid #BCE3C9;">
                                            <select class="product-category-select" name="categories[]"
                                                aria-label="{{ __('Select category') }}"
                                                style="border: none; height: 48px; padding: 0 15px; background: transparent;">
                                                <option value="">{{ __('All Categories') }}</option>
                                                {!! ProductCategoryHelper::renderProductCategoriesSelect() !!}
                                            </select>
                                        </div>
                                    @endif
                                    <input type="text" class="input-search-product" name="q"
                                        placeholder="{{ __('Search for items...') }}"
                                        value="{{ BaseHelper::stringify(request()->input('q')) }}" autocomplete="off"
                                        style="border: none; height: 48px; padding: 0 15px; width: 100%;">
                                    <button class="btn" type="submit" aria-label="{{ __('Submit') }}"
                                        style="background: transparent; border: none; padding: 0 15px;">
                                        <i class="fi-rs-search" style="font-size: 20px; color: #253D4E;"></i>
                                    </button>
                                    <div class="panel--search-result"></div>
                                </form>
                            </div>

                            <div class="search-style-2 vendor-search-box flex-grow-1">
                                <form action="#" class="form--quick-search-vendor" method="GET"
                                    style="position: relative; background: #fff; border: 1px solid #BCE3C9; border-radius: 5px; height: 50px; display: flex; align-items: center;">
                                    <input type="text" class="input-search-vendor" name="q"
                                        placeholder="{{ __('Search for vendors...') }}" autocomplete="off"
                                        style="border: none; height: 48px; padding: 0 15px; width: 100%; border-radius: 5px;">
                                    <button class="btn" type="button"
                                        style="background: transparent; border: none; padding: 0 15px;">
                                        <i class="fi-rs-search" style="font-size: 20px; color: #253D4E;"></i>
                                    </button>
                                    <div class="panel--search-result-vendor"
                                        style="position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #ececec; border-top: none; z-index: 999; display: none; max-height: 300px; overflow-y: auto; border-radius: 0 0 10px 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <style>
                    .location-badge {
                        /* display: flex;
                        align-items: center;
                        background-color: #e8f6ea; */
                        /* Light green background */
                        /* padding: 8px 15px;
                        border-radius: 50px;
                        border: 1px solid #BCE3C9; */
                        transition: all 0.3s ease;
                        min-width: 200px;

                    }

                    .location-badge:hover {
                        background-color: #fff;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                    }

                    .location-badge .icon-box {
                        width: 35px;
                        height: 35px;
                        background-color: #3BB77E;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-right: 10px;
                    }

                    .location-badge .icon-box i {
                        color: #fff;
                        font-size: 18px;
                    }

                    .location-badge .text-box {
                        display: flex;
                        flex-direction: column;
                        line-height: 1.2;
                        margin-right: 10px;
                    }

                    .location-badge .text-box .label {
                        font-size: 11px;
                        color: #7E7E7E;
                    }

                    .location-badge .text-box .value {
                        font-size: 14px;
                        font-weight: 700;
                        color: #3BB77E;
                    }

                    .search-style-2 form input {
                        font-size: 14px;
                    }

                    .search-style-2 form input::placeholder {
                        color: #838383;
                    }

                    .logo.logo-width-1 {
                        margin-right: -156px !important;
                        width: 486px !important;
                    }

                    @media (max-width: 767px) {
                        .logo.logo-width-1 {
                            margin-right: 70px !important;
                            width: auto !important;
                        }
                    }
                </style>

                <div class="header-action-right">
                    <div class="header-action-2">
                        @if (EcommerceHelper::isCompareEnabled())
                            <div class="header-action-icon-2">
                                <a href="{{ route('public.compare') }}">
                                    <img class="svgInject" alt="{{ __('Compare') }}"
                                        src="{{ Theme::asset()->url('imgs/theme/icons/icon-compare.svg') }}" />
                                    <span
                                        class="pro-count blue compare-count">{{ Cart::instance('compare')->count() }}</span>
                                </a>
                                {{-- <a href="{{ route('public.compare') }}"></a> --}}
                            </div>
                        @endif
                        @if (EcommerceHelper::isWishlistEnabled())
                            <div class="header-action-icon-2">
                                <a href="{{ route('public.wishlist') }}">
                                    <img class="svgInject" alt="{{ __('Wishlist') }}"
                                        src="{{ Theme::asset()->url('imgs/theme/icons/icon-heart.svg') }}" />
                                    <span class="pro-count blue wishlist-count">
                                        @if (auth('customer')->check())
                                            {{ auth('customer')->user()->wishlist()->count() }}
                                        @else
                                            {{ Cart::instance('wishlist')->count() }}
                                        @endif
                                    </span>
                                </a>
                                {{-- <a href="{{ route('public.wishlist') }}"><span
                                        class="lable">{{ __('Wishlist') }}</span></a> --}}
                            </div>
                        @endif

                        @if (EcommerceHelper::isCartEnabled())
                            <div class="header-action-icon-2">
                                <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                                    <img alt="{{ __('Cart') }}"
                                        src="{{ Theme::asset()->url('imgs/theme/icons/icon-cart.svg') }}" />
                                    <span class="pro-count blue">{{ Cart::instance('cart')->count() }}</span>
                                </a>
                                <a href="{{ route('public.cart') }}"><span
                                        class="lable">{{ __('Cart') }}</span></a>
                                <div class="cart-dropdown-wrap cart-dropdown-hm2 cart-dropdown-panel">
                                    {!! Theme::partial('cart-panel') !!}
                                </div>
                            </div>
                        @endif
                        <div class="header-action-icon-2">
                            <a href="{{ route('customer.overview') }}">
                                <img class="svgInject rounded-circle" alt="{{ __('Account') }}"
                                    src="{{ auth('customer')->check() ? auth('customer')->user()->avatar_url : Theme::asset()->url('imgs/theme/icons/icon-user.svg') }}" />
                            </a>
                            <a href="{{ route('customer.overview') }}"><span
                                    class="lable me-1">{{ auth('customer')->check() ? Str::limit(auth('customer')->user()->name, 10) : __('Account') }}</span></a>
                            <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                <ul>
                                    @if (auth('customer')->check())
                                        <li><a href="{{ route('customer.overview') }}"><i
                                                    class="fi fi-rs-user mr-10"></i>{{ __('My Account') }}</a>
                                        </li>
                                        @if (EcommerceHelper::isOrderTrackingEnabled())
                                            <li><a href="{{ route('public.orders.tracking') }}"><i
                                                        class="fi fi-rs-location-alt mr-10"></i>{{ __('Order Tracking') }}</a>
                                            </li>
                                        @endif
                                        @if (EcommerceHelper::isWishlistEnabled())
                                            <li><a href="{{ route('public.wishlist') }}"><i
                                                        class="fi fi-rs-heart mr-10"></i>{{ __('My Wishlist') }}</a>
                                            </li>
                                        @endif
                                        <li><a href="{{ route('customer.edit-account') }}"><i
                                                    class="fi fi-rs-settings-sliders mr-10"></i>{{ __('Update profile') }}</a>
                                        </li>
                                        <li><a href="{{ route('customer.logout') }}"><i
                                                    class="fi fi-rs-sign-out mr-10"></i>{{ __('Sign out') }}</a>
                                        </li>
                                    @else
                                        <li><a href="{{ route('customer.login') }}"><i
                                                    class="fi fi-rs-user mr-10"></i>{{ __('Login') }}</a>
                                        </li>
                                        <li><a href="{{ route('customer.register') }}"><i
                                                    class="fi fi-rs-user-add mr-10"></i>{{ __('Register') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div @class([
        'header-bottom header-bottom-bg-color',
        'sticky-bar' =>
            theme_option('enabled_sticky_header', 'yes') == 'yes' &&
            theme_option('sticky_header_content_position', 'bottom') ==
                ('bottom' || 'creative_header'),
    ]) id="hideHeaderOnMobile">
        <div class="container">
            <div class="header-wrap header-space-between position-relative" id="headerCustomize">

                <div class="logo logo-width-1 d-block d-lg-none">
                    <a href="{{ BaseHelper::getHomepageUrl() }}">
                        {!! Theme::getLogoImage(['style' => 'max-height: 55px']) !!}
                    </a>
                </div>

                <div class="header-nav d-none d-lg-flex">
                    @if (is_plugin_active('ecommerce') && theme_option('enabled_browse_categories_on_header', 'yes') == 'yes')
                        <div class="main-categories-wrap d-none d-lg-block">
                            <a class="categories-button-active" href="#">
                                <span class="fi-rs-apps"></span> {!! BaseHelper::clean(__('<span class="et">Browse</span> All Categories')) !!}
                                <i class="fi-rs-angle-down"></i>
                            </a>
                            <div class="categories-dropdown-wrap categories-dropdown-active-large font-heading"
                                style="top: 29px; left: -3px;">
                                @php
                                    $categories = ProductCategoryHelper::getProductCategoriesWithUrl(
                                        [],
                                        ['is_featured' => true, 'parent_id' => 0],
                                    );
                                @endphp

                                <div class="d-flex categories-dropdown-inner"
                                    style="margin-top: -24px; margin-left: -12px;">
                                    {!! Theme::partial('product-categories-dropdown', ['categories' => $categories, 'more' => false]) !!}
                                </div>
                                @if (count($categories) > 10)
                                    <div class="more_slide_open" style="display: none">
                                        <div class="d-flex categories-dropdown-inner">
                                            {!! Theme::partial('product-categories-dropdown', ['categories' => $categories, 'more' => true]) !!}
                                        </div>
                                    </div>
                                @endif
                                @if (count($categories) > 10)
                                    <div class="more_categories" data-text-show-more="{{ __('Show more...') }}"
                                        data-text-show-less="{{ __('Show less...') }}"><span class="icon"></span>
                                        <span class="heading-sm-1">{{ __('Show more...') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block font-heading">
                        <nav>
                            {!! Menu::renderMenuLocation('main-menu', [
                                'view' => 'main-menu',
                            ]) !!}
                        </nav>
                    </div>
                </div>
                @if ($hotline = theme_option('hotline'))
                    <div class="hotline d-none d-lg-flex">
                        <img src="{{ Theme::asset()->url('imgs/theme/icons/icon-headphone.svg') }}" alt="hotline" />
                        <p>{{ $hotline }}<span>{{ theme_option('hotline_subtitle_text') ?: __('24/7 Support Center') }}</span>
                        </p>
                    </div>
                @endif
                <div class="header-action-icon-2 d-block d-lg-none">
                    <div class="burger-icon burger-icon-white">
                        <span class="burger-icon-top"></span>
                        <span class="burger-icon-mid"></span>
                        <span class="burger-icon-bottom"></span>
                    </div>
                </div>
                @if (is_plugin_active('ecommerce'))
                    <div class="header-action-right d-block d-lg-none">
                        <div class="header-action-2">
                            @if (EcommerceHelper::isCompareEnabled())
                                <div class="header-action-icon-2">
                                    <a href="{{ route('public.compare') }}">
                                        <img alt="{{ __('Compare') }}"
                                            src="{{ Theme::asset()->url('imgs/theme/icons/icon-compare.svg') }}" />
                                        <span
                                            class="pro-count white compare-count">{{ Cart::instance('compare')->count() }}</span>
                                    </a>
                                </div>
                            @endif
                            @if (EcommerceHelper::isWishlistEnabled())
                                <div class="header-action-icon-2">
                                    <a href="{{ route('public.wishlist') }}">
                                        <img alt="{{ __('Wishlist') }}"
                                            src="{{ Theme::asset()->url('imgs/theme/icons/icon-heart.svg') }}" />
                                        <span class="pro-count white wishlist-count">
                                            @if (auth('customer')->check())
                                                {{ auth('customer')->user()->wishlist()->count() }}
                                            @else
                                                {{ Cart::instance('wishlist')->count() }}
                                            @endif
                                        </span>
                                    </a>
                                </div>
                            @endif
                            @if (EcommerceHelper::isCartEnabled())
                                <div class="header-action-icon-2">
                                    <a class="mini-cart-icon" href="#">
                                        <img alt="{{ __('Cart') }}"
                                            src="{{ Theme::asset()->url('imgs/theme/icons/icon-cart.svg') }}" />
                                        <span class="pro-count white">{{ Cart::instance('cart')->count() }}</span>
                                    </a>
                                    <div class="cart-dropdown-wrap cart-dropdown-hm2 cart-dropdown-panel">
                                        {!! Theme::partial('cart-panel') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
<div style="width: 80%;" class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-top">
            <div class="mobile-header-logo">
                <a href="{{ BaseHelper::getHomepageUrl() }}">
                    {!! Theme::getLogoImage(['style' => 'max-height: 55px']) !!}
                </a>
            </div>
            <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                <button class="close-style search-close">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="mobile-header-content-area">
            <div class="mobile-location-info mobile-header-border" style="margin-bottom: 20px; padding-bottom: 20px;">
                @if (Session::has('user_selected_location'))
                    <a href="javascript:void(0)" onclick="openLocationModal()" class="location-badge"
                        style="display: flex; align-items: center; gap: 10px; width: 100%;">
                        <div class="icon-box"
                            style="background: #e8f6ea; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fi-rs-marker" style="color: #3BB77E; font-size: 18px;"></i>
                        </div>
                        <div class="text-box" style="display: flex; flex-direction: column;">
                            <span class="label"
                                style="font-size: 12px; color: #7e7e7e;">{{ __('Delivery:') }}</span>
                            <span class="value"
                                style="font-weight: bold; color: #3BB77E;">{{ Session::get('user_selected_location')['area_name'] }}</span>
                        </div>
                        <i class="fi-rs-angle-small-down ms-auto"></i>
                    </a>
                @else
                    <a href="javascript:void(0)" onclick="openLocationModal()" class="location-badge"
                        style="display: flex; align-items: center; gap: 10px; width: 100%;">
                        <div class="icon-box"
                            style="background: #e8f6ea; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fi-rs-marker" style="color: #3BB77E; font-size: 18px;"></i>
                        </div>
                        <div class="text-box" style="display: flex; flex-direction: column;">
                            <span class="label"
                                style="font-size: 12px; color: #7e7e7e;">{{ __('Delivery:') }}</span>
                            <span class="value"
                                style="font-weight: bold; color: #3BB77E;">{{ __('Select Location') }}</span>
                        </div>
                        <i class="fi-rs-angle-small-down ms-auto"></i>
                    </a>
                @endif
            </div>
            @if (is_plugin_active('ecommerce') && theme_option('enabled_product_categories_on_search_keyword_box', 'yes') !== 'no')
                <div class="mobile-search search-style-3 mobile-header-border">
                    <form action="{{ route('public.products') }}" class="form--quick-search"
                        data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                        <input type="text" name="q" class="input-search-product"
                            placeholder="{{ __('Search for items...') }}"
                            value="{{ BaseHelper::stringify(request()->input('q')) }}" autocomplete="off">
                        <button type="submit"><i class="fi-rs-search"></i></button>
                        <div class="panel--search-result"></div>
                    </form>
                </div>
            @endif

            @if (is_plugin_active('marketplace'))
                <div class="mobile-search search-style-3 mobile-header-border" style="margin-top: 10px;">
                    <form action="#" class="form--quick-search-vendor-mobile" method="GET"
                        style="position: relative; background: #fff; border: 1px solid #BCE3C9; border-radius: 5px; height: 45px; display: flex; align-items: center;">
                        <input type="text" class="input-search-vendor-mobile" name="q"
                            placeholder="{{ __('Search for vendors...') }}" autocomplete="off"
                            style="border: none; height: 43px; padding: 0 15px; width: 100%; border-radius: 5px;">
                        <button class="btn" type="button"
                            style="background: transparent; border: none; padding: 0 15px;">
                            <i class="fi-rs-search" style="font-size: 18px; color: #253D4E;"></i>
                        </button>
                        <div class="panel--search-result-vendor-mobile"
                            style="position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #ececec; border-top: none; z-index: 999; display: none; max-height: 300px; overflow-y: auto; border-radius: 0 0 10px 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                        </div>
                    </form>
                </div>
            @endif

            @if (is_plugin_active('ecommerce'))
                <div class="mobile-menu-wrap mobile-header-border">
                    <div class="main-categories-wrap">
                        <a class="categories-button-active-2 active" href="#">
                            <i class="fi-rs-apps"></i> {{ __('Browse Categories') }} <i class="fi-rs-angle-down"></i>
                        </a>
                        <div style="display: block !important;"
                            class="categories-dropdown-wrap categories-dropdown-active-small">
                            <ul class="categories-dropdown">
                                @php
                                    $allCategories = ProductCategoryHelper::getProductCategoriesWithUrl(
                                        [],
                                        ['is_featured' => true],
                                    );

                                    $categoriesById = [];
                                    foreach ($allCategories as $category) {
                                        $categoriesById[$category->id] = $category;
                                        $category->children = [];
                                    }

                                    $categoryTree = [];
                                    foreach ($allCategories as $category) {
                                        if ($category->parent_id && isset($categoriesById[$category->parent_id])) {
                                            $categoriesById[$category->parent_id]->children[] = $category;
                                        } else {
                                            $categoryTree[] = $category;
                                        }
                                    }
                                @endphp

                                @foreach ($categoryTree as $category)
                                    @include('partials.mobile-category-item', ['category' => $category])
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- শুধুমাত্র এই CSS অংশটুকু আপনার পুরোনোটা দিয়ে বদলে দিন --}}
                <style>
                    /* এই একটি লাইনেই পরিবর্তন করা হয়েছে */
                    .categories-dropdown li {
                        /* এখানে '>' চিহ্নটি সরিয়ে দেওয়া হয়েছে */
                        display: block !important;
                        flex-wrap: wrap;
                        width: 100%;
                    }

                    /* ----- আপনার বাকি CSS কোড অপরিবর্তিত আছে ----- */
                    .category-item-container {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        width: 100%;
                    }

                    .category-item-container a {
                        display: flex;
                        align-items: center;
                        flex-grow: 1;
                    }

                    .category-item-container a img,
                    .category-item-container a i {
                        margin-right: 15px;
                    }

                    .menu-expand {
                        padding: 10px;
                        cursor: pointer;
                        margin-right: 5px;
                    }

                    .menu-expand i {
                        transition: transform 0.3s ease;
                    }

                    .menu-expand.open i {
                        transform: rotate(90deg);
                    }

                    .sub-menu {
                        list-style: none;
                        padding-left: 0;
                        margin-left: 0;
                        width: 100%;
                        overflow: hidden;
                        height: 0;
                        transition: height 0.4s ease;
                    }

                    .sub-menu .category-item-container a {
                        padding-left: 25px !important;
                    }

                    .sub-menu .sub-menu .category-item-container a {
                        padding-left: 50px !important;
                    }

                    .header-style-1 .header-middle-ptb-1 {
                        padding: 0px 0 !important;
                    }
                </style>

                {{-- আপনার JavaScript কোড অপরিবর্তিত আছে --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const expandButtons = document.querySelectorAll('.categories-dropdown .menu-expand');

                        expandButtons.forEach(button => {
                            button.addEventListener('click', function(e) {
                                e.preventDefault();

                                const li = this.closest('li');
                                const subMenu = li.querySelector('.sub-menu');
                                if (!subMenu) return;

                                this.classList.toggle('open');

                                if (subMenu.style.height && subMenu.style.height !== '0px') {
                                    subMenu.style.height = subMenu.scrollHeight + 'px';
                                    requestAnimationFrame(() => {
                                        subMenu.style.height = '0';
                                    });
                                    setTimeout(() => {
                                        subMenu.style.display = 'none';
                                    }, 400);
                                } else {
                                    subMenu.style.display = 'block';
                                    subMenu.style.height = 'auto';
                                    const height = subMenu.scrollHeight + 'px';
                                    subMenu.style.height = '0';
                                    requestAnimationFrame(() => {
                                        subMenu.style.transition = 'height 0.4s ease';
                                        subMenu.style.height = height;
                                    });
                                    setTimeout(() => {
                                        subMenu.style.height = 'auto';
                                    }, 400);
                                }
                            });
                        });
                    });
                </script>
            @endif

            <div class="mobile-menu-wrap mobile-header-border">
                <!-- mobile menu start -->
                <nav>
                    {!! Menu::renderMenuLocation('main-menu', [
                        'options' => ['class' => 'mobile-menu'],
                        'view' => 'mobile-menu',
                    ]) !!}
                </nav>
                <!-- mobile menu end -->
            </div>

            <div class="mobile-header-info-wrap">

                @if (is_plugin_active('language'))
                    <div class="single-mobile-header-info">
                        <a class="mobile-language-active" href="javascript:void(0)"><i class="fi-rs-globe"></i>
                            {{ __('Language') }} <span><i class="fi-rs-angle-down"></i></span></a>
                        <div class="lang-curr-dropdown lang-dropdown-active">
                            <ul>
                                @php
                                    $showRelated = setting(
                                        'language_show_default_item_if_current_version_not_existed',
                                        true,
                                    );
                                @endphp

                                @foreach (Language::getSupportedLocales() as $localeCode => $properties)
                                    <li><a rel="alternate" hreflang="{{ $localeCode }}"
                                            href="{{ $showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode) }}">{!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                                            {{ $properties['lang_name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (count($currencies) > 1)
                    <div class="single-mobile-header-info">
                        <a class="mobile-language-active" href="javascript:void(0)"><i class="fi-rs-money"></i>
                            {{ __('Currency') }} <span><i class="fi-rs-angle-down"></i></span></a>
                        <div class="lang-curr-dropdown lang-dropdown-active">
                            <ul>
                                @foreach ($currencies as $currency)
                                    <li><a
                                            href="{{ route('public.change-currency', $currency->title) }}">{{ $currency->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (is_plugin_active('ecommerce'))
                    @if (EcommerceHelper::isOrderTrackingEnabled())
                        <div class="single-mobile-header-info">
                            <a href="{{ route('public.orders.tracking') }}"><i class="fi-rs-shopping-cart"></i>
                                {{ __('Order tracking') }}</a>
                        </div>
                    @endif

                    @if (EcommerceHelper::isCompareEnabled())
                        <div class="single-mobile-header-info">
                            <a href="{{ route('public.compare') }}"><i class="fi-rs-refresh"></i>
                                {{ __('Compare') }}</a>
                        </div>
                    @endif
                    <div class="single-mobile-header-info">
                        <a href="{{ route('customer.login') }}"><i class="fi-rs-user"></i>
                            {{ __('Log In / Sign Up') }}</a>
                    </div>
                @endif
                @if ($hotline = theme_option('hotline'))
                    <div class="single-mobile-header-info">
                        <a href="tel:{{ $hotline }}"><i class="fi-rs-headphones"></i> {{ $hotline }}</a>
                    </div>
                @endif
            </div>
            @if ($socialLinks = theme_option('social_links'))
                <div class="mobile-social-icon mb-50">
                    <p class="mb-15 font-heading h6 me-2">{{ __('Follow Us') }}</p>
                    @foreach (json_decode($socialLinks, true) as $socialLink)
                        @if (count($socialLink) == 3)
                            <a href="{{ $socialLink[2]['value'] }}" title="{{ $socialLink[0]['value'] }}">
                                <img src="{{ RvMedia::getImageUrl($socialLink[1]['value']) }}"
                                    alt="{{ $socialLink[0]['value'] }}" />
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
            @if ($copyright = Theme::getSiteCopyright())
                <div class="site-copyright">{{ BaseHelper::clean($copyright) }}</div>
            @endif
        </div>
    </div>
</div>
{!! Theme::partial('location-modal') !!}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vendorInput = document.querySelector('.input-search-vendor');
        const vendorResult = document.querySelector('.panel--search-result-vendor');
        let timeout = null;

        console.log('Vendor search initialized:', vendorInput ? 'Input found' : 'Input NOT found');

        if (vendorInput) {
            vendorInput.addEventListener('input', function() {
                const query = this.value;
                clearTimeout(timeout);

                console.log('Vendor search query:', query);

                if (query.length < 2) {
                    vendorResult.style.display = 'none';
                    vendorResult.innerHTML = '';
                    return;
                }

                timeout = setTimeout(() => {
                    const url = "{{ route('public.ajax.search') }}?q=" + encodeURIComponent(
                        query);
                    console.log('Fetching from URL:', url);

                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Vendor search data:', data);
                            vendorResult.innerHTML = '';
                            if (data.data && data.data.length > 0) {
                                vendorResult.style.display = 'block';
                                const ul = document.createElement('ul');
                                ul.style.listStyle = 'none';
                                ul.style.padding = '0';
                                ul.style.margin = '0';

                                data.data.forEach(store => {
                                    const li = document.createElement('li');
                                    li.style.padding = '10px 20px';
                                    li.style.borderBottom = '1px solid #ececec';

                                    const areaBadge = store.is_from_user_area ?
                                        '<span style="background: #e8f6ea; color: #3BB77E; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 600; margin-left: 5px;"><i class="fi-rs-marker" style="font-size: 9px;"></i> {{ __('From your area') }}</span>' :
                                        '';

                                    li.innerHTML = `
                                        <a href="${store.url}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                                            <img src="${store.logo || '{{ Theme::asset()->url('imgs/theme/icons/icon-store.svg') }}'}" alt="${store.name}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
                                            <div style="flex: 1;">
                                                <div style="font-weight: bold; color: #3bb77e;">${store.name} ${areaBadge}</div>
                                                <div style="font-size: 12px; color: #7e7e7e;">
                                                    <span style="color: #ffb800;">★ ${store.rating}</span> • ${store.products_count} Products
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                    ul.appendChild(li);
                                });
                                vendorResult.appendChild(ul);
                                console.log('Vendor results displayed');
                            } else {
                                vendorResult.style.display = 'block';
                                vendorResult.innerHTML =
                                    '<div style="padding: 10px 20px; color: #7e7e7e;">{{ __('No vendors found') }}</div>';
                                console.log('No vendors found');
                            }
                        })
                        .catch(error => {
                            console.error('Vendor search error:', error);
                        });
                }, 500);
            });

            // Close on click outside
            document.addEventListener('click', function(e) {
                if (!vendorInput.contains(e.target) && !vendorResult.contains(e.target)) {
                    vendorResult.style.display = 'none';
                }
            });
        }

        // Mobile Vendor Search
        const vendorInputMobile = document.querySelector('.input-search-vendor-mobile');
        const vendorResultMobile = document.querySelector('.panel--search-result-vendor-mobile');

        if (vendorInputMobile && vendorResultMobile) {
            let vendorSearchTimeout;

            vendorInputMobile.addEventListener('input', function(e) {
                clearTimeout(vendorSearchTimeout);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    vendorResultMobile.style.display = 'none';
                    return;
                }

                vendorSearchTimeout = setTimeout(() => {
                    fetch("{{ route('public.ajax.search') }}?q=" + encodeURIComponent(
                            query), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            vendorResultMobile.innerHTML = '';
                            if (data.data && data.data.length > 0) {
                                vendorResultMobile.style.display = 'block';
                                const ul = document.createElement('ul');
                                ul.style.listStyle = 'none';
                                ul.style.padding = '0';
                                ul.style.margin = '0';

                                data.data.forEach(store => {
                                    const li = document.createElement('li');
                                    li.style.padding = '10px 20px';
                                    li.style.borderBottom = '1px solid #ececec';

                                    const areaBadge = store.is_from_user_area ?
                                        '<span style="background: #e8f6ea; color: #3BB77E; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 600; margin-left: 5px;"><i class="fi-rs-marker" style="font-size: 9px;"></i> {{ __('From your area') }}</span>' :
                                        '';

                                    li.innerHTML = `
                                        <a href="${store.url}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                                            <img src="${store.logo || '{{ Theme::asset()->url('imgs/theme/icons/icon-store.svg') }}'}" alt="${store.name}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
                                            <div style="flex: 1;">
                                                <div style="font-weight: bold; color: #3bb77e;">${store.name} ${areaBadge}</div>
                                                <div style="font-size: 12px; color: #7e7e7e;">
                                                    <span style="color: #ffb800;">★ ${store.rating}</span> • ${store.products_count} Products
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                    ul.appendChild(li);
                                });
                                vendorResultMobile.appendChild(ul);
                            } else {
                                vendorResultMobile.style.display = 'block';
                                vendorResultMobile.innerHTML =
                                    '<div style="padding: 10px 20px; color: #7e7e7e;">{{ __('No vendors found') }}</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Mobile vendor search error:', error);
                        });
                }, 500);
            });

            // Close on click outside
            document.addEventListener('click', function(e) {
                if (!vendorInputMobile.contains(e.target) && !vendorResultMobile.contains(e.target)) {
                    vendorResultMobile.style.display = 'none';
                }
            });
        }
    });
</script>
