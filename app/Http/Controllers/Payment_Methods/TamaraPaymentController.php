<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\PaymentRequest;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TamaraPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private PaymentRequest $payment;
    private User $user;
    private $api_token;
    private $base_url;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('tamara', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
            $this->base_url      = $this->config_values->base_url ?? 'https://api.tamara.co';
        } else {
            $this->config_values = json_decode($config?->test_values ?? '{}');
            $this->base_url      = $this->config_values->base_url ?? 'https://api-sandbox.tamara.co';
        }

        if ($config && isset($this->config_values)) {
            $this->api_token = $this->config_values->api_token ?? ($this->config_values->api_key ?? null);
        }

        $this->payment = $payment;
        $this->user    = $user;
    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $payment_data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($payment_data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        $payer      = json_decode($payment_data['payer_information']);
        $additional = json_decode($payment_data['additional_data'] ?? '{}');

        $customer_id = $additional->customer_id ?? null;
        $is_guest    = (int)($additional->is_guest ?? 0);
        $address_id  = $additional->address_id ?? null;

        // ─── عنوان الشحن الحقيقي ───────────────────────────────────────
        $shipping_address = $address_id ? ShippingAddress::find($address_id) : null;

        // ─── منتجات السلة أو الطلب الحقيقية ─────────────────────────────
        $items = $this->buildTamaraItems($customer_id, $is_guest, $payment_data, $additional);

        $currency = strtoupper($payment_data->currency_code);
        $amount   = round($payment_data->payment_amount, 2);

        $buyer_name  = $payer->name ?? 'Customer';
        $raw_phone   = $shipping_address->phone ?? ($payer->phone ?? null);
        $raw_email   = $shipping_address->email ?? ($payer->email ?? null);
        if ($customer_id) {
            $customer_obj = User::find($customer_id);
            if ($customer_obj) {
                if (empty($raw_phone)) {
                    $raw_phone = $customer_obj->phone ?? null;
                }
                if (empty($raw_email)) {
                    $raw_email = $customer_obj->email ?? null;
                }
            }
        }
        $buyer_phone    = $this->formatTamaraPhone($raw_phone);
        $phone_digits   = preg_replace('/[^\d]/', '', $buyer_phone);
        $fallback_email = !empty($phone_digits) ? 'c' . $phone_digits . '@customer.store' : 'buyer@tamara.co';
        $buyer_email    = filter_var($raw_email, FILTER_VALIDATE_EMAIL) ? $raw_email : $fallback_email;

        $ship_line1   = $shipping_address->address ?? ($payer->address ?? 'N/A');
        $ship_city    = $shipping_address->city    ?? ($payer->city    ?? 'Riyadh');
        $ship_country = $shipping_address->country ?? 'SA';
        $ship_phone   = $shipping_address->phone   ?? $buyer_phone;

        $data = [
            'order_reference_id' => $payment_data->id,
            'total_amount'       => [
                'amount'   => (float)$amount,
                'currency' => $currency,
            ],
            'description'        => 'Payment for order #' . $payment_data->id,
            'country_code'       => 'SA',
            'payment_type'       => 'PAY_BY_INSTALMENTS',
            'consumer'           => [
                'first_name'   => $buyer_name,
                'last_name'    => $buyer_name,
                'phone_number' => $buyer_phone,
                'email'        => $buyer_email,
            ],
            'shipping_address' => [
                'first_name'   => $buyer_name,
                'last_name'    => $buyer_name,
                'line1'        => $ship_line1,
                'city'         => $ship_city,
                'country_code' => $ship_country,
                'phone_number' => $ship_phone,
            ],
            'merchant_url'       => [
                'success'      => route('tamara.callback', ['payment_id' => $payment_data->id, 'status' => 'success']),
                'failure'      => route('tamara.callback', ['payment_id' => $payment_data->id, 'status' => 'failure']),
                'cancel'       => route('tamara.callback', ['payment_id' => $payment_data->id, 'status' => 'cancel']),
                'notification' => route('tamara.callback', ['payment_id' => $payment_data->id, 'status' => 'notification']),
            ],
            'shipping_amount'    => [
                'amount'   => 0.0,
                'currency' => $currency,
            ],
            'tax_amount'         => [
                'amount'   => 0.0,
                'currency' => $currency,
            ],
            'items'              => $items,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_token,
                'Content-Type'  => 'application/json'
            ])->post($this->base_url . '/checkout', $data);

            if ($response->successful()) {
                $resData = $response->json();
                $webUrl  = $resData['checkout_url'] ?? null;

                if ($webUrl) {
                    return redirect()->away($webUrl);
                }
            }

            Log::error('Tamara request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Tamara request exception: ' . $e->getMessage());
        }

        $payment_data_fail = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data_fail) && function_exists($payment_data_fail->failure_hook)) {
            call_user_func($payment_data_fail->failure_hook, $payment_data_fail);
        }
        return $this->payment_response($payment_data_fail, 'fail');
    }

    public function callback(Request $request)
    {
        $payment_id = $request->get('payment_id');
        $status     = $request->get('status');

        $payment_data = $this->payment::where(['id' => $payment_id])->first();

        if (isset($payment_data) && $status === 'success') {
            $this->payment::where(['id' => $payment_id])->update([
                'payment_method' => 'tamara',
                'is_paid'        => 1,
                'transaction_id' => $request->get('payment_id'),
            ]);

            $payment_data = $this->payment::where(['id' => $payment_id])->first();
            if (function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'success');
        }

        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        if ($status === 'cancel') {
            session()->flash('payment_cancel_reason', 'cancelled');
        } else {
            session()->flash('payment_cancel_reason', 'rejected');
        }

        return $this->payment_response($payment_data, 'fail');
    }

    // ─── Helper Methods ────────────────────────────────────────────────

    private function formatTamaraPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '0500000000';
        }
        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));
        if (str_starts_with($cleaned, '00966')) {
            return '+' . substr($cleaned, 2);
        }
        if (str_starts_with($cleaned, '966')) {
            return '+' . $cleaned;
        }
        if (str_starts_with($cleaned, '05')) {
            return '+966' . substr($cleaned, 1);
        }
        if (str_starts_with($cleaned, '5') && strlen($cleaned) == 9) {
            return '+966' . $cleaned;
        }
        if (!str_starts_with($cleaned, '+') && strlen($cleaned) > 0) {
            return '+' . $cleaned;
        }
        return $cleaned;
    }

    private function buildTamaraItems($customer_id, int $is_guest, $payment_data, $additional = null): array
    {
        $currency = strtoupper($payment_data->currency_code);
        $items = [];
        $additional = is_array($additional) ? (object)$additional : $additional;
        $order_ids  = $additional->order_ids ?? null;

        // 1. Fetch from OrderDetail if order_ids exist in additional data
        if (!empty($order_ids)) {
            $order_ids_array = [];
            if (is_array($order_ids)) {
                $order_ids_array = $order_ids;
            } elseif (is_string($order_ids)) {
                $decoded = json_decode($order_ids, true);
                $order_ids_array = is_array($decoded) ? $decoded : [$order_ids];
            } elseif (is_numeric($order_ids)) {
                $order_ids_array = [(int)$order_ids];
            }

            if (!empty($order_ids_array)) {
                $order_details = OrderDetail::whereIn('order_id', $order_ids_array)->get();
                foreach ($order_details as $detail) {
                    $product_info = json_decode($detail->product_details ?? '{}', true);
                    $name         = $product_info['name'] ?? ($detail->product_id ? 'Product #' . $detail->product_id : 'Product');
                    $unit_price   = max(0.01, ($detail->price ?? 0) - ($detail->discount ?? 0));
                    $qty          = max(1, (int)($detail->qty ?? 1));
                    $total_price  = $unit_price * $qty;

                    $items[] = [
                        'reference_id'    => (string)($detail->product_id ?? $detail->id),
                        'type'            => 'Physical',
                        'name'            => (string)$name,
                        'sku'             => 'SKU-' . ($detail->product_id ?? $detail->id),
                        'quantity'        => $qty,
                        'unit_price'      => ['amount' => (float)number_format($unit_price, 2, '.', ''),  'currency' => $currency],
                        'total_amount'    => ['amount' => (float)number_format($total_price, 2, '.', ''), 'currency' => $currency],
                        'discount_amount' => ['amount' => 0.0, 'currency' => $currency],
                        'tax_amount'      => ['amount' => 0.0, 'currency' => $currency],
                    ];
                }
            }
        }

        // 2. Fetch from OrderDetail if attribute is order
        if (empty($items) && ($payment_data->attribute ?? '') === 'order' && !empty($payment_data->attribute_id)) {
            $order_details = OrderDetail::where('order_id', $payment_data->attribute_id)->get();
            foreach ($order_details as $detail) {
                $product_info = json_decode($detail->product_details ?? '{}', true);
                $name         = $product_info['name'] ?? ($detail->product_id ? 'Product #' . $detail->product_id : 'Product');
                $unit_price   = max(0.01, ($detail->price ?? 0) - ($detail->discount ?? 0));
                $qty          = max(1, (int)($detail->qty ?? 1));
                $total_price  = $unit_price * $qty;

                $items[] = [
                    'reference_id'    => (string)($detail->product_id ?? $detail->id),
                    'type'            => 'Physical',
                    'name'            => (string)$name,
                    'sku'             => 'SKU-' . ($detail->product_id ?? $detail->id),
                    'quantity'        => $qty,
                    'unit_price'      => ['amount' => (float)number_format($unit_price, 2, '.', ''),  'currency' => $currency],
                    'total_amount'    => ['amount' => (float)number_format($total_price, 2, '.', ''), 'currency' => $currency],
                    'discount_amount' => ['amount' => 0.0, 'currency' => $currency],
                    'tax_amount'      => ['amount' => 0.0, 'currency' => $currency],
                ];
            }
        }

        // 3. Fetch from Cart
        $customer_id = $customer_id ?? $payment_data->payer_id ?? null;
        if (empty($items) && $customer_id) {
            $cart_items = Cart::where('customer_id', $customer_id)
                ->where('is_guest', $is_guest)
                ->get();

            if ($cart_items->isEmpty()) {
                $cart_items = Cart::where('customer_id', $customer_id)->get();
            }

            foreach ($cart_items as $cart) {
                $unit_price  = max(0.01, ($cart->price ?? 0) - ($cart->discount ?? 0));
                $qty         = max(1, (int)($cart->quantity ?? 1));
                $total_price = $unit_price * $qty;

                $items[] = [
                    'reference_id'    => (string)$cart->product_id,
                    'type'            => 'Physical',
                    'name'            => (string)($cart->name ?? 'Product'),
                    'sku'             => 'SKU-' . $cart->product_id,
                    'quantity'        => $qty,
                    'unit_price'      => ['amount' => (float)number_format($unit_price, 2, '.', ''),  'currency' => $currency],
                    'total_amount'    => ['amount' => (float)number_format($total_price, 2, '.', ''), 'currency' => $currency],
                    'discount_amount' => ['amount' => 0.0, 'currency' => $currency],
                    'tax_amount'      => ['amount' => 0.0, 'currency' => $currency],
                ];
            }
        }

        if (empty($items)) {
            return $this->fallbackTamaraItem($payment_data, $currency);
        }

        return $items;
    }

    private function fallbackTamaraItem($payment_data, string $currency): array
    {
        $amount = (float)number_format($payment_data->payment_amount, 2, '.', '');
        return [[
            'reference_id'    => (string)$payment_data->id,
            'type'            => 'Physical',
            'name'            => 'Order Item',
            'sku'             => 'SKU-0001',
            'quantity'        => 1,
            'unit_price'      => ['amount' => $amount, 'currency' => $currency],
            'total_amount'    => ['amount' => $amount, 'currency' => $currency],
            'discount_amount' => ['amount' => 0.0, 'currency' => $currency],
            'tax_amount'      => ['amount' => 0.0, 'currency' => $currency],
        ]];
    }
}
