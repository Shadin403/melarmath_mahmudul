<template>
    <div class="row row-cards">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('order.order_information') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="table-responsive" :class="{ 'loading-skeleton': checking }"
                            v-if="child_products.length">
                            <table class="table table-bordered table-vcenter">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('order.product_name') }} </th>
                                        <th width="90">{{ __('order.quantity') }}</th>
                                        <th>{{ __('order.price') }}</th>
                                        <th>{{ __('piece/wieght') }}</th>
                                        <th>{{ __('order.total') }}</th>
                                        <th>{{ __('order.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(variant, vKey) in child_products" v-bind:key="`${variant.id}-${vKey}`">
                                        <td>
                                            <img :src="variant.image_url" :alt="variant.name" width="50" />
                                        </td>
                                        <td>
                                            <a :href="variant.product_link" target="_blank">{{ variant.name }}</a>
                                            <p v-if="variant.variation_attributes">
                                                <small>{{ variant.variation_attributes }}</small>
                                            </p>
                                            <ul v-if="
                                                variant.option_values && Object.keys(variant.option_values).length
                                            ">
                                                <li>
                                                    <span>{{ __('order.price') }}: </span>
                                                    <span>{{ variant.original_price_label }}</span>
                                                </li>
                                                <li v-for="option in variant.option_values" v-bind:key="option.id">
                                                    <span>{{ option.title }}: </span>
                                                    <span v-for="value in option.values" v-bind:key="value.id">
                                                        {{ value.value }} <strong>+{{ value.price_label }}</strong>
                                                    </span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Toggle Button -->
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    @click="togglePricingMode(vKey)"
                                                    :title="getPricingMode(variant) === 'quantity' ? 'Switch to Weight' : 'Switch to Quantity'">
                                                    {{ getPricingMode(variant) === 'quantity' ? 'Qty' : 'Wgt' }}
                                                </button>

                                                <!-- Quantity Input (shown when in quantity mode) -->
                                                <input v-if="getPricingMode(variant) === 'quantity'"
                                                    class="form-control form-control-sm" :value="variant.select_qty"
                                                    type="number" min="1" step="1"
                                                    @input="handleChangeQuantity($event, variant, vKey)"
                                                    style="width: 80px;" />

                                                <!-- Weight Input (shown when in weight mode) -->
                                                <input v-else class="form-control form-control-sm"
                                                    :value="variant.piece" type="number" min="0.001" step="0.001"
                                                    @input="handleChangeWeight($event, variant, vKey)"
                                                    style="width: 80px;" />
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <input class="form-control form-control-sm me-2" :value="variant.price"
                                                    type="number" step="0.01" min="0"
                                                    @input="handleChangePrice($event, variant, vKey)"
                                                    style="width: 100px;" />
                                                <small class="text-muted">{{ currency }}</small>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <!-- Display the current multiplier value -->
                                            <span class="badge"
                                                :class="getPricingMode(variant) === 'quantity' ? 'bg-primary' : 'bg-light'">
                                                {{ getCurrentMultiplier(variant) }}
                                                {{ getPricingMode(variant) === 'quantity' ? 'pcs' : 'kg' }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ variant.total_price_label }}
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)"
                                                @click="handleRemoveVariant($event, variant, vKey)"
                                                class="text-decoration-none">
                                                <span class="icon-tabler-wrapper icon-sm icon-left">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-x" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M18 6l-12 12" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="position-relative box-search-advance product mt-3">
                            <input type="text" class="form-control textbox-advancesearch product"
                                :placeholder="__('order.search_or_create_new_product')"
                                @click="loadListProductsAndVariations()"
                                @keyup="handleSearchProduct($event.target.value)" />

                            <div class="card position-absolute z-1 w-100"
                                :class="{ active: list_products, hidden: hidden_product_search_panel }"
                                :style="[loading ? { minHeight: '10rem' } : {}]">
                                <div v-if="loading" class="loading-spinner"></div>
                                <div v-else class="list-group list-group-flush overflow-auto" style="max-height: 25rem">
                                    <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                                        v-ec-modal.add-product-item>
                                        <img width="28"
                                            src="/vendor/core/plugins/ecommerce/images/next-create-custom-line-item.svg"
                                            alt="icon" class="me-2" />
                                        {{ __('order.create_a_new_product') }}
                                    </a>
                                    <a v-for="product_item in list_products.data" :class="{
                                        'list-group-item list-group-item-action': true,
                                        'item-selectable': !product_item.variations.length,
                                        'item-not-selectable': product_item.variations.length,
                                    }" v-bind:key="product_item.id">
                                        <div class="row align-items-start">
                                            <div class="col-auto">
                                                <span class="avatar"
                                                    :style="{ backgroundImage: 'url(' + product_item.image_url + ')' }"></span>
                                            </div>
                                            <div class="col text-truncate">
                                                <ProductAction :ref="'product_actions_' + product_item.id"
                                                    :product="product_item" @select-product="selectProductVariant" />

                                                <div v-if="product_item.variations.length"
                                                    class="list-group list-group-flush">
                                                    <div class="list-group-item p-2"
                                                        v-for="variation in product_item.variations"
                                                        v-bind:key="variation.id">
                                                        <ProductAction :product="variation"
                                                            @select-product="selectProductVariant" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-3" v-if="list_products.data && list_products.data.length === 0">
                                        <p class="text-muted text-center mb-0">{{ __('order.no_products_found') }}</p>
                                    </div>
                                </div>
                                <div class="card-footer" v-if="
                                    ((list_products.links && list_products.links.next) ||
                                        (list_products.links && list_products.links.prev)) &&
                                    !loading
                                ">
                                    <ul class="pagination my-0 d-flex justify-content-end">
                                        <li :class="{
                                            'page-item': true,
                                            disabled: list_products.meta.current_page === 1,
                                        }">
                                            <span v-if="list_products.meta.current_page === 1" class="page-link"
                                                :aria-disabled="list_products.meta.current_page === 1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 6l-6 6l6 6" />
                                                </svg>
                                            </span>
                                            <a v-else href="javascript:void(0)" class="page-link" @click="
                                                loadListProductsAndVariations(
                                                    list_products.links.prev
                                                        ? list_products.meta.current_page - 1
                                                        : list_products.meta.current_page,
                                                    true
                                                )
                                                ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 6l-6 6l6 6" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li :class="{ 'page-item': true, disabled: !list_products.links.next }">
                                            <span v-if="!list_products.links.next" class="page-link"
                                                :aria-disabled="!list_products.links.next">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </span>
                                            <a v-else href="javascript:void(0)" class="page-link" @click="
                                                loadListProductsAndVariations(
                                                    list_products.links.next
                                                        ? list_products.meta.current_page + 1
                                                        : list_products.meta.current_page,
                                                    true
                                                )
                                                ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3 position-relative">
                                <label class="form-label" for="txt-note">{{ __('order.note') }}</label>
                                <textarea v-model="note" class="form-control textarea-auto-height" id="txt-note"
                                    rows="2" :placeholder="__('order.note_for_order')"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-borderless text-end table-vcenter">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td width="120"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ __('order.sub_amount') }}</td>
                                        <td>
                                            <span v-if="checking" class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="fw-bold">{{ child_sub_amount_label }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('order.tax_amount') }}</td>
                                        <td>
                                            <span v-if="checking" class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="fw-bold">{{ child_tax_amount_label }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('order.promotion_discount_amount') }}</td>
                                        <td>
                                            <span v-show="checking" class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span :class="{ 'fw-bold': true, 'text-success': child_promotion_amount }">
                                                {{ child_promotion_amount_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <button type="button" v-ec-modal.add-discounts
                                                class="btn btn-outline-primary btn-sm mb-1">
                                                <template v-if="!has_applied_discount">
                                                    <i class="icon-sm ti ti-plus"></i>
                                                    {{ __('order.add_discount') }}
                                                </template>
                                                <template v-else>{{ __('order.discount') }}</template>
                                            </button>
                                            <span class="d-block small fw-bold" v-if="has_applied_discount">
                                                {{ child_coupon_code || child_discount_description }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="checking" class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span :class="{ 'text-success fw-bold': child_discount_amount }">
                                                {{ child_discount_amount_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="is_available_shipping">
                                        <td>
                                            <button type="button" v-ec-modal.add-shipping
                                                class="btn btn-outline-primary btn-sm mb-1">
                                                <template v-if="!child_is_selected_shipping">
                                                    <i class="icon-sm ti ti-plus"></i>
                                                    {{ __('order.add_shipping_fee') }}
                                                </template>
                                                <template v-else>{{ __('order.shipping') }}</template>
                                            </button>
                                            <span class="d-block small fw-bold" v-if="child_shipping_method_name">
                                                {{ child_shipping_method_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-show="checking" class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span :class="{ 'fw-bold': child_shipping_amount }">
                                                {{ child_shipping_amount_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('order.total_amount') }}</td>
                                        <td>
                                            <span v-show="checking" class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                            <h4 class="d-inline-block">{{ child_total_amount_label }}</h4>
                                        </td>
                                    </tr>
                                    <tr v-if="Object.keys(paymentMethods).length > 0">
                                        <td colspan="2">
                                            <label for="payment-method" class="form-label">
                                                {{ __('order.payment_method') }}
                                            </label>
                                            <select class="form-select" id="payment-method"
                                                v-model="child_payment_method">
                                                <option v-for="(value, key) in paymentMethods" :key="key" :value="key">
                                                    {{ value }}
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr v-if="Object.keys(paymentMethods).length > 0">
                                        <td colspan="2">
                                            <label for="payment-status" class="form-label">
                                                {{ __('order.payment_status_label') }}
                                            </label>
                                            <select class="form-select" id="payment-status"
                                                v-model="child_payment_status">
                                                <option v-for="(value, key) in paymentStatuses" :key="key" :value="key">
                                                    {{ value }}
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr v-if="Object.keys(paymentMethods).length > 0">
                                        <td colspan="2">
                                            <label for="payment-status" class="form-label">
                                                {{ __('order.transaction_id') }}
                                            </label>
                                            <input type="text" class="form-control" v-model="child_transaction_id" />
                                            <small class="form-hint">{{
                                                __('order.incomplete_order_transaction_id_placeholder') }}</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Invoice Action Buttons - Always Visible -->
                            <button type="button" class="btn btn-outline-secondary btn-sm" @click="printInvoice()"
                                :disabled="!order_id && (!child_product_ids.length || !child_customer_id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                    <path
                                        d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                </svg>
                                {{ __('order.print_invoice') }}
                            </button>

                            <button type="button" class="btn btn-outline-info btn-sm" @click="downloadInvoice()"
                                :disabled="!order_id && (!child_product_ids.length || !child_customer_id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M12 17v-6" />
                                    <path d="M9.5 14.5l2.5 2.5l2.5 -2.5" />
                                </svg>
                                {{ __('order.download_invoice') }}
                            </button>

                            <p class="mb-0 text-uppercase text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                                    <path d="M3 10l18 0" />
                                    <path d="M7 15l.01 0" />
                                    <path d="M11 15l2 0" />
                                </svg>
                                {{ __('order.confirm_payment_and_create_order') }}
                            </p>
                        </div>

                        <button :disabled="!child_product_ids.length || !child_customer_id" type="submit"
                            class="btn btn-primary" v-ec-modal.create-order>
                            {{ order_id ? __('order.update_order') : __('order.create_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div v-if="!child_customer_id || !child_customer">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('order.customer_information') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="position-relative box-search-advance customer">
                            <input type="text" class="form-control textbox-advancesearch customer"
                                v-model="customer_keyword" @click="loadListCustomersForSearch()"
                                @keyup="handleSearchCustomer($event.target.value)"
                                :placeholder="__('order.search_or_create_new_customer')" />

                            <div class="card position-absolute w-100 z-1"
                                :class="{ active: customers, hidden: hidden_customer_search_panel }"
                                :style="[loading ? { minHeight: '10rem' } : {}]">
                                <div v-if="loading" class="loading-spinner"></div>
                                <div v-else class="list-group list-group-flush overflow-auto" style="max-height: 25rem">
                                    <div class="list-group-item cursor-pointer" v-ec-modal.add-customer>
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img width="28"
                                                    src="/vendor/core/plugins/ecommerce/images/next-create-customer.svg"
                                                    alt="icon" />
                                            </div>
                                            <div class="col">
                                                <span>{{ __('order.create_new_customer') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0)"
                                        v-for="customer in customers.data" v-bind:key="customer.id"
                                        @click="selectCustomer(customer)">
                                        <div class="flexbox-grid-default flexbox-align-items-center">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="avatar"
                                                        :style="{ backgroundImage: 'url(' + customer.avatar_url + ')' }"></span>
                                                </div>
                                                <div class="col text-truncate">
                                                    <div class="text-body d-block">{{ customer.name }}</div>
                                                    <div class="text-secondary text-truncate mt-n1"
                                                        v-if="customer.email">{{
                                                            customer.email }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="list-group-item" v-if="customers.data && customers.data.length === 0">
                                        {{ __('order.no_customer_found') }}
                                    </div>
                                </div>
                                <div class="card-footer"
                                    v-if="(customers.next_page_url || customers.prev_page_url) && !loading">
                                    <ul class="pagination my-0 d-flex justify-content-end">
                                        <li :class="{ 'page-item': true, disabled: customers.current_page === 1 }">
                                            <span v-if="customers.current_page === 1" class="page-link"
                                                :aria-disabled="customers.current_page === 1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 6l-6 6l6 6" />
                                                </svg>
                                            </span>
                                            <a v-else href="javascript:void(0)" class="page-link" @click="
                                                loadListCustomersForSearch(
                                                    customers.prev_page_url
                                                        ? customers.current_page - 1
                                                        : customers.current_page,
                                                    true
                                                )
                                                ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 6l-6 6l6 6" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li :class="{ 'page-item': true, disabled: !customers.next_page_url }">
                                            <span v-if="!customers.next_page_url" class="page-link"
                                                :aria-disabled="!customers.next_page_url">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </span>
                                            <a v-else href="javascript:void(0)" class="page-link" @click="
                                                loadListCustomersForSearch(
                                                    customers.next_page_url
                                                        ? customers.current_page + 1
                                                        : customers.current_page,
                                                    true
                                                )
                                                ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="child_customer_id && child_customer">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('order.customer') }}</h4>
                        <div class="card-actions">
                            <button type="button" data-bs-toggle="tooltip" data-placement="top" title="Delete customer"
                                @click="removeCustomer()" class="btn-action">
                                <span class="icon-tabler-wrapper icon-sm icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 6l-12 12" />
                                        <path d="M6 6l12 12" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3">
                            <div class="mb-3">
                                <span class="avatar avatar-lg avatar-rounded"
                                    :style="{ backgroundImage: `url(${child_customer.avatar_url || child_customer.avatar})` }"></span>
                            </div>

                            <div class="mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M4 13h3l3 3h4l3 -3h3" />
                                </svg>
                                {{ child_customer_order_numbers }}
                                {{ __('order.orders') }}
                            </div>

                            <div class="mb-n1">{{ child_customer.name }}</div>

                            <div class="d-flex justify-content-between align-items-center" v-if="child_customer.email">
                                <span>
                                    {{ child_customer.email }}
                                </span>

                                <a href="javascript:void(0)" v-ec-modal.edit-email data-placement="top"
                                    data-bs-toggle="tooltip" :data-bs-original-title="__('order.edit_email')"
                                    class="btn-action text-decoration-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path
                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <template v-if="is_available_shipping">
                            <div class="hr my-1"></div>
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">{{ __('order.shipping_address') }}</h4>
                                    <button v-ec-modal.edit-address type="button" class="btn-action"
                                        data-bs-toggle="tooltip" data-bs-title="Update address">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </button>
                                </div>

                                <div v-if="child_customer_addresses.length > 1" class="mb-3">
                                    <select class="form-select" @change="selectCustomerAddress($event)">
                                        <option v-for="address_item in child_customer_addresses"
                                            :value="address_item.id"
                                            :selected="address_item.id === child_customer_address.id"
                                            v-bind:key="address_item.id">
                                            {{ address_item.full_address }}
                                        </option>
                                    </select>
                                </div>

                                <dl class="row mb-0">
                                    <dd><strong>{{ child_customer_address.name || child_customer.name }}</strong></dd>
                                    <dd v-if="child_customer_address.phone">
                                        <a :href="'tel:' + child_customer_address.phone" class="text-decoration-none">
                                            <x-core::icon name="ti ti-phone" />
                                            <span dir="ltr">{{ child_customer_address.phone }}</span>
                                        </a>
                                    </dd>
                                    <dd v-if="child_customer_address.email">
                                        <a :href="'mailto:' + child_customer_address.email"
                                            class="text-decoration-none">{{ child_customer_address.email }}</a>
                                    </dd>
                                    <dd>
                                        <hr>
                                    </dd>
                                    <dd v-if="child_customer_address.map_location">
                                        <div
                                            style="border: 2px solid rgb(40, 167, 69); padding: 15px; border-radius: 8px; margin: 15px 0px;">
                                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                                <i class="fas fa-map-marker-alt"
                                                    style="color: #e74c3c; margin-right: 8px;"></i>
                                                <strong style="color: #d1e0f0;">{{ __('Delivery Area Location')
                                                }}:</strong>
                                            </div>
                                            <div style="padding-left: 20px;">
                                                <a :href="child_customer_address.map_location" target="_blank"
                                                    style="color: #3498db; text-decoration: none; word-break: break-all;">
                                                    <i class="fas fa-external-link-alt"
                                                        style="margin-right: 4px; font-size: 12px;"></i>
                                                    {{ child_customer_address.map_location }}
                                                </a>
                                            </div>
                                        </div>
                                    </dd>
                                    <dd v-if="child_customer_address.address" v-html="child_customer_address.address">
                                    </dd>
                                    <dd v-if="child_customer_address.city_name">{{ child_customer_address.city_name }}
                                    </dd>
                                    <dd v-if="child_customer_address.state_name">{{ child_customer_address.state_name }}
                                    </dd>
                                    <dd v-if="child_customer_address.country_name">{{
                                        child_customer_address.country_name }}</dd>
                                    <dd v-if="child_customer_address.is_inside_of_dhaka">
                                        {{ getGlobalOptionValue(child_customer_address.is_inside_of_dhaka) }}
                                    </dd>
                                    <dd v-if="child_customer_address.inside_dhaka">
                                        {{ getDhakaAreaName(child_customer_address.inside_dhaka) }}
                                    </dd>
                                    <dd v-if="child_customer_address.courier_option">
                                        <div class="courier-info-card" style="
                                            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                                            border-radius: 12px;
                                            padding: 16px;
                                            margin: 8px 0;
                                            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
                                            border-left: 5px solid #3498db;
                                        ">
                                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                                <span style="
                                                    background: rgba(255,255,255,0.2);
                                                    border-radius: 50%;
                                                    width: 32px;
                                                    height: 32px;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    margin-right: 12px;
                                                    font-size: 16px;
                                                "><i class="fas fa-truck" style="color: white;"></i></span>
                                                <div>
                                                    <div
                                                        style="color: white; font-weight: 600; font-size: 14px; margin-bottom: 2px;">
                                                        {{ __('Courier Service Selected') }}
                                                    </div>
                                                    <div style="
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
                                                            style="text-decoration: underline;">{{
                                                                child_customer_address.courier_option }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="
                                                background: rgba(255,255,255,0.1);
                                                border-radius: 8px;
                                                padding: 10px;
                                                color: rgba(255,255,255,0.95);
                                                font-size: 12px;
                                                border-left: 3px solid rgba(52, 152, 219, 0.5);
                                            ">
                                                <template
                                                    v-if="child_customer_address.courier_option == 'Sundorbon Courier'">
                                                    <strong>সুন্দরবন কুরিয়ার</strong><br>
                                                    <span style="opacity: 0.9;"><i class="fas fa-bolt"
                                                            style="color: #f1c40f; margin-right: 6px;"></i>
                                                        দ্রুত ডেলিভারি: ২-৩ কার্য দিবস</span>
                                                </template>
                                                <template
                                                    v-else-if="child_customer_address.courier_option == 'SA Paribahan'">
                                                    <strong>এস এ পরিবহন</strong><br>
                                                    <span style="opacity: 0.9;"><i class="fas fa-rocket"
                                                            style="color: #e74c3c; margin-right: 6px;"></i>
                                                        সুপার ফাস্ট: ১-২ কার্য দিবস</span>
                                                </template>
                                            </div>
                                        </div>
                                    </dd>
                                    <dd v-if="zip_code_enabled && child_customer_address.zip_code">{{
                                        child_customer_address.zip_code }}</dd>
                                    <dd
                                        v-if="child_customer_address.country || child_customer_address.state || child_customer_address.city || child_customer_address.address">
                                        <a :href="child_customer_address.map_location ? child_customer_address.map_location : 'https://maps.google.com/?q=' + child_customer_address.full_address"
                                            target="_blank" class="text-decoration-none">
                                            {{ __('order.see_on_maps') }}
                                        </a>
                                    </dd>
                                </dl>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <AddProductModal @create-product="createProduct" :store="store"></AddProductModal>

        <ec-modal id="add-discounts" :title="__('order.add_discount')" :ok-title="__('order.add_discount')"
            :cancel-title="__('order.close')" @ok="handleAddDiscount($event)">
            <div class="next-form-section">
                <div class="next-form-grid">
                    <div class="mb-3 position-relative">
                        <label class="form-label">{{ __('order.discount_based_on') }}</label>
                        <div class="row">
                            <div class="col-auto">
                                <button value="amount" class="btn btn-active"
                                    :class="{ active: discount_type === 'amount' }" @click="changeDiscountType($event)">
                                    {{ currency || '$' }}
                                </button>&nbsp;
                                <button value="percentage" class="btn btn-active"
                                    :class="{ active: discount_type === 'percentage' }"
                                    @click="changeDiscountType($event)">
                                    %
                                </button>
                            </div>
                            <div class="col">
                                <div class="input-group input-group-flat">
                                    <input class="form-control" v-model="discount_custom_value" />
                                    <span class="input-group-text">{{ discount_type_unit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="next-form-grid">
                    <div class="mb-3 position-relative">
                        <label class="form-label">{{ __('order.or_coupon_code') }}</label>
                        <input class="form-control coupon-code-input" v-model="child_coupon_code" />
                    </div>
                    <div class="position-relative">
                        <label class="form-label">{{ __('order.description') }}</label>
                        <input :placeholder="__('order.discount_description')" class="form-control"
                            v-model="child_discount_description" />
                    </div>
                </div>
            </div>
        </ec-modal>

        <ec-modal id="add-shipping" :title="__('order.shipping_fee')" :ok-title="__('order.update')"
            :cancel-title="__('order.close')" @ok="selectShippingMethod($event)">
            <div v-if="!child_products.length || !child_customer_address.phone">
                <div class="alert alert-success" role="alert">
                    <div class="d-flex">
                        <div>
                            <i class="icon alert-icon ti ti-alert-circle" />
                        </div>
                        <div>
                            <h4 class="alert-title">{{ __('order.how_to_select_configured_shipping') }}</h4>
                            <div class="text-muted">
                                {{ __('order.please_products_and_customer_address_to_see_the_shipping_rates') }}.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="position-relative">
                <label class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" value="free-shipping" name="shipping_type"
                        v-model="shipping_type" />
                    {{ __('order.free_shipping') }}
                </label>
            </div>

            <div v-if="child_products.length && child_customer_address.phone">
                <div class="mb-3 position-relative">
                    <label class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" value="custom" name="shipping_type"
                            v-model="shipping_type"
                            :disabled="shipping_methods && !Object.keys(shipping_methods).length" />
                        <span class="form-check-label">{{ __('order.custom') }}</span>
                        <small class="text-warning" v-if="shipping_methods && !Object.keys(shipping_methods).length">
                            {{ __('order.shipping_method_not_found') }}
                        </small>
                    </label>
                </div>

                <select class="form-select" v-show="shipping_type === 'custom'">
                    <option v-for="(shipping, shipping_key) in shipping_methods" :value="shipping_key"
                        :selected="shipping_key === `${child_shipping_method};${child_shipping_option}`"
                        v-bind:key="shipping_key" :data-shipping-method="shipping.method"
                        :data-shipping-option="shipping.option">
                        {{ shipping.title }}
                    </option>
                </select>
            </div>
        </ec-modal>

        <ec-modal id="create-order"
            :title="Object.keys(paymentMethods).length > 0 ? __('order.confirm_payment_title').replace(':status', paymentStatuses[child_payment_status]) : (order_id ? __('order.update_order') : __('order.create_order'))"
            :ok-title="order_id ? __('order.update_order') : __('order.create_order')" :cancel-title="__('order.close')"
            @ok="createOrder($event)">
            <div class="alert alert-warning" role="alert" v-if="paymentMethods.length">
                {{
                    __('order.confirm_payment_description').replace(
                        ':status',
                        paymentStatuses[child_payment_status]
                    )
                }}.
            </div>

            <div>
                <span>{{ __('order.order_amount') }}:</span>
                <h3 class="d-inline-block ms-2 mb-0">{{ child_total_amount_label }}</h3>
            </div>
        </ec-modal>

        <OrderCustomerAddress :customer="child_customer" :address="child_customer_address"
            :zip_code_enabled="zip_code_enabled" :use_location_data="use_location_data"
            :is_inside_of_dhaka="child_customer_address.is_inside_of_dhaka"
            :inside_dhaka="child_customer_address.inside_dhaka"
            :is_out_side_dhaka="child_customer_address.is_out_side_dhaka" @update-order-address="updateOrderAddress"
            @update-customer-email="updateCustomerEmail" @create-new-customer="createNewCustomer">
        </OrderCustomerAddress>
    </div>
</template>

<script>
import ProductAction from './partials/ProductActionComponent.vue'
import OrderCustomerAddress from './partials/OrderCustomerAddressComponent.vue'
import AddProductModal from './partials/AddProductModalComponent.vue'

export default {
    props: {
        order_id: {
            type: Number,
            default: () => null,
        },
        products: {
            type: Array,
            default: () => [],
        },
        product_ids: {
            type: Array,
            default: () => [],
        },
        customer_id: {
            type: Number,
            default: () => null,
        },
        customer: {
            type: Object,
            default: () => ({
                email: 'guest@example.com',
            }),
        },
        customer_addresses: {
            type: Array,
            default: () => [],
        },
        customer_address: {
            type: Object,
            default: () => ({
                name: null,
                email: null,
                address: null,
                phone: null,
                country: null,
                state: null,
                city: null,
                zip_code: null,
            }),
        },
        customer_order_numbers: {
            type: Number,
            default: () => 0,
        },
        sub_amount: {
            type: Number,
            default: () => 0,
        },
        sub_amount_label: {
            type: String,
            default: () => '',
        },
        tax_amount: {
            type: Number,
            default: () => 0,
        },
        tax_amount_label: {
            type: String,
            default: () => '',
        },
        total_amount: {
            type: Number,
            default: () => 0,
        },
        total_amount_label: {
            type: String,
            default: () => '',
        },
        coupon_code: {
            type: String,
            default: () => '',
        },
        promotion_amount: {
            type: Number,
            default: () => 0,
        },
        promotion_amount_label: {
            type: String,
            default: () => '',
        },
        discount_amount: {
            type: Number,
            default: () => 0,
        },
        discount_amount_label: {
            type: String,
            default: () => '',
        },
        discount_description: {
            type: String,
            default: () => null,
        },
        shipping_amount: {
            type: Number,
            default: () => 0,
        },
        shipping_amount_label: {
            type: String,
            default: () => '',
        },
        shipping_method: {
            type: String,
            default: () => 'default',
        },
        shipping_option: {
            type: String,
            default: () => '',
        },
        is_selected_shipping: {
            type: Boolean,
            default: () => false,
        },
        shipping_method_name: {
            type: String,
            default: function () {
                return 'order.free_shipping'
            },
        },
        payment_method: {
            type: String,
            default: () => 'cod',
        },
        currency: {
            type: String,
            default: () => null,
            required: true,
        },
        zip_code_enabled: {
            type: Number,
            default: () => 0,
            required: true,
        },
        use_location_data: {
            type: Number,
            default: () => 0,
        },
        is_tax_enabled: {
            type: Number,
            default: () => true,
        },
        paymentMethods: {
            type: Object,
            default: () => ({}),
        },
        paymentStatuses: {
            type: Object,
            default: () => ({}),
        },
    },
    data: function () {

        console.log(this.products);
        return {

            pricingModes: {},
            list_products: {
                data: [],
            },
            hidden_product_search_panel: true,
            loading: false,
            checking: false,
            note: null,
            customers: {
                data: [],
            },
            hidden_customer_search_panel: true,
            customer_keyword: null,
            shipping_type: 'free-shipping',
            shipping_methods: {},
            discount_type_unit: this.currency,
            discount_type: 'amount',
            child_discount_description: this.discount_description,
            has_invalid_coupon: false,
            has_applied_discount: this.discount_amount > 0,
            discount_custom_value: 0,
            child_coupon_code: this.coupon_code,
            child_customer: this.customer,
            child_customer_id: this.customer_id,
            child_customer_order_numbers: this.customer_order_numbers,
            child_customer_addresses: this.customer_addresses,
            child_customer_address: this.customer_address,
            child_products: this.products,
            child_product_ids: this.product_ids,
            child_sub_amount: this.sub_amount,
            child_sub_amount_label: this.sub_amount_label,
            child_tax_amount: this.tax_amount,
            child_tax_amount_label: this.tax_amount_label,
            child_total_amount: this.total_amount,
            child_total_amount_label: this.total_amount_label,
            child_promotion_amount: this.promotion_amount,
            child_promotion_amount_label: this.promotion_amount_label,
            child_discount_amount: this.discount_amount,
            child_discount_amount_label: this.discount_amount_label,
            child_shipping_amount: this.shipping_amount,
            child_shipping_amount_label: this.shipping_amount_label,
            child_shipping_method: this.shipping_method,
            child_shipping_option: this.shipping_option,
            child_shipping_method_name: this.shipping_method_name,
            child_is_selected_shipping: this.is_selected_shipping,
            child_payment_method: this.payment_method,
            child_transaction_id: null,
            child_payment_status: 'pending',
            productSearchRequest: null,
            timeoutProductRequest: null,
            timeoutChangePrice: null,
            timeoutChangeQuantity: null,
            customPrices: {}, // Track custom prices by product key
            customerSearchRequest: null,
            checkDataOrderRequest: null,
            store: {
                id: 0,
                name: null,
            },
            is_available_shipping: false,
        }
    },
    components: {
        ProductAction,
        OrderCustomerAddress,
        AddProductModal,
    },
    mounted: function () {
        let context = this
        $(document).on('click', 'body', (e) => {
            let container = $('.box-search-advance')

            if (!container.is(e.target) && container.has(e.target).length === 0) {
                context.hidden_customer_search_panel = true
                context.hidden_product_search_panel = true
            }
        })

        // Fix for reorder: ensure customer data is properly initialized
        if (context.customer_id && !context.child_customer && context.customer_addresses && context.customer_addresses.length > 0) {
            // Try to reconstruct customer data from address info if customer prop wasn't provided
            let customerAddress = context.child_customer_address || context.customer_addresses[0]
            if (customerAddress) {
                context.child_customer = {
                    id: context.customer_id,
                    name: customerAddress.name || 'Customer',
                    email: customerAddress.email || '',
                    phone: customerAddress.phone || '',
                    avatar: null
                }
                context.$forceUpdate()
            }
        }

        if (context.product_ids) {
            context.checkDataBeforeCreateOrder()
        }
    },
    methods: {
        loadListCustomersForSearch: function (page = 1, force = false) {
            let context = this
            context.hidden_customer_search_panel = false
            $('.textbox-advancesearch.customer')
                .closest('.box-search-advance.customer')
                .find('.panel')
                .addClass('active')
            if (_.isEmpty(context.customers.data) || force) {
                context.loading = true
                if (context.customerSearchRequest) {
                    context.customerSearchRequest.abort()
                }

                context.customerSearchRequest = new AbortController()

                axios
                    .get(
                        route('customers.get-list-customers-for-search', {
                            keyword: context.customer_keyword,
                            page: page,
                        }),
                        { signal: context.customerSearchRequest.signal }
                    )
                    .then((res) => {
                        context.customers = res.data.data
                        context.loading = false
                    })
                    .catch((error) => {
                        if (!axios.isCancel(error)) {
                            context.loading = false
                            Botble.handleError(error.response.data)
                        }
                    })
            }
        },
        handleSearchCustomer: function (value) {
            let context = this
            this.customer_keyword = value
            if (context.timeoutCustomerRequest) {
                clearTimeout(context.timeoutCustomerRequest)
            }
            context.timeoutCustomerRequest = setTimeout(() => {
                context.loadListCustomersForSearch(1, true)
            }, 500)
        },
        loadListProductsAndVariations: function (page = 1, force = false, show_panel = true) {
            let context = this
            if (show_panel) {
                context.hidden_product_search_panel = false
                $('.textbox-advancesearch.product')
                    .closest('.box-search-advance.product')
                    .find('.panel')
                    .addClass('active')
            } else {
                context.hidden_product_search_panel = true
            }

            if (_.isEmpty(context.list_products.data) || force) {
                context.loading = true
                if (context.productSearchRequest) {
                    context.productSearchRequest.abort()
                }

                context.productSearchRequest = new AbortController()

                axios
                    .get(
                        route('products.get-all-products-and-variations', {
                            keyword: context.product_keyword,
                            page: page,
                            product_ids: context.child_product_ids,
                        }),
                        { signal: context.productSearchRequest.signal }
                    )
                    .then((res) => {
                        context.list_products = res.data.data
                        context.loading = false
                    })
                    .catch((error) => {
                        if (!axios.isCancel(error)) {
                            Botble.handleError(error.response.data)
                            context.loading = false
                        }
                    })
            }
        },
        handleSearchProduct: function (value) {
            let context = this
            context.product_keyword = value
            if (context.timeoutProductRequest) {
                clearTimeout(context.timeoutProductRequest)
            }

            context.timeoutProductRequest = setTimeout(() => {
                context.loadListProductsAndVariations(1, true)
            }, 1000)
        },
        selectProductVariant: function (product, refOptions) {
            let context = this
            if (_.isEmpty(product) && product.is_out_of_stock) {
                Botble.showError(context.__('order.cant_select_out_of_stock_product'))
                return false
            }
            const requiredOptions = product.product_options.filter((item) => item.required)

            if (product.is_variation || !product.variations.length) {
                let refAction = context.$refs['product_actions_' + product.original_product_id][0]
                refOptions = refAction.$refs['product_options_' + product.original_product_id]
            }

            let productOptions = refOptions.values

            if (requiredOptions.length) {
                let errorMessage = []
                requiredOptions.forEach((item) => {
                    if (!productOptions[item.id]) {
                        errorMessage.push(context.__('order.please_choose_product_option') + ': ' + item.name)
                    }
                })

                if (errorMessage && errorMessage.length) {
                    errorMessage.forEach((message) => {
                        Botble.showError(message)
                    })
                    return
                }
            }

            let options = []

            product.product_options.map((item) => {
                if (productOptions[item.id]) {
                    options[item.id] = {
                        option_type: item.option_type,
                        values: productOptions[item.id],
                    }
                }
            })
            context.child_products.push({ id: product.id, quantity: 1, options })
            context.checkDataBeforeCreateOrder()

            context.hidden_product_search_panel = true
        },
        selectCustomer: function (customer) {
            // Set customer data immediately to trigger UI update
            this.child_customer = { ...customer }
            this.child_customer_id = customer.id

            // Clear search keyword and hide the search panel
            this.customer_keyword = ''
            this.hidden_customer_search_panel = true

            // Load additional customer data
            this.loadCustomerAddress(this.child_customer_id)
            this.getOrderNumbers()

            // Force Vue to recognize the change
            this.$forceUpdate()

            // Check data to update totals and persist customer selection
            this.checkDataBeforeCreateOrder()
        },
        checkDataBeforeCreateOrder: function (data = {}, onSuccess = null, onError = null) {
            let context = this
            let formData = { ...context.getOrderFormData(), ...data }

            // Debug log
            console.log('Checking data before create order:', formData)

            context.checking = true
            if (context.checkDataOrderRequest) {
                context.checkDataOrderRequest.abort()
            }

            context.checkDataOrderRequest = new AbortController()

            axios
                .post(route('orders.check-data-before-create-order'), formData, {
                    signal: context.checkDataOrderRequest.signal,
                })
                .then((res) => {
                    let data = res.data.data

                    console.log('Backend response:', data) // Debug log

                    if (data.update_context_data) {
                        // Preserve custom prices before updating products
                        let preservedCustomPrices = {}
                        context.child_products.forEach((product, index) => {
                            if (product.custom_price && context.customPrices[index] !== undefined) {
                                preservedCustomPrices[index] = {
                                    price: context.customPrices[index],
                                    price_label: context.currency + context.customPrices[index].toFixed(2),
                                    total_price_label: context.currency + (context.customPrices[index] * parseFloat(product.select_qty || 1)).toFixed(2)
                                }
                            }
                        })

                        context.child_products = data.products
                        context.child_product_ids = _.map(data.products, 'id')

                        // Restore custom prices after updating products
                        Object.keys(preservedCustomPrices).forEach(index => {
                            if (context.child_products[index]) {
                                context.child_products[index].price = preservedCustomPrices[index].price
                                context.child_products[index].price_label = preservedCustomPrices[index].price_label
                                context.child_products[index].total_price_label = preservedCustomPrices[index].total_price_label
                                context.child_products[index].custom_price = true
                                // Ensure the custom price is still tracked
                                context.customPrices[index] = preservedCustomPrices[index].price
                            }
                        })

                        // Set server values first (for non-custom calculations)
                        context.child_sub_amount = data.sub_amount
                        context.child_sub_amount_label = data.sub_amount_label

                        context.child_tax_amount = data.tax_amount
                        context.child_tax_amount_label = data.tax_amount_label

                        context.child_promotion_amount = data.promotion_amount
                        context.child_promotion_amount_label = data.promotion_amount_label

                        context.child_discount_amount = data.discount_amount
                        context.child_discount_amount_label = data.discount_amount_label

                        context.child_shipping_amount = data.shipping_amount
                        context.child_shipping_amount_label = data.shipping_amount_label

                        context.child_total_amount = data.total_amount
                        context.child_total_amount_label = data.total_amount_label

                        // Now override with custom calculations if custom prices exist
                        if (Object.keys(context.customPrices).length > 0) {
                            let customSubAmount = 0
                            context.child_products.forEach((product, index) => {
                                if (product.custom_price && context.customPrices[index] !== undefined) {
                                    customSubAmount += context.customPrices[index] * parseFloat(product.select_qty || 1)
                                } else {
                                    // Use original price for non-custom products
                                    customSubAmount += parseFloat(product.price || 0) * parseFloat(product.select_qty || 1)
                                }
                            })

                            // Update sub amount with custom calculation
                            context.child_sub_amount = customSubAmount
                            context.child_sub_amount_label = context.currency + customSubAmount.toFixed(2)

                            // Recalculate total amount (sub amount + tax + shipping - discount - promotion)
                            let customTotalAmount = customSubAmount +
                                parseFloat(context.child_tax_amount || 0) +
                                parseFloat(context.child_shipping_amount || 0) -
                                parseFloat(context.child_discount_amount || 0) -
                                parseFloat(context.child_promotion_amount || 0)

                            context.child_total_amount = customTotalAmount
                            context.child_total_amount_label = context.currency + customTotalAmount.toFixed(2)
                        }

                        context.shipping_methods = data.shipping_methods

                        context.child_shipping_method_name = data.shipping_method_name
                        context.child_shipping_method = data.shipping_method
                        context.child_shipping_option = data.shipping_option
                        context.is_available_shipping = data.is_available_shipping

                        context.store = data.store && data.store.id ? data.store : { id: 0, name: null }
                    }

                    if (res.data.error) {
                        Botble.showError(res.data.message)
                        if (onError) {
                            onError()
                        }
                    } else {
                        if (onSuccess) {
                            onSuccess()
                        }
                    }
                    context.checking = false
                })
                .catch((error) => {
                    if (!axios.isCancel(error)) {
                        context.checking = false
                        Botble.handleError(error.response.data)
                    }
                })
        },
        getOrderFormData: function () {
            let products = []
            let context = this
            _.each(this.child_products, function (item, index) {
                let productData = {
                    id: item.id,
                    quantity: item.select_qty || 1,
                    options: item.options || [],
                    piece: item.piece || 0, // ✅ Added weight
                }

                if (item.custom_price && context.customPrices[index] !== undefined) {
                    productData.custom_price = context.customPrices[index]
                    console.log('Including custom price for product:', {
                        productId: item.id,
                        index: index,
                        customPrice: context.customPrices[index],
                        quantity: item.select_qty || 1
                    })
                }

                products.push(productData)
            })


            let formData = {
                products,
                payment_method: this.child_payment_method,
                payment_status: this.child_payment_status,
                shipping_method: this.child_shipping_method,
                shipping_option: this.child_shipping_option,
                shipping_amount: this.child_shipping_amount,
                discount_amount: this.child_discount_amount,
                discount_description: this.child_discount_description,
                coupon_code: this.child_coupon_code,
                customer_id: this.child_customer_id,
                note: this.note,
                sub_amount: this.child_sub_amount,
                tax_amount: this.child_tax_amount,
                amount: this.child_total_amount,
                customer_address: this.child_customer_address,
                discount_type: this.discount_type,
                discount_custom_value: this.discount_custom_value,
                shipping_type: this.shipping_type,
                transaction_id: this.child_transaction_id,
                // Include flag to indicate we have custom prices
                has_custom_prices: Object.keys(this.customPrices).length > 0,
            }

            console.log('Form data being sent to backend:', formData)
            return formData
        },
        removeCustomer: function () {
            this.child_customer = null
            this.child_customer_id = null
            this.child_customer_addresses = []
            this.child_customer_address = {
                name: null,
                email: null,
                address: null,
                phone: null,
                country: null,
                state: null,
                city: null,
                zip_code: null,
                full_address: null,
            }
            this.child_customer_order_numbers = 0

            // Clear search keyword
            this.customer_keyword = ''

            // Force Vue to recognize the change
            this.$forceUpdate()

            this.checkDataBeforeCreateOrder()
        },
        handleRemoveVariant: function (event, variant, vKey) {
            event.preventDefault()

            // Remove custom price tracking for this variant
            if (this.customPrices[vKey] !== undefined) {
                delete this.customPrices[vKey]
            }

            // Reindex custom prices after removal
            let newCustomPrices = {}
            Object.keys(this.customPrices).forEach(key => {
                let index = parseInt(key)
                if (index > vKey) {
                    newCustomPrices[index - 1] = this.customPrices[key]
                } else if (index < vKey) {
                    newCustomPrices[index] = this.customPrices[key]
                }
            })
            this.customPrices = newCustomPrices

            this.child_product_ids = this.child_product_ids.filter((item, k) => k !== vKey)
            this.child_products = this.child_products.filter((item, k) => k !== vKey)

            this.checkDataBeforeCreateOrder()
        },
        createOrder: function (event) {
            event.preventDefault()

            $(event.target).addClass('btn-loading')

            // Auto-create customer if we have customer address data but no customer_id
            let context = this
            if (!context.child_customer_id && context.child_customer_address && context.child_customer_address.name) {
                // Create customer first, then create the order
                context.createCustomerThenOrder(event)
                return
            }

            // Determine if we're creating or updating
            const isUpdate = this.order_id !== null
            const url = isUpdate ? route('orders.edit', this.order_id) : route('orders.create')
            const method = 'post' // Both create and update use POST

            console.log('Creating/Updating order with data:', this.getOrderFormData()) // Debug log

            // Additional debugging for reorder scenario
            if (isUpdate && Object.keys(this.customPrices).length > 0) {
                console.log('REORDER: Custom prices detected for update:', {
                    customPrices: this.customPrices,
                    productsWithCustomPrices: this.child_products.filter((p, i) => this.customPrices[i] !== undefined),
                    orderFormData: this.getOrderFormData()
                })
            }

            // Force recalculation before creating order to ensure totals are correct
            this.recalculateOrderTotals()

            axios({
                method: method,
                url: url,
                data: this.getOrderFormData()
            })
                .then((res) => {
                    let data = res.data.data
                    if (res.data.error) {
                        Botble.showError(res.data.message)
                    } else {
                        Botble.showSuccess(res.data.message)

                        // Show additional message for reorders to inform about invoice regeneration
                        if (isUpdate) {
                            setTimeout(() => {
                                Botble.showInfo('Order updated successfully. Customer information and invoice have been automatically updated to reflect any changes.')
                            }, 1500)
                        }

                        $event.emit('ec-modal:close', 'create-order')

                        setTimeout(() => {
                            if (isUpdate) {
                                // For updates, redirect to the same order edit page
                                window.location.href = route('orders.edit', this.order_id)
                            } else {
                                // For creates, redirect to the new order edit page
                                window.location.href = route('orders.edit', data.id)
                            }
                        }, 1000)
                    }
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
                .then(() => {
                    $(event.target).removeClass('btn-loading')
                })
        },
        createCustomerThenOrder: function (event) {
            let context = this

            // Create customer first
            axios
                .post(route('customers.create-customer-when-creating-order'), {
                    customer_id: null,
                    name: context.child_customer_address.name,
                    email: context.child_customer_address.email,
                    phone: context.child_customer_address.phone,
                    address: context.child_customer_address.address,
                    country: (context.child_customer_address.country ? context.child_customer_address.country.toString() : ''),
                    state: (context.child_customer_address.state ? context.child_customer_address.state.toString() : ''),
                    city: (context.child_customer_address.city ? context.child_customer_address.city.toString() : ''),
                    zip_code: context.child_customer_address.zip_code,
                    is_out_side_dhaka: context.child_customer_address.is_out_side_dhaka,
                    is_inside_of_dhaka: context.child_customer_address.is_inside_of_dhaka,
                    is_inside_dhaka: context.child_customer_address.is_inside_dhaka,
                    inside_dhaka: context.child_customer_address.inside_dhaka,
                    courier_option: context.child_customer_address.courier_option,
                    map_location: context.child_customer_address.map_location,
                })
                .then((res) => {
                    if (res.data.error) {
                        Botble.showError(res.data.message)
                        $(event.target).removeClass('btn-loading')
                    } else {
                        // Update customer data
                        context.child_customer = res.data.data.customer
                        context.child_customer_id = context.child_customer.id
                        context.child_customer_address = res.data.data.address

                        Botble.showSuccess('Customer created successfully')

                        // Now create the order
                        context.createOrder(event)
                    }
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                    $(event.target).removeClass('btn-loading')
                })
        },
        createProduct: function (event, product) {
            event.preventDefault()
            $(event.target).addClass('btn-loading')
            let context = this
            if (context.store && context.store.id) {
                product.store_id = context.store.id
            }

            axios
                .post(route('products.create-product-when-creating-order'), product)
                .then((res) => {
                    if (res.data.error) {
                        Botble.showError(res.data.message)
                    } else {
                        context.product = res.data.data

                        context.list_products = {
                            data: [],
                        }

                        let productItem = context.product
                        productItem.select_qty = 1

                        context.child_products.push(productItem)
                        context.child_product_ids.push(context.product.id)

                        context.hidden_product_search_panel = true

                        Botble.showSuccess(res.data.message)

                        $event.emit('ec-modal:close', 'add-product-item')

                        context.checkDataBeforeCreateOrder()
                    }
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
                .then(() => {
                    $(event.target).removeClass('btn-loading')
                })
        },
        updateCustomerEmail: function (event) {
            event.preventDefault()

            $(event.target).addClass('btn-loading')

            let context = this

            axios
                .post(route('customers.update-email', context.child_customer.id), {
                    email: context.child_customer.email,
                })
                .then(({ data }) => {
                    if (data.error) {
                        Botble.showError(data.message)
                    } else {
                        Botble.showSuccess(data.message)

                        $event.emit('ec-modal:close', 'edit-email')
                    }
                })
                .catch(({ response }) => {
                    Botble.handleError(response.data)
                })
                .then(() => {
                    $(event.target).removeClass('btn-loading')
                })
        },
        updateOrderAddress: function (event) {
            event.preventDefault()

            if (this.customer) {
                $(event.target).addClass('btn-loading')

                this.checkDataBeforeCreateOrder(
                    {},
                    () => {
                        setTimeout(() => {
                            $(event.target).removeClass('btn-loading')
                            $event.emit('ec-modal:close', 'edit-address')
                        }, 500)
                    },
                    () => {
                        setTimeout(() => {
                            $(event.target).removeClass('btn-loading')
                        }, 500)
                    }
                )
            }
        },
        createNewCustomer: function (event) {
            event.preventDefault()
            let context = this

            $(event.target).addClass('btn-loading')

            axios
                .post(route('customers.create-customer-when-creating-order'), {
                    customer_id: context.child_customer_id,
                    name: context.child_customer_address.name,
                    email: context.child_customer_address.email,
                    phone: context.child_customer_address.phone,
                    address: context.child_customer_address.address,
                    country: (context.child_customer_address.country ? context.child_customer_address.country.toString() : ''),
                    state: (context.child_customer_address.state ? context.child_customer_address.state.toString() : ''),
                    city: (context.child_customer_address.city ? context.child_customer_address.city.toString() : ''),
                    zip_code: context.child_customer_address.zip_code,
                    is_out_side_dhaka: context.child_customer_address.is_out_side_dhaka,
                    is_inside_of_dhaka: context.child_customer_address.is_inside_dhaka,
                    is_inside_dhaka: context.child_customer_address.is_inside_dhaka,
                    inside_dhaka: context.child_customer_address.inside_dhaka,
                    courier_option: context.child_customer_address.courier_option,
                    map_location: context.child_customer_address.map_location,
                })
                .then((res) => {
                    if (res.data.error) {
                        Botble.showError(res.data.message)
                    } else {
                        context.child_customer_address = res.data.data.address
                        context.child_customer = res.data.data.customer
                        context.child_customer_id = context.child_customer.id

                        context.customers = {
                            data: [],
                        }

                        Botble.showSuccess(res.data.message)
                        context.checkDataBeforeCreateOrder()

                        $event.emit('ec-modal:close', 'add-customer')
                    }
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
                .then(() => {
                    $(event.target).removeClass('btn-loading')
                })
        },
        selectCustomerAddress: function (event) {
            let context = this
            _.each(this.child_customer_addresses, (item) => {
                if (parseInt(item.id) === parseInt(event.target.value)) {
                    context.child_customer_address = item
                }
            })

            this.checkDataBeforeCreateOrder()
        },
        getOrderNumbers: function () {
            let context = this
            axios
                .get(route('customers.get-customer-order-numbers', context.child_customer_id))
                .then((res) => {
                    context.child_customer_order_numbers = res.data.data
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
        },
        loadCustomerAddress: function () {
            let context = this
            axios
                .get(route('customers.get-customer-addresses', context.child_customer_id))
                .then((res) => {
                    context.child_customer_addresses = res.data.data
                    if (!_.isEmpty(context.child_customer_addresses)) {
                        context.child_customer_address = _.first(context.child_customer_addresses)
                    }
                    this.checkDataBeforeCreateOrder()
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
        },
        selectShippingMethod: function (event) {
            event.preventDefault()
            let context = this
            let $button = $(event.target)
            let $modal = $button.closest('.modal')
            $button.addClass('btn-loading')

            context.child_is_selected_shipping = true

            if (context.shipping_type === 'free-shipping') {
                context.child_shipping_method_name = context.__('order.free_shipping')
                context.child_shipping_amount = 0
            } else {
                let selected_shipping = $modal.find('.form-select').val()

                if (!_.isEmpty(selected_shipping)) {
                    let option = $modal.find('.form-select option:selected')
                    context.child_shipping_method = option.data('shipping-method')
                    context.child_shipping_option = option.data('shipping-option')
                }
            }

            this.checkDataBeforeCreateOrder(
                {},
                () => {
                    setTimeout(function () {
                        $button.removeClass('btn-loading')
                        $event.emit('ec-modal:close', 'add-shipping')
                    }, 500)
                },
                () => {
                    setTimeout(function () {
                        $button.removeClass('btn-loading')
                    }, 500)
                }
            )
        },
        changeDiscountType: function (event) {
            if ($(event.target).val() === 'amount') {
                this.discount_type_unit = this.currency
            } else {
                this.discount_type_unit = '%'
            }
            this.discount_type = $(event.target).val()
        },
        handleAddDiscount: function (event) {
            event.preventDefault()
            let $target = $(event.target)
            let context = this

            context.has_applied_discount = true
            context.has_invalid_coupon = false

            let $button = $target.find('.btn-primary')

            $button.addClass('btn-loading').prop('disabled', true)

            if (context.child_coupon_code) {
                context.discount_custom_value = 0
            } else {
                context.discount_custom_value = Math.max(parseFloat(context.discount_custom_value), 0)
                if (context.discount_type === 'percentage') {
                    context.discount_custom_value = Math.min(context.discount_custom_value, 100)
                }
            }

            context.checkDataBeforeCreateOrder(
                {},
                () => {
                    setTimeout(function () {
                        if (!context.child_coupon_code && !context.discount_custom_value) {
                            context.has_applied_discount = false
                        }
                        $button.removeClass('btn-loading').prop('disabled', false)
                        $event.emit('ec-modal:close', 'add-discounts')
                    }, 500)
                },
                () => {
                    if (context.child_coupon_code) {
                        context.has_invalid_coupon = true
                    }
                    $button.removeClass('btn-loading').prop('disabled', false)
                }
            )
        },
        handleChangeQuantity(event, variant, vKey) {
            let newQuantity = parseInt(event.target.value) || 0;

            // Stock management check
            if (variant.with_storehouse_management && newQuantity > variant.quantity) {
                newQuantity = variant.quantity;
                event.target.value = newQuantity;
            }

            // Direct assignment is reactive in Vue 3
            variant.select_qty = newQuantity;
            this.updateProductTotal(variant, vKey);
            this.recalculateOrderTotals();
        },
        handleChangePrice(event, variant, vKey) {
            let newPrice = parseFloat(event.target.value) || 0;

            // Update both variant.price AND customPrices
            variant.price = newPrice; // ✅ Update the actual price

            // Update custom price for tracking
            if (!this.customPrices) {
                this.customPrices = {};
            }
            this.customPrices[vKey] = newPrice;
            variant.custom_price = true;

            this.updateProductTotal(variant, vKey);
            this.recalculateOrderTotals();

            console.log('Price updated:', {
                product: variant.name,
                newPrice: newPrice,
                vKey: vKey
            });
        },
        getPricingMode: function (variant) {
            // Use stored mode, or auto-detect based on available data
            if (this.pricingModes[variant.id] !== undefined) {
                return this.pricingModes[variant.id];
            }

            // Auto-detect: if product has weight data, default to weight mode
            if (variant.piece && variant.piece > 0) {
                return 'weight';
            }

            // Default to quantity mode
            return 'quantity';
        },
        togglePricingMode(vKey) {
            let variant = this.child_products[vKey];
            let currentMode = this.getPricingMode(variant);
            let newMode = currentMode === 'quantity' ? 'weight' : 'quantity';

            // In Vue 3, direct assignment is reactive for objects
            this.pricingModes[variant.id] = newMode;

            console.log('Pricing mode changed:', {
                productId: variant.id,
                productName: variant.name,
                from: currentMode,
                to: newMode
            });

            // Recalculate totals with new mode
            this.updateProductTotal(variant, vKey);
            this.recalculateOrderTotals();
        },
        getCurrentMultiplier(variant) {
            let mode = this.getPricingMode(variant);

            if (mode === 'weight') {
                return variant.piece || 0;
            } else {
                return variant.select_qty || 0;
            }
        },
        handleChangeWeight(event, variant, vKey) {
            let newWeight = parseFloat(event.target.value) || 0;

            // Direct assignment is reactive in Vue 3
            variant.piece = newWeight;
            this.updateProductTotal(variant, vKey);
            this.recalculateOrderTotals();
        },
        updateProductTotal(variant, vKey) {
            let currentPrice = variant.custom_price && this.customPrices[vKey] !== undefined
                ? this.customPrices[vKey]
                : parseFloat(variant.price);

            let mode = this.getPricingMode(variant);
            let multiplier;

            if (mode === 'weight') {
                // Use weight for calculation
                multiplier = variant.piece || 0;
            } else {
                // Use quantity for calculation
                multiplier = variant.select_qty || 0;
            }

            let itemTotal = currentPrice * multiplier;

            // Direct assignment in Vue 3
            variant.total_price_label = this.currency + itemTotal.toFixed(2);

            console.log('Product total updated:', {
                product: variant.name,
                mode: mode,
                price: currentPrice,
                multiplier: multiplier,
                total: itemTotal
            });
        },

        recalculateOrderTotals() {
            let newSubAmount = 0;

            this.child_products.forEach((product, index) => {
                let currentPrice = product.custom_price && this.customPrices[index] !== undefined
                    ? this.customPrices[index]
                    : parseFloat(product.price);

                let mode = this.getPricingMode(product);
                let multiplier;

                if (mode === 'weight') {
                    multiplier = product.piece || 0;
                } else {
                    multiplier = product.select_qty || 0;
                }

                let itemTotal = currentPrice * multiplier;
                newSubAmount += itemTotal;

                // Update individual product total label
                product.total_price_label = this.currency + itemTotal.toFixed(2);
            });

            this.child_sub_amount = newSubAmount;
            this.child_sub_amount_label = this.currency + newSubAmount.toFixed(2);

            // Calculate final total
            let newTotalAmount = newSubAmount +
                parseFloat(this.child_tax_amount || 0) +
                parseFloat(this.child_shipping_amount || 0) -
                parseFloat(this.child_discount_amount || 0) -
                parseFloat(this.child_promotion_amount || 0);

            this.child_total_amount = newTotalAmount;
            this.child_total_amount_label = this.currency + newTotalAmount.toFixed(2);

            console.log('Order totals recalculated:', {
                subAmount: newSubAmount,
                totalAmount: newTotalAmount
            });
        },
        printInvoice: function () {
            if (this.order_id) {
                // For existing orders, open print view
                let printUrl = route('orders.print-invoice', this.order_id)
                window.open(printUrl, '_blank')
            } else {
                // For new orders, create a temporary invoice preview
                this.generateInvoicePreview('print')
            }
        },
        downloadInvoice: function () {
            if (this.order_id) {
                // For existing orders, download PDF
                let downloadUrl = route('orders.download-invoice', this.order_id)
                window.open(downloadUrl, '_blank')
            } else {
                // For new orders, create a temporary invoice preview
                this.generateInvoicePreview('download')
            }
        },
        generateInvoicePreview: function (action = 'print') {
            // Validate that we have required data
            if (!this.child_product_ids.length || !this.child_customer_id) {
                Botble.showError(this.__('order.please_add_products_and_customer_first'))
                return
            }

            let context = this
            let loadingBtn = action === 'print' ? 'btn-loading-print' : 'btn-loading-download'

            // Add loading state to button
            $(`.btn:contains("${action === 'print' ? this.__('order.print_invoice') : this.__('order.download_invoice')}")`).addClass('btn-loading')

            // Generate invoice preview data
            axios.post(route('orders.preview-invoice'), this.getOrderFormData())
                .then((response) => {
                    if (response.data.success) {
                        if (action === 'print') {
                            // Open print preview in new window
                            let printWindow = window.open('', '_blank')
                            printWindow.document.write(response.data.html)
                            printWindow.document.close()
                            printWindow.onload = function () {
                                printWindow.print()
                            }
                        } else {
                            // Download PDF
                            if (response.data.pdf_url) {
                                window.open(response.data.pdf_url, '_blank')
                            }
                        }
                    } else {
                        Botble.showError(response.data.message || this.__('order.error_generating_invoice'))
                    }
                })
                .catch((error) => {
                    Botble.handleError(error.response?.data || error)
                })
                .finally(() => {
                    // Remove loading state
                    $(`.btn:contains("${action === 'print' ? this.__('order.print_invoice') : this.__('order.download_invoice')}")`).removeClass('btn-loading')
                })
        },
        getGlobalOptionValue: function (optionId) {
            // The data should already be available in the address object
            // from the OrderAddressResource which includes is_inside_of_dhaka_name
            if (this.child_customer_address && this.child_customer_address.is_inside_of_dhaka_name) {
                return this.child_customer_address.is_inside_of_dhaka_name
            }

            // Fallback: fetch from backend if not available in address data
            if (optionId) {
                return axios.get(route('admin.ajax.thanas'))
                    .then(res => {
                        const thanas = res.data.data
                        return thanas[optionId] || `Option ${optionId}`
                    })
                    .catch(error => {
                        console.error('Error fetching thanas:', error)
                        return `Option ${optionId}`
                    })
            }
            return ''
        },
        getDhakaAreaName: function (areaId) {
            // The data should already be available in the address object
            // from the OrderAddressResource which includes inside_dhaka_name
            if (this.child_customer_address && this.child_customer_address.inside_dhaka_name) {
                return this.child_customer_address.inside_dhaka_name
            }

            // Fallback: if we have thana_id, we can fetch areas for that thana
            if (areaId && this.child_customer_address && this.child_customer_address.is_inside_of_dhaka) {
                return axios.get(route('admin.ajax.areas', { thana_id: this.child_customer_address.is_inside_of_dhaka }))
                    .then(res => {
                        const areas = res.data.data
                        const area = areas.find(a => a.id == areaId)
                        return area ? area.name : `Area ${areaId}`
                    })
                    .catch(error => {
                        console.error('Error fetching areas:', error)
                        return `Area ${areaId}`
                    })
            }
            return ''
        },
    },
    watch: {
        child_payment_method: function () {
            this.checkDataBeforeCreateOrder()
        },
    },
}
</script>
