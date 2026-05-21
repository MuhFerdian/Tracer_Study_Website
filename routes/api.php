<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileQuestionController;
use App\Http\Controllers\Api\MobileAnswerController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\MobileResultController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationHistoryController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Api\LowonganController;
use App\Http\Controllers\TracerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================
// AUTH (ALUMNI / MOBILE)
// ==========================
Route::post('/login', [MobileAuthController::class, 'login']);

Route::post('/register', [MobileAuthController::class, 'register']);

Route::get('/cek-alumni', [MobileAuthController::class, 'checkAlumni']);
// Route::post('/register', [MobileAuthController::class, 'register']);
// Route::post('/login', [MobileAuthController::class, 'login']);

Route::get('/questions', [MobileQuestionController::class, 'index']);

// Protected routes - require Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/answers', [MobileAnswerController::class, 'store']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/update-profile', [AlumniController::class, 'updateProfile']);
    Route::post('/save-fcm-token', function (Request $request) {
        $user = $request->user(); // ambil dari token, bukan dari request body
        if ($user) {
            $user->fcm_token = $request->token;
            $user->save();
        }
        return response()->json(['status' => true]);
    });
});
Route::post('/verify-otp', [MobileAuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [MobileAuthController::class, 'resendOtp']);

Route::post('/forgot-password', [MobileAuthController::class, 'forgotPassword']);
Route::post('/reset-password-otp', [MobileAuthController::class, 'resetPasswordOtp']);

// Route::post('/send-notif', [NotificationController::class, 'send']);
Route::get('/notifications', [NotificationHistoryController::class, 'index']);
Route::post('/notifications/send', [NotificationController::class, 'send']);
Route::get('/notifications/{user_id}', function ($id) {
    return DB::table('notifications')
        ->where('user_id', $id)
        ->latest()
        ->get();
});

// Dipindah ke dalam auth:sanctum di atas
// Route::post('/save-fcm-token', ...);
Route::get('/reminder-survey', [ReminderController::class, 'kirimReminder']);

Route::get('/lowongan', [LowonganController::class, 'index']);
Route::get('/lowongan/{id}', [LowonganController::class, 'show']);
Route::post('/lowongan', [LowonganController::class, 'store']);

Route::get(
    '/statistik-alumni',
    [TracerController::class, 'statistikAlumni']
);