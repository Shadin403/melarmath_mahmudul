<x-core::form :url="$url">
    <input name="order_id" type="hidden" value="{{ $orderId }}" />

    <div class="row">
        <div class="col-md-6">
            <x-core::form.text-input :label="__('পুরো নাম')" :required="true" name="name" :value="$address->name"
                :placeholder="__('পুরো নাম লিখুন')" />
        </div>

        <div class="col-md-6">
            <x-core::form.text-input :label="__('ফোন নাম্বার')" name="phone" :value="$address->phone" :placeholder="__('ফোন নাম্বার')" />
        </div>
    </div>

    <x-core::form.text-input :label="__('ইমেইল')" type="email" name="email" :value="$address->email" :placeholder="__('ইমেইল লিখুন')" />


    <fieldset style="border: 3px solid #1f8ef1; padding: 10px; border-radius: 4px; margin-bottom: 12px;">
        <legend style="font-weight:600; color:#0d6efd;">
            {{ __('ঢাকার বাইরে') }}
        </legend>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="order-outside-mode" name="is_out_side_dhaka"
                    value="1" @checked((bool) data_get($address, 'is_out_side_dhaka'))>
                <label class="form-check-label" for="order-outside-mode">
                    {{ __('কুরিয়ার এর মাধ্যমে') }}
                </label>
            </div>
        </div>
    </fieldset>

    @if (EcommerceHelper::isUsingInMultipleCountries())
        <x-core::form.select :label="__('দেশ')" name="country" data-type="country" :options="EcommerceHelper::getAvailableCountries()" :value="$address->country"
            :searchable="true" />
    @else
        <input name="country" type="hidden" value="{{ EcommerceHelper::getFirstCountryId() }}">
    @endif

    @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
        <x-core::form.select :label="__('বিভাগ')" name="state" data-type="state" :data-url="route('ajax.states-by-country')" :searchable="true">
            <option value="">{{ __('বিভাগ নির্বাচন করুন') }}</option>
            @if ($address->state || !EcommerceHelper::isUsingInMultipleCountries())
                @foreach (EcommerceHelper::getAvailableStatesByCountry($address->country) as $stateId => $stateName)
                    <option value="{{ $stateId }}" @if ($address->state == $stateId) selected @endif>
                        {{ $stateName }}
                    </option>
                @endforeach
            @endif
        </x-core::form.select>
    @else
        <x-core::form.text-input :label="__('বিভাগ')" name="state" :value="$address->state" :placeholder="__('বিভাগ লিখুন')" />
    @endif

    @if (!EcommerceHelper::useCityFieldAsTextField())
        <x-core::form.select :label="__('জেলা')" name="city" data-type="city" data-using-select2="false"
            :data-url="route('ajax.cities-by-state')">
            <option value="">{{ __('জেলা নির্বাচন করুন') }}</option>
            @if ($address->city)
                @foreach (EcommerceHelper::getAvailableCitiesByState($address->state) as $cityId => $cityName)
                    <option value="{{ $cityId }}" @if ($address->city == $cityId) selected @endif>
                        {{ $cityName }}
                    </option>
                @endforeach
            @endif
        </x-core::form.select>
    @else
        <x-core::form.text-input :label="__('জেলা')" name="city" :value="$address->city" :placeholder="__('জেলা লিখুন')" />
    @endif

    {{-- ✅ Fixed section starts here --}}
    <x-core::form.select :label="'ঢাকার ভিতরে'" name="is_inside_of_dhaka" id="rh_inside_thana" data-type="inside_thana"
        data-using-select2="false">
        <option value="">{{ __('থানা নির্বাচন করুন') }}</option>
        @foreach (\Botble\Ecommerce\Models\GlobalOptionValue::where('option_id', 7)->get() as $globalOptionValue)
            <option value="{{ $globalOptionValue->id }}" @if ($globalOptionValue->id == $address->is_inside_of_dhaka) selected @endif>
                {{ $globalOptionValue->option_value }}
            </option>
        @endforeach
    </x-core::form.select>

    <x-core::form.select :label="__('ঢাকার ভিতরে এরিয়া')" name="inside_dhaka" id="rh_inside_area" data-type="inside_area"
        data-using-select2="false">
        <option value="">{{ __('এলাকা নির্বাচন করুন') }}</option>
    </x-core::form.select>
    {{-- ✅ Fixed section ends here --}}

    <x-core::form.text-input :label="__('বিস্তারিত ঠিকানা')" :required="true" name="address" :value="$address->address"
        :placeholder="__('ঠিকানা লিখুন')" />

    {{-- Map Location Field - Always visible like name, phone, email --}}
    <x-core::form.text-input :label="__('ডেলিভারি এলাকা অবস্থান')" type="url" name="map_location" :value="$address->map_location"
        :placeholder="__('Google Maps বা অন্য কোনো মানচিত্র লিংক লিখুন (ঐচ্ছিক)')" />
    <small class="text-muted mb-3 d-block">
        <i class="fas fa-info-circle"></i> গুগল ম্যাপস থেকে আপনার অবস্থানের লিংক কপি করে এখানে পেস্ট করুন
    </small>
    <br>

    {{-- Courier Option Field - Only show when outside Dhaka --}}
    <div id="courier-option-wrapper" style="display: none;">
        <fieldset style="border: 2px solid #28a745; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <legend style="font-weight: 600; color: #28a745; padding: 0 10px;">কুরিয়ার সেবা নির্বাচন</legend>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="sundorbon_courier" name="courier_option"
                            value="Sundorbon Courier" @if ($address->courier_option == 'Sundorbon Courier') checked @endif>
                        <label class="form-check-label" for="sundorbon_courier">
                            <strong>Sundorbon Courier</strong><br>
                            <small class="text-muted">সুন্দরবন কুরিয়ার - ২-৩ দিন</small>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="sa_paribahan" name="courier_option"
                            value="SA Paribahan" @if ($address->courier_option == 'SA Paribahan') checked @endif>
                        <label class="form-check-label" for="sa_paribahan">
                            <strong>SA Paribahan</strong><br>
                            <small class="text-muted">এস এ পরিবহন - ১-২ দিন</small>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <small class="text-info">
                    <i class="fas fa-info-circle"></i> ঢাকার বাইরে ডেলিভারির জন্য কুরিয়ার সেবা নির্বাচন আবশ্যক।
                </small>
            </div>
        </fieldset>
    </div>

    @if (EcommerceHelper::isZipCodeEnabled())
        <x-core::form.text-input :label="__('জিপ কোড')" name="zip_code" :value="$address->zip_code" :placeholder="__('জিপ কোড লিখুন')" />
    @endif
