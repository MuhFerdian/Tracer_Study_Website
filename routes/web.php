<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenAlumniController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ManajemenDosenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// LANDING PAGE
Route::get('/', function () {
    return view('layoutLandingPage.hero');
});

// LOGIN & LOGOUT
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// LANDING PAGE
Route::get('/landingpage', function () {
    return view('layoutLandingPage.hero');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:Admin,Dosen', 'prevent-back']
], function () {

    // =======================
    // DASHBOARD PAGE
    // =======================
    // Route::get('/', [DashboardController::class, 'layoutAdmin.index']);
    // Route::get('/', function () { 
    //     return view('layoutAdmin.index');
    // });

    Route::get('/', [DashboardController::class, 'index']);
    Route::prefix('dashboard')->group(function () {
        Route::get('/summary', [DashboardController::class, 'getSummary']);
        Route::get('/instansi-chart', [DashboardController::class, 'getInstansiChartData']);
        Route::get('/profesi-chart', [DashboardController::class, 'getProfesiChart']);
        Route::get('/rekap-alumni', [DashboardController::class, 'getRekapAlumni']);
        Route::get('/average-waiting-time', [DashboardController::class, 'getAverageWaitingTime']);
        Route::get('/alumni-satisfaction', [DashboardController::class, 'getAlumniSatisfaction']);

        Route::get('/kerjasama-chart', [DashboardController::class, 'getKerjaSama']);
        Route::get('/keahlian-chart', [DashboardController::class, 'keahlianChart']);
        Route::get('/kemampuan-bahasa-chart', [DashboardController::class, 'kemampuanBahasaChart']);
        Route::get('/kemampuan-komunikasi-chart', [DashboardController::class, 'kemampuanKomunikasiChart']);
        Route::get('/pengembangan-diri-chart', [DashboardController::class, 'pengembanganDiriChart']);
        Route::get('/kepemimpinan-chart', [DashboardController::class, 'kepemimpinanChart']);
        Route::get('/etos-kerja-chart', [DashboardController::class, 'etosKerjaChart']);
    });

    // =======================
    // ALUMNI (PAKAI CONTROLLER LAMA)
    // =======================
    Route::prefix('alumni')->group(function () {

        Route::get('/', function () {
            return view('layoutAdmin.manajemenAlumni.alumni');
        });

        Route::post('/listalumni', [ManajemenAlumniController::class, 'list']);
        Route::get('/import_ajax', [ManajemenAlumniController::class, 'import']);
        Route::post('/import_ajax', [ManajemenAlumniController::class, 'import_ajax']);
        Route::get('/create_ajax', [ManajemenAlumniController::class, 'create_ajax']);
        Route::post('/store', [ManajemenAlumniController::class, 'store']);
        Route::get('/{id}/edit_ajax', [ManajemenAlumniController::class, 'edit']);
        Route::put('/update/{id}', [ManajemenAlumniController::class, 'update']);
        Route::get('/{id}/delete_ajax', [ManajemenAlumniController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [ManajemenAlumniController::class, 'delete_ajax']);
        Route::get('/{id}/answers', [ManajemenAlumniController::class, 'showAnswers']);
    });
    // =======================
    // PERTANYAAN
    // =======================
    Route::prefix('pertanyaan')->group(function () {
        Route::get('/', [PertanyaanController::class, 'index']);
        Route::get('/list', [PertanyaanController::class, 'list']);
        Route::get('/check-urutan', [PertanyaanController::class, 'checkUrutan']);
        Route::get('/create_ajax', [PertanyaanController::class, 'create_ajax']);
        Route::post('/store', [PertanyaanController::class, 'store']);
        Route::get('/{id}/edit_ajax', [PertanyaanController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [PertanyaanController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [PertanyaanController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [PertanyaanController::class, 'delete_ajax']);
    });

    // =======================
    // DOSEN
    // =======================
    Route::prefix('manajemen-dosen')
    ->middleware('role:Admin') // Hanya admin yang bisa akses manajemen dosen
    ->group(function () {

        Route::get('/', [ManajemenDosenController::class, 'index']);
        Route::get('/list', [ManajemenDosenController::class, 'list']);
        Route::get('/create_ajax', [ManajemenDosenController::class, 'create_ajax']);
        Route::post('/store', [ManajemenDosenController::class, 'store']);
        Route::get('/{id}/edit_ajax', [ManajemenDosenController::class, 'edit_ajax']);
        Route::put('/{id}/update', [ManajemenDosenController::class, 'update']);
        Route::get('/{id}/delete_ajax', [ManajemenDosenController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete', [ManajemenDosenController::class, 'destroy']);
    });

    // =======================
    // SURVEI ALUMNI
    // =======================
    Route::get('/alumni-sudah-mengisi', [ExportController::class, 'showAlumniSudahMengisi'])->name('alumni.sudah.mengisi');
    Route::get('/alumni-belum-mengisi', [ExportController::class, 'showAlumniBelumMengisi'])->name('alumni.belum.mengisi');

    // =======================
    // EXPORT SEMUA DATA ALUMNI
    // =======================
    Route::get('/admin/export/alumni', [ExportController::class, 'exportExcel'])->name('export.alumni');
    Route::get('/admin/export/alumni-sudah', [ExportController::class, 'exportExcelSudahMengisi'])->name('export.alumni.sudah');
    Route::get('/admin/export/alumni-belum', [ExportController::class, 'exportExcelBelumMengisi'])->name('export.alumni.belum');
});