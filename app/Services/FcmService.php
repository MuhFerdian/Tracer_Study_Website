<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FcmService
{
    public function sendNotification($token, $title, $body, $userId = 1)
    {
        $projectId = config('services.firebase.project_id');

        // ========================
        // SIMPAN KE DATABASE
        // ========================
        DB::table('notifications')->insert([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ========================
        // AUTH FIREBASE (V1)
        // ========================
        $credentialsPath = storage_path("app/firebase/service-account.json");
        $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
        $accessToken = $this->getAccessToken($serviceAccount);

        // ========================
        // KIRIM FCM
        // ========================
        $response = Http::withToken($accessToken)->post(
            "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                ]
            ]
        );
        return $response->json();
    }
    public function sendToUser($userId, $title, $body)
{
    $user = User::find($userId);

    if (!$user || !$user->fcm_token) {
        return ['error' => 'Token tidak ditemukan'];
    }

    return $this->sendNotification(
        $user->fcm_token,
        $title,
        $body,
        $userId
    );
}

    private function getAccessToken(array $serviceAccount): string
    {
        $now = time();

        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signature = '';
        openssl_sign($header . '.' . $claims, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);

        $jwt = $header . '.' . $claims . '.' . $this->base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json('access_token');
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}