</x-core::form>


<script>
    const rh_inside_thana = document.getElementById('rh_inside_thana');
    const rh_inside_area = document.getElementById('rh_inside_area');
    const outsideCheckbox = document.getElementById('order-outside-mode');
    const courierWrapper = document.getElementById('courier-option-wrapper');

    // Try to locate state/city inputs (could be select or text)
    const stateInput = document.querySelector('[name="state"], #state, #address_state');
    const cityInput = document.querySelector('[name="city"], #city, #address_city');

    const getWrapper = (el) => {
        if (!el) return null;
        const group = el.closest('.form-group');
        if (group) return group;
        const inputWrap = el.closest('.form-input-wrapper');
        return inputWrap ? inputWrap.parentElement : el.parentElement;
    };

    const thanaWrap = getWrapper(rh_inside_thana);
    const areaWrap = getWrapper(rh_inside_area);
    const stateWrap = getWrapper(stateInput);
    const cityWrap = getWrapper(cityInput);

    const showInsideDhaka = () => {
        if (thanaWrap) thanaWrap.style.display = '';
        if (areaWrap) areaWrap.style.display = '';
        if (stateWrap) stateWrap.style.display = 'none';
        if (cityWrap) cityWrap.style.display = 'none';
        if (courierWrapper) courierWrapper.style.display = 'none';
    };

    const showOutsideDhaka = () => {
        if (thanaWrap) thanaWrap.style.display = 'none';
        if (areaWrap) areaWrap.style.display = 'none';
        if (stateWrap) stateWrap.style.display = '';
        if (cityWrap) cityWrap.style.display = '';
        if (courierWrapper) courierWrapper.style.display = 'block';
    };

    const resetAreaOptions = () => {
        if (!rh_inside_area) return;
        rh_inside_area.innerHTML = '<option value="">{{ __('Select Area...') }}</option>';
    };

    const resetStateCity = () => {
        if (stateInput) {
            if (stateInput.tagName && stateInput.tagName.toLowerCase() === 'select') {
                stateInput.selectedIndex = 0;
                stateInput.dispatchEvent(new Event('change'));
            } else {
                stateInput.value = '';
            }
        }
        if (cityInput) {
            if (cityInput.tagName && cityInput.tagName.toLowerCase() === 'select') {
                cityInput.selectedIndex = 0;
                cityInput.dispatchEvent(new Event('change'));
            } else {
                cityInput.value = '';
            }
        }
    };

    const applyMode = () => {
        if (outsideCheckbox && outsideCheckbox.checked) {
            showOutsideDhaka();
        } else {
            showInsideDhaka();
        }
    };

    rh_inside_thana.addEventListener('change', function() {
        const thanaId = this.value;
        resetAreaOptions();

        if (thanaId) {
            fetch(`/get-dhaka-area/${thanaId}`)
                .then(response => response.json())
                .then(data => {
                    data?.areas?.forEach(area => {
                        const option = document.createElement('option');
                        option.value = area.id;
                        option.textContent = area.name;
                        rh_inside_area.appendChild(option);
                    });
                    // Auto-select first fetched area if available
                    if (data?.areas && data.areas.length > 0) {
                        rh_inside_area.selectedIndex = 1; // index 0 is placeholder
                        rh_inside_area.dispatchEvent(new Event('change'));
                    }
                })
                .catch(error => console.error('Error fetching areas:', error));
        }
    });
    if (rh_inside_thana.value != (null || '')) {
        rh_inside_thana.dispatchEvent(new Event('change'))
    }

    setTimeout(() => {
        rh_inside_area.value = '{{ $address->inside_dhaka }}'
    }, 1000)

    if (outsideCheckbox) {
        outsideCheckbox.addEventListener('change', function() {
            // Do not reset field values; only toggle visibility per business rule
            applyMode();

            // Make courier option required when outside Dhaka is selected
            const courierRadios = document.querySelectorAll('input[name="courier_option"]');
            courierRadios.forEach(radio => {
                if (outsideCheckbox.checked) {
                    radio.setAttribute('required', 'required');
                } else {
                    radio.removeAttribute('required');
                    radio.checked = false; // Clear selection when going back to inside Dhaka
                }
            });
        });
    }

    // Initialize mode on page load based on current checkbox state
    applyMode();
</script>
