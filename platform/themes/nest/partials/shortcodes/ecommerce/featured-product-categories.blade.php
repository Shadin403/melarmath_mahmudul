@if ($categories->isNotEmpty())
    {{-- আপনার দেওয়া CSS কোড অপরিবর্তিত রাখা হয়েছে --}}
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
        /* দ্রুত সোয়াইপের জন্য কার্সর পরিবর্তন */
        .swiper-category-container .swiper-wrapper {
            cursor: grab;
        }
    </style>

    {{-- আপনার দেওয়া HTML কোড অপরিবর্তিত রাখা হয়েছে --}}
    <section class="popular-categories section-padding">
        <div class="container wow animate__animated animate__fadeIn">
            <div class="section-title">
                <div class="title">
                    <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                </div>
                <div class="slider-arrow slider-arrow-2 flex-right carousel-10-columns-arrow" id="carousel-10-columns-arrows"></div>
            </div>

            <div class="swiper swiper-category-container position-relative">
                <div class="swiper-wrapper">
                    @foreach($categories as $category)
                    <div class="swiper-slide">
                        <div class="card-2" style="{{ $category->getMetaData('background_color', true) ? 'background-color:' . $category->getMetaData('background_color', true) : '' }}; {{ ($shortcode->show_products_count ?: 'yes') == 'no' ? 'min-height: 160px' : '' }}">
                            <figure class="img-hover-scale overflow-hidden">
                                <a href="{{ $category->url }}"><img src="{{ RvMedia::getImageUrl($category->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy" /></a>
                            </figure>
                            <p class="heading-card"><a href="{{ $category->url }}" title="{{ $category->name }}">{{ $category->name }}</a></p>
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

    {{-- Swiper.js চালু করার জন্য নতুন এবং ফাস্ট-সোয়াইপ স্ক্রিপ্ট --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {
                const swiper = new Swiper('.swiper-category-container', {
                    // ======= ফাস্ট সোয়াইপের জন্য বিশেষ সেটিংস =======

                    // ১. ফ্রি মোড চালু করা
                    freeMode: true,          // সোয়াইপ করার পর স্লাইডারকে নিজের গতিতে চলতে দেয়
                    freeModeMomentum: true,  // গতিকে আরও মসৃণ করে
                    freeModeMomentumRatio: 1, // মোমেন্টামের অনুপাত, মান বাড়ালে বেশি দূর যাবে
                    freeModeMomentumVelocityRatio: 1, // গতির অনুপাত, মান বাড়ালে অল্প টানে বেশি গতি পাবে

                    // ২. দ্রুত স্লাইড স্পিড
                    speed: 400, // অ্যারো ক্লিক বা সোয়াইপের পর স্লাইড স্থির হওয়ার গতি

                    // ৩. অন্যান্য বেসিক সেটিংস
                    loop: false,
                    spaceBetween: 20,
                    grabCursor: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    // আপনার দেওয়া রেসপন্সিভ সেটিংস অপরিবর্তিত রাখা হয়েছে
                    breakpoints: {
                        320: { slidesPerView: 2, spaceBetween: 10 },
                        480: { slidesPerView: 3, spaceBetween: 15 },
                        768: { slidesPerView: 5, spaceBetween: 20 },
                        992: { slidesPerView: 7, spaceBetween: 20 },
                        1200: { slidesPerView: 10, spaceBetween: 20 }
                    }
                });
            }
        });
    </script>
@endif

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
