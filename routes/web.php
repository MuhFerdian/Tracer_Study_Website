<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenAlumniController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\PertanyaanReferenceController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ManajemenDosenController;
use App\Http\Controllers\LowonganPekerjaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyPeriodController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// LANDING PAGE
Route::get('/', function () {
    return redirect()->route('landing');
});

Route::get('/landingpage', [LandingPageController::class, 'index'])->name('landing');

// LOGIN & LOGOUT
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// LANDING PAGE
// Route::get('/landingpage', function () {
//     return view('layoutLandingPage.hero');
// });

   /*
|--------------------------------------------------------------------------
| LOWONGAN PUBLIC landing page
|--------------------------------------------------------------------------
*/

Route::get('/lowongan', [LowonganPekerjaanController::class, 'publicIndex']);

Route::get('/lowongan/{id}', [LowonganPekerjaanController::class, 'show']);

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
        Route::get('/penghasilan-alumni',    [DashboardController::class, 'getPenghasilan']);
        Route::get('/average-waiting-time', [DashboardController::class, 'getAverageWaitingTime']);
        Route::get('/alumni-satisfaction', [DashboardController::class, 'getAlumniSatisfaction']);

        Route::get('/kerjasama-chart', [DashboardController::class, 'getKerjaSama']);
        Route::get('/keahlian-chart', [DashboardController::class, 'keahlianChart']);
        Route::get('/kemampuan-bahasa-chart', [DashboardController::class, 'kemampuanBahasaChart']);
        Route::get('/kemampuan-komunikasi-chart', [DashboardController::class, 'kemampuanKomunikasiChart']);
        Route::get('/pengembangan-diri-chart', [DashboardController::class, 'pengembanganDiriChart']);
        // Route::get('/kepemimpinan-chart', [DashboardController::class, 'kepemimpinanChart']);
        // Route::get('/etos-kerja-chart', [DashboardController::class, 'etosKerjaChart']);
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
        Route::get('/reference', [PertanyaanReferenceController::class, 'index'])->name('pertanyaan.reference');
        Route::get('/reference/download-template', [PertanyaanReferenceController::class, 'downloadTemplate'])->name('pertanyaan.download-template');
        Route::post('/reference/import-questions', [PertanyaanReferenceController::class, 'importQuestions'])->name('pertanyaan.import-questions');
        Route::get('/list', [PertanyaanController::class, 'list']);
        Route::get('/check-urutan', [PertanyaanController::class, 'checkUrutan']);
        Route::get('/create_ajax', [PertanyaanController::class, 'create_ajax']);
        Route::post('/store', [PertanyaanController::class, 'store']);
        // Arsip
        Route::get('/arsip', [PertanyaanController::class, 'arsip_index'])->name('pertanyaan.arsip');
        Route::get('/arsip/list', [PertanyaanController::class, 'arsip_list']);
        Route::patch('/{id}/arsip', [PertanyaanController::class, 'arsip_ajax']);
        Route::patch('/{id}/restore', [PertanyaanController::class, 'restore_ajax']);
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
    // LOWONGAN PEKERJAAN
    // =======================
    Route::prefix('lowongan-pekerjaan')->group(function () {

    Route::get('/', [LowonganPekerjaanController::class, 'index']);
    Route::get('/list', [LowonganPekerjaanController::class, 'list']);
    Route::get('/create', [LowonganPekerjaanController::class, 'create']);
    Route::post('/store', [LowonganPekerjaanController::class, 'store']);
    Route::get('/{id}/show', [LowonganPekerjaanController::class, 'showAdmin']);
    Route::get('/{id}/edit', [LowonganPekerjaanController::class, 'edit']);
    Route::put('/{id}/update', [LowonganPekerjaanController::class, 'update']);
    Route::delete('/{id}/delete', [LowonganPekerjaanController::class, 'destroy']);
   });

    // =======================
    // SURVEI ALUMNI
    // =======================
    Route::get('/alumni-sudah-mengisi', [ExportController::class, 'showAlumniSudahMengisi'])->name('alumni.sudah.mengisi');
    Route::get('/alumni-belum-mengisi', [ExportController::class, 'showAlumniBelumMengisi'])->name('alumni.belum.mengisi');

    // =======================
    // LAPORAN PDF
    // =======================
    Route::get('/laporan-pdf', [ExportController::class, 'laporanPdf'])->name('laporan.pdf');

    // =======================
    // EXPORT SEMUA DATA ALUMNI
    // =======================
    Route::get('/admin/export/alumni', [ExportController::class, 'exportExcel'])->name('export.alumni');
    Route::get('/admin/export/alumni-sudah', [ExportController::class, 'exportExcelSudahMengisi'])->name('export.alumni.sudah');
    Route::get('/admin/export/alumni-belum', [ExportController::class, 'exportExcelBelumMengisi'])->name('export.alumni.belum');

    // =======================
    // PERIODE SURVEI
    // =======================
    Route::prefix('survey-period')->group(function () {
        Route::get('/',              [SurveyPeriodController::class, 'index']);
        Route::get('/aktif',         [SurveyPeriodController::class, 'getAktif']);
        Route::post('/store',        [SurveyPeriodController::class, 'store']);
        Route::put('/{id}/update',   [SurveyPeriodController::class, 'update']);
        Route::post('/{id}/aktifkan',[SurveyPeriodController::class, 'aktifkan']);
        Route::post('/{id}/tutup',   [SurveyPeriodController::class, 'tutup']);
        Route::delete('/{id}/delete',[SurveyPeriodController::class, 'destroy']);
    });

    // =======================
    // PROFILE / GANTI PASSWORD
    // =======================
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change-password.form');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // =======================
    // NOTIFIKASI ALUMNI
    // =======================
    Route::prefix('notifikasi')->group(function () {
        Route::get('/alumni-mengisi',    [\App\Http\Controllers\NotificationController::class, 'getAlumniMengisi'])->name('notif.alumni.mengisi');
        Route::post('/mark-read/{id}',   [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notif.mark.read');
        Route::post('/mark-all-read',    [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notif.mark.all.read');
        Route::get('/unread-count',      [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notif.unread.count');
    });
});