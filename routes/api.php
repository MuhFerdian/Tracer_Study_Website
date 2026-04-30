<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileQuestionController;
use App\Http\Controllers\Api\MobileAnswerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/cek-alumni', [MobileAuthController::class, 'checkAlumni']);
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);

Route::get('/questions', [MobileQuestionController::class, 'index']);
Route::post('/answers', [MobileAnswerController::class, 'store']);
