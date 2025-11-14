{{-- ধাপ ২: Swiper.js এর জন্য HTML --}}
<div class="swiper-container-wrapper">
    <div class="swiper product-swiper">
        <div class="swiper-wrapper">
            @foreach($products as $product)
            <div class="swiper-slide">
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', compact('product'))
            </div>
            @endforeach
        </div>

        <!-- Navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</div>

{{-- ধাপ ৩: Swiper CSS --}}
<style>
    .swiper-container-wrapper {
        margin: 20px 0;
        overflow: hidden;
        position: relative;
    }

    .product-swiper {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {
        height: auto;
    }

    /* Navigation buttons style */
    .swiper-button-next,
    .swiper-button-prev {
        width: 40px;
        height: 40px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 16px;
        font-weight: bold;
    }

    /* Position center vertically */
    .swiper-button-next,
    .swiper-button-prev {
        top: 50%;
        transform: translateY(-50%);
    }

    /* Right & left positioning */
    .swiper-button-next {
        right: -10px;
    }

    .swiper-button-prev {
        left: -10px;
    }

    /* Hover effect */
    .swiper-button-next:hover,
    .swiper-button-prev:hover {
        background: #333;
        color: #fff;
    }

</style>

{{-- ধাপ ৪: Swiper JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var swiper = new Swiper('.product-swiper', {
            slidesPerView: 4
            , spaceBetween: 15
            , freeMode: true
            , navigation: {
                nextEl: '.swiper-button-next'
                , prevEl: '.swiper-button-prev'
            , }
            , breakpoints: {
                0: {
                    slidesPerView: 2
                    , spaceBetween: 10
                }
                , 768: {
                    slidesPerView: 3
                    , spaceBetween: 15
                }
                , 1200: {
                    slidesPerView: 4
                    , spaceBetween: 15
                }
            }
        });
    });

</script>
