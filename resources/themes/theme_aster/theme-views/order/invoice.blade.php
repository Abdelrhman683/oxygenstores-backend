@php
    use Illuminate\Support\Facades\Session;
    $currencyCode = getCurrencyCode(type: 'default');
    $direction = 'rtl';
    $lang = getDefaultLanguage();
    $orderTotalPriceSummary = \App\Utils\OrderManager::getOrderTotalPriceSummary(order: $order);
    $companyName = $companyName ?? getWebConfig(name: 'company_name');
    $companyEmail = $companyEmail ?? getWebConfig(name: 'company_email');
    $companyPhone = $companyPhone ?? getWebConfig(name: 'company_phone');
    $shopCity = getWebConfig(name: 'shop_city') ?? '';

    $sellerName = ($order->seller_is != 'admin' && isset($order->seller) && isset($order->seller->shop)) ? $order->seller->shop->name : $companyName;
    $vatNumber = ($order->seller_is != 'admin' && isset($order->seller) && $order->seller->gst != null) ? $order->seller->gst : '301157358600003';
    $totalAmount = $orderTotalPriceSummary['totalAmount'] ?? $order->order_amount;
    $vatAmount = $orderTotalPriceSummary['taxTotal'] ?? 0;
    $zatcaQr = \App\Utils\Helpers::getZatcaQrCodeValue($sellerName, $vatNumber, $order['created_at'], $totalAmount, $vatAmount);
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl"
      style="text-align: right;"
      xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>{{ translate('invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
       @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap');

@font-face {
    font-family: 'PingARLT';
    src: url('../fonts/pingarlt/PingARLT-Light.woff2') format('woff2');
    font-weight: 300;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'PingARLT';
    src: url('../fonts/pingarlt/PingARLT-Regular.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'PingARLT';
    src: url('../fonts/pingarlt/PingARLT-Medium.woff2') format('woff2');
    font-weight: 500;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'PingARLT';
    src: url('../fonts/pingarlt/PingARLT-Bold.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'PingARLT';
    src: url('../fonts/pingarlt/PingARLT-Heavy.woff2') format('woff2');
    font-weight: 800;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'PingARLT';
    src: url('../fonts/pingarlt/PingARLT-Black.woff2') format('woff2');
    font-weight: 900;
    font-style: normal;
    font-display: swap;
}

*, body, p, a, span, button, input, h1, h2, h3, h4, h5, h6, div, select, textarea {
    font-family: 'PingARLT', 'Cairo', 'Tajawal', sans-serif !important;
}

        * {
            margin: 0;
            padding: 0;
            line-height: 1.5;
            color: #303030;
        }

        body {
            font-size: 9px !important;
            color: #303030;
            background-color: #FFFFFF;
        }

        .main-content {
            padding: 20px;
            width: 100%;
            max-width: 595px;
            margin: 0 auto;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .color-bar {
            height: 4px;
            background-color: #FF5A36;
            margin-bottom: 20px;
            width: 100%;
        }

        .invoice-title-sec {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-title-ar {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 4px;
        }

        .invoice-title-en {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            letter-spacing: 1px;
        }

        .bill-cards-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .bill-card-td {
            width: 48%;
            background-color: #F8F9FA;
            border-radius: 6px;
            padding: 12px;
            vertical-align: top;
        }

        .bill-card-inner-table {
            width: 100%;
            border-collapse: collapse;
                        direction: ltr !important;

        }

        .card-title-en {
            color: #FF5A36;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        .card-title-ar {
            color: #FF5A36;
            font-size: 11px;
            font-weight: bold;
            text-align: right;
        }

        .card-row td {
            padding: 4px 0;
            font-size: 9px;
            vertical-align: top;
        }

        .card-label-en {
            color: #7F8185;
            text-align: left;
            width: 25%;
        }

        .card-value {
            color: #303030;
            text-align: center;
            font-weight: bold;
            width: 50%;
            padding: 0 4px;
        }

        .card-label-ar {
            color: #7F8185;
            text-align: right;
            width: 25%;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .product-table th {
            background-color: #1A365D;
            color: #FFFFFF;
            padding: 8px 6px;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #1A365D;
        }

        .product-table th div {
            color: #FFFFFF;
        }

        .product-table td {
            padding: 8px 6px;
            font-size: 9px;
            border-bottom: 1px solid #EAEAEA;
            vertical-align: middle;
        }

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
                        direction: ltr !important;

        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-row td {
            padding: 5px 0;
            font-size: 9px;
        }

        .totals-label-en {
            color: #303030;
            text-align: left;
            width: 25%;
        }

        .totals-value {
            color: #000000;
            font-weight: bold;
            text-align: center;
            width: 50%;
        }

        .totals-label-ar {
            color: #303030;
            text-align: right;
            width: 25%;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .main-content {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="main-content">

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <table>
                    <tr>
                        <td style="font-size: 9px; color: #7F8185;">تاريخ الفاتورة</td>
                        <td style="padding: 2px 8px; font-weight: bold; font-size: 9px; text-align: center;">{{ date('Y-m-d', strtotime($order['created_at'])) }}</td>
                        <td style="padding-left: 8px; text-align: left; font-size: 9px; color: #7F8185;">Invoice Date</td>
                    </tr>
                    <tr>
                        <td style="font-size: 9px; color: #7F8185;">رقم الطلب</td>
                        <td style="padding: 2px 8px; font-weight: bold; font-size: 9px; text-align: center;">{{ $order->id }}</td>
                        <td style="padding-left: 8px; text-align: left; font-size: 9px; color: #7F8185;">Order No.</td>
                    </tr>
                    <tr>
                        <td style="font-size: 9px; color: #7F8185;">طريقة الدفع</td>
                        <td style="padding: 2px 8px; font-weight: bold; font-size: 9px; text-align: center;">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                        <td style="padding-left: 8px; text-align: left; font-size: 9px; color: #7F8185;">Paymet</td>
                    </tr>
                </table>
            </td>
            <td style="text-align: left;">
                <img height="45" src="{{ asset('assets/front-end/img/logo.png') }}" alt="OXYGEN Logo" style="object-fit: contain;" onerror="this.src='https://placehold.co/180x50/png?text=OXYGEN'">
            </td>
        </tr>
    </table>

    <!-- Separator -->
    <div class="color-bar"></div>

    <!-- Title -->
    <div class="invoice-title-sec">
        <div class="invoice-title-ar">فاتورة ضريبية مبسطة</div>
        <div class="invoice-title-en">TAX INVOICE</div>
    </div>

    <!-- Bill Cards -->
    <table class="bill-cards-table">
        <tr>
            <!-- Bill From -->
            <td class="bill-card-td">
                <table class="bill-card-inner-table">
                    <tr>
                        <td class="card-title-en">Bill From</td>
 <td class="card-title-ar"></td>

                        <td class="card-title-ar">الفاتورة من</td>
                    </tr>
                    <tr><td colspan="2" style="height: 6px;"></td></tr>
                    <tr class="card-row">
                        <td class="card-label-en">Name</td>
                        <td class="card-value">
                            @if($order->seller_is != 'admin' && isset($order->seller) && isset($order->seller->shop))
                                {{ $order->seller->shop->name }}
                            @else
                                {{ $companyName }}
                            @endif
                        </td>
                        <td class="card-label-ar">اسم</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Address</td>
                        <td class="card-value">
                            @if($order->seller_is != 'admin' && isset($order->seller) && isset($order->seller->shop))
                                {{ $order->seller->shop->address ?? getWebConfig('shop_address') }}
                            @else
                                {{ getWebConfig('shop_address') }}
                            @endif
                        </td>
                        <td class="card-label-ar">العنوان</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Governorate</td>
                        <td class="card-value">{{ $shopCity }}</td>
                        <td class="card-label-ar">المحافظة</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">VAT No.</td>
                        <td class="card-value">
                            @if($order->seller_is != 'admin' && isset($order->seller) && $order->seller->gst != null)
                                {{ $order->seller->gst }}
                            @else
                                301157358600003
                            @endif
                        </td>
                        <td class="card-label-ar">الرقم الضريبي</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Email</td>
                        <td class="card-value">
                            @if($order->seller_is != 'admin' && isset($order->seller))
                                {{ $order->seller->email ?? $companyEmail }}
                            @else
                                {{ $companyEmail }}
                            @endif
                        </td>
                        <td class="card-label-ar">البريد الإلكتروني</td>
                    </tr>
                </table>
            </td>

            <td style="width: 4%;"></td>

            @php($billingAddress = $order->billing_address_data)
            @php($shippingAddress = $order->shipping_address_data)
            <td class="bill-card-td">
                <table class="bill-card-inner-table">
                    <tr>
                        <td class="card-title-en">Bill To</td>
                                                <td class="card-title-ar"></td>

                        <td class="card-title-ar">الفاتورة إلى</td>
                    </tr>
                    <tr><td colspan="2" style="height: 6px;"></td></tr>
                    <tr class="card-row">
                        <td class="card-label-en">Name</td>
                        <td class="card-value">
                            @if(!empty((array)$billingAddress))
                                {{ $billingAddress->contact_person_name ?? '' }}
                            @elseif(!empty((array)$shippingAddress))
                                {{ $shippingAddress->contact_person_name ?? '' }}
                            @else
                                {{ $order->customer != null ? $order->customer['f_name'].' '.$order->customer['l_name'] : translate('guest_User') }}
                            @endif
                        </td>
                        <td class="card-label-ar">اسم</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Address</td>
                        <td class="card-value">
                            @if(!empty((array)$billingAddress))
                                {{ $billingAddress->address ?? '' }}
                            @elseif(!empty((array)$shippingAddress))
                                {{ $shippingAddress->address ?? '' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="card-label-ar">العنوان</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Governorate</td>
                        <td class="card-value">
                            @if(!empty((array)$billingAddress))
                                {{ $billingAddress->city ?? '' }}
                            @elseif(!empty((array)$shippingAddress))
                                {{ $shippingAddress->city ?? '' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="card-label-ar">المحافظة</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Email</td>
                        <td class="card-value">
                            {{ $order->customer != null ? $order->customer['email'] : translate('email_not_found') }}
                        </td>
                        <td class="card-label-ar">البريد الإلكتروني</td>
                    </tr>
                    <tr class="card-row">
                        <td class="card-label-en">Phone</td>
                        <td class="card-value">
                            @if(!empty((array)$billingAddress))
                                {{ $billingAddress->phone ?? '' }}
                            @elseif(!empty((array)$shippingAddress))
                                {{ $shippingAddress->phone ?? '' }}
                            @else
                                {{ $order->customer != null ? $order->customer['phone'] : translate('phone_not_found') }}
                            @endif
                        </td>
                        <td class="card-label-ar">رقم الهاتف</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Products Table - RTL fixed: Product Name (right) → Qty → Price Excl TAX → TAX Rate → TAX Price → Price Incl TAX (left) -->
    <table class="product-table">
        <thead>
        <tr>
            <th style="text-align: right; width: 40%;">
                <div>Product Name</div>
                <div style="font-size: 7px; font-weight: normal; margin-top: 1px;">إسم المنتج</div>
            </th>
            <th style="text-align: center; width: 8%;">
                <div>Quantity</div>
                <div style="font-size: 7px; font-weight: normal; margin-top: 1px;">الكمية</div>
            </th>
            <th style="text-align: center; width: 14%;">
                <div>Price Excl TAX</div>
                <div style="font-size: 7px; font-weight: normal; margin-top: 1px;">السعر بدون ضريبة</div>
            </th>
            <th style="text-align: center; width: 10%;">
                <div>TAX Rate</div>
                <div style="font-size: 7px; font-weight: normal; margin-top: 1px;">نسبة الضريبة</div>
            </th>
            <th style="text-align: center; width: 12%;">
                <div>TAX Price</div>
                <div style="font-size: 7px; font-weight: normal; margin-top: 1px;">سعر الضريبة</div>
            </th>
            <th style="text-align: left; width: 16%;">
                <div>Price Incl TAX</div>
                <div style="font-size: 7px; font-weight: normal; margin-top: 1px;">السعر شامل الضريبة</div>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->details as $key => $details)
            @php($productDetails = $details?->product ?? json_decode($details->product_details))
            @php($tax_rate = $productDetails->tax ?? 15)
            <tr>
                <td style="text-align: right;">
                    <span style="font-weight: bold;">{{ $productDetails->name }}</span>
                    @if($details['variant'])
                        <div style="font-size: 8px; color: #7F8185; margin-top: 2px;">{{ translate('variation') }} : {{ $details['variant'] }}</div>
                    @endif
                    @if(isset($productDetails->code) && $productDetails->code)
                        <div style="font-size: 8px; color: #7F8185; margin-top: 1px;">{{ translate('product_code') }}: {{ $productDetails->code }}</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ $details->qty }}</td>
                <td style="text-align: center;">{{ webCurrencyConverter(amount: ($details['price'] / (1 + ($tax_rate / 100))) * $details['qty']) }}</td>
                <td style="text-align: center;">%{{ $tax_rate }}</td>
                <td style="text-align: center;">{{ webCurrencyConverter(amount: $details['tax']) }}</td>
                <td style="text-align: left; font-weight: bold;">{{ webCurrencyConverter(amount: $details['price'] * $details['qty']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Totals + QR — RTL: totals on left side, QR on right side -->
    <table class="bottom-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <table class="totals-table">
                    <tr class="totals-row">
                        <td class="totals-label-en">Subtotal</td>
                        <td class="totals-value">{{ webCurrencyConverter(amount: $orderTotalPriceSummary['itemPrice']) }}</td>
                        <td class="totals-label-ar">المجموع</td>
                    </tr>
                    @php($total_discount = $orderTotalPriceSummary['extraDiscount'] + $orderTotalPriceSummary['couponDiscount'] + $orderTotalPriceSummary['referAndEarnDiscount'] + $orderTotalPriceSummary['itemDiscount'])
                    @if($total_discount > 0)
                        <tr class="totals-row">
                            <td class="totals-label-en">Discount</td>
                            <td class="totals-value" style="color: #FF5A36;">-{{ webCurrencyConverter(amount: $total_discount) }}</td>
                            <td class="totals-label-ar">خصم</td>
                        </tr>
                    @endif
                    <tr class="totals-row">
                        <td class="totals-label-en">Shipping</td>
                        <td class="totals-value">
                            @if($order?->is_shipping_free == 1)
                                شحن مجاني
                            @else
                                {{ webCurrencyConverter(amount: $orderTotalPriceSummary['shippingTotal']) }}
                            @endif
                        </td>
                        <td class="totals-label-ar">الشحن</td>
                    </tr>
                    <tr class="totals-row">
                        <td class="totals-label-en">Payment method</td>
                        <td class="totals-value" style="color: #FF5A36;">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                        <td class="totals-label-ar">وسيلة الدفع</td>
                    </tr>
                    <tr class="totals-row" style="border-top: 1px solid #D7DAE0;">
                        <td class="totals-label-en" style="font-weight: bold; padding-top: 8px;">Total</td>
                        <td class="totals-value" style="font-size: 11px; padding-top: 8px;">
                            <div>{{ webCurrencyConverter(amount: $orderTotalPriceSummary['totalAmount']) }}</div>
                            <div style="font-size: 8px; font-weight: normal; color: #7F8185; margin-top: 2px;">(يتضمن {{ webCurrencyConverter(amount: $orderTotalPriceSummary['taxTotal']) }} ضريبة القيمة المضافة)</div>
                        </td>
                        <td class="totals-label-ar" style="font-weight: bold; padding-top: 8px;">الإجمالي</td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div style="display: inline-block; border: 1px solid #EAEAEA; padding: 6px; border-radius: 6px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($zatcaQr ?? '') }}" style="width: 80px; height: 80px; display: block;" alt="QR Code"/>
                </div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
