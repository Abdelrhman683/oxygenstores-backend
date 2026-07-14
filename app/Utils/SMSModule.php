<?php

namespace App\Utils;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Modules\Gateways\Traits\SmsGateway;

class SMSModule
{
    public static function sendCentralizedSMS($phone, $token)
    {
        $paymentPublishedStatus = config('get_payment_publish_status') ?? 0;
        Log::info('[OTP] sendCentralizedSMS called', [
            'phone'                  => $phone,
            'payment_published_status' => $paymentPublishedStatus,
            'gateway_module'         => $paymentPublishedStatus == 1 ? 'SmsGateway (Module)' : 'SMSModule::send',
        ]);
        $result = $paymentPublishedStatus == 1 ? SmsGateway::send($phone, $token) : SMSModule::send($phone, $token);
        Log::info('[OTP] sendCentralizedSMS result', ['phone' => $phone, 'result' => $result]);
        return $result;
    }

    public static function send($receiver, $otp): string
    {
        $gateways = ['twilio', 'nexmo', '2factor', 'msg91', 'releans', 'alphanet_sms', 'taqnyat'];
        $gatewayStatus = [];

        foreach ($gateways as $gw) {
            $cfg = self::get_settings($gw);
            $gatewayStatus[$gw] = isset($cfg) ? (int)($cfg['status'] ?? 0) : 'not_configured';
        }

        Log::info('[OTP] SMSModule::send - gateway statuses', [
            'receiver' => $receiver,
            'gateways' => $gatewayStatus,
        ]);

        $config = self::get_settings('twilio');
        if (isset($config) && $config['status'] == 1) {
            return self::twilio($receiver, $otp);
        }

        $config = self::get_settings('nexmo');
        if (isset($config) && $config['status'] == 1) {
            return self::nexmo($receiver, $otp);
        }

        $config = self::get_settings('2factor');
        if (isset($config) && $config['status'] == 1) {
            return self::two_factor($receiver, $otp);
        }

        $config = self::get_settings('msg91');
        if (isset($config) && $config['status'] == 1) {
            return self::msg_91($receiver, $otp);
        }

        $config = self::get_settings('releans');
        if (isset($config) && $config['status'] == 1) {
            return self::releans($receiver, $otp);
        }

        $config = self::get_settings('alphanet_sms');
        if (isset($config) && $config['status'] == 1) {
            return self::alphanet_sms($receiver, $otp);
        }

        $config = self::get_settings('taqnyat');
        if (isset($config) && $config['status'] == 1) {
            return self::taqnyat($receiver, $otp);
        }

        Log::warning('[OTP] SMSModule::send - NO active SMS gateway found, returning not_found', [
            'receiver' => $receiver,
            'gateways' => $gatewayStatus,
        ]);

        return 'not_found';
    }

