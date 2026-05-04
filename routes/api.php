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


// ==========================
// MOBILE (FLUTTER - ALUMNI)
// ==========================
Route::prefix('mobile')->group(function () {

    // ambil pertanyaan
    Route::get('/questions', [MobileQuestionController::class, 'index']);

    // harus login untuk kirim jawaban
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/answers', [MobileAnswerController::class, 'store']);
    });

});


// ==========================
// ADMIN / DOSEN (WEB)
// ==========================
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {

    // kelola pertanyaan
    Route::get('/questions', [QuestionController::class, 'index']);

    // nanti bisa tambah:
    // Route::post('/questions', ...)
    // Route::get('/dashboard', ...)

});


// ==========================
// GET USER (DEFAULT)
// ==========================
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware('auth:sanctum')->group(function () {
        Route::post('/mobile/answers', [MobileAnswerController::class, 'store']);
        Route::get('/mobile/results', [MobileResultController::class, 'index']);
});

//     // 🔹 ambil data user login
//     Route::get('/user', [AuthController::class, 'me']);

//     // 🔹 logout
//     Route::post('/logout', [AuthController::class, 'logout']);

//     // 🔹 ambil pertanyaan (untuk Flutter)
//     Route::get('/questions', [QuestionController::class, 'index']);

//     // 🔹 kirim jawaban kuisioner
//     Route::post('/answers', [AnswerController::class, 'store']);
// });