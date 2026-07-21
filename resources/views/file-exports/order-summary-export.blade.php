<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
</head>
<body>
<table>
    <thead>
    <tr>
        <th colspan="4" style="font-size: 14px; font-weight: bold; text-align: right;">
            @if(!empty($data['from']) && !empty($data['to']))
                من {{ $data['from'] }} إلى {{ $data['to'] }}
            @else
                الطلبات المخصصة
            @endif
        </th>
    </tr>
    <tr style="background-color: #F2F2F2; font-weight: bold;">
        <th style="font-weight: bold;">اسم المنتج</th>
        <th style="font-weight: bold;">الكمية</th>
        <th style="font-weight: bold;">سعر القطعة</th>
        <th style="font-weight: bold;">السعر الإجمالي</th>
    </tr>
    </thead>
    <tbody>
    @php
        $productsMap = [];
        $totalAllQty = 0;
        $totalAllRevenue = 0;
        $paymentMethodsMap = [];

        $paymentMethodTranslations = [
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'pay_by_wallet' => 'الدفع عبر المحفظة',
            'bank_transfer' => 'حوالة مصرفية',
            'offline_payment' => 'حوالة مصرفية',
            'paymob' => 'Paymob - Card',
            'apple_pay' => 'Paymob - Apple Pay',
            'tamara' => 'Tamara',
            'tabby' => 'تابي: قسّمها على 4',
        ];

        foreach ($data['orders'] as $order) {
            $pm = $order->payment_method ?? 'أخرى';
            $pmName = $paymentMethodTranslations[$pm] ?? $pm;

            if (!isset($paymentMethodsMap[$pmName])) {
                $paymentMethodsMap[$pmName] = [
                    'count' => 0,
                    'amount' => 0,
                ];
            }

            $orderTotal = $order->order_amount ?? 0;
            $orderQtySum = 0;

            foreach ($order->details as $detail) {
                $productName = '';
                if ($detail->product) {
                    $productName = $detail->product->name ?? '';
                } else {
                    $productDetails = is_string($detail->product_details) ? json_decode($detail->product_details, true) : $detail->product_details;
                    $productName = $productDetails['name'] ?? 'منتج غير معروف';
                }

                $unitPrice = $detail->price;
                $qty = $detail->qty;
                $itemTotal = ($unitPrice * $qty) - $detail->discount + $detail->tax;

                if (!isset($productsMap[$productName])) {
                    $productsMap[$productName] = [
                        'qty' => 0,
                        'price' => $unitPrice,
                        'total' => 0,
                    ];
                }

                $productsMap[$productName]['qty'] += $qty;
                $productsMap[$productName]['total'] += $itemTotal;
                $totalAllQty += $qty;
                $totalAllRevenue += $itemTotal;
                $orderQtySum += $qty;
            }

            $paymentMethodsMap[$pmName]['count'] += ($orderQtySum > 0 ? $orderQtySum : 1);
            $paymentMethodsMap[$pmName]['amount'] += $orderTotal;
        }
    @endphp

    @foreach($productsMap as $pName => $pData)
        <tr>
            <td>{{ $pName }}</td>
            <td>{{ $pData['qty'] }}</td>
            <td>{{ number_format($pData['price'], 2, '.', '') }}</td>
            <td>{{ number_format($pData['total'], 2, '.', '') }}</td>
        </tr>
    @endforeach

    <tr style="font-weight: bold; background-color: #E6ECF5;">
        <td style="font-weight: bold;">إجمالي المبيعات</td>
        <td style="font-weight: bold;">{{ $totalAllQty }}</td>
        <td></td>
        <td style="font-weight: bold;">{{ number_format($totalAllRevenue, 2, '.', '') }}</td>
    </tr>

    @foreach($paymentMethodsMap as $pmName => $pmData)
        <tr>
            <td>{{ $pmName }}</td>
            <td>{{ $pmData['count'] }}</td>
            <td></td>
            <td>{{ number_format($pmData['amount'], 2, '.', '') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
