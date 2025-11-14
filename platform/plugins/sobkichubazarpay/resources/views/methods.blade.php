@if (setting('payment_sobkichubazarpay_status') == 1)
    <li class="list-group-item">
        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_sobkichubazarpay"
            @if ($selecting == SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME) checked @endif value="sobkichubazarpay" data-bs-toggle="collapse"
            data-bs-target=".payment_sobkichubazarpay_wrap" data-toggle="collapse"
            data-target=".payment_sobkichubazarpay_wrap" data-parent=".list_payment_method">
        <label for="payment_sobkichubazarpay"
            class="text-start">{{ setting('payment_sobkichubazarpay_name', trans('plugins/sobkichubazarpay::sobkichubazarpay.payment_via_sobkichubazarpay')) }}</label>
        <div class="payment_sobkichubazarpay_wrap payment_collapse_wrap collapse @if ($selecting == SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME) show @endif"
            style="padding: 15px 0;">
            {{-- <p>{!! BaseHelper::clean(setting('payment_sobkichubazarpay_description')) !!}</p> --}}
            <div class="d-none" id="customAdditionalPayment">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group mb-3 mt-3">
                            <div class="product-option-item-values" id="deleveryChargePayment"
                                style="position: relative;">
                                <label for="fullPayment"
                                    style="position: absolute; top: -10px; font-weight: bold; background: yellow; padding: 0 5px; font-size: 11px;">{{ _('Confirm the order with only the courier charge.') }}</label>
                                <input name="options[115][option_type]" type="hidden" value="checkbox">
                                <div class="form-checkbox">
                                    <input checked id="deleveryChargePay" name="address[pay_delevery_charge]"
                                        value="helf_payment" data-extra-price="700" type="radio">
                                    <label
                                        for="deleveryChargePay">&nbsp;{{ \Botble\Ecommerce\Models\GlobalOptionValue::where('option_id', 8)->first()?->option_value }}<strong
                                            class="extra-price">+
                                            {{ \Botble\Ecommerce\Models\GlobalOptionValue::where('option_id', 8)->first()?->affect_price }}
                                            Tk</strong></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group mb-3 mt-3">
                            <div class="product-option-item-values" id="fullPayment" style="position: relative;">
                                <label for="fullPayment"
                                    style="position: absolute; top: -10px; font-weight: bold; background: yellow; padding: 0 5px; font-size: 11px;">{{ _('Confirm the order with full payment.') }}</label>
                                <input name="options[115][option_type]" type="hidden" value="checkbox">
                                <div class="form-checkbox">
                                    <input id="deleveryChargeFullPayment" name="address[pay_delevery_charge]"
                                        value="full_payment" data-extra-price="700" type="radio">
                                    <label for="deleveryChargeFullPayment">&nbsp;Full Payment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <script>
        if (document.getElementById('outSideOfDhaka').checked) {
            document.getElementById('customAdditionalPayment').classList.remove('d-none')
        } else {
            document.getElementById('customAdditionalPayment').classList.add('d-none')
        }
    </script>
@endif