    public static function twilio($receiver, $otp): string
    {
        $config = self::get_settings('twilio');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $message = str_replace("#OTP#", $otp, $config['otp_template']);
            $sid = $config['sid'];
            $token = $config['token'];
            try {
                $twilio = new Client($sid, $token);
                $twilio->messages
                    ->create($receiver,
                        array(
                            "messagingServiceSid" => $config['messaging_service_sid'],
                            "body" => $message
                        )
                    );
                $response = 'success';
                Log::info('[OTP] Twilio: SMS sent successfully', ['receiver' => $receiver]);
            } catch (Exception $exception) {
                Log::error('[OTP] Twilio: SMS failed', [
                    'receiver' => $receiver,
                    'error'    => $exception->getMessage(),
                ]);
            }
        }
        return $response;
    }

    public static function nexmo($receiver, $otp): string
    {
        $config = self::get_settings('nexmo');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $message = str_replace("#OTP#", $otp, $config['otp_template']);
            try {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://rest.nexmo.com/sms/json');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "from=" . $config['from'] . "&text=" . $message . "&to=" . $receiver . "&api_key=" . $config['api_key'] . "&api_secret=" . $config['api_secret']);

                $headers = array();
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    Log::error('[OTP] Nexmo: curl error', ['receiver' => $receiver, 'error' => curl_error($ch), 'response' => $result]);
                } else {
                    Log::info('[OTP] Nexmo: SMS sent successfully', ['receiver' => $receiver, 'response' => $result]);
                }
                curl_close($ch);
                $response = 'success';
            } catch (Exception $exception) {
                Log::error('[OTP] Nexmo: exception', ['receiver' => $receiver, 'error' => $exception->getMessage()]);
                $response = 'error';
            }
        }
        return $response;
    }

    public static function two_factor($receiver, $otp): string
    {
        $config = self::get_settings('2factor');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $api_key = $config['api_key'];
            $otp_template = $config['otp_template'];
            $apiUrl = "https://2factor.in/API/V1/$api_key/SMS/$receiver/$otp/$otp_template";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $result = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if (!$err) {
                Log::info('[OTP] 2factor: SMS sent successfully', ['receiver' => $receiver, 'response' => $result]);
                $response = 'success';
            } else {
                Log::error('[OTP] 2factor: curl error', ['receiver' => $receiver, 'error' => $err]);
                $response = 'error';
            }
        }
        return $response;
    }

    public static function msg_91($receiver, $otp): string
    {
        $config = self::get_settings('msg91');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $receiver = str_replace("+", "", $receiver);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=" . $config['template_id'] . "&mobile=" . $receiver . "&authkey=" . $config['auth_key'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{\"OTP\":\"$otp\"}",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));
            $result = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) {
                Log::info('[OTP] msg91: SMS sent successfully', ['receiver' => $receiver, 'response' => $result]);
                $response = 'success';
            } else {
                Log::error('[OTP] msg91: curl error', ['receiver' => $receiver, 'error' => $err]);
                $response = 'error';
            }
        }
        return $response;
    }

    public static function releans($receiver, $otp): string
    {
        $config = self::get_settings('releans');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $curl = curl_init();
            $from = $config['from'];
            $to = $receiver;
            $message = str_replace("#OTP#", $otp, $config['otp_template']);

            try {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.releans.com/v2/message",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "sender=$from&mobile=$to&content=$message",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer " . $config['api_key']
                    ),
                ));
                $result = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    Log::error('[OTP] Releans: curl error', ['receiver' => $receiver, 'error' => $err]);
                    $response = 'error';
                } else {
                    Log::info('[OTP] Releans: SMS sent successfully', ['receiver' => $receiver, 'response' => $result]);
                    $response = 'success';
                }
            } catch (Exception $exception) {
                Log::error('[OTP] Releans: exception', ['receiver' => $receiver, 'error' => $exception->getMessage()]);
                $response = 'error';
            }
        }
        return $response;
    }

    public static function alphanet_sms($receiver, $otp): string
    {
        $config = self::get_settings('alphanet_sms');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $receiver = str_replace("+", "", $receiver);
            $message = str_replace("#OTP#", $otp, $config['otp_template']);
            $api_key = $config['api_key'];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('api_key' => $api_key, 'msg' => $message, 'to' => $receiver),
            ));

            $result = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if (!$err) {
                Log::info('[OTP] AlphaNet: SMS sent successfully', ['receiver' => $receiver, 'response' => $result]);
                $response = 'success';
            } else {
                Log::error('[OTP] AlphaNet: curl error', ['receiver' => $receiver, 'error' => $err]);
                $response = 'error';
            }
        }
        return $response;
    }

    public static function taqnyat($receiver, $otp): string
    {
        $config = self::get_settings('taqnyat');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            // Taqnyat requires international format without + (e.g. 9665xxxxxxxx)
            $receiverFormatted = ltrim(str_replace('+', '', $receiver), '0');
            $message = str_replace('#OTP#', $otp, $config['otp_template']);
            $bearer_token = $config['bearer_token'];
            $sender = $config['sender'];

            $data = [
                'sender'     => $sender,
                'recipients' => [$receiverFormatted],
                'body'       => $message,
            ];

            Log::info('[OTP] Taqnyat: sending request', [
                'original_receiver'   => $receiver,
                'formatted_receiver'  => $receiverFormatted,
                'sender'              => $sender,
                'bearer_token_length' => strlen($bearer_token),
                'message'             => $message,
                'payload'             => $data,
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api.taqnyat.sa/v1/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $bearer_token,
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $result   = curl_exec($curl);
            $err      = curl_error($curl);
            $errno    = curl_errno($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            Log::info('[OTP] Taqnyat: raw curl result', [
                'receiver'  => $receiverFormatted,
                'http_code' => $httpCode,
                'curl_errno'=> $errno,
                'curl_error'=> $err,
                'raw_result'=> $result,
            ]);

            if ($result === false || $errno) {
                Log::error('[OTP] Taqnyat: curl failed', [
                    'receiver'   => $receiverFormatted,
                    'curl_errno' => $errno,
                    'curl_error' => $err,
                ]);
            } else {
                $decoded = json_decode($result, true);
                // Taqnyat returns statusCode 201 on success
                if (isset($decoded['statusCode']) && $decoded['statusCode'] == 201) {
                    $response = 'success';
                    Log::info('[OTP] Taqnyat: SMS sent successfully', ['receiver' => $receiverFormatted]);
                } else {
                    Log::error('[OTP] Taqnyat: API returned non-201 status', [
                        'receiver'   => $receiverFormatted,
                        'http_code'  => $httpCode,
                        'statusCode' => $decoded['statusCode'] ?? 'N/A (json_decode failed or missing key)',
                        'response'   => $decoded ?? $result,
                    ]);
                }
            }
        }
        return $response;
    }

    public static function get_settings($name)
    {
        try {
            $config = DB::table('addon_settings')->where('key_name', $name)
                ->where('settings_type', 'sms_config')->first();
        } catch (Exception $exception) {
            return null;
        }

        return (isset($config)) ? json_decode($config->live_values, true) : null;
    }
}
