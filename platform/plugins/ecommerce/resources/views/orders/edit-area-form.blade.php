@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div id="main-area-content">
        <div class="row justify-content-center">
            <div class="col-xxl-9 col-xl-8 col-lg-10">
                <div class="row">
                    <div class="col-md-9">
                        <x-core::card class="mb-3">
                            <x-core::card.header>
                                <x-core::card.title>
                                    {{ trans('core/base::forms.edit_item', ['name' => $dhakaArea->name]) }}
                                </x-core::card.title>
                            </x-core::card.header>

                            @if (defined('LANGUAGE_MODULE_SCREEN_NAME') &&
                                    defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') &&
                                    str_starts_with(request()->get('ref_lang', ''), 'en'))
                                <x-core::alert type="info" class="mx-3 mt-3">
                                    <x-core::icon name="ti ti-info-circle" class="me-2" />
                                    <strong>{{ __('English Mode') }}:</strong>
                                    {{ __('You are editing the English version of this area. Bengali name is required, English name is optional.') }}
                                </x-core::alert>
                            @endif

                            <x-core::card.body>
                                {!! Form::open([
                                    'route' => ['orders.updateDhakaArea', $dhakaArea->id],
                                    'method' => 'PUT',
                                    'id' => 'main-area-form',
                                ]) !!}
                                @csrf

                                {{-- Hidden ref_lang field to preserve language context --}}
                                @if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME'))
                                    @php
                                        $currentLanguage = request()->get(
                                            'ref_lang',
                                            \Botble\Language\Facades\Language::getDefaultLocaleCode(),
                                        );
                                    @endphp
                                    <input type="hidden" name="ref_lang" value="{{ $currentLanguage }}">
                                @endif

                                @php
                                    $currentLang = request()->get(
                                        'ref_lang',
                                        \Botble\Language\Facades\Language::getDefaultLocaleCode(),
                                    );
                                    $isEnglishMode = str_starts_with($currentLang, 'en');
                                @endphp

                                @if ($isEnglishMode)
                                    {{-- English Mode - Show only English name input --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required" for="name_en">{{ __('Area Name') }}
                                                    ({{ __('English') }})</label>
                                                @php
                                                    $englishTranslation =
                                                        $dhakaArea->getTranslation('name', $currentLang, false) ?? '';
                                                @endphp
                                                <input type="text" class="form-control" name="name_en" id="name_en"
                                                    value="{{ $englishTranslation }}" required maxlength="250"
                                                    placeholder="Enter area name in English">
                                                {{-- Hidden fields to preserve other data --}}
                                                <input type="hidden" name="name" value="{{ $dhakaArea->name }}">
                                                <input type="hidden" name="thana_id" value="{{ $dhakaArea->thana_id }}">
                                                <input type="hidden" name="price" value="{{ $dhakaArea->price }}">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Default Mode - Show all inputs: Thana, Area Name, Amount --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required"
                                                    for="thana_id">{{ __('Thana') }}</label>
                                                <select class="form-select select-search-full" name="thana_id"
                                                    id="thana_id" required>
                                                    <option value="">{{ __('Select a Thana...') }}</option>
                                                    @php
                                                        $thanas = \Botble\Ecommerce\Models\GlobalOptionValue::where(
                                                            'option_id',
                                                            7,
                                                        )->pluck('option_value', 'id');
                                                    @endphp
                                                    @foreach ($thanas as $id => $name)
                                                        <option value="{{ $id }}"
                                                            {{ $dhakaArea->thana_id == $id ? 'selected' : '' }}>
                                                            {{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required" for="name">{{ __('Area Name') }}
                                                    ({{ __('Bengali') }})</label>
                                                <input type="text" class="form-control" name="name" id="name"
                                                    value="{{ $dhakaArea->name }}" required maxlength="250"
                                                    placeholder="এলাকার নাম বাংলায় লিখুন">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label required"
                                                    for="price">{{ __('Amount') }}</label>
                                                <input type="number" class="form-control" name="price" id="price"
                                                    value="{{ $dhakaArea->price }}" required step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {!! Form::close() !!}
                            </x-core::card.body>
                        </x-core::card>
                    </div>

                    <div class="col-md-3 right-sidebar d-flex flex-column-reverse flex-md-column">

                        <x-core::card class="mb-3">
                            <x-core::card.header>
                                <x-core::card.title>
                                    {{ trans('plugins/language::language.name') }}
                                </x-core::card.title>
                            </x-core::card.header>
                            <!-- Language Switcher at the top -->
                            @if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME'))
                                @php
                                    $currentLanguage = request()->get(
                                        'ref_lang',
                                        \Botble\Language\Facades\Language::getDefaultLocaleCode(),
                                    );
                                    $languages = \Botble\Language\Facades\Language::getActiveLanguage([
                                        'lang_code',
                                        'lang_name',
                                        'lang_flag',
                                    ]);
                                    $route = ['edit' => 'orders.editDhakaArea'];
                                    $args = [$dhakaArea];
                                @endphp

                                <input name="language" type="hidden" value="{{ $currentLanguage }}">

                                <div id="list-others-language"
                                    style="display: flex; justify-content: center ; margin-block-start: 20px;">
                                    @foreach ($languages as $language)
                                        @continue(!$currentLanguage || $language->lang_code === $currentLanguage)
                                        <a class="gap-2 d-flex align-items-center text-decoration-none"
                                            href="{{ Route::has($route['edit']) ? Request::url() . ($language->lang_code != \Botble\Language\Facades\Language::getDefaultLocaleCode() ? '?' . \Botble\Language\Facades\Language::refLangKey() . '=' . $language->lang_code : null) : '#' }}"
                                            target="_blank">
                                            {!! language_flag($language->lang_flag, $language->lang_name) !!}
                                            <span>{{ $language->lang_name }} <x-core::icon
                                                    name="ti ti-external-link" /></span>
                                        </a>
                                    @endforeach
                                </div>

                                @push('header')
                                    <meta name="{{ \Botble\Language\Facades\Language::refFromKey() }}"
                                        content="{{ !empty($args[0]) && $args[0]->id ? $args[0]->id : 0 }}">
                                    <meta name="{{ \Botble\Language\Facades\Language::refLangKey() }}"
                                        content="{{ $currentLanguage }}">
                                @endpush
                            @endif
                            <!-- End Language Switcher -->

                            <x-core::card.body>

                            </x-core::card.body>
                        </x-core::card>


                        <x-core::card class="mb-3">
                            <x-core::card.header>
                                <x-core::card.title>
                                    {{ trans('core/base::forms.publish') }}
                                </x-core::card.title>
                            </x-core::card.header>

                            <x-core::card.body>
                                <div class="btn-list d-grid">
                                    @if (
                                        !defined('LANGUAGE_MODULE_SCREEN_NAME') ||
                                            !defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') ||
                                            !request()->input('ref_lang') ||
                                            request()->input('ref_lang') == \Botble\Language\Facades\Language::getDefaultLocaleCode() ||
                                            str_starts_with(request()->get('ref_lang', ''), 'en'))
                                        <x-core::button type="submit" form="main-area-form" name="submitter" value="save"
                                            color="success" icon="ti ti-device-floppy">
                                            {{ trans('core/base::forms.save') }}
                                        </x-core::button>

                                        <x-core::button type="submit" form="main-area-form" name="submitter" value="apply"
                                            color="info" icon="ti ti-device-floppy">
                                            {{ trans('core/base::forms.save_and_continue') }}
                                        </x-core::button>
                                    @endif

                                    <x-core::button tag="a" :href="route('orders.inside-dhaka')" color="secondary"
                                        icon="ti ti-arrow-left">
                                        {{ trans('core/base::forms.cancel') }}
                                    </x-core::button>
                                </div>

                                <div class="mt-3 pt-3 border-top">
                                    <div class="row">
                                        <div class="col-6">
                                            <span class="text-muted">{{ trans('core/base::tables.created_at') }}:</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>{{ $dhakaArea->created_at ? $dhakaArea->created_at->format('M d, Y H:i') : 'N/A' }}</strong>
                                        </div>
                                    </div>

                                    @if ($dhakaArea->updated_at && $dhakaArea->updated_at != $dhakaArea->created_at)
                                        <div class="row mt-1">
                                            <div class="col-6">
                                                <span
                                                    class="text-muted">{{ trans('core/base::tables.updated_at') }}:</span>
                                            </div>
                                            <div class="col-6">
                                                <strong>{{ $dhakaArea->updated_at->format('M d, Y H:i') }}</strong>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </x-core::card.body>
                        </x-core::card>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
