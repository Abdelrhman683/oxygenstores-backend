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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobController extends Controller
{
    use Processor;

    private mixed $config_values;
    private PaymentRequest $payment;
    private User $user;
    private string $base_url;

    private array $supportedCountries = [
        'egypt' => 'https://accept.paymob.com',
        'PAK' => 'https://pakistan.paymob.com',
        'KSA' => 'https://ksa.paymob.com',
        'oman' => 'https://oman.paymob.com',
        'UAE' => 'https://uae.paymob.com',
    ];
    private string $defaultBaseUrl = 'https://accept.paymob.com';

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('paymob_accept', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values, true);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values, true);
        }
        $this->payment = $payment;
        $this->user = $user;
        $country = $this->config_values['supported_country'] ?? '';
        if (array_key_exists($country, $this->supportedCountries)) {
            $this->base_url = $this->supportedCountries[$country];
        } else {
            $this->base_url = $this->defaultBaseUrl;
        }
    }

    public function credit(Request $request): JsonResponse|RedirectResponse
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

        session()->put('payment_id', $payment_data->id);

        $additional    = json_decode($payment_data['additional_data'] ?? '{}');
        $business_name = $additional->business_name ?? 'my_business';
        $customer_id   = $additional->customer_id ?? null;
        $is_guest      = (int)($additional->is_guest ?? 0);
        $address_id    = $additional->address_id ?? null;

        $payer            = json_decode($payment_data['payer_information']);
        $shipping_address = $address_id ? ShippingAddress::find($address_id) : null;

        // ─── منتجات السلة أو الطلب الحقيقية ─────────────────────────────
        $items = $this->buildPaymobItems($payment_data, $additional, $business_name);

        $url = $this->base_url . '/v1/intention/';
        $config = $this->config_values;
        $token = $config['secret_key'];

        $integration_id = (int)$config['integration_id'];

        $first_name = !empty($payer->name) ? explode(' ', $payer->name)[0] : 'Customer';
        $last_name  = !empty($payer->name) ? (explode(' ', $payer->name)[1] ?? 'Customer') : 'Customer';

        $data = [
            'amount' => round($payment_data->payment_amount * 100),
            'currency' => $payment_data->currency_code,
            'payment_methods' => [$integration_id],
            'items' => $items,
            'billing_data' => [
                "apartment" => "N/A",
                "email" => !empty($payer->email) ? $payer->email : 'test@gmail.com',
                "floor" => "N/A",
                "first_name" => $first_name,
                "street" => $shipping_address->address ?? "N/A",
                "building" => "N/A",
                "phone_number" => !empty($payer->phone) ? $payer->phone : "0182780000000",
                "shipping_method" => "PKG",
                "postal_code" => $shipping_address->zip ?? "N/A",
                "city" => $shipping_address->city ?? "N/A",
                "country" => $shipping_address->country ?? "SA",
                "last_name" => $last_name,
                "state" => $shipping_address->state ?? "N/A",
            ],
            'special_reference' => time(),
            'customer' => [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => !empty($payer->email) ? $payer->email : 'test@gmail.com',
            ],
            "redirection_url" => route('paymob.callback'),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $token,
                'Content-Type'  => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['client_secret'])) {
                    $secret_key = $result['client_secret'];
                    $publicKey = $config['public_key'];
                    $urlRedirect = $this->base_url . "/unifiedcheckout/?publicKey=$publicKey&clientSecret=$secret_key";
                    return redirect()->to($urlRedirect);
                }
            }

            Log::error('Paymob Intention request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Paymob Intention request exception: ' . $e->getMessage());
        }

        return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        ksort($data);
        $hmac = $data['hmac'] ?? '';
        $array = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $connectedString = '';
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = is_array($this->config_values) ? $this->config_values['hmac'] : $this->config_values->hmac;
        $hased = hash_hmac('sha512', $connectedString, $secret);

        if ($hased == $hmac && $data['success'] === "true") {

            $this->payment::where(['id' => session('payment_id')])->update([
                'payment_method' => 'paymob_accept',
                'is_paid' => 1,
                'transaction_id' => session('payment_id'),
            ]);

            $payment_data = $this->payment::where(['id' => session('payment_id')])->first();

            if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'success');
        }
        $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }

    private function buildPaymobItems(PaymentRequest $payment_data, object|array $additional, string $business_name): array
    {
        $items = [];
        $additional = is_array($additional) ? (object)$additional : $additional;
        $customer_id = $additional->customer_id ?? $payment_data->payer_id ?? null;
        $is_guest    = (int)($additional->is_guest ?? 0);
        $order_ids   = $additional->order_ids ?? null;

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
                    $items[] = [
                        'name'        => (string)$name,
                        'amount'      => (int)round($unit_price * 100),
                        'description' => 'Product ID: ' . ($detail->product_id ?? ''),
                        'quantity'    => $qty,
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
                $items[] = [
                    'name'        => (string)$name,
                    'amount'      => (int)round($unit_price * 100),
                    'description' => 'Product ID: ' . ($detail->product_id ?? ''),
                    'quantity'    => $qty,
                ];
            }
        }

        // 3. Fetch from Cart if items still empty
        if (empty($items) && $customer_id) {
            $cart_items = Cart::where('customer_id', $customer_id)
                ->where('is_guest', $is_guest)
                ->get();

            if ($cart_items->isEmpty()) {
                $cart_items = Cart::where('customer_id', $customer_id)->get();
            }

            foreach ($cart_items as $cart) {
                $unit_price = max(0.01, ($cart->price ?? 0) - ($cart->discount ?? 0));
                $qty        = max(1, (int)($cart->quantity ?? 1));
                $items[] = [
                    'name'        => (string)($cart->name ?? 'Product'),
                    'amount'      => (int)round($unit_price * 100),
                    'description' => 'Product ID: ' . $cart->product_id,
                    'quantity'    => $qty,
                ];
            }
        }

        // 4. Adjust items total to match overall intention amount
        $total_payment_cents = (int)round($payment_data->payment_amount * 100);

        if (empty($items)) {
            $items[] = [
                'name'        => (string)$business_name,
                'amount'      => $total_payment_cents,
                'description' => 'Payable Amount',
                'quantity'    => 1,
            ];
        } else {
            $sum_items_cents = 0;
            foreach ($items as $item) {
                $sum_items_cents += ($item['amount'] * $item['quantity']);
            }

            $diff = $total_payment_cents - $sum_items_cents;
            if ($diff > 0) {
                $items[] = [
                    'name'        => 'Shipping & Extra Fees',
                    'amount'      => $diff,
                    'description' => 'Shipping cost, taxes, or additional fees',
                    'quantity'    => 1,
                ];
            } elseif ($diff < 0) {
                $abs_diff = abs($diff);
                for ($i = count($items) - 1; $i >= 0; $i--) {
                    $item_total = $items[$i]['amount'] * $items[$i]['quantity'];
                    if ($item_total > $abs_diff) {
                        $unit_deduct = (int)floor($abs_diff / $items[$i]['quantity']);
                        if ($unit_deduct > 0 && ($items[$i]['amount'] - $unit_deduct) > 0) {
                            $items[$i]['amount'] -= $unit_deduct;
                            $abs_diff -= ($unit_deduct * $items[$i]['quantity']);
                        } else {
                            $items[$i]['amount'] = max(1, $items[$i]['amount'] - $abs_diff);
                            $abs_diff = 0;
                        }
                    }
                    if ($abs_diff <= 0) break;
                }
            }
        }

        return $items;
    }
}
