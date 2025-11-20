@php
    $userLocation = Session::get('user_selected_location');
    $userAreaId = $userLocation['area_id'] ?? null;

    $matchingCount = 0;
    $totalCount = $products->count();

    if ($userAreaId) {
        foreach ($products as $product) {
            if ($product->delivery_areas) {
                $deliveryAreas = is_string($product->delivery_areas)
                    ? json_decode($product->delivery_areas, true)
                    : $product->delivery_areas;
                $deliveryAreas = \Illuminate\Support\Arr::flatten($deliveryAreas ?? []);
                if (in_array((string) $userAreaId, $deliveryAreas)) {
                    $matchingCount++;
                }
            }
        }
    }
@endphp

@if ($products->isNotEmpty())
    @if ($userAreaId && $totalCount > 0)
        <div class="panel__header" style="padding: 10px 15px; background: #f7f8f9; border-bottom: 1px solid #ececec;">
            <span style="color: #3BB77E; font-weight: 600;">আপনার এলাকায় {{ $matchingCount }}টি প্রোডাক্ট পাওয়া
                গেছে</span>
            <span style="color: #7e7e7e;"> • মোট {{ $totalCount }}টি</span>
        </div>
    @endif

    <div class="panel__content ">
        <div class="row py-2 mx-0 ">
            @foreach ($products as $product)
                @php
                    $hasDelivery = false;
                    if ($userAreaId && $product->delivery_areas) {
                        $deliveryAreas = is_string($product->delivery_areas)
                            ? json_decode($product->delivery_areas, true)
                            : $product->delivery_areas;
                        $deliveryAreas = \Illuminate\Support\Arr::flatten($deliveryAreas ?? []);
                        $hasDelivery = in_array((string) $userAreaId, $deliveryAreas);
                    }
                @endphp
                <div class="col-12 px-1 px-md-2 py-1 product-cart-wrap border-0 rounded-0">
                    <div class="row mx-md-2 gx-md-2 gx-1">
                        <div class="col-xl-2 col-3 product-img-action-wrap mb-0">
                            <div class="product-img product-img-zoom">
                                <a href="{{ $product->url }}">
                                    <img class="default-img"
                                        src="{{ RvMedia::getImageUrl($product->image, 'product-thumb', false, RvMedia::getDefaultImage()) }}"
                                        alt="{{ $product->name }}">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-9 product__content">
                            <div class="product-content-wrap px-1 px-md-3">
                                <a class="product__title" href="{{ $product->url }}">{!! BaseHelper::clean($product->name) !!}</a>

                                @if ($userAreaId)
                                    @if ($hasDelivery)
                                        <div style="margin-top: 5px;">
                                            <span
                                                style="background: #e8f6ea; color: #3BB77E; padding: 3px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; display: inline-block;">
                                                <i class="fi-rs-marker" style="font-size: 10px;"></i> আপনার এলাকায়
                                                ডেলিভারি আছে
                                            </span>
                                        </div>
                                    @else
                                        <div style="margin-top: 5px;">
                                            <span style="color: #adadad; font-size: 11px; display: inline-block;">
                                                অন্য এলাকায় পাওয়া যাচ্ছে
                                            </span>
                                        </div>
                                    @endif
                                @endif

                                @if (EcommerceHelper::isReviewEnabled() && $product->reviews_avg > 0)
                                    <div class="rating_wrap">
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating"
                                                style="width: {{ $product->reviews_avg * 20 }}%"></div>
                                        </div>
                                        <span class="rating_num">({{ $product->reviews_count }})</span>
                                    </div>
                                @endif
                                @include(Theme::getThemeNamespace('views.ecommerce.includes.product-price'))
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($products->hasMorePages() && $products->nextPageUrl())
            <div class="col-12 text-center loadmore-container">
                <a class="loadmore position-relative mx-auto pt-1 pb-3"
                    href="{{ $products->withQueryString()->nextPageUrl() }}">
                    <span>{{ __('Load more') }}</span>
                </a>
            </div>
        @endif
    </div>
    <div class="panel__footer text-center">
        <a href="{{ route('public.products', $queries) }}">{{ __('See all results') }}</a>
    </div>
@else
    <div class="panel__content row py-2 mx-0">
        <div class="text-center">{{ __('No products found.') }}</div>
    </div>
@endif
