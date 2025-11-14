<div class="customer-address-payment-form">
    <input type="hidden" name="update-tax-url" id="update-checkout-tax-url"
        value="{{ route('public.ajax.checkout.update-tax') }}">
    <div class="mb-3 form-group">
        @if (auth('customer')->check())
            <p>{{ __('Account') }}: <strong>{{ auth('customer')->user()->name }}</strong> - {!! Html::email(auth('customer')->user()->email) !!} (<a
                    href="{{ route('customer.logout') }}">{{ __('Logout') }})</a></p>
        @else
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Login') }}</a></p>
        @endif

    </div>

    {!! apply_filters('ecommerce_checkout_address_form_before') !!}

    @auth('customer')
        <div class="mb-3 form-group">
            @if ($isAvailableAddress)
                <label class="mb-2 form-label" for="address_id">{{ __('Select available addresses') }}:</label>
            @endif
            @php
                $oldSessionAddressId = old('address.address_id', $sessionAddressId);
            @endphp
            {{-- <div class="list-customer-address @if (!$isAvailableAddress) d-none @endif">
                <div class="select--arrow">
                    <select class="form-control" id="address_id" name="address[address_id]" @required($isAvailableAddress)>
                        <option value="new" selected @selected($oldSessionAddressId == 'new')>{{ __('Add new address...') }}</option>
                        @if ($isAvailableAddress)
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}">
                                    {{ $address->full_address }}</option>
                            @endforeach
                        @endif
                    </select>
                    <x-core::icon name="ti ti-chevron-down" />
                </div>
                <br>
                <div class="address-item-selected @if (!$sessionAddressId) d-none @endif">
                    @if ($isAvailableAddress && $oldSessionAddressId != 'new')
                        @if ($oldSessionAddressId && $addresses->contains('id', $oldSessionAddressId))
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $addresses->firstWhere('id', $oldSessionAddressId),
                            ])
                        @elseif ($defaultAddress = get_default_customer_address())
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $defaultAddress,
                            ])
                        @else
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => Arr::first($addresses),
                            ])
                        @endif
                    @endif
                </div>
                <div class="list-available-address d-none">
                    @if ($isAvailableAddress)
                        @foreach ($addresses as $address)
                            <div class="address-item-wrapper" data-id="{{ $address->id }}">
                                @include(
                                    'plugins/ecommerce::orders.partials.address-item',
                                    compact('address'))
                            </div>
                        @endforeach
                    @endif
                </div>
            </div> --}}
        </div>
    @endauth

    {{-- Old --}}
    {{-- <div class="address-form-wrapper @if (auth('customer')->check() && $oldSessionAddressId !== 'new' && $isAvailableAddress) d-none @endif"> --}}
    {{-- New --}}
    <div class="address-form-wrapper @if (auth('customer')->check() && false && $isAvailableAddress) d-none @endif">
        @if (!in_array('phone', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group mb-3 @error('address.phone') has-error @enderror">
                <div class="form-input-wrapper">
                    <input class="form-control" id="address_phone" name="address[phone]" autocomplete="phone"
                        type="tel" value="{{ old('address.phone') }}">
                    <label for="address_phone" style="font-weight: bold; color: #000;">{{ __('Phone') }}</label>
                    <div id="address-suggestion-box"></div>
                </div>
                {!! Form::error('address.phone', $errors) !!}
            </div>
        @endif
        <div class="form-group mb-3 @error('address.name') has-error @enderror">
            <div class="form-input-wrapper">
                <input class="form-control" id="address_name" name="address[name]" autocomplete="family-name"
                    type="text" value="{{ old('address.name') }}" required>
                <label for="address_name" style="font-weight: bold; color: #000;">{{ __('Full Name') }}</label>
            </div>
            {!! Form::error('address.name', $errors) !!}
        </div>

        <div class="row">
            @if (!in_array('email', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div class="col-12">
                    <div class="form-group mb-3 @error('address.email') has-error @enderror">
                        <div class="form-input-wrapper">
                            <input class="form-control" id="address_email" name="address[email]" autocomplete="email"
                                type="email" value="{{ old('address.email') }}" required>
                            <label for="address_email"
                                style="font-weight: bold; color: #000;">{{ __('Email') }}</label>
                        </div>
                        {!! Form::error('address.email', $errors) !!}
                    </div>
                </div>
            @endif
        </div>


        @php
            $deleveryOptions = \Botble\Ecommerce\Models\GlobalOption::whereIn('id', [7, 8])->get();

        @endphp
        <div class="row">
            @foreach ($deleveryOptions as $deleveryOption)
                <div class="col-6">
                    <div class="form-group mb-3 @error('address.deleveryArea') has-error @enderror">
                        @if ($loop->first)
                            <div class="select--arrow form-input-wrapper" id="insideOfDhakaContainer">
                                <select class="form-control" id="insideOfDhaka" name="address[is_inside_of_dhaka]"
                                    autocomplete="is_inside_of_dhaka" data-form-parent=".customer-address-payment-form"
                                    data-type="insideOfDhaka">
                                    <option value="">{{ __('Select Thana') }}</option>
                                    @foreach (\Botble\Ecommerce\Models\GlobalOptionValue::where('option_id', $deleveryOption->id)->get() as $globalOptionValue)
                                        <option value="{{ $globalOptionValue->id }}">
                                            {{ $globalOptionValue->option_value }}</option>
                                    @endforeach
                                </select>
                                <x-core::icon name="ti ti-chevron-down" />
                                <label for="address_state"
                                    style="font-weight: bold; color: #000;">{{ $deleveryOption->name }}</label>
                            </div>
                        @else
                            <div class="product-option-item-values" id="outSideOfDhakaContainer"
                                style="position: relative;">
                                <label for="outSideOfDhaka"
                                    style="position: absolute; top: -12px; background: #fff; padding: 0 5px;">{{ $deleveryOption->name }}</label>
                                <input name="options[115][option_type]" type="hidden" value="checkbox">
                                <div class="form-checkbox">
                                    <input id="outSideOfDhaka" name="address[is_out_side_dhaka]"
                                        data-extra-price="{{ \Botble\Ecommerce\Models\GlobalOptionValue::where('option_id', $deleveryOption->id)?->first()?->affect_price }}"
                                        type="checkbox" data-form-parent=".customer-address-payment-form"
                                        data-type="outSideOfDhaka" />
                                    <label for="outSideOfDhaka" style="font-size: 12px; font-weight: bold;">কুরিয়ার এর
                                        মাধ্যমে</label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>


        {{-- <div class="row">
                <div class="col-sm-6 col-12">
                    <div class="form-group mb-3 @error('address.deleveryArea') has-error @enderror">
                            <div class="select--arrow form-input-wrapper" id="insideOfDhakaContainer">
                                <select
                                    class="form-control"
                                    id="inside_dhaka_select"
                                    name="address[is_inside_of_dhaka]"
                                    autocomplete="is_inside_of_dhaka"
                                    data-form-parent=".customer-address-payment-form"
                                    data-type="insideOfDhaka"
                                >
                                <option value="">{{ __('Select Area...') }}</option>
                                <option value="1">Hello Brother</option>
                                </select>
                                <x-core::icon name="ti ti-chevron-down" />
                                <label for="address_state">{{ $deleveryOption->name }}</label>
                            </div>
                    </div>
                </div>
        </div> --}}

        <div class="row" id="rh_dhaka_area_container">
            <div class="col-sm-12 col-12">
                <div class="form-group mb-3">
                    <div class="select--arrow form-input-wrapper" id="insideOfDhakaContainer">
                        <select class="form-control" id="inside_dhaka_select" name="address[inside_dhaka]"
                            autocomplete="inside_dhaka_select" data-form-parent=".customer-address-payment-form"
                            data-type="inside_dhaka">
                            <option value="">{{ __('Select Area...') }}</option>
                        </select>
                        <x-core::icon name="ti ti-chevron-down" />
                        <label for="address_state" style="font-weight: bold; color: #000;">{{ __('Area') }}</label>
                    </div>
                </div>
            </div>
        </div>

        {!! apply_filters('ecommerce_checkout_address_form_inside', null) !!}

        @if (EcommerceHelper::isUsingInMultipleCountries() && !in_array('country', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group mb-3 @error('address.country') has-error @enderror">
                <div class="select--arrow form-input-wrapper">
                    <select class="form-control" id="address_country" name="address[country]" autocomplete="country"
                        data-form-parent=".customer-address-payment-form" data-type="country" required>
                        @foreach (EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                            <option value="{{ $countryCode }}" @selected(old('address.country', Arr::get($sessionCheckoutData, 'country', EcommerceHelper::getDefaultCountryId())) == $countryCode)>
                                {{ $countryName }}
                            </option>
                        @endforeach
                    </select>
                    <x-core::icon name="ti ti-chevron-down" />
                    <label for="address_country" style="font-weight: bold; color: #000;">{{ __('Country') }}</label>
                </div>
                {!! Form::error('address.country', $errors) !!}
            </div>
        @else
            <input id="address_country" name="address[country]" type="hidden"
                value="{{ EcommerceHelper::getFirstCountryId() }}">
        @endif

        <div class="row" id="rh_bivag_and_jela">
            @if (!in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div class="col-sm-6 col-12">
                    <div class="form-group mb-3 @error('address.state') has-error @enderror">
                        @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                            <div class="select--arrow form-input-wrapper">
                                <select class="form-control" id="address_state" name="address[state]"
                                    autocomplete="state" data-form-parent=".customer-address-payment-form"
                                    data-type="state" data-url="{{ route('ajax.states-by-country') }}">
                                    <option value="">{{ __('Select state...') }}</option>
                                    @if (old('address.country', Arr::get($sessionCheckoutData, 'country') ?: EcommerceHelper::getDefaultCountryId()) ||
                                            !EcommerceHelper::isUsingInMultipleCountries())
                                        @foreach (EcommerceHelper::getAvailableStatesByCountry(old('address.country', Arr::get($sessionCheckoutData, 'country') ?: EcommerceHelper::getDefaultCountryId())) as $stateId => $stateName)
                                            <option value="{{ $stateId }}"
                                                @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) == $stateId) selected @endif>{{ $stateName }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-core::icon name="ti ti-chevron-down" />
                                <label for="address_state"
                                    style="font-weight: bold; color: #000;">{{ __('State') }}</label>
                            </div>
                        @else
                            <div class="form-input-wrapper">
                                <input class="form-control" id="address_state" name="address[state]"
                                    autocomplete="state" type="text"
                                    value="{{ old('address.state', Arr::get($sessionCheckoutData, 'state')) }}">
                                <label for="address_state"
                                    style="color: #000; font-weight: bold;">{{ __('State') }}</label>
                            </div>
                        @endif
                        {!! Form::error('address.state', $errors) !!}
                    </div>
                </div>
            @endif

            @if (!in_array('city', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div @class([
                    'col-sm-6 col-12' => !in_array(
                        'state',
                        EcommerceHelper::getHiddenFieldsAtCheckout()),
                    'col-12' => in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()),
                ])>
                    <div class="form-group mb-3 @error('address.city') has-error @enderror">
                        @if (EcommerceHelper::useCityFieldAsTextField())
                            <div class="form-input-wrapper">
                                <input class="form-control" id="address_city" name="address[city]"
                                    autocomplete="city" type="text"
                                    value="{{ old('address.city', Arr::get($sessionCheckoutData, 'city')) }}"
                                    required>
                                <label for="address_city"
                                    style="font-weight: bold; color: #000;">{{ __('City') }}</label>
                            </div>
                        @else
                            <div class="select--arrow form-input-wrapper">
                                <select class="form-control" id="address_city" name="address[city]"
                                    autocomplete="city" data-type="city" data-using-select2="false"
                                    data-url="{{ route('ajax.cities-by-state') }}" required>
                                    <option value="">{{ __('Select city...') }}</option>
                                    @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) ||
                                            in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()))
                                        @foreach (EcommerceHelper::getAvailableCitiesByState(old('address.state', Arr::get($sessionCheckoutData, 'state')), old('address.country', Arr::get($sessionCheckoutData, 'country'))) as $cityId => $cityName)
                                            <option value="{{ $cityId }}"
                                                @if (old('address.city', Arr::get($sessionCheckoutData, 'city')) == $cityId) selected @endif>{{ $cityName }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-core::icon name="ti ti-chevron-down" />
                                <label for="address_city"
                                    style="font-weight: bold; color: #000;">{{ __('City') }}</label>
                            </div>
                        @endif
                        {!! Form::error('address.city', $errors) !!}
                    </div>
                </div>
            @endif
        </div>


        {{-- <div class="row">
            <div class="col-sm-6 col-12">
                <div class="form-group mb-3 @error('address.road_name') has-error @enderror">
                    <div class="form-input-wrapper">
                        <input class="form-control" id="address_city" name="address[road_name]" autocomplete="city"
                            type="text" value="{{ old('address.road_name') }}" required>
                        <label for="address_city">{{ __('Road Name') }}</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-12">
                <div class="form-group mb-3 @error('address.house_name') has-error @enderror">
                    <div class="form-input-wrapper">
                        <input class="form-control" id="house_name" name="address[house_name]" autocomplete="city"
                            type="text" value="{{ old('address.house_name') }}" required>
                        <label for="address_city">{{ __('House Name/Number') }}</label>
                    </div>
                </div>
            </div>
        </div> --}}


        <div id="courier"></div>

        <div class="row">
            <div class="col-sm-12 col-12">
                <div class="form-group mb-3 @error('address.map_location') has-error @enderror">
                    <div class="form-input-wrapper map-location-wrapper" style="position: relative;">
                        <input class="form-control" id="map_location" name="address[map_location]"
                            autocomplete="city" type="text"
                            value="{{ old('address.map_location', Arr::get($sessionCheckoutData, 'map_location')) }}"
                            style="padding-right: 120px;">
                        <label for="map_location"
                            style="font-weight: bold; color: #000;">{{ __('Map Location (optional)') }}</label>
                        <div class="map-location-icons"
                            style="
                            position: absolute;
                            right: 10px;
                            top: 50%;
                            transform: translateY(-50%);
                            display: flex;
                            gap: 8px;
                            z-index: 10;
                        ">
                            <button type="button" class="btn btn-link map-icon-btn" id="openGoogleMaps"
                                title="Open Google Maps"
                                style="
                                padding: 4px 8px;
                                color: #666;
                                font-size: 16px;
                                line-height: 1;
                                border: none;
                                background: transparent;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <svg class="icon svg-icon-ti-ti-external-link" xmlns="http://www.w3.org/2000/svg"
                                    width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                                    <path d="M11 13l9 -9"></path>
                                    <path d="M15 4h5v5"></path>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-link location-icon-btn" id="getCurrentLocation"
                                title="Get Current Location"
                                style="
                                padding: 4px 8px;
                                color: #666;
                                font-size: 16px;
                                line-height: 1;
                                border: none;
                                background: transparent;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <svg class="icon svg-icon-ti-ti-current-location" xmlns="http://www.w3.org/2000/svg"
                                    width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="map-help-text mt-2">
                        <small class="text-muted">
                            <svg class="icon svg-icon-ti-ti-external-link" xmlns="http://www.w3.org/2000/svg"
                                width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="margin-right: 5px; vertical-align: middle;">
                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                                <path d="M11 13l9 -9"></path>
                                <path d="M15 4h5v5"></path>
                            </svg>
                            Click to open Google Maps
                            <svg class="icon svg-icon-ti-ti-current-location" xmlns="http://www.w3.org/2000/svg"
                                width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="margin: 0 8px; vertical-align: middle;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Click to get current location
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {!! apply_filters('ecommerce_checkout_address_form_after_city_field', null, $sessionCheckoutData) !!}

        @if (!in_array('address', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group mb-3 @error('address.address') has-error @enderror">
                <div class="form-input-wrapper">
                    <input class="form-control" id="address_address" name="address[address]" autocomplete="address"
                        type="text" value="{{ old('address.address') }}" required>
                    <label for="address_address"style="font-weight: bold; color: #000;">{{ __('Address') }}</label>
                </div>
                {!! Form::error('address.address', $errors) !!}
            </div>
        @endif

        @if (EcommerceHelper::isZipCodeEnabled())
            <div class="form-group mb-3 @error('address.zip_code') has-error @enderror">
                <div class="form-input-wrapper">
                    <input class="form-control" id="address_zip_code" name="address[zip_code]"
                        autocomplete="postal-code" type="text" value="{{ old('address.zip_code') }}" required>
                    <label for="address_zip_code"
                        style="color: #000; font-weight: bold;">{{ __('Zip code') }}</label>
                </div>
                {!! Form::error('address.zip_code', $errors) !!}
            </div>
        @endif
    </div>

    @if (!auth('customer')->check())
        <div id="register-an-account-wrapper">
            <div class="mb-3">
                <label class="form-check">
                    <input id="create_account" name="create_account" type="checkbox" value="1"
                        class="form-check-input" @if (old('create_account') == 1) checked @endif>
                    <span class="form-check-label">{{ __('Register an account with above information?') }}</span>
                </label>
            </div>

            <div class="password-group @if (!$errors->has('password') && !$errors->has('password_confirmation')) d-none @endif">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group  @error('password') has-error @enderror">
                            <div class="form-input-wrapper">
                                <input class="form-control" id="password" name="password" type="password"
                                    autocomplete="new-password">
                                <label for="password">{{ __('Password') }}</label>
                            </div>
                            {!! Form::error('password', $errors) !!}
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group @error('password_confirmation') has-error @enderror">
                            <div class="form-input-wrapper">
                                <input class="form-control" id="password-confirm" name="password_confirmation"
                                    type="password" autocomplete="password-confirmation">
                                <label for="password-confirm">{{ __('Password confirmation') }}</label>
                            </div>
                            {!! Form::error('password_confirmation', $errors) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {!! apply_filters('ecommerce_checkout_address_form_after', null, $sessionCheckoutData) !!}
</div>

<style>
    #address-suggestion-box {
        position: absolute;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
        top: 100%;
        left: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-top: 4px;
    }

    .form-input-wrapper {
        position: relative;
    }

    #address-suggestion-box .address-suggestion {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f5f5f5;
        transition: background-color 0.2s ease;
    }

    #address-suggestion-box .address-suggestion:last-child {
        border-bottom: none;
    }

    #address-suggestion-box .address-suggestion:hover {
        background-color: #f9f9f9;
    }

    .address-suggestion strong {
        display: block;
        font-size: 1rem;
        color: #333;
        margin-bottom: 4px;
    }

    .address-suggestion span {
        display: block;
        font-size: 0.875rem;
        color: #666;
        line-height: 1.4;
    }

    .address-suggestion-header {
        padding: 12px 15px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: bold;
        color: #495057;
    }

    .address-suggestion-footer {
        padding: 12px 15px;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.11.0/axios.min.js"
    integrity="sha512-h9644v03pHqrIHThkvXhB2PJ8zf5E9IyVnrSfZg8Yj8k4RsO4zldcQc4Bi9iVLUCCsqNY0b4WXVV4UB+wbWENA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        const addressPhone = document.getElementById('address_phone');
        const addressSuggestionBox = document.getElementById('address-suggestion-box');

        addressPhone.addEventListener('input', function() {
            const phone = this.value;
            if (phone.length === 11) {
                axios.post('{{ route('public.ajax.get-customer-address-by-phone') }}', {
                    phone: phone
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    addressSuggestionBox.innerHTML = '';
                    if (response.data.data.length > 0) {
                        // Add header section
                        console.log(response.data.data);
                        const headerDiv = document.createElement('div');
                        headerDiv.classList.add('address-suggestion-header');
                        headerDiv.innerHTML = '<strong>আপনার পুর্বে থাকা এড্রেস লিস্ট</strong>';
                        addressSuggestionBox.appendChild(headerDiv);

                        response.data.data.forEach(address => {
                            const addressDiv = document.createElement('div');
                            addressDiv.classList.add('address-suggestion');

                            let locationDetails = '';
                            if (address.city_name && address.state_name) {
                                locationDetails +=
                                    `${address.city_name}, ${address.state_name}`;
                            }
                            if (address.thana_name && address.area_name) {
                                if (locationDetails) {
                                    locationDetails += ', ';
                                }
                                locationDetails +=
                                    `${address.area_name}, ${address.thana_name}`;
                            }

                            if (!locationDetails) {
                                locationDetails = [address.country].filter(Boolean)
                                    .join(', ');
                            }


                            let fullAddress = `${address.address}, ${locationDetails}`;

                            addressDiv.innerHTML = `
                                <strong>${address.name}</strong>
                                <span>${fullAddress}</span>
                                <span>Phone: ${address.phone}</span>
                            `;

                            addressDiv.addEventListener('click', function() {
                                // Set a hidden field to indicate this is an existing address selection
                                let existingAddressIdField = document
                                    .getElementById('existing_address_id');
                                if (!existingAddressIdField) {
                                    existingAddressIdField = document
                                        .createElement('input');
                                    existingAddressIdField.type = 'hidden';
                                    existingAddressIdField.id =
                                        'existing_address_id';
                                    existingAddressIdField.name =
                                        'address[address_id]';
                                    document.querySelector(
                                            '.customer-address-payment-form')
                                        .appendChild(existingAddressIdField);
                                }
                                existingAddressIdField.value = address.id;

                                document.getElementById('address_name').value =
                                    address.name;
                                document.getElementById('address_email').value =
                                    address.email;
                                document.getElementById('address_phone').value =
                                    address.phone;
                                document.getElementById('address_address')
                                    .value = address.address;
                                document.getElementById('address_city').value =
                                    address.city;
                                document.getElementById('address_state').value =
                                    address.state;
                                if (document.getElementById(
                                        'address_zip_code')) {
                                    document.getElementById('address_zip_code')
                                        .value = address.zip_code;
                                }
                                if (document.getElementById('map_location')) {
                                    document.getElementById('map_location')
                                        .value = address.map_location;
                                }

                                const countrySelect = document.getElementById(
                                    'address_country');
                                if (countrySelect) {
                                    countrySelect.value = address.country;
                                }

                                const insideOfDhakaSelect = document
                                    .getElementById('insideOfDhaka');
                                const outSideOfDhakaCheckbox = document
                                    .getElementById('outSideOfDhaka');

                                if (address.is_inside_dhaka) {
                                    insideOfDhakaSelect.value = address
                                        .is_inside_dhaka;
                                    insideOfDhakaSelect.dispatchEvent(new Event(
                                        'change', {
                                            bubbles: true
                                        }));
                                    outSideOfDhakaCheckbox.checked = false;
                                    outSideOfDhakaCheckbox.dispatchEvent(
                                        new Event('change', {
                                            bubbles: true
                                        }));

                                    setTimeout(() => {
                                        const insideDhakaSelect =
                                            document.getElementById(
                                                'inside_dhaka_select');
                                        if (insideDhakaSelect) {
                                            insideDhakaSelect.value =
                                                address.inside_dhaka;
                                        }
                                    }, 1500);
                                } else if (address.is_out_side_dhaka) {
                                    outSideOfDhakaCheckbox.checked = true;
                                    outSideOfDhakaCheckbox.dispatchEvent(
                                        new Event('change', {
                                            bubbles: true
                                        }));
                                    insideOfDhakaSelect.value = '';
                                    insideOfDhakaSelect.dispatchEvent(new Event(
                                        'change', {
                                            bubbles: true
                                        }));

                                    if (address.courier_option) {
                                        setTimeout(() => {
                                            const courierRadio =
                                                document.querySelector(
                                                    `input[name="address[courier_option]"][value="${address.courier_option}"]`
                                                );
                                            if (courierRadio) {
                                                courierRadio.checked =
                                                    true;
                                            }
                                        }, 500);
                                    }
                                } else {
                                    insideOfDhakaSelect.value = '';
                                    insideOfDhakaSelect.dispatchEvent(new Event(
                                        'change', {
                                            bubbles: true
                                        }));
                                    outSideOfDhakaCheckbox.checked = false;
                                    outSideOfDhakaCheckbox.dispatchEvent(
                                        new Event('change', {
                                            bubbles: true
                                        }));
                                }

                                addressSuggestionBox.style.display = 'none';
                            });
                            addressSuggestionBox.appendChild(addressDiv);
                        });

                        // Add footer section with "Add new address" button
                        const footerDiv = document.createElement('div');
                        footerDiv.classList.add('address-suggestion-footer');
                        footerDiv.innerHTML =
                            '<button type="button" class="btn btn-primary btn-sm w-100" id="add-new-address-btn">নতুন ঠিকানা যোগ করুন</button>';
                        addressSuggestionBox.appendChild(footerDiv);

                        // Add event listener for the "Add new address" button
                        const addNewAddressBtn = footerDiv.querySelector(
                            '#add-new-address-btn');
                        addNewAddressBtn.addEventListener('click', function() {
                            addressSuggestionBox.style.display = 'none';
                        });

                        addressSuggestionBox.style.display = 'block';
                    } else {
                        addressSuggestionBox.style.display = 'none';
                    }
                });
            } else {
                addressSuggestionBox.style.display = 'none';
            }
        });

        const rh_bivag_and_jela = document.getElementById('rh_bivag_and_jela')
        const insideOfDhaka = document.getElementById('insideOfDhaka')
        const outSideOfDhaka = document.getElementById('outSideOfDhaka')
        const outSideOfDhakaContainer = document.getElementById('outSideOfDhakaContainer')
        const rh_dhaka_area_container = document.getElementById('rh_dhaka_area_container')
        const courierHTML = `<div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group mb-3 mt-3">
                    <div class="product-option-item-values" id="deleveryChargePayment" style="position: relative;">
                        <div class="form-checkbox">
                            <input checked id="sundorbon_courier" name="address[courier_option]" value="Sundorbon Courier" data-extra-price="700" type="radio">
                            <label for="sundorbon_courier">Sundorbon Courier</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group mb-3 mt-3">
                    <div class="product-option-item-values" id="fullPayment" style="position: relative;">
                        <div class="form-checkbox">
                            <input id="sa_paribahan" name="address[courier_option]" value="SA Paribahan" data-extra-price="700" type="radio">
                            <label for="sa_paribahan">SA Paribahan</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        const courier = document.getElementById('courier');


        if (insideOfDhaka.value == (null || '') && !outSideOfDhaka
            .checked) {
            rh_bivag_and_jela.style.display = 'none'
        }
        if (insideOfDhaka.value == (null || '') && !outSideOfDhaka
            .checked) {
            rh_dhaka_area_container.style.display = 'none';
        }




        let customAdditionalPayment = document.getElementById(
            'customAdditionalPayment')
        insideOfDhaka.addEventListener('change', function(e) {
            if (e.target.value == (null || '') &&
                outSideOfDhaka.checked) {
                rh_bivag_and_jela.style.display = 'flex'
            } else {
                rh_bivag_and_jela.style.display = 'none'
                outSideOfDhaka.checked = false
                const insideDhakaValue = e.target.value;
                const inside_dhaka_select = document
                    .getElementById('inside_dhaka_select')

                courier.innerHTML = '';
                if (insideDhakaValue != (null || '')) {
                    axios.get(
                        ` /get-dhaka-area/${insideDhakaValue}`, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => {
                        inside_dhaka_select
                            .innerHTML = '';
                        response.data.areas.forEach(
                            el => {
                                const
                                    createOption =
                                    document
                                    .createElement(
                                        'option'
                                    )
                                createOption
                                    .value = el
                                    .id;
                                createOption
                                    .textContent =
                                    el.name;
                                inside_dhaka_select
                                    .appendChild(
                                        createOption
                                    )
                            })
                        rh_dhaka_area_container
                            .style.display =
                            'block';
                        rh_bivag_and_jela.style
                            .display = 'none';
                    });
                }

            }


            setTimeout(() => {
                customAdditionalPayment = document
                    .getElementById(
                        'customAdditionalPayment')
                if (e.target.value != (null ||
                        '')) {
                    customAdditionalPayment
                        .classList.add('d-none')
                }
            }, 500);
        })
        outSideOfDhaka.addEventListener('change', function(e) {
            if (e.target.checked) {
                rh_bivag_and_jela.style.display = 'flex'
                insideOfDhaka.value = '';
                rh_dhaka_area_container.style.display =
                    'none';
                courier.innerHTML = courierHTML;

            } else {
                console.log('ami cheking nai')
                rh_bivag_and_jela.style.display = 'none'
                courier.innerHTML = '';
            }
            setTimeout(() => {
                customAdditionalPayment = document
                    .getElementById(
                        'customAdditionalPayment')
                if (e.target.checked) {
                    customAdditionalPayment
                        .classList.remove('d-none')
                } else {
                    customAdditionalPayment
                        .classList.add('d-none')
                }
            }, 500);
        })

        // Map Location functionality
        const mapLocationInput = document.getElementById(
            'map_location');
        const openGoogleMapsBtn = document.getElementById(
            'openGoogleMaps');
        const getCurrentLocationBtn = document.getElementById(
            'getCurrentLocation');

        // Open Google Maps
        openGoogleMapsBtn.addEventListener('click', function() {
            const currentValue = mapLocationInput.value
                .trim();
            let mapsUrl = 'https://www.google.com/maps';

            if (currentValue) {
                // If there's a location in the input, search for it
                mapsUrl += '/search/?api=1&query=' +
                    encodeURIComponent(currentValue);
            }

            window.open(mapsUrl, '_blank');
        });

        // Get current location and auto-populate input field
        getCurrentLocationBtn.addEventListener('click', function() {
            if ('geolocation' in navigator) {
                // Show loading state
                getCurrentLocationBtn.innerHTML =
                    '<i class="ti ti-loader"></i>';
                getCurrentLocationBtn.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords
                            .latitude;
                        const longitude = position
                            .coords.longitude;

                        // Use reverse geocoding to get address from coordinates
                        fetch(
                                `
                                                https
                                                : //api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=en`
                            )
                            .then(response => response
                                .json())
                            .then(data => {
                                let locationString =
                                    '';

                                if (data.city &&
                                    data
                                    .principalSubdivision
                                ) {
                                    locationString =
                                        `${data.city}, ${data.principalSubdivision}`;
                                } else if (data
                                    .localityInfo &&
                                    data
                                    .localityInfo
                                    .administrative
                                ) {
                                    locationString =
                                        data
                                        .localityInfo
                                        .administrative
                                        .join(
                                            ', ');
                                } else {
                                    locationString =
                                        `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                                }

                                // Convert location to Google Maps URL and auto-populate the input field
                                const
                                    encodedLocationString =
                                    encodeURIComponent(
                                        locationString
                                    );
                                const
                                    locationMapsUrl =
                                    `https://www.google.com/maps/search/?api=1&query=${encodedLocationString}`;

                                // Auto-populate the input field with the Google Maps URL
                                mapLocationInput
                                    .value =
                                    locationMapsUrl;

                                // Reset button state - use the current location icon
                                getCurrentLocationBtn
                                    .innerHTML =
                                    '<svg class="icon svg-icon-ti-ti-current-location" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
                                getCurrentLocationBtn
                                    .disabled =
                                    false;

                                // Show success feedback
                                getCurrentLocationBtn
                                    .title =
                                    'Location detected successfully!';
                                setTimeout(() => {
                                    getCurrentLocationBtn
                                        .title =
                                        'Get Current Location';
                                }, 3000);
                            })
                            .catch(error => {
                                console.error(
                                    'Error getting location details:',
                                    error);

                                // Fallback: convert coordinates to Google Maps URL if reverse geocoding fails
                                const
                                    coordinateString =
                                    `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                                const
                                    encodedCoordinates =
                                    encodeURIComponent(
                                        coordinateString
                                    );
                                const
                                    coordinateMapsUrl =
                                    `https://www.google.com/maps/search/?api=1&query=${encodedCoordinates}`;

                                // Auto-populate with Google Maps URL even for coordinates
                                mapLocationInput
                                    .value =
                                    coordinateMapsUrl;

                                // Reset button state
                                getCurrentLocationBtn
                                    .innerHTML =
                                    '<svg class="icon svg-icon-ti-ti-external-link" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path><path d="M11 13l9 -9"></path><path d="M15 4h5v5"></path></svg>';
                                getCurrentLocationBtn
                                    .disabled =
                                    false;
                            });
                    },
                    function(error) {
                        let errorMessage = '';
                        switch (error.code) {
                            case error
                            .PERMISSION_DENIED:
                                errorMessage =
                                    'Location access denied by user. Please enable location permissions and try again.';
                                break;
                            case error
                            .POSITION_UNAVAILABLE:
                                errorMessage =
                                    'Location information unavailable. Please check your GPS settings.';
                                break;
                            case error.TIMEOUT:
                                errorMessage =
                                    'Location request timed out. Please try again.';
                                break;
                            default:
                                errorMessage =
                                    'An unknown error occurred while getting your location.';
                                break;
                        }

                        alert('Error getting location: ' +
                            errorMessage);

                        // Reset button state even on error
                        getCurrentLocationBtn
                            .innerHTML =
                            '<svg class="icon svg-icon-ti-ti-external-link" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path><path d="M11 13l9 -9"></path><path d="M15 4h5v5"></path></svg>';
                        getCurrentLocationBtn.disabled =
                            false;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000 // 5 minutes
                    }
                );
            } else {
                alert(
                    'Geolocation is not supported by this browser. Please update your browser or enable location services.'
                );
            }
        });

        // Auto-format location as Google Maps URL when user types
        mapLocationInput.addEventListener('input', function() {
            const locationValue = this.value.trim();

            // Debounce the URL update to avoid too many updates
            clearTimeout(this.urlUpdateTimeout);
            this.urlUpdateTimeout = setTimeout(function() {
                    if (locationValue && locationValue
                        .includes('maps/search/') ===
                        false) {
                        // If it's not already a URL, convert it to Google Maps URL
                        const encodedLocation =
                            encodeURIComponent(
                                locationValue);
                        const mapsUrl =
                            `https://www.google.com/maps/search/?api=1&query=${encodedLocation}`;

                        // Update the input field with the Google Maps URL
                        mapLocationInput.value =
                            mapsUrl;
                    }
                },
                1500
            ); // Wait 1.5 seconds after user stops typing
        });
    })
</script>
