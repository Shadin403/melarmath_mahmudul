<dd>{{ $address->name }}</dd>
@if ($address->phone)
    <dd>
        <a href="tel:{{ $address->phone }}">
            <x-core::icon name="ti ti-phone" />
            <span dir="ltr">{{ $address->phone }}</span>
        </a>
    </dd>
@endif

@if ($address->email)
    <dd><a href="mailto:{{ $address->email }}">{{ $address->email }}</a></dd>
@endif
<dd>
    <hr>
</dd>
@if ($address->map_location)
    <dd>
        <div style="border: 2px solid rgb(40, 167, 69); padding: 15px; border-radius: 8px; margin: 15px 0px;">
            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                <i class="fas fa-map-marker-alt" style="color: #e74c3c; margin-right: 8px;"></i>
                <strong style="color: #d1e0f0;">{{ __('Delivery Area Location') }}:</strong>
            </div>
            <div style="padding-left: 20px;">
                <a href="{{ $address->map_location }}" target="_blank"
                    style="color: #3498db; text-decoration: none; word-break: break-all;">
                    <i class="fas fa-external-link-alt" style="margin-right: 4px; font-size: 12px;"></i>
                    {{ $address->map_location }}
                </a>
            </div>
        </div>
    </dd>
@endif
@if ($address->address)
    <dd>{!! BaseHelper::clean($address->address) !!}</dd>
@endif
@if ($address->city)
    <dd>{{ $address->city_name }}</dd>
@endif
@if ($address->state)
    <dd>{{ $address->state_name }}</dd>
@endif
@if ($address->country_name)
    <dd>{{ $address->country_name }}</dd>
@endif
@if ($address->is_inside_of_dhaka)
    <dd>{{ \Botble\Ecommerce\Models\GlobalOptionValue::where('id', $address->is_inside_of_dhaka)->first()?->option_value }}
    </dd>
@endif
@if ($address->inside_dhaka)
    <dd>{{ \App\Models\DhakaArea::where('id', $address->inside_dhaka)->first()?->name }}
    </dd>
@endif
@if ($address->courier_option)
    <dd>
        <div class="courier-info-card"
            style="
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border-radius: 12px;
            padding: 16px;
            margin: 8px 0;
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
            border-left: 5px solid #3498db;
        ">
            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                <span
                    style="
                    background: rgba(255,255,255,0.2);
                    border-radius: 50%;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 12px;
                    font-size: 16px;
                "><i
                        class="fas fa-truck" style="color: white;"></i></span>
                <div>
                    <div style="color: white; font-weight: 600; font-size: 14px; margin-bottom: 2px;">
                        {{ __('Courier Service Selected') }}
                    </div>
                    <div
                        style="
                        background: rgba(52, 152, 219, 0.1);
                        color: white;
                        padding: 4px 12px;
                        border-radius: 20px;
                        font-weight: 600;
                        font-size: 13px;
                        display: inline-block;
                        border: 1px solid rgba(52, 152, 219, 0.3);
                    ">
                        <i class="fas fa-truck" style="margin-right: 6px;"></i><strong
                            style="text-decoration: underline;">{{ $address->courier_option }}</strong>
                    </div>
                </div>
            </div>
            <div
                style="
                background: rgba(255,255,255,0.1);
                border-radius: 8px;
                padding: 10px;
                color: rgba(255,255,255,0.95);
                font-size: 12px;
                border-left: 3px solid rgba(52, 152, 219, 0.5);
            ">
                @if ($address->courier_option == 'Sundorbon Courier')
                    <strong>সুন্দরবন কুরিয়ার</strong><br>
                    <span style="opacity: 0.9;"><i class="fas fa-bolt" style="color: #f1c40f; margin-right: 6px;"></i>
                        দ্রুত ডেলিভারি: ২-৩ কার্য দিবস</span>
                @elseif ($address->courier_option == 'SA Paribahan')
                    <strong>এস এ পরিবহন</strong><br>
                    <span style="opacity: 0.9;"><i class="fas fa-rocket" style="color: #e74c3c; margin-right: 6px;"></i>
                        সুপার ফাস্ট: ১-২ কার্য দিবস</span>
                @endif
            </div>
        </div>
    </dd>
@endif
@if (EcommerceHelper::isZipCodeEnabled() && $address->zip_code)
    <dd>{{ $address->zip_code }}</dd>
@endif
@if ($address->country || $address->state || $address->city || $address->address)
    <dd>
        <a href="{{ $address->map_location ? $address->map_location : "https://maps.google.com/?q=$address->full_address" }}"
            target="_blank">
            {{ trans('plugins/ecommerce::order.see_on_maps') }}
        </a>
    </dd>
@endif
