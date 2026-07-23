<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
</head>
<body>
<table>
    <thead>
    <tr>
        <th colspan="15" style="font-size: 14px; font-weight: bold; text-align: right;">
            @if(!empty($data['from']) && !empty($data['to']))
                من {{ $data['from'] }} إلى {{ $data['to'] }}
            @else
                جميع الطلبات
            @endif
        </th>
    </tr>
    <tr style="background-color: #F2F2F2; font-weight: bold;">
        <th style="font-weight: bold;">رقم الطلب</th>
        <th style="font-weight: bold;">تاريخ الطلب</th>
        <th style="font-weight: bold;">رقم المعاملة</th>
        <th style="font-weight: bold;">طريقة الدفع</th>
        <th style="font-weight: bold;">اسم المنتج</th>
        <th style="font-weight: bold;">الماركة</th>
        <th style="font-weight: bold;">سعر القطعة</th>
        <th style="font-weight: bold;">الكمية</th>
        <th style="font-weight: bold;">الكوبونات</th>
        <th style="font-weight: bold;">الخصومات</th>
        <th style="font-weight: bold;">الإجمالي قبل الضريبة</th>
        <th style="font-weight: bold;">سعر الضريبة</th>
        <th style="font-weight: bold;">الإجمالي بعد الضريبة</th>
        <th style="font-weight: bold;">حالة الطلب</th>
        <th style="font-weight: bold;">المدينة</th>
    </tr>
    </thead>
    <tbody>
    @php
        $statusTranslations = [
            'pending' => 'قيد الإنتظار',
            'confirmed' => 'تم التأكيد',
            'processing' => 'قيد التجهيز',
            'out_for_delivery' => 'جاري التوصيل',
            'delivered' => 'مُكتمل',
            'canceled' => 'ملغي',
            'returned' => 'مسترجع',
            'failed' => 'فشل التوصيل',
        ];

        $statusStyles = [
            'pending' => 'background-color: #e8f4fd; color: #0284c7; font-weight: bold; text-align: center;',
            'confirmed' => 'background-color: #ecfdf5; color: #059669; font-weight: bold; text-align: center;',
            'processing' => 'background-color: #fff8f0; color: #ea580c; font-weight: bold; text-align: center;',
            'out_for_delivery' => 'background-color: #f8fafc; color: #475569; font-weight: bold; text-align: center;',
            'delivered' => 'background-color: #f0fdf4; color: #16a34a; font-weight: bold; text-align: center;',
            'canceled' => 'background-color: #fef2f2; color: #dc2626; font-weight: bold; text-align: center;',
            'returned' => 'background-color: #fef2f2; color: #dc2626; font-weight: bold; text-align: center;',
            'failed' => 'background-color: #fef2f2; color: #dc2626; font-weight: bold; text-align: center;',
            'on_hold' => 'background-color: #f8fafc; color: #475569; font-weight: bold; text-align: center;',
        ];

        $paymentMethodTranslations = [
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'pay_by_wallet' => 'الدفع عبر المحفظة',
            'bank_transfer' => 'حوالة مصرفية',
            'offline_payment' => 'حوالة مصرفية',
            'paymob' => 'Paymob - Card',
            'apple_pay' => 'Paymob - Apple Pay',
            'tamara' => 'تابي / تمارا',
            'tabby' => 'تابي: قسّمها على 4',
        ];
    @endphp

    @foreach($data['orders'] as $order)
        @php
            $details = $order->details;
            $orderDate = date('Y/m/d', strtotime($order->created_at));
            $transactionRef = $order->transaction_ref ?? $order->id;
            
            $paymentMethod = $order->payment_method;
            if (isset($paymentMethodTranslations[$paymentMethod])) {
                $paymentMethod = $paymentMethodTranslations[$paymentMethod];
            }

            $statusArabic = $statusTranslations[$order->order_status] ?? $order->order_status;
            $currentStatusStyle = $statusStyles[$order->order_status] ?? 'text-align: center;';
            
            $city = '';
            if ($order->shippingAddress) {
                $city = $order->shippingAddress->city ?? $order->shippingAddress->address ?? '';
            } elseif ($order->billingAddress) {
                $city = $order->billingAddress->city ?? $order->billingAddress->address ?? '';
            }
        @endphp

        @if($details->count() > 0)
            @foreach($details as $index => $detail)
                @php
                    $productName = '';
                    $brandName = '';
                    if ($detail->product) {
                        $productName = $detail->product->name ?? '';
                        $brandName = $detail->product->brand->name ?? '';
                    } else {
                        $productDetails = is_string($detail->product_details) ? json_decode($detail->product_details, true) : $detail->product_details;
                        $productName = $productDetails['name'] ?? '';
                    }

                    $unitPrice = $detail->price;
                    $qty = $detail->qty;
                    $discount = $detail->discount;
                    $tax = $detail->tax;

                    $subtotalExclTax = ($unitPrice * $qty) - $discount;
                    $totalInclTax = $subtotalExclTax + $tax;
                @endphp
                <tr>
                    @if($index === 0)
                        <td>{{ $order->id }}</td>
                        <td>{{ $orderDate }}</td>
                        <td>{{ $transactionRef }}</td>
                        <td>{{ $paymentMethod }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif

                    <td>{{ $productName }}</td>
                    <td>{{ $brandName }}</td>
                    <td>{{ number_format($unitPrice, 2, '.', '') }}</td>
                    <td>{{ $qty }}</td>

                    @if($index === 0)
                        <td>{{ $order->coupon_code ?? '' }}</td>
                        <td>{{ number_format($order->discount_amount ?? 0, 2, '.', '') }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif

                    <td>{{ number_format($subtotalExclTax, 2, '.', '') }}</td>
                    <td>{{ number_format($tax, 2, '.', '') }}</td>
                    <td>{{ number_format($totalInclTax, 2, '.', '') }}</td>

                    @if($index === 0)
                        <td style="{{ $currentStatusStyle }}">{{ $statusArabic }}</td>
                        <td>{{ $city }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $orderDate }}</td>
                <td>{{ $transactionRef }}</td>
                <td>{{ $paymentMethod }}</td>
                <td>-</td>
                <td>-</td>
                <td>0.00</td>
                <td>0</td>
                <td>{{ $order->coupon_code ?? '' }}</td>
                <td>{{ number_format($order->discount_amount ?? 0, 2, '.', '') }}</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>{{ number_format($order->order_amount, 2, '.', '') }}</td>
                <td style="{{ $currentStatusStyle }}">{{ $statusArabic }}</td>
                <td>{{ $city }}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
</body>
</html>
