@php
    // Check if a specific variation ID is requested
    $requestedVariationId = request('variation_id') ?: null;

    if ($requestedVariationId) {
        $variations = \Botble\Ecommerce\Models\ProductVariation::query()
            ->where('id', $requestedVariationId)
            ->where('configurable_product_id', $product->getKey())
            ->with('translations')
            ->get();
    }

    $languages = \Botble\Language\Facades\Language::getActiveLanguage(['lang_code', 'lang_name', 'lang_flag']);
    $defaultLocale = \Botble\Language\Facades\Language::getDefaultLocaleCode();
@endphp

@if ($variations->isEmpty())
    <div class="alert alert-info">
        {{ trans('plugins/ecommerce::products.no_variations_found') }}
    </div>
@else
    @foreach ($variations as $variation)
        <div class="variation-item mb-4 p-3 border rounded">
            <h6 class="variation-title">
                {{ trans('plugins/ecommerce::products.variation') }} #{{ $variation->id }}
                @if ($variation->is_default)
                    <span class="badge badge-primary">{{ trans('plugins/ecommerce::products.default') }}</span>
                @endif
            </h6>

            @if ($languages->count() > 1)
                <div class="language-translations mt-3">
                    <h6>{{ trans('plugins/language::language.translations') }}</h6>

                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($languages as $language)
                            @if ($language->lang_code !== $defaultLocale)
                                <li class="nav-item">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="modal-variation-{{ $variation->id }}-{{ $language->lang_code }}-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#modal-variation-{{ $variation->id }}-{{ $language->lang_code }}"
                                        type="button" role="tab"
                                        aria-controls="modal-variation-{{ $variation->id }}-{{ $language->lang_code }}"
                                        {{ $loop->first ? 'aria-selected="true"' : 'aria-selected="false"' }}>
                                        <span
                                            class="flag-icon flag-icon-{{ strtolower($language->lang_flag) }}"></span>
                                        {{ $language->lang_name }}
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="tab-content mt-3">
                        @foreach ($languages as $language)
                            @if ($language->lang_code !== $defaultLocale)
                                @php
                                    $translation = $variation->translations
                                        ->where('lang_code', $language->lang_code)
                                        ->first();
                                @endphp
                                <div class="tab-pane {{ $loop->first ? 'active show' : '' }}"
                                    id="modal-variation-{{ $variation->id }}-{{ $language->lang_code }}">
                                    <div class="form-group">
                                        <label>{{ trans('plugins/ecommerce::products.variation_title') }}
                                            ({{ $language->lang_name }})
                                        </label>
                                        <input type="text"
                                            name="variations[{{ $variation->id }}][translations][{{ $language->lang_code }}][variation_title]"
                                            value="{{ $translation ? $translation->variation_title : '' }}"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('plugins/ecommerce::products.variation_description') }}
                                            ({{ $language->lang_name }})</label>
                                        <textarea name="variations[{{ $variation->id }}][translations][{{ $language->lang_code }}][variation_desc]"
                                            class="form-control variationDescription" rows="3">{{ $translation ? $translation->variation_desc : '' }}</textarea>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach
@endif
