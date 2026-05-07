<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
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

        $credentials = new ServiceAccountCredentials(
            "https://www.googleapis.com/auth/firebase.messaging",
            $credentialsPath
        );

        $tokenData = $credentials->fetchAuthToken();
        $accessToken = $tokenData['access_token'];

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
}