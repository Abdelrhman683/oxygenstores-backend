<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Validation\ValidationException;

class RecaptchaService
{
    public static function verify(string $token, ?string $action = null): bool
    {
        $secretKey = getWebConfig(name: 'recaptcha')['secret_key'];

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        $data = $response->json();
        if (!($data['success'] ?? false)) {
            ToastMagic::error(translate('ReCAPTCHA_Failed'));
            return false;
        }

        if (($data['score'] ?? 0) < 0.5) {
            ToastMagic::error(translate('ReCAPTCHA_Score_Too_Low_Please_Try_Again'));
            return false;
        }
        if ($action !== null && ($data['action'] ?? '') !== $action) {
            ToastMagic::error(translate('ReCAPTCHA_Action_Invalid'));
            return false;
        }

        return true;
    }

    public static function verificationStatus(object|array $request, string $session, ?string $action = 'default', ?bool $firebase = false): array
    {
        return [
            'status' => true,
            'message' => translate('ReCAPTCHA_verification_success.'),
        ];
    }
}


?>
