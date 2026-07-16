<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Models\Cart;
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

        // ─── منتجات السلة الحقيقية ─────────────────────────────────────
        $items = [];
        if ($customer_id) {
            $cart_items = Cart::where('customer_id', $customer_id)
                ->where('is_guest', $is_guest)
                ->get();

            foreach ($cart_items as $cart) {
                $unit_price = max(0.01, ($cart->price ?? 0) - ($cart->discount ?? 0));
                $items[] = [
                    'name'        => $cart->name ?? 'Product',
                    'amount'      => round($unit_price * 100),
                    'description' => 'Product ID: ' . $cart->product_id,
                    'quantity'    => max(1, (int)($cart->quantity ?? 1)),
                ];
            }
        }

        if (empty($items)) {
            $items[] = [
                'name'        => $business_name,
                'amount'      => round($payment_data->payment_amount * 100),
                'description' => 'payable amount',
                'quantity'    => 1,
            ];
        }

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
}
