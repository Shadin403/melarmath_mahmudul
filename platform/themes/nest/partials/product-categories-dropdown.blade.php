@php
    use Botble\Ecommerce\Models\ProductCategory;
    $categories = ProductCategory::query()
        ->where('status', 'published')
        ->where('parent_id', 0)
        ->with(['children.children'])
        ->get();
@endphp
<div class="mega-menu-container">
    <ul class="category-list" style="border: none !important;">
        @foreach ($categories as $category)
            <li class="category-item">
                <a href="{{ route('public.single', $category->url) }}" class="category-link">
                    @if ($categoryImage = $category->icon_image)
                        <img src="{{ RvMedia::getImageUrl($categoryImage) }}" alt="{{ $category->name }}" width="30"
                            height="30" class="category-icon">
                    @elseif ($categoryIcon = $category->icon)
                        <i class="{{ $categoryIcon }} category-icon"></i>
                    @endif
                    <span class="category-name">{{ $category->name }}</span>
                    @if ($category->children->count() > 0)
                        <span class="sub-toggle"><i class="fi-rs-angle-right"></i></span>
                    @endif
                </a>

                @if ($category->children->count() > 0)
                    <div class="mega-dropdown">
                        <div class="mega-dropdown-content">
                            @foreach ($category->children as $childCategory)
                                <div class="mega-column">
                                    <h4 class="column-title">
                                        <a href="{{ route('public.single', $childCategory->url) }}">
                                            {{ $childCategory->name }}
                                        </a>
                                    </h4>
                                    @if ($childCategory->children->count() > 0)
                                        <ul class="sub-category-list">
                                            @foreach ($childCategory->children as $grandChildCategory)
                                                <li>
                                                    <a href="{{ route('public.single', $grandChildCategory->url) }}">
                                                        {{ $grandChildCategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</div>

<style>
    .mega-menu-container {
        position: relative;
    }



    .categories-dropdown-wrap {
        width: 80%;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
        border: 1px solid #ececec;
        width: 220px;
        background: #fff;
    }

    .category-item {
        position: relative;
    }

    .category-link {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #253d4e;
        font-size: 14px;
        font-weight: 500;
        border-bottom: 1px solid #ececec;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .category-link:hover {
        background-color: #f7f7f7;
        color: #3BB77E;
    }

    .category-icon {
        margin-right: 12px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .category-name {
        flex: 1;
    }

    .sub-toggle {
        margin-left: auto;
        font-size: 12px;
        color: #7e7e7e;
    }

    .category-item:last-child .category-link {
        border-bottom: none;
    }

    /* Mega Dropdown Styles */
    .mega-dropdown {
        position: absolute;
        left: 235px;
        top: 0;
        width: 400px;
        background: #fff;
        border: 1px solid #ececec;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateX(10px);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .category-item:hover .mega-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    .mega-dropdown-content {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 20px;
        padding: 15px 20px;
        max-height: 400px;
        overflow-y: auto;
    }

    .mega-column {
        padding: 0;
    }

    .column-title {
        font-size: 15px;
        font-weight: 700;
        color: #253d4e;
        margin: 0 0 12px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #3BB77E;
    }

    .column-title a {
        color: #253d4e;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .column-title a:hover {
        color: #3BB77E;
    }

    .sub-category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sub-category-list li {
        margin-bottom: 8px;
    }

    .sub-category-list li a {
        color: #7e7e7e;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
        padding: 4px 0;
    }

    .sub-category-list li a:hover {
        color: #3BB77E;
        padding-left: 5px;
    }

    /* Scrollbar Styling */
    .mega-dropdown-content::-webkit-scrollbar {
        width: 6px;
    }

    .mega-dropdown-content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .mega-dropdown-content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .mega-dropdown-content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .mega-dropdown {
            min-width: 300px;
        }

        .mega-dropdown-content {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryItems = document.querySelectorAll('.category-item');

        categoryItems.forEach(item => {
            const dropdown = item.querySelector('.mega-dropdown');

            if (dropdown) {
                let hoverTimeout;

                item.addEventListener('mouseenter', () => {
                    clearTimeout(hoverTimeout);
                    dropdown.style.opacity = '1';
                    dropdown.style.visibility = 'visible';
                    dropdown.style.transform = 'translateX(0)';
                });

                item.addEventListener('mouseleave', () => {
                    hoverTimeout = setTimeout(() => {
                        dropdown.style.opacity = '0';
                        dropdown.style.visibility = 'hidden';
                        dropdown.style.transform = 'translateX(10px)';
                    }, 100);
                });
            }
        });
    });
</script>
