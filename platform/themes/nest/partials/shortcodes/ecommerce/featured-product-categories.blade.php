@if ($categories->isNotEmpty())
    <style>
        .swiper-category-container {
            padding-bottom: 20px;
        }

        .swiper-category-container .swiper-slide {
            height: auto;
        }

        /* Story Card Style */
        .card-story {
            position: relative;
            height: 200px;
            /* Fixed height for story look */
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card-story:hover {
            transform: translateY(-3px);
        }

        .card-story .story-bg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .card-story:hover .story-bg {
            transform: scale(1.05);
        }

        .card-story .story-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            display: flex;
            align-items: flex-end;
            padding: 10px;
        }

        .card-story .story-title {
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            line-height: 1.2;
            margin: 0;
            width: 100%;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Navigation Buttons */
        .swiper-category-container .swiper-button-next,
        .swiper-category-container .swiper-button-prev {
            background-color: #fff;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            color: #253d4e;
        }

        .swiper-category-container .swiper-button-next:after,
        .swiper-category-container .swiper-button-prev:after {
            font-size: 14px;
            font-weight: 700;
        }
    </style>

    <section class="popular-categories section-padding">
        <div class="container wow animate__animated animate__fadeIn">
            <div class="section-title">
                <div class="title">
                    <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                </div>
            </div>

            <div class="swiper swiper-category-container position-relative">
                <div class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <a href="{{ $category->url }}" class="card-story d-block">
                                {{-- Use original image or a larger thumb if available for better quality --}}
                                <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
                                    alt="{{ $category->name }}" class="story-bg" loading="lazy" />
                                <div class="story-overlay">
                                    <h5 class="story-title">{{ $category->name }}</h5>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {
                const swiper = new Swiper('.swiper-category-container', {
                    freeMode: true,
                    freeModeMomentum: true,
                    speed: 400,
                    loop: false,
                    spaceBetween: 10,
                    grabCursor: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 3.5,
                            spaceBetween: 10
                        },
                        480: {
                            slidesPerView: 4.5,
                            spaceBetween: 10
                        },
                        768: {
                            slidesPerView: 5.5,
                            spaceBetween: 15
                        },
                        992: {
                            slidesPerView: 7,
                            spaceBetween: 20
                        },
                        1200: {
                            slidesPerView: 8,
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
