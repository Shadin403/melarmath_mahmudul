<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Label</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 0;
            size: 10cm 15cm; /* Set the page size to 10x15 cm */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-size: 12px;
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #fff;
            color: black;
            display: flex;
            flex-direction: column; /* Stack labels vertically */
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .label-container {
            width: 9.5cm; /* Slightly smaller than the paper size */
            height: 14.5cm; /* Slightly smaller than the paper size */
            border: 3px solid black;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px;
            box-sizing: border-box;
            margin: 5px auto; /* Center labels and add space between them */
            overflow: hidden;
            page-break-after: always; /* Force each label to start on a new page */
        }

        .section {
            margin-bottom: 5px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        td span {
            font-size: larger;
            font-weight: 500;
        }

        h2 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        h3 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        h4, p {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            vertical-align: top;
            padding: 2px 0;
        }

        .logo {
            max-width: 100px;
            height: auto;
        }

        .qr-barcode {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5px;
        }

        .qr-barcode svg {
            height: 100px;
            width: 100px;
        }

        .barcode {
            font-size: 10px;
        }

        hr {
            border: solid 2px black;
            height: 100%;
        }

    </style>
</head>
<body>
@foreach($pdfData as $data)
    <div class="label-container">
        <!-- Sender Information -->
        <div class="section" style="border-bottom: 2px solid black;">
            <table>
                <tr>
                    <td style="width: 20%; font-size: larger; font-weight: 700;">Sender:</td>
                    <td>
                        <h4>{{ $data['company_name'] }}</h4>
                        <p>{{ $data['company_address'] }}</p>
                        <p>{{ $data['company_email'] }}</p>
                    </td>
                    <td style="width: auto; text-align: right;">
                        <img src="{{ asset('public/storage/' . $data['company_logo']) }}" alt="Logo" class="logo">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Receiver Information -->
        <div class="section" style="border-bottom: 2px solid black;">
            <h2>{{ $data['customer_name'] }}</h2>
            <h4>{{ $data['customer_address'] }}, {{ $data['customer_city'] }}, {{ $data['customer_state'] }}<br>{{ $data['customer_country'] }}, {{ $data['customer_zip_code'] }}</h4>
            <h4>{{ $data['customer_email'] }}</h4>
            <h4>{{ $data['customer_phone'] }}</h4>
        </div>

        <!-- Parcel Details -->
        <div class="section" style="border-bottom: 2px solid black;">
            <table>
                <tr>
                    <td style="padding-bottom: 5px;">
                        <span>Parcel ID:</span>
                        <h3>{{ $data['steadfast_consignment_id'] }}</h3>
                    </td>
                    <td style="padding-bottom: 5px;">
                        <span>Order ID:</span>
                        <h3>{{ $data['order_code'] }}</h3>
                    </td>
                    <td style="padding-bottom: 5px;">
                        <span>Order Date:</span>
                        <h3>{{ $data['order_date'] }}</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Shipping Method:</span>
                        <h3>SteadFast</h3>
                    </td>
                    <td colspan="2">
                        <span>COD Amount:</span>
                        <h3>{{ !empty($data['steadfast_amount']) ? $data['steadfast_amount'] : $data['total_amount'] }}</h3>
                    </td>
                </tr>
            </table>
        </div>

        <!-- QR and Barcode -->
        <div class="qr-barcode">
            <div>{!! $data['qr_code_image'] !!}</div>
            <hr>
            <div class="barcode">
                {!! $data['barcode_image'] !!}
                <h1 style="text-align: center;">
                    {{ $data['steadfast_consignment_id'] }}
                </h1>
            </div>
        </div>
    </div>
@endforeach
</body>
</html>
