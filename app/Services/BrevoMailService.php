<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailService
{
    public static function send($to, $subject, $html)
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
                'headers' => [
                    'api-key' => config('services.brevo.key'), // api brevo
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ]
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => config('mail.from.name'),
                    'email' => config('mail.from.address'),
                ],
                'to' => [
                    ['email' => $to]
                ],
                'subject' => $subject,
                'htmlContent' => $html,
            ]);

            return [
                'status' => $response->status(),
                'body' => $response->body(),
            ];

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}