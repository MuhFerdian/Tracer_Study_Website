<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileQuestionController;
use App\Http\Controllers\Api\MobileAnswerController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\MobileResultController;

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
Route::post('/answers', [MobileAnswerController::class, 'store']);
Route::post('/verify-otp', [MobileAuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [MobileAuthController::class, 'resendOtp']);