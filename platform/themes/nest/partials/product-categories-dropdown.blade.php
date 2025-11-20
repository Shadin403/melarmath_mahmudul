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
        top: -26px;
        left: -19px;
    }

    .categories-dropdown-wrap {
        width: 110%;

    }

    .category-list {
        list-style: none !important;
        padding: 10px 0 !important;
        margin: 0 !important;
        border: 1px solid #f0f0f0 !important;
        width: 250px !important;
        background: #fff !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .category-item {
        position: relative;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        background: transparent !important;
        width: 116%;
    }

    .category-link {
        display: flex !important;
        align-items: center;
        padding: 10px 20px !important;
        color: #253d4e !important;
        font-size: 15px !important;
        font-weight: 500;
        border: none !important;
        /* Reset all borders first */
        border-bottom: 1px solid #f8f8f8 !important;
        /* Re-apply bottom separator */
        text-decoration: none !important;
        transition: all 0.2s ease;
        width: 100%;
        background: transparent !important;
        border-radius: 0 !important;
    }

    .category-link:hover {
        background-color: transparent !important;
        color: #3BB77E !important;
        padding-left: 20px !important;
    }

    .category-icon {
        margin-right: 15px !important;
        width: 24px !important;
        height: 24px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        object-fit: contain;
        opacity: 0.8;
    }

    .category-name {
        flex: 1;
        margin-right: 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .sub-toggle {
        margin-left: auto;
        font-size: 12px;
        color: #b6b6b6;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        transition: transform 0.3s ease;
    }

    .category-item:last-child .category-link {
        border-bottom: none !important;
    }

    /* Mega Dropdown Styles */
    .mega-dropdown {
        position: absolute;
        left: 100%;
        top: 0;
        width: 850px;
        min-height: 100%;
        background: #fff;
        border: 1px solid #f0f0f0;
        opacity: 0;
        visibility: hidden;
        transform: translateX(10px);
        transition: all 0.2s ease-in-out;
        z-index: 9999;
        padding: 30px;
        margin-left: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        /* Slight shadow for dropdown only */
    }

    .category-item:hover .mega-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    .mega-dropdown-content {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        width: 100%;
    }

    .mega-column {
        padding: 0;
    }

    .column-title {
        font-size: 16px;
        font-weight: 700;
        color: #253d4e;
        margin: 0 0 15px 0;
        padding-bottom: 0;
        border-bottom: none;
        display: block;
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
        margin-bottom: 10px;
    }

    .sub-category-list li a {
        color: #7e7e7e;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: block;
    }

    .sub-category-list li a:hover {
        color: #3BB77E;
        transform: translateX(5px);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .mega-dropdown {
            width: 600px;
        }

        .mega-dropdown-content {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .mega-dropdown {
            width: 500px;
        }
    }

    @media (max-width: 768px) {
        .mega-dropdown {
            display: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryItems = document.querySelectorAll('.category-item');

        categoryItems.forEach(item => {
            const dropdown = item.querySelector('.mega-dropdown');
            const subToggle = item.querySelector('.sub-toggle i');

            if (dropdown) {
                let hoverTimeout;

                item.addEventListener('mouseenter', () => {
                    clearTimeout(hoverTimeout);
                    dropdown.style.opacity = '1';
                    dropdown.style.visibility = 'visible';
                    dropdown.style.transform = 'translateX(0)';

                    // Rotate icon to point left when hovering
                    if (subToggle) {
                        subToggle.style.transform = 'rotate(180deg)';
                    }
                });

                item.addEventListener('mouseleave', () => {
                    hoverTimeout = setTimeout(() => {
                        dropdown.style.opacity = '0';
                        dropdown.style.visibility = 'hidden';
                        dropdown.style.transform = 'translateX(10px)';

                        // Rotate icon back to point right when not hovering
                        if (subToggle) {
                            subToggle.style.transform = 'rotate(0deg)';
                        }
                    }, 100);
                });
            }
        });
    });
</script>
