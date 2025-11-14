<li>

    <div class="category-item-container">
        {{-- যদি সাব-ক্যাটেগরি না থাকে তবে লিংক কাজ করবে, অন্যথায় নয় --}}
        @php
        $categoryUrl = url($category->url); // এটা absolute URL বানাবে
        @endphp

        <a href="{{ $categoryUrl }}">
            @if ($categoryImage = $category->icon_image)
            <img src="{{ RvMedia::getImageUrl($categoryImage) }}" alt="{{ $category->name }}" width="30" height="30">
            @elseif ($categoryIcon = $category->icon)
            <i class="{{ $categoryIcon }}"></i>
            @endif
            <span>{{ $category->name }}</span>
        </a>
        {{-- যদি সাব-ক্যাটেগরি থাকে, তাহলে এক্সপান্ড আইকন দেখাবে --}}
        @if (count($category->children) > 0)
        <span class="menu-expand"><i class="fi-rs-angle-down"></i></span>
        @endif
    </div>

    {{-- যদি সাব-ক্যাটেগরি থাকে, তবে একটি নতুন ul তৈরি হবে এবং তার ভেতরে আবার এই ফাইলটি কল হবে --}}
    @if (count($category->children) > 0)
    <ul class="sub-menu">
        @foreach ($category->children as $child)
        @include('partials.mobile-category-item', ['category' => $child])
        @endforeach
    </ul>
    @endif
</li>
