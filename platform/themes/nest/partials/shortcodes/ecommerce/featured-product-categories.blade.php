@if ($categories->isNotEmpty())
    {{-- CSS কোড --}}
    <style>
        .swiper-category-container .swiper-button-next,
        .swiper-category-container .swiper-button-prev {
            background-color: #fff;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            color: #253d4e;
            transition: background-color 0.3s ease;
        }

        .swiper-category-container .swiper-button-next:hover,
        .swiper-category-container .swiper-button-prev:hover {
            background-color: #f2f3f4;
        }

        .swiper-category-container .swiper-button-next:after,
        .swiper-category-container .swiper-button-prev:after {
            font-size: 16px;
            font-weight: 700;
        }

        .swiper-category-container .swiper-button-prev {
            left: -10px;
        }

        .swiper-category-container .swiper-button-next {
            right: -10px;
        }

        .swiper-category-container .swiper-slide {
            height: auto;
        }

        .swiper-category-container .card-2 {
            height: 100%;
            margin-bottom: 0;
        }

        .carousel-10-columns-arrow {
            display: none !important;
        }

        .swiper-category-container .swiper-wrapper {
            cursor: grab;
        }
    </style>

    {{-- HTML কোড --}}
    <section class="popular-categories section-padding">
        <div class="container wow animate__animated animate__fadeIn">
            <div class="section-title">
                <div class="title">
                    <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                </div>
                <div class="slider-arrow slider-arrow-2 flex-right carousel-10-columns-arrow"
                    id="carousel-10-columns-arrows"></div>
            </div>

            <div class="swiper swiper-category-container position-relative">
                <div class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <div class="card-2"
                                style="{{ $category->getMetaData('background_color', true) ? 'background-color:' . $category->getMetaData('background_color', true) : '' }}; {{ ($shortcode->show_products_count ?: 'yes') == 'no' ? 'min-height: 160px' : '' }}">
                                <figure class="img-hover-scale overflow-hidden">
                                    <a href="{{ $category->url }}"><img
                                            src="{{ RvMedia::getImageUrl($category->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                            alt="{{ $category->name }}" loading="lazy" /></a>
                                </figure>
                                <p class="heading-card"><a href="{{ $category->url }}"
                                        title="{{ $category->name }}">{{ $category->name }}</a></p>
                                @if (($shortcode->show_products_count ?: 'yes') == 'yes')
                                    <span>{{ __(':count items', ['count' => $category->count_all_products]) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    {{-- স্ক্রিপ্ট আপডেট করা হয়েছে --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {
                const swiper = new Swiper('.swiper-category-container', {
                    // ফাস্ট সোয়াইপ সেটিংস
                    freeMode: true,
                    freeModeMomentum: true,
                    freeModeMomentumRatio: 1,
                    freeModeMomentumVelocityRatio: 1,
                    speed: 400,

                    // সাধারণ সেটিংস
                    loop: false,
                    spaceBetween: 20,
                    grabCursor: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    // ======= ব্রেকপয়েন্ট আপডেট করা হয়েছে =======
                    breakpoints: {
                        // মোবাইল (ছোট স্ক্রিন): এখানে ৩.৫ করা হয়েছে
                        320: {
                            slidesPerView: 3.5,
                            spaceBetween: 10
                        },

                        // মোবাইল (ল্যান্ডস্কেপ / একটু বড় ফোন): ৪.৫ করা হয়েছে যাতে সামঞ্জস্য থাকে
                        480: {
                            slidesPerView: 4.5,
                            spaceBetween: 15
                        },

                        // ট্যাবলেট
                        768: {
                            slidesPerView: 5,
                            spaceBetween: 20
                        },

                        // ছোট ল্যাপটপ/ডেস্কটপ
                        992: {
                            slidesPerView: 7,
                            spaceBetween: 20
                        },

                        // বড় স্ক্রিন
                        1200: {
                            slidesPerView: 10,
                            spaceBetween: 20
                        }
                    }
                });
            }
        });
    </script>
@endif

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
