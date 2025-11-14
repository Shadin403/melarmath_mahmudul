@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('head')
  <style></style>
@endsection

@section('content')
  <div id="main-order-content">
    <div class="row row-cards">
      <div class="col-md-12">
        {{-- this is x:core card header --}}
        <x-core::card class="mb-3">
          <div class="alert alert-info" id="success-message" style="display: none;">
            Product deleted succsssfully
          </div>
          <form action="{{ route('orders.custom-order-update', $order->id) }}" method="post">
            @csrf
            <!-- Header -->
            <x-core::card.header class="d-flex align-content-center justify-content-between">
              <x-core::card.title>
                {{ trans('plugins/ecommerce::order.order_information') }} {{ $order->code }}
              </x-core::card.title>
              <div class="d-flex align-items-center gap-2">
                <x-core::button type="button" tag="a" :href="route('orders.edit', $order->id)" class="btn btn-success"
                  icon="ti ti-arrow-left">
                  {{ trans('plugins/ecommerce::order.back') }}
                </x-core::button>
              </div>
            </x-core::card.header>

            <!-- Table for Product Items -->
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>Image</th>
                  <th>Product</th>
                  <th class="text-center">Unit Price</th>
                  <th class="text-center">Quantity</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody id="product-table-body">
                @foreach ($order->products as $orderProduct)
                  @php
                    $product = $orderProduct->product->original_product;
                    $editProductRoute = Auth::user()->hasPermission('products.edit') ? 'products.edit' : null;
                  @endphp
                  <tr>
                    <td class="text-center" style="width: 80px">
                      <div>
                        <img
                          src="{{ RvMedia::getImageUrl($orderProduct->product_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                          alt="{{ $orderProduct->product_name }}">
                      </div>
                    </td>
                    <td>
                      <input type="hidden" name="products[{{ $loop->iteration }}][id]" value="{{ $orderProduct->id }}">
                      <input type="hidden" name="products[{{ $loop->iteration }}][product_id]"
                        value="{{ $orderProduct->product_id }}">

                      <div class="d-flex align-items-center flex-wrap">

                        <strong class="text-primary">
                          @if ($editProductRoute && $product->getKey() && $product->original_product->getKey())
                            <a href="{{ route($editProductRoute, $product->original_product->getKey()) }}"
                              title="{{ $orderProduct->product_name }}" target="_blank" class="me-2">
                              {{ $orderProduct->product_name }}
                            </a>
                          @else
                            <span class="me-2">{{ $orderProduct->product_name }}</span>
                          @endif
                        </strong>
                        @if ($sku = Arr::get($orderProduct->options, 'sku') ?: ($product && $product->sku ? $product->sku : null))
                          <p class="mb-0">({{ trans('plugins/ecommerce::order.sku') }}:
                            <strong>{{ $sku }}</strong>)
                          </p>
                        @endif

                      </div>

                      @if ($attributes = Arr::get($orderProduct->options, 'attributes'))
                        <div>
                          <small>{{ $attributes }}</small>
                        </div>
                      @endif

                      @if ($isInAdmin)
                        @if (!empty($orderProduct->product_options) && is_array($orderProduct->product_options))
                          {!! render_product_options_html($orderProduct->product_options, $orderProduct->price) !!}
                        @endif
                      @endif

                      @include(EcommerceHelper::viewPath('includes.cart-item-options-extras'), [
                          'options' => $orderProduct->options,
                      ])
                      {!! apply_filters(ECOMMERCE_ORDER_DETAIL_EXTRA_HTML, null, $orderProduct, $order) !!}
                      {!! apply_filters('ecommerce_order_product_item_extra_info', null, $orderProduct, $order) !!}

                      @if (!EcommerceHelper::isDisabledPhysicalProduct() && $order->shipment->id)
                        <ul class="list-unstyled ms-1 small">
                          <li>
                            <span class="bull">↳</span>
                            <span class="black">{{ trans('plugins/ecommerce::order.shipping') }}</span>
                            @if ($isInAdmin)
                              <a class="text-underline bold-light"
                                href="{{ route('ecommerce.shipments.edit', $order->shipment->id) }}"
                                title="{{ $order->shipping_method_name }}"
                                target="_blank">{{ $order->shipping_method_name }}</a>
                            @else
                              <span class="text-underline bold-light">{{ $order->shipping_method_name }}</span>
                            @endif
                          </li>

                          @if ($isInAdmin && is_plugin_active('marketplace') && $order->store?->name)
                            <li class="ws-nm">
                              <span class="bull">↳</span>
                              <span class="black">{{ trans('plugins/marketplace::store.store') }}</span>
                              <a class="fw-semibold text-decoration-underline" href="{{ $order->store->url }}"
                                target="_blank">{{ $order->store->name }}</a>
                            </li>
                          @endif
                        </ul>
                      @endif
                    </td>

                    <td class="text-center">
                      <input min="1" required name="products[{{ $loop->iteration }}][price]" type="number"
                        step="0.1" class="form-control form-control-md text-center d-inline-block"
                        value="{{ $orderProduct->price }}" style="width: 120px;">
                    </td>
                    <td class="text-center">
                      <input min="0.001" required name="products[{{ $loop->iteration }}][qty]" type="number"
                        step="0.001" class="form-control form-control-md text-center d-inline-block"
                        value="{{ $orderProduct->qty }}" style="width: 80px;">
                    </td>
                    <td class="text-center">
                      {{-- <span>{{ get_application_currency()->symbol }}</span> --}}
                      {{ format_price($orderProduct->price * $orderProduct->qty) }}
                    </td>
                    <td class="text-center">
                      @if ($loop->iteration != 1)
                        <button data-order-product-id="{{ $orderProduct->id }}"
                          class="btn btn-outline-secondary btn-sm btn-remove-row">Remove</button>
                      @endif
                    </td>
                  </tr>
                @endforeach

              </tbody>

            </table>
            <!-- Summary Table -->
            <x-core::card.body>
              <div class="row">
                <div class="col-md-6 offset-md-6">
                  <x-core::table :hover="false" :striped="false" class="table-borderless text-end">
                    <x-core::table.body>
                      {{-- here show qty --}}
                      <x-core::table.body.row>
                        <x-core::table.body.cell>{{ trans('plugins/ecommerce::order.quantity') }}</x-core::table.body.cell>
                        <x-core::table.body.cell id="orderQty">
                          {{-- <span>{{ get_application_currency()->symbol }}</span>
                          <span id="orderQty">
                            {{ number_format($order->products->sum('qty'), 2) }}
                          </span> --}}
                        </x-core::table.body.cell>
                      </x-core::table.body.row>
                      {{-- here show sub amount --}}
                      <x-core::table.body.row>
                        <x-core::table.body.cell>
                          {{ trans('plugins/ecommerce::order.sub_amount') }}</x-core::table.body.cell>
                        <x-core::table.body.cell id="orderSubtotal">
                          {{-- <span>{{ get_application_currency()->symbol }}</span>
                          <span id="orderSubtotal">
                            {{ format_price($order->sub_total) }}

                          </span> --}}
                        </x-core::table.body.cell>
                      </x-core::table.body.row>
                      {{-- here show if discount --}}
                      <x-core::table.body.row>
                        <x-core::table.body.cell>
                          {{ trans('plugins/ecommerce::order.discount') }}
                          @if ($order->coupon_code)
                            <p class="mb-0">
                              {!! trans('plugins/ecommerce::order.coupon_code', [
                                  'code' => Html::tag('strong', $order->coupon_code)->toHtml(),
                              ]) !!}
                            </p>
                          @elseif ($order->discount_description)
                            <p class="mb-0">{{ $order->discount_description }}</p>
                          @endif
                        </x-core::table.body.cell>
                        <x-core::table.body.cell id="orderDiscount">
                          {{-- <span>{{ get_application_currency()->symbol }}</span>
                          <span id="orderDiscount">
                            {{ format_price($order->discount_amount) }}

                          </span> --}}
                        </x-core::table.body.cell>
                      </x-core::table.body.row>
                      {{-- here show shpping free --}}
                      @if ($order->shipping_method_name)
                        <x-core::table.body.row>
                          <x-core::table.body.cell>
                            <p class="mb-1">{{ trans('plugins/ecommerce::order.shipping_fee') }}</p>
                            <span class="small d-block">{{ $order->shipping_method_name }}</span>
                            <span class="small d-block">{{ number_format(ecommerce_convert_weight($weight)) }}
                              {{ ecommerce_weight_unit(true) }}</span>
                          </x-core::table.body.cell>
                          <x-core::table.body.cell id="orderShippingFee">
                            {{-- <span>{{ get_application_currency()->symbol }}</span>
                            <span id="orderShippingFee">
                              {{ format_price($order->shipping_amount) }}
                            </span> --}}

                          </x-core::table.body.cell>
                        </x-core::table.body.row>
                      @endif
                      {{-- here show is tax is included --}}
                      @if (EcommerceHelper::isTaxEnabled())
                        <x-core::table.body.row>
                          <x-core::table.body.cell>
                            {{ trans('plugins/ecommerce::order.tax') }}
                          </x-core::table.body.cell>
                          <x-core::table.body.cell id="orderTax">
                            {{ format_price($order->tax_amount) }}
                          </x-core::table.body.cell>
                        </x-core::table.body.row>
                      @endif
                      {{-- here show the total amount --}}
                      <x-core::table.body.row>
                        <x-core::table.body.cell>
                          {{ trans('plugins/ecommerce::order.total_amount') }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell id="orderAmount" class="text-warning">
                          {{-- <span>{{ get_application_currency()->symbol }}</span>
                          <span id="orderAmount">
                            @if (is_plugin_active('payment') && $order->payment->id)
                            <span @class([
                                'text-warning' =>
                                    $order->payment->status !=
                                    Botble\Payment\Enums\PaymentStatusEnum::COMPLETED,
                            ]) class="text-warning">
                              {{ format_price($order->amount) }}
                            </span>
                          @else
                            {{ format_price($order->amount) }}
                          @endif
                          </span> --}}

                        </x-core::table.body.cell>
                      </x-core::table.body.row>
                    </x-core::table.body>
                  </x-core::table>
                  <div class="btn-list justify-content-end my-3 border-top pt-3">

                    <x-core::button type="button" tag="a" :href="route('orders.edit', $order->id)" class="btn btn-warning"
                      icon="ti ti-circle-dashed-x">
                      {{ trans('plugins/ecommerce::order.cancel') }}
                    </x-core::button>


                    {{-- <x-core::button type="submit" id="update-order-btn" class="btn btn-info"
                      icon="ti ti-circle-plus">
                      {{ trans('plugins/ecommerce::order.update') }}
                    </x-core::button> --}}
                    <button class="btn btn-info" disabled="true" type="submit" id="update-order-btn">
                      {{ trans('plugins/ecommerce::order.update') }}
                    </button>

                  </div>
                </div>
              </div>
            </x-core::card.body>

          </form>
        </x-core::card>
      </div>
    </div>
  </div>
@endsection

@section('javascript')
  <script>
    $(document).ready(function() {

      // Remove product row
      $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
      });

      // the qty field validation
      const minQty = 0.001;
      const maxQty = 50; // Set your desired max limit

      $('#product-table-body').on('input', 'input[name^="products"][name$="[qty]"]', function() {
        let value = parseFloat($(this).val());

        if (isNaN(value) || value < minQty) {
          $(this).val(minQty);
        } else if (value > maxQty) {
          $(this).val(maxQty);
        }
      });
    });
  </script>
  <script>
    function changeQty(delta) {
      const qtyInput = document.getElementById('quantity');
      let value = parseFloat(qtyInput.value) || 0.001;
      value += delta;
      if (value < 0.001) value = 0.001;
      qtyInput.value = value.toFixed(3);
    }
    //  remove the product
    $(document).on('click', '.btn-remove-row', function(e) {
      e.preventDefault();

      const orderProductId = $(this).data('order-product-id');

      $.ajax({
        url: '{{ route('orders.custom-order-remove') }}', // Replace with your actual endpoint
        type: 'POST',
        data: {
          order_product_id: orderProductId,
          _token: '{{ csrf_token() }}' // Include CSRF token if using Laravel
        },
        success: function(response) {
          // Handle success (e.g., remove row, show a message)
          console.log(response);
          if (response.success) {
            // Remove the row from the table
            $('#success-message').show();
            calculateTotals();
            toggleButton();
            setTimeout(function() {
              $('#success-message').hide();
            }, 20000); // Hide after 20 seconds
          } else {
            // Handle error (e.g., show an error message)
            console.error(response.error);
            calculateTotals();
          }
        },
        error: function(xhr) {
          // Handle error
          console.error(xhr.responseText);
          calculateTotals();
        }
      });
    });
  </script>
  <script>
    // disable and enable the button
    function toggleButton() {
      if ($('#update-order-btn').prop('disabled') === true) {
        $('#update-order-btn').prop('disabled', false);
      }
    }

    function calculateTotals() {
      const currencySymbol = @json(get_application_currency()->symbol);
      let totalNewAmount = 0;
      let totalQty = 0;
      let subAmount = 0;
      let totalAmount = 0;
      let discount = 0;
      let couponType = '{{ $couponType }}';
      let couponAmount = parseFloat('{{ $couponAmount }}');
      let shippingAmount = parseFloat('{{ $order->shipping_amount }}');
      let couponExists = '{{ $couponExists }}';
      let taxAmount = parseFloat('{{ $order->tax_amount }}');

      // Loop through each product row
      $('#product-table-body tr').each(function() {
        const row = $(this).closest('tr');
        const priceInput = row.find('input[name*="[price]"]');
        const qtyInput = row.find('input[name*="[qty]"]');

        let price = parseFloat(priceInput.val()) || 0;
        let qty = parseFloat(qtyInput.val()) || 0;

        // Calculate line total and update it
        const lineTotal = price * qty;
        row.find('td').eq(4).text(currencySymbol + lineTotal.toFixed(2)); // assuming 5th td is total


        totalNewAmount += lineTotal; // Add to total amount
        // Add to total values
        totalQty += qty;
        totalAmount += lineTotal;

      });

      // calculate discount
      if (couponExists) {
        if (couponType === 'fixed') {
          discount = parseFloat(couponAmount);
        } else if (couponType === 'percentage') {
          discount = (totalNewAmount * parseFloat(couponAmount)) / 100;
        }
      }
      // apply value
      subAmount = (totalNewAmount + shippingAmount + taxAmount) - discount;

      // Update total quantity and sub amount in the summary table
      $('#orderQty').text(totalQty.toFixed(2));
      $('#orderSubtotal').text(currencySymbol + totalAmount.toFixed(2));
      $('#orderDiscount').text(currencySymbol + discount.toFixed(2));
      $('#orderShippingFee').text(currencySymbol + shippingAmount.toFixed(2));
      // $('#orderShippingFee').text(shippingAmount.toFixed(2));
      $('#orderAmount').text(currencySymbol + subAmount.toFixed(2));

    }

    // Trigger calculation on input change
    // $(document).on('input', 'input[name*="[price]"], input[name*="[qty]"]', calculateTotals, toggleButton);
    $(document).on('input', 'input[name*="[price]"], input[name*="[qty]"]', function() {
      calculateTotals();
      toggleButton();
    });

    // Initial calculation
    $(document).ready(function() {
      calculateTotals();
    });
  </script>
@endsection
