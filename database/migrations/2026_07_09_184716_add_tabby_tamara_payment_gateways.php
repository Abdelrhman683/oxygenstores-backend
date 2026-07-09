<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $gateways = [
            [
                'key_name'        => 'tabby',
                'live_values'     => json_encode([
                    'gateway'       => 'tabby',
                    'mode'          => 'live',
                    'status'        => '0',
                    'public_key'    => '',
                    'secret_key'    => '',
                    'merchant_code' => '',
                ]),
                'test_values'     => json_encode([
                    'gateway'       => 'tabby',
                    'mode'          => 'test',
                    'status'        => '0',
                    'public_key'    => '',
                    'secret_key'    => '',
                    'merchant_code' => '',
                ]),
                'settings_type'   => 'payment_config',
                'mode'            => 'test',
                'is_active'       => 0,
                'additional_data' => json_encode([
                    'gateway_title' => 'Tabby',
                    'gateway_image' => '',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'key_name'        => 'tamara',
                'live_values'     => json_encode([
                    'gateway'            => 'tamara',
                    'mode'               => 'live',
                    'status'             => '0',
                    'api_token'          => '',
                    'notification_token' => '',
                    'public_key'         => '',
                ]),
                'test_values'     => json_encode([
                    'gateway'            => 'tamara',
                    'mode'               => 'test',
                    'status'             => '0',
                    'api_token'          => '',
                    'notification_token' => '',
                    'public_key'         => '',
                ]),
                'settings_type'   => 'payment_config',
                'mode'            => 'test',
                'is_active'       => 0,
                'additional_data' => json_encode([
                    'gateway_title' => 'Tamara',
                    'gateway_image' => '',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ];

        foreach ($gateways as $gateway) {
            $exists = DB::table('addon_settings')
                ->where('key_name', $gateway['key_name'])
                ->where('settings_type', 'payment_config')
                ->exists();
            if (!$exists) {
                $gateway['id'] = \Illuminate\Support\Str::uuid()->toString();
                DB::table('addon_settings')->insert($gateway);
            } else {
                DB::table('addon_settings')
                    ->where('key_name', $gateway['key_name'])
                    ->where('settings_type', 'payment_config')
                    ->update($gateway);
            }
        }
    }

    public function down(): void
    {
        DB::table('addon_settings')
            ->where('settings_type', 'payment_config')
            ->whereIn('key_name', ['tabby', 'tamara'])
            ->delete();
    }
};
