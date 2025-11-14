@if (($attributes = $attributes->where('attribute_set_id', $set->id)) && $attributes->isNotEmpty())
    <div class="text-swatches-wrapper attribute-swatches-wrapper attribute-swatches-wrapper form-group product__attribute product__color"
        data-type="text">
        <label class="attribute-name">{{ $set->title }}</label>
        <div class="attribute-values">

            <ul class="text-swatch attribute-swatch color-swatch">
                @foreach ($attributes as $attribute)
                    <li data-slug="{{ $attribute->slug }}" data-id="{{ $attribute->id }}"
                        onclick="handleVariationTitleAndPriceChange(event, '{{ $productVariationsInfo->where('id', $attribute->id)->first()->variation_id }}', {{ $product->id }} ,'{{ $attribute->id }}')"
                        class="attribute-swatch-item @if (!$variationInfo->where('id', $attribute->id)->count()) pe-none @endif">
                        <div>
                            <label>
                                <input class="product-filter-item" type="radio"
                                    name="attribute_{{ $set->slug }}_{{ $key }}"
                                    value="{{ $attribute->id }}"
                                    {{ $selected->where('id', $attribute->id)->count() ? 'checked' : '' }}>
                                <span>{{ $attribute->title }}</span>
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.11.0/axios.min.js"
        integrity="sha512-h9644v03pHqrIHThkvXhB2PJ8zf5E9IyVnrSfZg8Yj8k4RsO4zldcQc4Bi9iVLUCCsqNY0b4WXVV4UB+wbWENA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function highlightProductText(text) {
            if (!text) return text;
            let highlighted = text;
            // Specific words
            const highlightWords = ['লাইভ ওয়েট', 'Live Weight'];
            highlightWords.forEach(word => {
                const pattern = new RegExp(`\\(\\s*${word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\s*\\)`, 'gi');
                highlighted = highlighted.replace(pattern, '<span class="highlight-text">$&</span><br>');
            });
            // Dynamic pattern for numbers with units
            const dynamicPattern = /\(\s*.*?\d+(\.\d+)?\s*(কেজি|kg|গ্রাম|gm|পিস|pieces)\s*\)/gi;
            highlighted = highlighted.replace(dynamicPattern, '<span class="highlight-text">$&</span><br>');
            // Curly braces
            const curlyPattern = /\{[^}]*\}/g;
            highlighted = highlighted.replace(curlyPattern, '<span class="highlight-text">$&</span><br>');
            return highlighted;
        }

        const variation_product_name = document.getElementById('variation_product_name');
        const productDescription = document.getElementById('product-description');
        const variationDefaultProductName = variation_product_name ? variation_product_name.innerText : '';
        const productDescriptionDefaultValue = productDescription ? productDescription.innerHTML : '';

        function handleVariationTitleAndPriceChange(e, id, product_id = null, attribute_id = null) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const lang = document.documentElement.lang;
            axios.get(`/change-variation-info/${id}?lang=${lang}`, {
                headers: {
                    'X-CSRF-TOKEN': token
                }
            }).then(response => {
                if (response) {
                    if (response.data.variation_title && variation_product_name) {
                        variation_product_name.innerHTML = highlightProductText(response.data.variation_title);
                    } else if (variation_product_name) {
                        variation_product_name.innerHTML = highlightProductText(variationDefaultProductName);
                    }
                    if (response.data.variation_desc && productDescription) {
                        productDescription.innerHTML = response.data.variation_desc;
                    } else if (productDescription) {
                        productDescription.innerHTML = productDescriptionDefaultValue;
                    }
                }
            });
            if (attribute_id != null) {
                axios.get(`/product-variation/${product_id}?attributes[]=${attribute_id}`, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                }).then(response => {
                    console.log(response, 'this is my console');
                    // Update price in main product page
                    const currentPriceElement = document.querySelector('.product-price span.current-price');
                    if (currentPriceElement) {
                        currentPriceElement.textContent = response.data.data.display_price;
                    }

                    // Update price in quick view modal
                    const quickViewPriceElement = document.getElementById('quickViewPrice');
                    if (quickViewPriceElement) {
                        quickViewPriceElement.textContent = response.data.data.display_price;
                    }
                });
            }
        }
    </script>
@endif
