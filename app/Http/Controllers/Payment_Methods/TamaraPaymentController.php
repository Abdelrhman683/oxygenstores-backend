<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Models\PaymentRequest;
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
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('tamara', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }
        $this->payment = $payment;
        $this->user = $user;
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

        $payer = json_decode($payment_data['payer_information']);
        $currency = strtoupper($payment_data->currency_code);
        $amount = round($payment_data->payment_amount, 2);

        $isLive = (!is_null($this->config_values) && isset($this->config_values->mode) && $this->config_values->mode === 'live');
        $url = $isLive ? 'https://api.tamara.co/checkout' : 'https://api-sandbox.tamara.co/checkout';

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
                'first_name'   => $payer->name ?? 'Customer',
                'last_name'    => $payer->name ?? 'Customer',
                'phone_number' => $payer->phone ?? '0000000000',
                'email'        => $payer->email ?? 'buyer@tamara.co',
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
            'items'              => [
                [
                    'name'         => 'Order Payment',
                    'type'         => 'Physical',
                    'reference_id' => $payment_data->id,
                    'sku'          => 'order-payment',
                    'quantity'     => 1,
                    'total_amount' => [
                        'amount'   => (float)$amount,
                        'currency' => $currency,
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . ($this->config_values->api_token ?? ''),
                'Content-Type'  => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                $resData = $response->json();
                $webUrl = $resData['checkout_url'] ?? null;

                if ($webUrl) {
                    return redirect()->away($webUrl);
                }
            }

            Log::error('Tamara request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Tamara request exception: ' . $e->getMessage());
        }

        return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
    }

    public function callback(Request $request)
    {
        $payment_id = $request->get('payment_id');
        $status = $request->get('status');

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
        return $this->payment_response($payment_data, 'fail');
    }
}
