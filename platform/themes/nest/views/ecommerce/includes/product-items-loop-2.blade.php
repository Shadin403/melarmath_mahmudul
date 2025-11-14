{{-- ধাপ ১: Swiper.js এর জন্য CDN Link --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

{{-- ধাপ ২: Swiper.js এর জন্য HTML গঠন --}}
<div class="swiper-container-wrapper">
    <div class="swiper product-swiper">
        <div class="swiper-wrapper">
            {{-- লুপের ভেতরের কোড একই থাকবে --}}
            @foreach($products as $product)
            <div class="swiper-slide">
                {{-- আপনার প্রোডাক্ট কার্ডের include statement --}}
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', compact('product'))
            </div>
            @endforeach
        </div>
    </div>
</div>


{{-- ধাপ ৩: Swiper.js এর জন্য প্রয়োজনীয় কিন্তু মিনিমাল CSS --}}
<style>
    .swiper-container-wrapper {
        margin: 20px 0;
        overflow: hidden;
    }

    .product-swiper {
        width: 100%;
        height: 100%;
    }

    /*
     * অতিরিক্ত স্টাইল মুছে ফেলা হয়েছে যাতে আপনার প্রোডাক্ট কার্ডের
     * নিজস্ব ডিজাইন নষ্ট না হয়।
    */
    .swiper-slide {
        height: auto;
        /* এটি জরুরি, যাতে কার্ডগুলো সমান উচ্চতা পায় */
    }

</style>


{{-- ধাপ ৪: Swiper.js এর জন্য JavaScript (কোনো পরিবর্তন নেই) --}}
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var swiper = new Swiper('.product-swiper', {
            freeMode: true
            , freeModeMomentum: true
            , slidesPerView: 4
            , spaceBetween: 15
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
