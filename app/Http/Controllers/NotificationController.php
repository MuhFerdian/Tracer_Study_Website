<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FcmService;
use App\Models\Notification;

class NotificationController extends Controller
{
   public function send(Request $request)
{
    $request->validate([
        'token' => 'required'
    ]);

    // 1. simpan ke database
    $notif = Notification::create([
        'title' => 'Info Tracer Study',
        'body' => 'Data kamu berhasil diperbarui!',
        'type' => 'info'
    ]);

    // 2. kirim FCM
    $fcm = new FcmService();

    $result = $fcm->sendNotification(
        $request->token,
        $notif->title,
        $notif->body
    );

    return response()->json([
        'db' => $notif,
        'fcm' => $result
    ]);
}
}