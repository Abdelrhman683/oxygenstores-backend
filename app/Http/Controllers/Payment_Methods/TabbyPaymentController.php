<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Models\Cart;
use App\Models\Order;
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
use Illuminate\Support\Str;

class TabbyPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private PaymentRequest $payment;
    private User $user;
    private $api_key;
    private $merchant_code;
    private $base_url;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('tabby', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
            $this->base_url      = 'https://api.tabby.ai/api/v2';
        } else {
            $this->config_values = json_decode($config?->test_values ?? '{}');
            $this->base_url      = 'https://api.tabby.ai/api/v2';
        }

        if ($config) {
            $this->api_key       = $this->config_values->secret_key ?? ($this->config_values->api_key ?? ($this->config_values->public_key ?? null));
            $this->merchant_code = $this->config_values->merchant_code ?? '';
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
        $address_id         = $additional->address_id ?? null;
        $billing_address_id = $additional->billing_address_id ?? null;

        // ─── عنوان الشحن والفاتورة الحقيقي ─────────────────────────────
        $shipping_address = $address_id ? ShippingAddress::find($address_id) : null;
        $billing_address  = $billing_address_id ? ShippingAddress::find($billing_address_id) : null;

        // ─── منتجات السلة أو الطلب الحقيقية ─────────────────────────────
        $items = $this->buildCartItems($customer_id, $is_guest, $payment_data, $additional);

        // ─── هاتف وبريد المشترِي بتنسيق متوافق مع تابي ──────────────────────────
        $raw_phone = $shipping_address->phone ?? ($billing_address->phone ?? ($payer->phone ?? null));
        $raw_email = $shipping_address->email ?? ($billing_address->email ?? ($payer->email ?? null));
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
        $buyer_phone = $this->formatTabbyPhone($raw_phone);
        $phone_digits = preg_replace('/[^\d]/', '', $buyer_phone);
        $fallback_email = !empty($phone_digits) ? 'c' . $phone_digits . '@customer.store' : 'customer@store.com';
        $buyer_email = filter_var($raw_email, FILTER_VALIDATE_EMAIL) ? $raw_email : $fallback_email;

        // ─── سجل العميل ────────────────────────────────────────────────
        $loyalty_level    = 0;
        $registered_since = now()->utc()->toIso8601ZuluString();
        $order_history    = [];

        if ($customer_id && !$is_guest) {
            $customer = User::find($customer_id);
            if ($customer) {
                $registered_since = $customer->created_at->utc()->toIso8601ZuluString();
                $loyalty_level    = Order::where('customer_id', $customer_id)
                    ->where('payment_status', 'paid')
                    ->count();
            }

            $past_orders = Order::where('customer_id', $customer_id)
                ->latest()
                ->take(10)
                ->get();

            foreach ($past_orders as $order) {
                $order_history[] = [
                    'purchased_at'   => $order->created_at->utc()->toIso8601ZuluString(),
                    'amount'         => number_format($order->order_amount, 2, '.', ''),
                    'payment_method' => $this->mapPaymentMethod($order->payment_method ?? ''),
                    'status'         => $this->mapOrderStatus($order->payment_status ?? ''),
                ];
            }
        }

        $currency = strtoupper($payment_data->currency_code);
        $amount   = round($payment_data->payment_amount, 2);

        $payload = [
            'payment' => [
                'amount'      => (string)number_format($amount, 2, '.', ''),
                'currency'    => $currency,
                'description' => 'Payment for order #' . $payment_data->id,
                'buyer'       => [
                    'phone' => $buyer_phone,
                    'email' => $buyer_email,
                    'name'  => !empty($payer->name) ? $payer->name : ($shipping_address->contact_person_name ?? 'Customer'),
                    'dob'   => '1990-01-01',
                ],
                'buyer_history' => [
                    'registered_since' => $registered_since,
                    'loyalty_level'    => $loyalty_level,
                ],
                'order' => [
                    'tax_amount'      => '0.00',
                    'shipping_amount' => '0.00',
                    'discount_amount' => '0.00',
                    'updated_at'      => now()->utc()->toIso8601ZuluString(),
                    'reference_id'    => $payment_data->id,
                    'items'           => $items,
                ],
                'shipping_address' => [
                    'city'    => $shipping_address->city    ?? ($payer->city    ?? 'Riyadh'),
                    'address' => $shipping_address->address ?? ($payer->address ?? 'N/A'),
                    'zip'     => $shipping_address->zip     ?? ($payer->zip     ?? '11111'),
                    'phone'   => $buyer_phone,
                ],
                'order_history' => $order_history,
            ],
            'merchant_code' => !empty($this->merchant_code) ? $this->merchant_code : 'SA',
            'lang'          => 'ar',
            'merchant_urls' => [
                'success' => route('tabby.callback', ['payment_id' => $payment_data->id, 'status' => 'success']),
                'cancel'  => route('tabby.callback', ['payment_id' => $payment_data->id, 'status' => 'cancel']),
                'failure' => route('tabby.callback', ['payment_id' => $payment_data->id, 'status' => 'failure']),
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type'  => 'application/json'
            ])->post($this->base_url . '/checkout', $payload);

            $result = $response->json();

            Log::info('Tabby Create Session Response (Oxygen)', [
                'status_code' => $response->status(),
                'status'      => $result['status'] ?? null,
            ]);

            if ($response->successful() && isset($result['status']) && $result['status'] === 'created') {
                
                // ─── Background Pre-Scoring Check ─────────────────────────
                $available = $result['configuration']['available_products'] ?? [];
                $has_installments = !empty($available['installments'] ?? []);
                $has_pay_in_full  = !empty($available['pay_in_full'] ?? []);

                if (!$has_installments && !$has_pay_in_full) {
                    Log::warning('Tabby (Oxygen): Pre-scoring rejected - no available products', ['customer_id' => $customer_id]);
                    $payment_data_fail = $this->payment::where(['id' => $request['payment_id']])->first();
                    return $this->payment_response($payment_data_fail, 'fail');
                }

                $webUrl = null;
                foreach ($available['installments'] ?? [] as $product) {
                    if (isset($product['web_url'])) {
                        $webUrl = $product['web_url'];
                        break;
                    }
                }
                if (!$webUrl) {
                    $webUrl = $available['pay_in_full']['web_url'] ?? null;
                }

                if ($webUrl) {
                    return redirect()->away($webUrl);
                }
            }

            Log::error('Tabby request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Tabby request exception: ' . $e->getMessage());
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
            
            // ─── Verify & Capture ──────────────────────────────────────────
            $tabby_payment_id = $request->query('payment_id');
            if ($tabby_payment_id && $tabby_payment_id !== $payment_id) {
                try {
                    $this->verifyAndCapture($tabby_payment_id);
                } catch (\Exception $e) {
                    Log::error('Tabby Callback Capture Error (Oxygen): ' . $e->getMessage());
                }
            }

            $this->payment::where(['id' => $payment_id])->update([
                'payment_method' => 'tabby',
                'is_paid'        => 1,
                'transaction_id' => $tabby_payment_id ?? $payment_id,
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

    public function verifyAndCapture(string $tabbyPaymentId): bool
    {
        $retrieve = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json',
        ])->get($this->base_url . '/payments/' . $tabbyPaymentId);

        if (!$retrieve->successful()) {
            Log::error('Tabby (Oxygen): Retrieve failed', ['id' => $tabbyPaymentId, 'body' => $retrieve->body()]);
            return false;
        }

        $paymentData = $retrieve->json();

        if (($paymentData['status'] ?? null) !== 'AUTHORIZED') {
            Log::warning('Tabby (Oxygen): Not AUTHORIZED', ['status' => $paymentData['status'] ?? null, 'id' => $tabbyPaymentId]);
            return false;
        }

        $capture = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json',
        ])->post($this->base_url . '/payments/' . $tabbyPaymentId . '/captures', [
            'amount' => $paymentData['amount'] ?? null,
        ]);

        if (!$capture->successful()) {
            Log::error('Tabby (Oxygen): Capture failed', ['id' => $tabbyPaymentId, 'body' => $capture->body()]);
            return false;
        }

        Log::info('Tabby (Oxygen): Payment captured', ['id' => $tabbyPaymentId]);
        return true;
    }

    // ─── Helper Methods ────────────────────────────────────────────────

    private function formatTabbyPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
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

    private function buildCartItems($customer_id, int $is_guest, $payment_data, $additional = null): array
    {
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
                    $title        = $product_info['name'] ?? ($detail->product_id ? 'Product #' . $detail->product_id : 'Product');
                    $unit_price   = max(0.01, ($detail->price ?? 0) - ($detail->discount ?? 0));
                    $qty          = max(1, (int)($detail->qty ?? 1));
                    $thumbnail    = $product_info['thumbnail'] ?? null;
                    $image_url    = $thumbnail
                        ? asset('storage/app/public/product/' . $thumbnail)
                        : asset('public/assets/back-end/img/logo.png');
                    $product_url  = url('/product/' . ($product_info['slug'] ?? $detail->product_id));

                    $items[] = [
                        'title'           => (string)$title,
                        'quantity'        => $qty,
                        'unit_price'      => number_format($unit_price, 2, '.', ''),
                        'discount_amount' => '0.00',
                        'reference_id'    => (string)($detail->product_id ?? $detail->id),
                        'image_url'       => $image_url,
                        'product_url'     => $product_url,
                        'category'        => 'General',
                    ];
                }
            }
        }

        // 2. Fetch from OrderDetail if attribute is order
        if (empty($items) && ($payment_data->attribute ?? '') === 'order' && !empty($payment_data->attribute_id)) {
            $order_details = OrderDetail::where('order_id', $payment_data->attribute_id)->get();
            foreach ($order_details as $detail) {
                $product_info = json_decode($detail->product_details ?? '{}', true);
                $title        = $product_info['name'] ?? ($detail->product_id ? 'Product #' . $detail->product_id : 'Product');
                $unit_price   = max(0.01, ($detail->price ?? 0) - ($detail->discount ?? 0));
                $qty          = max(1, (int)($detail->qty ?? 1));
                $thumbnail    = $product_info['thumbnail'] ?? null;
                $image_url    = $thumbnail
                    ? asset('storage/app/public/product/' . $thumbnail)
                    : asset('public/assets/back-end/img/logo.png');
                $product_url  = url('/product/' . ($product_info['slug'] ?? $detail->product_id));

                $items[] = [
                    'title'           => (string)$title,
                    'quantity'        => $qty,
                    'unit_price'      => number_format($unit_price, 2, '.', ''),
                    'discount_amount' => '0.00',
                    'reference_id'    => (string)($detail->product_id ?? $detail->id),
                    'image_url'       => $image_url,
                    'product_url'     => $product_url,
                    'category'        => 'General',
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
                $product_url = url('/product/' . ($cart->slug ?? $cart->product_id));
                $image_url   = $cart->thumbnail
                    ? asset('storage/app/public/product/' . $cart->thumbnail)
                    : asset('public/assets/back-end/img/logo.png');

                $items[] = [
                    'title'           => (string)($cart->name ?? 'Product'),
                    'quantity'        => max(1, (int)($cart->quantity ?? 1)),
                    'unit_price'      => number_format($unit_price, 2, '.', ''),
                    'discount_amount' => '0.00',
                    'reference_id'    => (string)$cart->product_id,
                    'image_url'       => $image_url,
                    'product_url'     => $product_url,
                    'category'        => 'General',
                ];
            }
        }

        if (empty($items)) {
            return $this->fallbackItem($payment_data);
        }

        return $items;
    }

    private function fallbackItem($payment_data): array
    {
        return [[
            'title'           => 'Order',
            'quantity'        => 1,
            'unit_price'      => number_format($payment_data->payment_amount, 2, '.', ''),
            'discount_amount' => '0.00',
            'reference_id'    => $payment_data->id,
            'image_url'       => asset('public/assets/back-end/img/logo.png'),
            'product_url'     => url('/'),
            'category'        => 'General',
        ]];
    }

    private function mapPaymentMethod(string $method): string
    {
        return match ($method) {
            'cash_on_delivery' => 'cod',
            'tabby'            => 'tabby',
            'tamara'           => 'tamara',
            default            => 'card',
        };
    }

    private function mapOrderStatus(string $status): string
    {
        return match ($status) {
            'paid'   => 'complete',
            default  => 'processing',
        };
    }
}
