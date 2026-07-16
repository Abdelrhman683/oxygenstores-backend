<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TabbyWebhookController extends Controller
{
    use Processor;

    private $api_key;
    private $base_url;

    public function __construct()
    {
        $config = $this->payment_config('tabby', 'payment_config');

        $config_values = null;
        if (!is_null($config) && $config->mode == 'live') {
            $config_values  = json_decode($config->live_values);
            $this->base_url = 'https://api.tabby.ai/api/v2';
        } else {
            $config_values  = json_decode($config?->test_values ?? '{}');
            $this->base_url = 'https://api.tabby.ai/api/v2';
        }

        $this->api_key = $config_values->secret_key ?? ($config_values->api_key ?? ($config_values->public_key ?? null));
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Tabby Webhook Received (Oxygen)', [
            'ip'      => $request->ip(),
            'payload' => $payload,
        ]);

        $tabby_payment_id = $payload['id']
            ?? ($payload['payment']['id'] ?? null);

        $status = $payload['status']
            ?? ($payload['payment']['status'] ?? null);

        if (!$tabby_payment_id || strtolower((string)$status) !== 'authorized') {
            Log::info('Tabby Webhook (Oxygen): Ignored', ['status' => $status]);
            return response()->json(['status' => 'ignored'], 200);
        }

        $retrieve = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json',
        ])->get($this->base_url . '/payments/' . $tabby_payment_id);

        if (!$retrieve->successful()) {
            Log::error('Tabby Webhook (Oxygen): Failed to retrieve payment', [
                'tabby_id' => $tabby_payment_id,
                'body'     => $retrieve->body(),
            ]);
            return response()->json(['status' => 'error'], 200);
        }

        $paymentData    = $retrieve->json();
        $verifiedStatus = $paymentData['status'] ?? null;

        if ($verifiedStatus !== 'AUTHORIZED') {
            Log::warning('Tabby Webhook (Oxygen): Status not AUTHORIZED', [
                'status'   => $verifiedStatus,
                'tabby_id' => $tabby_payment_id,
            ]);
            return response()->json(['status' => 'not_authorized'], 200);
        }

        $capture = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json',
        ])->post($this->base_url . '/payments/' . $tabby_payment_id . '/captures', [
            'amount' => $paymentData['amount'] ?? null,
        ]);

        if ($capture->successful()) {
            Log::info('Tabby Webhook (Oxygen): Payment captured successfully', [
                'tabby_id' => $tabby_payment_id,
            ]);
        } else {
            Log::error('Tabby Webhook (Oxygen): Capture failed', [
                'tabby_id' => $tabby_payment_id,
                'body'     => $capture->body(),
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
