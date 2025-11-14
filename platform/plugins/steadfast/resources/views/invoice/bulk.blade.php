
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SteadFast Invoice</title>
    <style>
        

        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&amp;display=swap");
        
        .steadfast-invoice table th {
            background-color: transparent !important;
        }
        
        .steadfast-invoice table tbody td {
            background-color: transparent !important;
        }
        
        .steadfast-invoice table tbody tr:nth-child(2n) td, fieldset, fieldset legend {
            background-color: transparent !important;
        }
        
        .steadfast-invoice .stdf-courier-logo {
            width: 16% !important;
        }
        
        .steadfast-invoice {
            color: #666;
            font-family: "Inter", sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.6em;
            overflow-x: hidden;
            background-color: #f5f6fa;
        }
        
        .steadfast-invoice table {
            width: 100%;
            caption-side: bottom;
            border-collapse: collapse;
            border: none;
        }
        
        .steadfast-invoice table td {
            border: none;
        }
        
        /**
         * 1. Add the correct box sizing in Firefox.
         * 2. Show the overflow in Edge and IE.
         */
        .steadfast-invoice .hr hr {
            -webkit-box-sizing: content-box;
            box-sizing: content-box; /* 1 */
            height: 0; /* 1 */
            overflow: visible; /* 2 */
        }
        
        .steadfast-invoice table tr td {
            padding: 10px 15px;
            line-height: 1.55em;
        }
        
        .steadfast-invoice table thead tr th {
            padding: 6px 8px;
            border: none !important;
            text-align: left;
        }
        
        .steadfast-invoice table thead tr th:last-child {
            text-align: right;
        }
        
        .steadfast-invoice .tm_table .std-payment-info {
            padding-bottom: 10px;
        }
        
        .steadfast-invoice .std-payment-info table tbody tr td {
            padding-bottom: 0;
        }
        
        /*--------------------------------------------------------------
        2. Typography
        ----------------------------------------------------------------*/
        
        .steadfast-invoice .std-consignment {
            margin-bottom: 15px !important;
        }
        
        .steadfast-invoice .std-consignment h5 {
            margin-top: 15px;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        /*--------------------------------------------------------------
        3. Invoice General Style
        ----------------------------------------------------------------*/
        .steadfast-invoice .tm_f16 {
            font-size: 16px;
        }
        
        .steadfast-invoice .tm_f30 {
            font-size: 30px;
        }
        
        .steadfast-invoice .tm_semi_bold {
            font-weight: 600;
        }
        
        .steadfast-invoice .tm_bold {
            font-weight: 700;
        }
        
        .steadfast-invoice .tm_m0 {
            margin: 0;
        }
        
        .steadfast-invoice .tm_pt0 {
            padding-top: 0;
        }
        
        .steadfast-invoice .tm_width_1 {
            width: 8.33333333%;
        }
        
        .steadfast-invoice .tm_width_2 {
            width: 16.66666667%;
        }
        
        .steadfast-invoice .tm_width_3 {
            width: 25% !important;
        }
        
        .steadfast-invoice .tm_width_4 {
            width: 33.33333333% !important;
        }
        
        .steadfast-invoice .tm_border {
            border: 1px solid #dbdfea;
        }
        
        .steadfast-invoice .tm_border_bottom {
            border-bottom: 1px solid #dbdfea;
        }
        
        .steadfast-invoice .tm_border_top {
            border-top: 1px solid #dbdfea;
        }
        
        .steadfast-invoice .tm_round_border {
            border: 1px solid #dbdfea;
            overflow: hidden;
            border-radius: 6px;
        }
        
        .steadfast-invoice .tm_primary_color {
            color: #111;
        }
        
        .steadfast-invoice .tm_ternary_color {
            color: #b5b5b5;
        }
        
        .steadfast-invoice .tm_gray_bg {
            background: #f5f6fa;
        }
        
        .steadfast-invoice .tm_invoice_in {
            position: relative;
            z-index: 100;
        }
        
        .steadfast-invoice .tm_container {
            max-width: 900px;
            padding: 30px 15px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }
        
        .steadfast-invoice .tm_text_uppercase {
            text-transform: uppercase;
        }
        
        .steadfast-invoice .tm_text_right {
            text-align: right;
        }
        
        .steadfast-invoice .tm_align_center {
            -webkit-box-align: center;
            -ms-flex-align: center;
            /*align-items: center;*/
        }
        
        .steadfast-invoice .tm_border_top_0 {
            border-top: 0;
        }
        
        .steadfast-invoice .tm_border_none {
            border: none !important;
        }
        
        .steadfast-invoice .tm_table_responsive {
            overflow-x: auto;
        }
        
        .steadfast-invoice .tm_table_responsive > table {
            min-width: 600px;
        }
        
        .steadfast-invoice hr {
            background: #dbdfea;
            height: 1px;
            border: none;
            margin: 0;
        }
        
        .steadfast-invoice .tm_invoice {
            background: #fff;
            border-radius: 10px;
            padding: 50px;
        }
        
        .steadfast-invoice .tm_invoice_footer {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }
        
        .steadfast-invoice .tm_invoice_footer table {
            margin-top: -1px;
        }
        
        .steadfast-invoice .tm_invoice_footer .tm_left_footer {
            width: 58%;
            padding: 10px 15px;
            -webkit-box-flex: 0;
            -ms-flex: none;
            flex: none;
        }
        
        .steadfast-invoice .tm_invoice_footer .tm_right_footer {
            width: 42%;
        }
        
        .steadfast-invoice .tm_note {
            margin-top: 30px;
            font-style: italic;
        }
        
        .steadfast-invoice .tm_invoice_right {
            -webkit-box-flex: 0;
            -ms-flex: none;
            flex: none;
            width: auto;
        }
        
        .steadfast-invoice .tm_invoice_head {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
        }
        
        .steadfast-invoice .tm_invoice_head .tm_invoice_right div {
            line-height: 1em;
        }
        
        .steadfast-invoice .tm_invoice_info {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
        }
        
        .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_seperator {
            min-height: 2px;
            border-radius: 1.6em;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
            margin-right: 20px;
            background-color: black;
        }
        
        .steadfast-invoice .invoice_img img {
            height: 25px;
        }
        
        .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_info_list {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }
        
        .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_info_list > *:not(:last-child) {
            margin-right: 20px;
        }
        
        .steadfast-invoice .tm_invoice.tm_style1 .tm_logo img {
            max-height: 50px;
        }
        
        .steadfast-invoice .tm_invoice_wrap {
            position: relative;
        }
        
        .steadfast-invoice .tm_note_list li:not(:last-child) {
            margin-bottom: 5px;
        }
        
        .steadfast-invoice .tm_list.tm_style1 {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .steadfast-invoice .tm_list.tm_style1 svg {
            width: 16px;
            height: initial;
        }
        
        .steadfast-invoice .tm_list.tm_style1 li {
            padding-left: 25px;
            position: relative;
        }
        
        .steadfast-invoice .tm_list.tm_style1 li:not(:last-child) {
            margin-bottom: 5px;
        }
        
        .steadfast-invoice .tm_list.tm_style1.tm_text_right li {
            padding-left: 0;
            padding-right: 25px;
        }
        
        .steadfast-invoice .tm_padd_15_20 {
            padding: 15px 20px;
        }
        
        
        .steadfast-invoice .tm_padd_15 {
            padding: 15px;
        }

        .qr-code-container svg {
                width: 70px;
                height: 70px;
                padding: 0;
                margin:  0;
            }
            .qr-code-container{
                display: flex;
                align-content: center;
                justify-content: center;
                align-items: center;
                flex-wrap: nowrap;
                flex-direction: column;
                gap: 10px;
            }
           
        
        
        @media (max-width: 767px) {
            .steadfast-invoice .tm_invoice {
                padding: 30px 20px;
            }
        
            .steadfast-invoice .tm_invoice .tm_right_footer {
                width: 100%;
            }
        
            .steadfast-invoice .tm_invoice_footer {
                -webkit-box-orient: vertical;
                -webkit-box-direction: reverse;
                -ms-flex-direction: column-reverse;
                flex-direction: column-reverse;
            }
        
            .steadfast-invoice .tm_invoice_footer .tm_left_footer {
                width: 100%;
                border-top: 1px solid #dbdfea;
                margin-top: -1px;
                padding: 15px 0;
            }
        
            .steadfast-invoice .tm_invoice_footer.tm_type1 {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: column;
                flex-direction: column;
            }
        
            .steadfast-invoice .tm_invoice.tm_style2 .tm_invoice_info > * {
                width: 100%;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 {
                padding: 30px 20px;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 .tm_invoice_head {
                height: initial;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 .tm_invoice_info {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: column;
                flex-direction: column;
                -webkit-box-align: start;
                -ms-flex-align: start;
                align-items: flex-start;
                padding-left: 15px;
                padding-right: 15px;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 .tm_invoice_seperator {
                width: 100%;
                -webkit-transform: initial;
                transform: initial;
                right: 0;
                top: 0;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 .tm_logo img {
                max-height: 60px;
            }
        
            .steadfast-invoice .tm_border_none_md {
                border: none !important;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 .tm_invoice_seperator,
            .tm_invoice.tm_style1.tm_type1 .tm_invoice_seperator img {
                -webkit-transform: initial;
                transform: initial;
            }
        
            .steadfast-invoice .tm_shape_2.tm_type1 {
                display: none;
            }
        }
        
        @media (max-width: 500px) {
            .steadfast-invoice .tm_border_none_sm {
                border: none !important;
            }
        
            .steadfast-invoice .tm_m0_sm {
                margin-bottom: 0;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_logo {
                margin-bottom: 10px;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_head {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: column;
                flex-direction: column;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_head .tm_invoice_left,
            .tm_invoice.tm_style1 .tm_invoice_head .tm_invoice_right {
                width: 100%;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_head .tm_invoice_right {
                text-align: left;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_left {
                max-width: 100%;
            }
        
            .steadfast-invoice .tm_f50 {
                font-size: 30px;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_info {
                -webkit-box-orient: vertical;
                -webkit-box-direction: reverse;
                -ms-flex-direction: column-reverse;
                flex-direction: column-reverse;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_seperator {
                -webkit-box-flex: 0;
                -ms-flex: none;
                flex: none;
                width: 100%;
                margin-right: 0;
                min-height: 5px;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_info_list {
                width: 100%;
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1 .tm_invoice_seperator + .tm_invoice_info_list {
                margin-bottom: 5px;
            }
        
            .steadfast-invoice .tm_f30 {
                font-size: 22px;
            }
        }

        /* Page Break for Printing */
        .page-break {
       
            page-break-after: always;  /* Forces a page break after the element */
            page-break-inside: avoid;  /* Prevents page breaks inside the element */
        }
        /*--------------------------------------------------------------
          Will apply only print window
        ----------------------------------------------------------------*/
        @media print {
        
            @page {
                size: auto;
                margin: 0;
            }

            .page-break {
            
                page-break-after: always;  /* Forces a page break after the element */
                page-break-inside: avoid;  /* Prevents page breaks inside the element */
            }
        
            .steadfast-invoice .tm_gray_bg {
                background-color: #f5f6fa !important;
                -webkit-print-color-adjust: exact;
        
            }
        
            .steadfast-invoice .tm_ternary_color {
                color: #b5b5b5 !important;
                -webkit-print-color-adjust: exact;
            }
        
            .steadfast-invoice .tm_hide_print {
                display: none !important;
            }
        
            .steadfast-invoice .hr hr {
                background: #dbdfea !important;
                -webkit-print-color-adjust: exact;
            }
        
            .steadfast-invoice .tm_invoice.tm_style1.tm_type1 .tm_invoice_seperator {
                top: initial;
                margin-right: 0;
                border-radius: 0;
                -webkit-transform: skewX(35deg);
                transform: skewX(35deg);
                position: absolute;
                height: 100%;
                width: 57.5%;
                right: -60px;
                overflow: hidden;
                border: none;
            }
        }
        
    </style>
</head>
<body>
@php
    use Illuminate\Support\Str;
@endphp
@foreach($pdfData as $data)
<div class="steadfast-invoice">
        <div class="tm_container">
            <div class="tm_invoice_wrap">
                <div class="tm_invoice tm_style1" id="tm_download_section">
                    <div class="tm_invoice_in">
                        <div class="tm_invoice_head tm_align_center tm_mb20">
                            <div class="tm_invoice_left">
                                <div class="tm_logo">
                                    <img src="{{ asset('public/storage/' . $data['company_logo']) }}" alt="Business Logo">
                                </div>
                            </div>
                            <div class="tm_invoice_right tm_text_right">
                                <div class="tm_primary_color tm_f30 tm_text_uppercase">Invoice</div>
                            </div>
                        </div>
                        <div class="tm_invoice_info tm_mb20">
                            <div class="tm_invoice_seperator"></div>
                            <div class="tm_invoice_info_list">
                                <p class="tm_invoice_number tm_m0">Invoice No: <b class="tm_primary_color">{{ $data['order_code'] }}</b></p>
                                <p class="tm_invoice_date tm_m0">Date: <b class="tm_primary_color">{{ $data['order_date'] }}</b></p>
                            </div>
                        </div>
                        <div class="tm_invoice_head tm_mb10">
                            <div class="tm_invoice_left">
                                <p class="tm_mb2"><b class="tm_primary_color">Invoice To:</b></p>
                                <p>
                                    {{ $data['customer_name'] }}
                                    <br>
                                    {{ $data['customer_address'] }}, {{ $data['customer_city'] }}, {{ $data['customer_state'] }}
                                    <br>
                                    {{ $data['customer_country'] }}, {{ $data['customer_zip_code'] }}
                                    <br>
                                    {{ $data['customer_email'] }}
                                    <br>
                                    {{ $data['customer_phone'] }}
                                </p>
                            </div>
                            <div class="tm_invoice_right tm_text_right">
                                <p class="tm_mb2"><b class="tm_primary_color">Pay To:</b></p>
                                <p>
                                    {{ $data['company_name'] }}
                                    <br>
                                    {{ $data['company_address'] }}
                                    <br>
                                    {{ $data['company_email'] }}
                                </p>
                            </div>
                        </div>
                        <div class="std-consignment">
                            <div class="tm_padd_15_20 tm_round_border">
                                <div class="tm_mb10 tm_invoice_head tm_align_center">
                                    <div class="tm_invoice_left">
                                        <p class="tm_mb3"><b class="tm_primary_color">For SteadFast Courier LTD</b></p>
                                    </div>
                                    <div class="tm_invoice_right tm_text_right invoice_img stdf-courier-logo ">
                                        <img class="SteadFast_logo" src="" id="SteadFast_logo" alt="SteadFast Logo">
                                    </div>
                                </div>
                                <hr class="hr">
                                <div class="tm_invoice_head tm_mb10">
                                    <div class="tm_invoice_left">
                                        <h5 class="tm_mb2"><b class="tm_primary_color">Parcel ID: #{{ $data['steadfast_consignment_id'] }}</b></h5>
                                    </div>

                                    <div class="qr-code-container">
                                        {!! $data['qr_code_image'] !!}
                                        {!! $data['barcode_image'] !!}
                                    </div>

                                    <div class="tm_invoice_right tm_text_right">
                                        <h5 class="tm_mb2">
                                            <b class="tm_primary_color">
                                                COD Amount: &#2547;
                                                {{ !empty($data['steadfast_amount']) ? $data['steadfast_amount'] : $data['total_amount'] }}
                                            </b>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tm_table tm_style1 tm_mb30">
                            <div class="tm_round_border">
                                <div class="tm_table_responsive">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th class="tm_width_3 tm_semi_bold tm_primary_color tm_gray_bg">Item</th>
                                            <th class="tm_width_2 tm_semi_bold tm_primary_color tm_gray_bg">Price</th>
                                            <th class="tm_width_1 tm_semi_bold tm_primary_color tm_gray_bg">Qty</th>
                                            <th class="tm_width_2 tm_semi_bold tm_primary_color tm_gray_bg">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data['order_products'] as $product) 
                                            <tr>
                                                <td class="tm_width_3">{{ $product->product_name }}</td>
                                                <td class="tm_width_2">&#2547;{{ $product->price }}</td>
                                                <td class="tm_width_1">{{ $product->qty }}</td>
                                                <td class="tm_width_2 tm_text_right">&#2547;{{ $product->qty * $product->price }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tm_invoice_footer std-payment-info">
                                <div class="tm_left_footer">
                                    <p class="tm_mb2"><b class="tm_primary_color">Payment info:</b></p>
                                    <p class="tm_m0">{{ $data['order_payment_method'] }}</p>
                                </div>
                                <div class="tm_right_footer">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td class="tm_width_3 tm_primary_color tm_border_none tm_bold">Subtotal</td>
                                            <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_bold">&#2547;{{ $data['sub_total'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">Shipping cost</td>
                                            <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0">&#2547;{{ $data['shipping_amount'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">Tax <span class="tm_ternary_color">(0%)</span></td>
                                            <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0">&#2547;{{ $data['tax_amount'] }}</td>
                                        </tr>
                                        <tr class="tm_border_top tm_border_bottom">
                                            <td class="tm_width_3 tm_border_top_0 tm_border_bottom tm_bold tm_f16 tm_primary_color">Grand Total</td>
                                            <td class="tm_width_3 tm_border_top_0 tm_border_bottom tm_bold tm_f16 tm_primary_color tm_text_right">&#2547;{{ $data['total_amount'] }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tm_padd_15_20 tm_round_border">
                            <p class="tm_mb5"><b class="tm_primary_color">Terms & Conditions:</b></p>
                            <p>{{ $data['terms_Conditions'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-break"></div>
@endforeach



<script>
    var image_data = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAK4AAAAlCAYAAADbe9L1AAAABmJLR0QA/wD/AP+gvaeTAAAQxklEQVR42u1dCXQUVRYFBkcdt3HfxwV0NI2p7qoKkJUEENwFx4jCKByEdHViWIJMBFEjiHJAtpDe6CBLd7EECEgUgiIoixoGIYMEDQzCCLLIEjUIAobMe9VVofLrV3dVJxjE/ue806GruupX1f3v33ff+0WzZudOax4XF3dDPMfd095qvZ3juCtSU1Nb4oaO3oxWzWqbNW8WbdH2W7ak++67MpGJs7S3xXWOt/HPJdi43ASWzwt+shkA1nSwbrhPenr6n/A3lqL0P3dwC44OTmFkp4Ksq6N3MdoapYE3vCgltu0d7Xk+MZFlnwAQZgMI30hguenxLLs0keX+A3/vAtsIIJ2baONGAjh7Jtps8IPEy9THwn8juOPj4y9Gz5ricvTu4BIq4LNn9E5Hm6GG4AFw3ZTEshzYo+gRJe/I8l4AYQnYBrC9YKfBamU7BbZD2s5xY9Crqr2nDvBbJths8QjkZJvtWvwuZUq/OwCwH4J9l+qx89GnEW26DYA2BAC3Gj4r41nuqAqMVANPug8BCh70LfSgCdY4q8Vi+bPR88k8VqIG6LWlL8HLdnDZcwCwP4NtSJzywk3RJxNt4ab8lgDa8SHA+jWAdVyCjf9HSlzcrZGepx3Lxiay/GAELPyzRd35nZmXpriEBQDYWgRtfOHzV0WfSrSZ8bwDiOkfbY0Zb0o9rs2WEs/yYxOs/OOoHKi3oVoAYP1SBu26dvnZl9OOERsbewkA/j6Zvpg2aBfE27g3g3RHMW7S+fLskJbVvzbem8hxacr2NJeDS3U5RqQ6BQ/MbF4zBr8b1CQO1ZtxDcQ4uYQ9pgMyDLDqe1wMtOqmdDNKAse1Aw9bgByZxnXTCuydAKyHZdBuTJzW9zKNl27X7nIA3Azox6/hKEwoatO2bdur4e8aYlvJeeN0WPY1zbVD/CABwC28Aff3tHyfzZtbmNAU15TitPfQ9seeHeIm8C9rHz6/TIr4DY9+dhYOAvR0tH3AAzwMHTku35jdqfn9bqHs1gJAuypSwJ4xdkGijX9M+2C5EecPcCFGqX991XjvwTk8HjFg68yR3iQe12Uv0PTFmRkXWlmQplUNCJaHAi9O5zA9vQqA8CXExl6n2yGn/UnoxEl5BFUle+wx9D7wzzQctDDoOK6fxNHJmcQal3A+gBbvO1zPCeL6SnEb3OP1DQWujlM56w3OXU705Yf0In2VSmnNQTUQtUDgP0m1WC7V7MtxvQEcRagwhHT/bqEjdOAXuSM1KR77QyE8/wri/KdkReNICDtNoTqtQS35nPj+JGbokuLi7kQD7/SX3wtQH2zd+kLMLCp9x3tPGazDkvOzr6VQhBqwnWA7jBhwys8bpdN5eS0gCL8BnvddyLfDWaozI0nuq7rvSw3ruTDNrtc8cPCEdfvwvA087PsAgoFqpYA6gsDNw8mr60ayU8jT2xcDQjjXMWLQ5JnRouWExlUISopHqmftWfZe6uCFAAeuzwX7lIP9RPwOefc2GBiDyKBTfQwIertIsxDLfam9Jq1hEKqhXyzbXu7HTgpXr6XxWwxkSO+Z4hSmmB4o+dkXYiIIgrS5cIz/gp0I46V31vUb4hZ4zm/DdwcbTlnq2bGQnU7muBvhxu9WFAbkrwqwJB2X5ebhPuEuvqNbuBmTCnU30CWU4ijU5cksm0zxImmRDHZMM4d50EfIQYcPHr7/zDAlgaSL5hogsQLbvjBJbQ6qBwHKjzDLFJs8hsRvgSOO0Uz7QNNMTdduoTv87ltT1MIlzJCcB8iaQAW3NDJgFVsdPtjCFK2Nf165oZDlipGCtaAeG55kT+9zEZyoTHXSg8nejBtDBxv8vzTAhWxeJMAFvv56mAf9HiELZsqZQDNgOaXm9TDwesB3v0QSSNZXZSRqZJbXL5c54uqG8NUUl31URJzYKfQJnt8x8yyBthYHpRkMNEdpCz2A5GXRYxqo2IIpppCQV7obiJKnm3xYOH1uQyWDnLbh+5Wh5TJ2+JlBygmRBoFAGbrKwO+ClCoy6Y5/QZIBrda74N9VEQajwzhvxgVyFtIwGNR8FjzzixGDyi3cjnwW/j5F2X7AALf+n4G+PmZMkwW+iN4AHooDgREk/o5lHTz2+8MEY08R0spMQ16S49wRKwkcl09w5Z+JfXbIKWfJ2sW2u0Xm620pgPsOOSwcs5Vai5buBXFeTHRIerGN20/p15eYeYxnmJtDDU7kt5jJlAuUSPnuA4wvkO8qiRVaP5DmJLuFtmYBBx52opKsOKP6qKUo4QOIU9LUlXrghDYT++1SUQzSSxYY8vQF9hRK/0oAOxmKdfZmXGFIl0Uvi1G4PAUkw4H2yEBcpstrC7JuQ7lLdfIjCW7hOkNaMM/f3wAJ7DSCUMVVSY80U4eefEImLhRQS/QIZLVgwREa9wNx3J8Q2ACuHEp/SmnZRznQUu/3PToFAGdfipozWmdm2kDnt45BpoELTkamGCJF+B+riX8oqoXCb2XJkwT+frBpkrmEIqAUATjnaynOzHvry2D2YZS+9TLHD1n2EbgZTtQKpSkMUrIA1qOqg55OK8iy0HU4+3KCIjhMpTGlrFuk4A0qEDSuDJ7vWVqWj0Ihhsre/yUD2bliGUhkMHacpmnDgLqNMlPMl4+xldhWTlNssBSUwsWD/NYpzDMNXGfmrV3GPXsJ8XzxuX1Bo4RACZ6mTOG9g1mvzFspcpae/QrAtKu49Xu0vhkHLUhc6F20spYwuf4ocxRS0nXPESffYEQ8pqgLT0Cdw0IA4L9l70KzrZopFwIyGQQlpDdO5bhrKN52NE3/xUo2I+nmeJutkwykmvrH4JdQr4uivcL5spCSaL/n7UbVEuS3stcklYBDGNDoGYD1df1p2pFhKHYBw1lWlUBwmRg4P8RPGHwxxk04MxPevtJwEIagRTWBnvmSiPcx1cGPd/L1u15dHEHodjVhU3UNaJJkRyQeAPA4tbSQkxLqh7tdZ8rVBHAIWrnGIRRwj2HFWwiv7TEafCIlo2cL2Y5m6hNkb0fWG8w3JH857ZmUYOsRncwWGUTtIJMO4OQGY0rfCHjT3EIinKsNhab4jfS9BUbW4aQnbR45OGKDrt7hJqYP91nN08PUT5l2W2EZJQVo03WA9BUFdL1kgHSUUuCg1SomSWwQqCocWAZuEuUYH+qcbycReO2XEh4wCCjX4tA5xiRi5vkM+a1OcUqOwcKWgRTQD9BQq4L+d1M47DTd4N7luBLsTjSgBTboz0oNcKGuAikDhd/ajVR0JRlJf8qjWp1B+R5dPXjbewgZpNpoQCZz6q7qiN+IITiIB71brnV4juK95tAq1dATU0D+PfSnu16hkObeMW3v1smEuaFPDytKAF6jlg5w83Q5OfZDDjYN6eZuYZIGVB57vEGP+wzFG/4I4BkCx01VUrJUjdctSLFDuLRuMJ1r36RVHRzt4RhOmmargB5n82a0EsJwaVsi+HqH5EIkscYLNHo8BAhFuoqkGmyWzCP7hdZd+YVnuLTp7JSaV/ZWDYBdER0DZjll4OoqJSxXEYLnb8BBplNY8wsmgYw8A6R8GCw1pBgH1wqa/729StKeKcAlMn8vNniKlqcLdeRI5qOPpE4c9FcT9btJjVENpiQCUBMNUxMwUO3pIz0fJgtUFW19IwIuz/9d8pa4GgVWnER0DOTIMOtpagmcwqcm07zOCIC7DX8bVCXMA1+pW6HTnDOG+nTjFPtSpAuVBx7e4GJo85mnZerMGUy9fl3gQoFQfa7MvW36nDbuMDFLNaeVUIaxvZQahyqzxfJ4bh1VYLyZ54DgC/1cqcXmU2UKmRCBt12E3lYJ6KCO5WOdfatT8/JaNgpwIRLsqnOSg7QVDaGDLO79BoAW6wImKXpzHd+DlRvgTSfAth+J/auoXNfGP43FROGqyVR8erGOjNceKQt49W8MHGO2ZvaBckXYFqBUpOnRozkyv32poYU1dWWILkc3AP0SOU0bLgXbUw7MexosmdyK1WZ4DlIjxlwBZvHURVl1hVmN1qTVuVKpGzmKhpkuholwTRmWJIZbF4cglZbcyzWs6tSrnrqCUpiyv57RNGFqwAQ1zJg2hzjies1x4PtQ/cbt+P6KkH2Rj4HBixLIKIZliY2yIgFoH6oDyGXP1jl+40p1+6sEcH+Ovnkm2s75FqxJOBOkRVKsHG3R1kReV1inZMkgs9Y6ekeatqHSga8EAPpViAZB60SjtOasJIkg8MZM5Dl3o8DLvqyUn0Vh07RNeo8by76rXo4UXHnddO+RIBWcc6aBl7UGaYL9iSh0mtjTwpJ+XFBpCOQQsEoyIKxnk4qZbLxGgUiy2RhQZh6o+w0WE8EqETKIlN5/AW9EwrJR9fmD74jjXBowQxETvgwRBxnWiij6e/3+8c/jGz0xswgD8qlGv2FY+YU1ur/L6PI8avCQJ2PBuiHQAgBxJbdCIRBsAKDNJKXA8k58G5Fq2u+oFBbVfcfzbeC3B2gLPdHbKtV6SpMLo0oUdUde21hJoRi4eCHfaNo9MrrgFvo2yGu7Hb5Uj8MP2nAgzZPl7ejOEju7HWIXb5a389TMwAO+Af4Hp2YHHvQN8D76zkDxcbBu0weK3Wfk+J70v+jvMWtooEdgqKdnYKjYa84w8VlxuNh77su+3vNG+PvPHxHIWPiqR1jwmuhYmCcOWDRSHFT8hm/wu6P8Qxa/FRj63lhPbsk4cXjpOHFE6QTxtdLxvlHLJ/tHfZgfGP3RFP/bq1y+cR+7xQmfeMWCtV7flHWFfue6GQHf535/YZnoKyybI87cMEf0b5rvm12+wD+vvDiwoPxd/+ItS3xLKkrE979aKpZ+vcL3QeUK/4rtqwIf71jtX/vNOt+nuz4V13+7Xty0d5OvfG+5f/PezYGv92/1Vx6s9G0/tE3ceWSnuPvIbnHvT3u9+6v3Bw4dPeSvOn7YV33iR/HYyWPiiZoTYk1tjbe2tjYj+KCllxe2MjR9Y5bPGhdX3wvyC0kuihqx+pUEWDqJr5YljyWvS6Tx2wys6yYG2Aj5pTG5kkEhEa7w0P6WWxPqjZ+N5nUb8vtO79hj0jx2Bq3LZKHN/VMzrY9MszNdZ2RZusJ3XT3ZYDnMw1D69tD0Qdbu07KZbvCZXjQkpse0HOaf/hzmmRmDLenicGuP4lymD3z2KsqLsc/NZez+V5j+s3Mt/cU8q71kJDOoaLR1yOI3Y3KKRzI5JfnM4CUjLcMXjbXmlo5h8hZNtL65Ij9m5MrxzJg1+cyYlR5m3Cp3m/wyD+NcNd1asHaGxbPGw3jK0PxM4bpZbaZtKWbE9aJ1dvlsy9yyuYy/rJiZC7akYoml9JtSZlHFImvp9pWWlZUrmTVb1jD4ubZiraVsz0Zm/Z711vJ95fB3GbPxwEam8sAWpmJfhWVP1R5mZ9U2a9XRKmbf4X0xB44eYKqqDjDV1dWWkydPMkdrj1oBsAyYBezGoHfkivCtPRoKoapiO+NJ+YlqDRlS7n8DMK2jTOlLCTB9RBZiYfkm6t46s4CPXAmO/URNWzkOvkZAWUFezyvDDBKdR/8ADflosOie7SOtoQPaIGXgKKWRuDxKeokL0AC5sL2cVoWGBTyYAEEKgQtL4VibtIMguAKE6tkpxfRAKV6B44xCTp7E86nIYcnBhavIscIv+lT/IA29l7SaIvjfFPQPJUMFl8CzQ3F/9Li0ffCtOdLrYWElhgQ0eNk3xSt31QeudlswYINBFexjH/IN9cogDNX3/wM1+UKQ7jSFZQAAAABJRU5ErkJggg==";
    // Select all elements with the class 'SteadFast_logo' and set their src
    document.querySelectorAll('.SteadFast_logo').forEach(function(element) {
        element.src = image_data;
    });
</script>

</body>