<!doctype html>
<html {{ html_attributes }}>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ 'plugins/ecommerce::order.invoice_for_order' | trans }} {{ invoice . code }}</title>

    {{ settings . font_css }}

    <style>
        @font-face {
            font-family: 'SolaimanLipi';
            src: url('https://sobkichubazar.com.bd/asset/font/SolaimanLipi.ttf') format('truetype');
        }

        body {
            font-size: 15px;
            font-family: '{{ settings . font_family }}', Arial, 'SolaimanLipi', sans-serif !important;
            position: relative;
        }


        .header {
            text-align: center;
            margin-bottom: 2px;
            margin-top: -30px;
            padding-bottom: 5px;
            border-bottom: 2px solid #0a9928;
        }

        .header .logo-small {
            max-width: 50px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header .logo {
            max-width: 130px;
            margin: 0 auto 2px auto;
        }

        .header p {
            margin: 0;
            font-size: 13px;
            color: #666666;
        }


        table {
            border-collapse: collapse;
            width: 100%;
        }

        table tr td {
            padding: 0;
        }

        table tr td:last-child {
            text-align: right;
        }

        /* thead tr th{
            padding: 20px 30px;
        } */

        .line-items-container.rh_table_container tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .line-items-container.rh_table_container tr:nth-child(even) {
            background-color: white;
        }

        .rh_table_container thead tr {
            background-color: green !important;
            padding: 0 5px;
            color: #fff;
            margin-bottom: 5px;
            border-spacing: 5px 0;
        }

        .rh_table_container table,
        .rh_table_container tbody,
        .rh_table_container th,
        .rh_table_container td {
            border: 1px solid #dddddd;
        }


        .rh_table_container table {
            border-collapse: separate;
        }

        .bold,
        strong,
        b,
        .total,
        .stamp {
            font-weight: 700;
        }

        .right {
            text-align: right;
        }

        .large {
            font-size: 1.75em;
        }

        .total {
            color: #fb7578;
        }

        .large.total img {
            width: 14px;
        }

        .logo-container {
            margin: 0 0 50px;
        }

        .invoice-info-container {
            font-size: .875em;
        }

        .invoice-info-container td {
            padding: 4px 0;
        }

        .line-items-container {
            font-size: .875em;
            margin: 10px 0;
        }

        .line-items-container th {
            border-bottom: 2px solid #ddd;
            font-size: .75em;
            padding: 10px 0 15px;
            text-align: left;
            text-transform: uppercase;
        }

        .line-items-container th:last-child {
            text-align: right;
        }

        .line-items-container td {
            padding: 10px 0;
        }

        .line-items-container tbody tr:first-child td {
            padding-top: 25px;
        }

        .line-items-container.has-bottom-border tbody tr:last-child td {
            border-bottom: 2px solid #ddd;
            padding-bottom: 25px;
        }

        .line-items-container th.heading-quantity {
            width: 50px;
        }

        .line-items-container th.heading-price {
            text-align: right;
            width: 100px;
        }

        .line-items-container th.heading-subtotal {
            width: 100px;
        }

        .payment-info {
            font-size: .875em;
            line-height: 1.5;
            width: 38%
        }

        small {
            font-size: 80%;
        }

        .stamp {
            border: 2px solid #555;
            color: #555;
            display: inline-block;
            font-size: 18px;
            line-height: 1;
            opacity: .5;
            padding: .3rem .75rem;
            position: fixed;
            text-transform: uppercase;
            top: 40%;
            left: 40%;
            transform: rotate(-14deg);
        }

        .is-failed {
            border-color: #d23;
            color: #d23;
        }

        .is-completed {
            border-color: #0a9928;
            color: #0a9928;
        }

        body[dir=rtl] {
            direction: rtl;
        }

        body[dir=rtl] .right {
            text-align: left;
        }

        body[dir=rtl] table tr td:last-child {
            text-align: left;
        }

        body[dir=rtl] .line-items-container th.heading-price {
            text-align: left;
        }

        body[dir=rtl] .line-items-container th:last-child {
            text-align: left;
        }

        body[dir=rtl] .line-items-container th {
            text-align: right;
        }

        .invoice-info-container p {
            line-height: 1px;
        }

        {{ settings . extra_css }}
    </style>

    {{ settings . header_html }}

    {{ invoice_header_filter | raw }}
</head>

<body {{ body_attributes }}>

    {{ invoice_body_filter | raw }}

    {% if (get_ecommerce_setting('enable_invoice_stamp', 1) == 1) %}
    {% if invoice.status == 'canceled' %}
    <div class="stamp is-failed">
        {{ invoice . status }}
    </div>
    {% elseif (payment_status_label) %}
    <div class="stamp {% if payment_status == 'completed' %} is-completed {% else %} is-failed {% endif %}">
        {{ payment_status_label }}
    </div>
    {% endif %}
    {% endif %}

    <div class="header">
        <table>
            <tr>
                <td style="text-align: left;">
                    <img class="logo-small" src="./asset/logo-small.png" alt="">
                </td>
                <td style="text-align: right;">
                    {% if logo_full_path %}
                    <img style="margin-left: 100px !important;" src="{{ logo_full_path }}" alt="{{ company_name }}"
                        class="logo">
                    {% else %}
                    <h1>{{ company_name }}</h1>
                    {% endif %}
                </td>
                <td style="text-align: right;">
                    {{ 'plugins/marketplace::withdrawal.invoice.title' | trans }} #{{ invoice . code }}
                </td>
            </tr>
        </table>
    </div>

    <table class="invoice-info-container">
        <tr>
            <td>
                <h3>Customer Information:</h3>
                {% if invoice.customer_name %}
                <p>{{ invoice . customer_name }}</p>
                {% endif %}
                {% if invoice.customer_email %}
                <p>{{ invoice . customer_email }}</p>
                {% endif %}
                {% if invoice.customer_phone %}
                <p>{{ invoice . customer_phone }}</p>
                {% endif %}
                {% if invoice.is_inside_dhaka %}
                <p>থানা: {{ invoice . is_inside_dhaka }}</p>
                {% endif %}
                {% if invoice.inside_dhaka %}
                <p>এরিয়া: {{ invoice . inside_dhaka }}</p>
                {% endif %}
                {% if invoice.customer_address %}
                <p>{{ invoice . customer_address }}</p>
                {% endif %}
                {% if invoice.customer_tax_id %}
                <p>{{ 'plugins/ecommerce::ecommerce.tax_id' | trans }}: {{ invoice . customer_tax_id }}</p>
                {% endif %}
            </td>

            {% if invoice . qr_code %}
            <td class="text-align: center; width: 100%; display: block;">
                <img src="./storage/{{ invoice . qr_code }}" style="margin-left: 100px; display: inline-block;"
                    height="70" width="70" alt="">
            </td>
            {% endif %}


            <td>
                <h3>Company Information:</h3>
                {% if company_name %}
                <p>{{ company_name }}</p>
                {% endif %}

                {% if company_address %}
                <p>{{ company_address }}</p>
                {% endif %}

                {% if company_phone %}
                <p>{{ company_phone }}</p>
                {% endif %}

                {% if company_email %}
                <p>{{ company_email }}</p>
                {% endif %}

                {% if company_tax_id %}
                <p>{{ 'plugins/ecommerce::ecommerce.tax_id' | trans }}: {{ company_tax_id }}</p>
                {% endif %}
            </td>

        </tr>
    </table>

    {% if invoice.description %}
    <table class="invoice-info-container">
        <tr style="text-align: left">
            <td style="text-align: left">
                <p>{{ 'plugins/ecommerce::order.note' | trans }}: {{ invoice . description }}</p>
            </td>
        </tr>
    </table>
    {% endif %}

    <h1>{{ invoice . rh_name }}</h1>
    <table class="line-items-container rh_table_container">
        <thead>
            <tr>
                <th style="padding: 4px 20px; font-weight: bold;" class="heading-description">
                    {{ 'plugins/ecommerce::products.form.product' | trans }}</th>
                <th style="padding: 4px 20px; font-weight: bold;" class="heading-description">
                    {{ 'plugins/ecommerce::products.form.options' | trans }}</th>
                <th style="padding: 4px 20px; font-weight: bold;" class="heading-quantity">
                    {{ 'plugins/ecommerce::products.form.quantity' | trans }}</th>
                <th style="padding: 4px 20px; font-weight: bold;" class="heading-price">
                    {{ 'plugins/ecommerce::products.form.price' | trans }}</th>
                <th style="padding: 4px 20px; font-weight: bold;" class="heading-subtotal">
                    {{ 'plugins/ecommerce::products.form.total' | trans }}</th>
            </tr>
        </thead>
        <tbody>

            {% for item in invoice.items %}
            <tr style="padding: 1px 20px;">
                <td style="padding: 4px 20px;"><p>{{ item . name }}</p>
                    {% if item.options.sku %}
                    ({{ item . options . sku }}) {% endif %}</td>
                <td style="padding: 4px 20px;">
                    {% if item.options %}
                    {% if item.options.attributes %}
                    <div><small>{{ 'plugins/ecommerce::invoice.detail.attributes' | trans }}:
                            {{ item . options . attributes }}</small></div>
                    {% endif %}
                    {% if item.options.product_options %}
                    <div><small>{{ 'plugins/ecommerce::invoice.detail.product_options' | trans }}:
                            {{ item . options . product_options }}</small></div>
                    {% endif %}
                    {% if item.options.license_code %}
                    <div><small>{{ 'plugins/ecommerce::invoice.detail.license_code' | trans }}:
                            {{ item . options . license_code }}</small></div>
                    {% endif %}
                    {% endif %}
                </td>
                <td style="padding: 4px 20px; font-weight: bold; text-align: center;">{{ item . qty }}</td>
                <td style="padding: 4px 20px; font-weight: bold;" class="right">{{ (item . price) | price_format }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold">
                    {{ (item . sub_total) | price_format }}
                </td>
            </tr>
            {% endfor %}

            {% if invoice.tax_amount > 0 %}
            <tr>
                <td style="padding: 4px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Subtotal' }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold">
                    {{ (invoice . sub_total) | price_format }}
                </td>
            </tr>

            {% if invoice.shipping_amount > 0 %}
            <tr>
                <td style="padding: 4px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Courier/Delivery Charge' }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold">
                    {{ (invoice . shipping_amount) | price_format }}
                </td>
            </tr>

            <tr>
                <td style="padding: 4px 20px; font-weight: bold;" colspan="4" class="right">
                    <strong>{{ 'Subtotal + Shipping' }}</strong>
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold" style="background-color: #e8f4fd; border: 2px solid #17a2b8;">
                    {{ ((invoice.sub_total + invoice.shipping_amount)) | price_format }}
                </td>
            </tr>
            {% endif %}
            {% endif %}



            {% if invoice.discount_amount > 0 %}
            <tr>
                <td style="padding: 4px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'plugins/ecommerce::products.form.discount' | trans }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold">
                    {{ (invoice . discount_amount) | price_format }}
                </td>
            </tr>
            {% endif %}

            {% if invoice.payment_fee > 0 %}
            <tr>
                <td style="padding: 4px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'plugins/payment::payment.payment_fee' | trans }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold">
                    {{ (invoice . payment_fee) | price_format }}
                </td>
            </tr>
            {% endif %}

            <tr>
                <td style="padding: 4px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Total Amount' }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold;" class="bold">
                    {{ (invoice . amount) | price_format }}
                </td>
            </tr>



            {% if invoice.is_paid_delivery_charge == 1 %}
            {% if payment_status == 'pending' %}
            <tr>
                <td style="padding: 6px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Paid' }}
                </td>
                <td style="padding: 4px 5px; font-weight: bold; background: green; color: white; text-align: center;"
                    class="bold">
                    -{{ (invoice . shipping_amount) | price_format }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Total Due Amount' }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold; background: yellow;" class="bold">
                    {{ (invoice . shipping_amount - invoice . amount) | price_format }}
                </td>
            </tr>
            {% endif %}
            {% if payment_status == 'completed' %}
            <tr>
                <td style="padding: 6px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Paid' }}
                </td>
                <td style="padding: 4px 5px; font-weight: bold; background: green; color: white; text-align: center;"
                    class="bold">
                    -{{ (invoice . amount) | price_format }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Total Due Amount' }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold; background: yellow;" class="bold">
                    {{ 0 | price_format }}
                </td>
            </tr>
            {% endif %}
            {% else %}
            <tr>
                <td style="padding: 6px 20px; font-weight: bold;" colspan="4" class="right">
                    {{ 'Total Due Amount' }}
                </td>
                <td style="padding: 4px 20px; font-weight: bold; background: yellow;" class="bold">
                    {{ (invoice . amount) | price_format }}
                </td>
            </tr>
            {% endif %}


        </tbody>
    </table>
    {{ ecommerce_invoice_footer | raw }}
    <br>
    <br>

    <a target="_blank"
        href="https://sobkichubazar.com.bd/orders/tracking?order_id={{ invoice . order_code }}&email={{ invoice . customer_email }}">View
        Order Update Status</a>

    {% if qr_code is defined and qr_code %}
    <div style="text-align: center; margin-top: 5px; margin-right:550px">
        <img src="{{ qr_code }}" alt="QR Code" style="width:90px; height: 90px;" />
    </div>
    {% endif %}
</body>

</html>
