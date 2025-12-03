@php
    use Botble\Ecommerce\Models\ProductCategory;
    $categories = ProductCategory::query()
        ->where('status', 'published')
        ->where('parent_id', 0)
        ->with(['children.children'])
        ->get();
@endphp
<div class="mega-menu-container"
    @if (isset($layout) && $layout == 'sidebar') style="position: relative; top: -26px; left: -19px;" @endif>
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
                    <ul class="dropdown-list">
                        @foreach ($category->children as $childCategory)
                            <li class="category-item">
                                <a href="{{ route('public.single', $childCategory->url) }}" class="category-link">
                                    <span class="category-name">{{ $childCategory->name }}</span>
                                    @if ($childCategory->children->count() > 0)
                                        <span class="sub-toggle"><i class="fi-rs-angle-right"></i></span>
                                    @endif
                                </a>
                                @if ($childCategory->children->count() > 0)
                                    <ul class="dropdown-list">
                                        @foreach ($childCategory->children as $grandChildCategory)
                                            <li class="category-item">
                                                <a href="{{ route('public.single', $grandChildCategory->url) }}"
                                                    class="category-link">
                                                    <span class="category-name">{{ $grandChildCategory->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>

<style>
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
        position: relative;
    }

    .category-item {
        position: relative;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        background: transparent !important;
        width: 100%;
    }

    .category-link {
        display: flex !important;
        align-items: center;
        padding: 10px 20px !important;
        color: #253d4e !important;
        font-size: 15px !important;
        font-weight: 500;
        border: none !important;
        border-bottom: 1px solid #f8f8f8 !important;
        text-decoration: none !important;
        transition: all 0.2s ease;
        width: 100%;
        background: transparent !important;
        border-radius: 0 !important;
        white-space: nowrap;
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

    /* Nested Dropdown Styles */
    .dropdown-list {
        position: absolute;
        left: 100%;
        top: 0;
        width: 250px;
        /* Same width as parent */
        background: #fff;
        border: 1px solid #f0f0f0;
        list-style: none !important;
        padding: 10px 0 !important;
        margin: 0 !important;
        display: none;
        /* Hidden by default */
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-radius: 0 5px 5px 5px;
    }

    /* Show dropdown on hover */
    .category-item:hover>.dropdown-list {
        display: block;
    }

    /* Hover effect for items in nested list */
    .dropdown-list .category-link:hover {
        color: #3BB77E !important;
        background-color: #f9f9f9 !important;
    }

    /* Rotate arrow on hover */
    .category-item:hover>.category-link .sub-toggle i {
        transform: rotate(0deg);
        /* Keep it right or adjust if needed */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dropdown-list {
            display: none !important;
        }
    }
</style>

<script>
    // No JS needed for pure CSS hover, but keeping empty listener if needed later
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add delay or animation logic here if CSS isn't enough
    });
</script>
