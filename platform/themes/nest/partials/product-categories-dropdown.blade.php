<style>
    .end {
        list-style: none;
        padding: 0;
        margin: 0;
        width: 250px;
        border: 1px solid #ececec;
    }

    .categories-dropdown-inner>ul {
        width: 70%;
    }

    .main-categories-wrap .categories-dropdown-inner {
        min-width: 500px !important;
    }

    .end>li {
        position: relative;
    }

    .end>li>a {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #253d4e;
        font-size: 14px;
        font-weight: 500;
        border-bottom: 1px solid #ececec;
    }

    .end>li>a img {
        margin-right: 15px;
    }

    .end>li:last-child>a {
        border-bottom: none;
    }

    .end>li .sub-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    .end>li .level-menu {
        position: absolute;
        left: 100%;
        top: 0;
        width: 250px;
        background: #fff;
        border: 1px solid #ececec;
        list-style: none;
        padding: 0;
        margin: 0;
        display: none;
        /* Hide by default */
        z-index: 1000;
    }

    .end>li:hover>.level-menu {
        display: block;
        /* Show on hover */
    }

    .end>li .level-menu li a {
        display: block;
        padding: 10px 15px;
        color: #253d4e;
        font-size: 14px;
        border-bottom: 1px solid #ececec;
    }

    .end>li .level-menu li:last-child a {
        border-bottom: none;
    }
</style>
@php
    use Botble\Ecommerce\Models\ProductCategory;
    $categories = ProductCategory::query()
        ->where('status', 'published')
        ->where('parent_id', 0)
        ->with(['children'])
        ->get();
@endphp<ul class="end">
    @foreach ($categories as $category)
        <li>
            <a href="{{ route('public.single', $category->url) }}">
                @if ($categoryImage = $category->icon_image)
                    <img src="{{ RvMedia::getImageUrl($categoryImage) }}" alt="{{ $category->name }}" width="30"
                        height="30">
                @elseif ($categoryIcon = $category->icon)
                    <i class="{{ $categoryIcon }}"></i>
                @endif
                {{ $category->name }}

                @if ($category->children->count() > 0)
                    <span class="sub-toggle"><i class="fi-rs-angle-right"></i></span>
                @endif
            </a>
            @if ($category->children->count() > 0)
                <ul class="level-menu level-menu-modify">
                    @foreach ($category->children as $childCategory)
                        <li><a href="{{ route('public.single', $childCategory->url) }}">{{ $childCategory->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
