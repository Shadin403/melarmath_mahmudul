<!doctype html>
<html {{ html_attributes }}>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>

    {{ settings.font_css }}

    <style>
        body {
            
            font-family: '{{ settings.font_family }}', Arial, sans-serif !important;
            position: relative;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            font-size: 14px;
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

        .bold, strong, b, .total, .stamp {
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
            margin: 20px 0 50px;
        }

        .invoice-info-container {
            font-size: .875em;
        }

        .invoice-info-container td {
            padding: 4px 0;
        }

        .line-items-container {
            font-size: .875em;
            margin: 70px 0;
        }

        .line-items-container th {
            border-bottom: 2px solid #ddd;
            color: #999;
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

       

table {
    border-collapse: collapse;
    width: 100%;
}

.main-table > tbody > tr:nth-child(odd) {
    background-color: #f0f0f0; /* light gray */
}

.main-table > tbody > tr:nth-child(even) {
    background-color: #ffffff; /* white */
}

td {
    padding: 2px;
    vertical-align: top;
    border: none;
}
    @media print {
            body {
                font-size: 15pt;
                padding: 5mm;
            }
            .product-cell {
                border-color: #999 !important; /* Darker borders for print */
            }
        }

        {{ settings.extra_css }}
    </style>

    {{ settings.header_html }}

    
</head>
<body {{ body_attributes }}>
<table class="w-100 main-table" cellpadding="0" cellspacing="0" border="0" style="width: 100%; border-collapse: collapse;">
    {% for order in orders %}
        <tr style="background-color: {{ loop.index is odd ? '#f0f0f0' : '#ffffff' }};">
            <td style="vertical-align: top; width: 20%; padding: 0; text-align: left;">
                <strong style="font-size:14px">{{ order|first.product_name }}</strong>
            </td>
            <td style="vertical-align: top; width: 80%; padding: 0; text-align: left;">
                <div style="line-height: 1.1; width: 100%;">
                    <table style="width: 100%; border-collapse: collapse; border: none; table-layout: fixed;">
                        <tr>
                            {% for details in order %}
                                <td style="text-align: center; 
                                        vertical-align: top; 
                                        padding: 1px 5px;
                                        background-color: {% if details.product_id in excluded %}black{% else %}transparent{% endif %}; 
                                        color: {% if details.product_id in excluded %}white{% else %}black{% endif %};
                                        border: none;
                                        width: 12.5%; /* Changed to 12.5% for 8 items per row (100%/8) */
                                        ">
                                    {% if details.weight %}
                                        <div style="margin: 0; padding: 0; display: inline; font-size: 12px;">{{ details.weight }}</div>
                                    {% endif %}
                                    <hr style="margin: 1px 0; border: 0; border-top: 1px solid #000;" />
                                    <div style="margin: 0; padding: 0; overflow: hidden; text-overflow: ellipsis; font-size: 12px;">{{ details.option_value }}</div>
                                    <div style="margin: 0; padding: 0; font-size: 10px; overflow: hidden; text-overflow: ellipsis;">Quantity: {{ details.qty }}</div>
                                    <div style="margin: 0; padding: 0; font-size: 10px; overflow: hidden; text-overflow: ellipsis;">{{ details.note }}</div>
                                </td>
                                {% if loop.index % 8 == 0 and not loop.last %}  {# Changed to 8 items per row #}
                                    </tr><tr>
                                {% endif %}
                            {% endfor %}
                            
                            {# Fill remaining cells if needed #}
                            {% set remainder = order|length % 8 %}  {# Changed to modulus 8 #}
                            {% if remainder > 0 %}
                                {% for i in range(1, 8 - remainder + 1) %}  {# Changed to fill up to 8 #}
                                    <td style="border: none;">&nbsp;</td>
                                {% endfor %}
                            {% endif %}
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    {% endfor %}
</table>
</body>
</html>