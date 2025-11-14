@extends('core/base::layouts.master')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-edit"></i>
                        {{ trans('plugins/ecommerce::products.edit_variations_for', ['name' => $product->name]) }}
                    </h4>
                </div>

                <form method="POST" action="{{ route('products.variations.update', $product->getKey()) }}"
                    class="variation-translation-form">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="product-info mb-4">
                                    <h5>{{ $product->name }}</h5>
                                    <p class="text-muted">{{ $product->sku }}</p>
                                </div>

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
                                                    <span
                                                        class="badge badge-primary">{{ trans('plugins/ecommerce::products.default') }}</span>
                                                @endif
                                            </h6>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('plugins/ecommerce::products.variation_title') }}</label>
                                                        <input type="text"
                                                            name="variations[{{ $variation->id }}][variation_title]"
                                                            value="{{ $variation->variation_title }}" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('plugins/ecommerce::products.variation_description') }}</label>
                                                        <textarea name="variations[{{ $variation->id }}][variation_desc]" class="form-control" rows="3">{{ $variation->variation_desc }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Language Tabs -->
                                            @php
                                                $languages = \Botble\Language\Facades\Language::getActiveLanguage([
                                                    'lang_code',
                                                    'lang_name',
                                                    'lang_flag',
                                                ]);
                                                $defaultLocale = \Botble\Language\Facades\Language::getDefaultLocaleCode();
                                            @endphp

                                            @if ($languages->count() > 1)
                                                <div class="language-translations mt-3">
                                                    <h6>{{ trans('plugins/language::language.translations') }}</h6>

                                                    <ul class="nav nav-tabs" role="tablist">
                                                        @foreach ($languages as $language)
                                                            @if ($language->lang_code !== $defaultLocale)
                                                                <li class="nav-item">
                                                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                                        href="#variation-{{ $variation->id }}-{{ $language->lang_code }}"
                                                                        data-bs-toggle="tab">
                                                                        <span
                                                                            class="flag-icon flag-icon-{{ strtolower($language->lang_flag) }}"></span>
                                                                        {{ $language->lang_name }}
                                                                    </a>
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
                                                                <div class="tab-pane {{ $loop->first ? 'active' : '' }}"
                                                                    id="variation-{{ $variation->id }}-{{ $language->lang_code }}">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>{{ trans('plugins/ecommerce::products.variation_title') }}
                                                                                    ({{ $language->lang_name }})
                                                                                </label>
                                                                                <input type="text"
                                                                                    name="variations[{{ $variation->id }}][translations][{{ $language->lang_code }}][variation_title]"
                                                                                    value="{{ $translation ? $translation->variation_title : '' }}"
                                                                                    class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>{{ trans('plugins/ecommerce::products.variation_description') }}
                                                                                    ({{ $language->lang_name }})</label>
                                                                                <textarea name="variations[{{ $variation->id }}][translations][{{ $language->lang_code }}][variation_desc]"
                                                                                    class="form-control" rows="3">{{ $translation ? $translation->variation_desc : '' }}</textarea>
                                                                            </div>
                                                                        </div>
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
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            {{ trans('core/base::forms.save') }}
                        </button>

                        <a href="{{ route('products.edit', $product->getKey()) }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left"></i>
                            {{ trans('core/base::forms.back_to_product') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        $(document).ready(function() {
            $('.variation-translation-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.error) {
                            Botble.showError(response.message);
                        } else {
                            Botble.showSuccess(response.message);
                        }
                    },
                    error: function(xhr) {
                        Botble.handleError(xhr);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endpush
