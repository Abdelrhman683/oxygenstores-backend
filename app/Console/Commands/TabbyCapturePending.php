<?php

namespace App\Console\Commands;

use App\Traits\Processor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TabbyCapturePending extends Command
{
    use Processor;

    protected $signature   = 'tabby:capture-pending';
    protected $description = 'Capture all pending AUTHORIZED Tabby payments (runs hourly via scheduler)';

    public function handle()
    {
        $config = $this->payment_config('tabby', 'payment_config');

        if (!$config) {
            $this->error('Tabby config not found in database');
            return 1;
        }

        $config_values = ($config->mode == 'live')
            ? json_decode($config->live_values)
            : json_decode($config->test_values ?? '{}');

        $api_key  = $config_values->secret_key ?? ($config_values->api_key ?? null);
        $base_url = 'https://api.tabby.ai/api/v2';

        if (!$api_key) {
            $this->error('Tabby API key not configured');
            return 1;
        }

        $this->info('Fetching AUTHORIZED payments from Tabby...');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ])->get($base_url . '/payments', ['status' => 'authorized']);

            if (!$response->successful()) {
                $this->error('Failed to fetch payments: ' . $response->body());
                Log::error('TabbyCapturePending (Oxygen): Failed to fetch payments', ['body' => $response->body()]);
                return 1;
            }

            $payments = $response->json('payments')
                ?? $response->json('results')
                ?? [];

            if (empty($payments)) {
                $this->info('No authorized payments found.');
                return 0;
            }

            $captured = 0;
            $failed   = 0;

            foreach ($payments as $payment) {
                $tabby_id = $payment['id']     ?? null;
                $status   = $payment['status'] ?? null;

                if (!$tabby_id || strtoupper((string)$status) !== 'AUTHORIZED') {
                    continue;
                }

                $capture = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                ])->post($base_url . '/payments/' . $tabby_id . '/captures', [
                    'amount' => $payment['amount'] ?? null,
                ]);

                if ($capture->successful()) {
                    $this->info("✅ Captured: {$tabby_id}");
                    Log::info('TabbyCapturePending (Oxygen): Captured', ['tabby_id' => $tabby_id]);
                    $captured++;
                } else {
                    $this->warn("⚠️ Failed: {$tabby_id} → " . $capture->body());
                    Log::warning('TabbyCapturePending (Oxygen): Capture failed', [
                        'tabby_id' => $tabby_id,
                        'body'     => $capture->body(),
                    ]);
                    $failed++;
                }
            }

            $this->info("Done! Captured: {$captured} | Failed: {$failed}");
            return 0;

        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            Log::error('TabbyCapturePending (Oxygen): Exception', ['message' => $e->getMessage()]);
            return 1;
        }
    }
}
