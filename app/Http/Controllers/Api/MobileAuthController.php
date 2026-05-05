<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\alumniModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Services\BrevoMailService;

class MobileAuthController extends Controller
{
    // =========================
    // CEK ALUMNI
    // =========================
    public function checkAlumni(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
        ]);

        $alumni = alumniModel::where('nim', $request->nim)->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data alumni tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Alumni ditemukan',
            'data' => $alumni,
        ]);
    }

    // =========================
    // REGISTER + KIRIM OTP
    // =========================
    public function register(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $alumni = alumniModel::where('nim', $request->nim)->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'NIM tidak terdaftar',
            ], 404);
        }

        if (!empty($alumni->user_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Akun sudah terdaftar',
            ], 400);
        }

        // CEK EMAIL DUPLIKAT
        $existingEmail = alumniModel::where('email', $request->email)
            ->where('id', '!=', $alumni->id)
            ->exists();

        if ($existingEmail) {
            return response()->json([
                'status' => false,
                'message' => 'Email sudah digunakan',
            ], 400);
        }

        // =========================
        // RATE LIMIT OTP
        // =========================
        $limitKey = 'otp_limit_'.$request->email;

        if (Cache::has($limitKey)) {
            return response()->json([
                'status' => false,
                'message' => 'Tunggu 1 menit sebelum request OTP lagi'
            ], 429);
        }

        Cache::put($limitKey, true, now()->addMinute());

        // =========================
        // GENERATE OTP
        // =========================
        $otp = random_int(100000, 999999);

        // =========================
        // SIMPAN CACHE (5 MENIT)
        // =========================
        Cache::put('register_'.$request->email, [
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => Hash::make($otp),
        ], now()->addMinutes(5));

        // =========================
        // KIRIM EMAIL VIA BREVO
        // =========================
        try {
            BrevoMailService::send(
                $request->email,
                'Kode OTP Registrasi',
                "
                <div style='font-family:sans-serif'>
                    <h2>Kode OTP Kamu</h2>
                    <h1 style='letter-spacing:5px;'>$otp</h1>
                    <p>Berlaku 5 menit</p>
                </div>
                "
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal kirim email'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP dikirim'
        ]);
    }

    // =========================
    // VERIFIKASI OTP
    // =========================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $data = Cache::get('register_'.$request->email);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired'
            ], 400);
        }

        if (!Hash::check($request->otp, $data['otp'])) {
            return response()->json([
                'status' => false,
                'message' => 'OTP salah'
            ], 400);
        }

        $alumni = alumniModel::where('nim', $data['nim'])->first();

        $roleId = DB::table('role')
            ->where('role_kode', 'ALM')
            ->value('role_id');

        DB::transaction(function () use ($data, $alumni, $roleId) {

            $userId = DB::table('users')->insertGetId([
                'role_id' => $roleId,
                'username' => $data['email'],
                'name' => $alumni->nama,
                'email' => $data['email'],
                'password' => $data['password'],
                'status' => 'active',
                'is_verified' => true,
                'email_verified_at' => now(), // 🔥 tambahan penting
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $alumni->update([
                'user_id' => $userId,
                'email' => $data['email'],
            ]);
        });

        Cache::forget('register_'.$request->email);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil'
        ]);
    }

    // =========================
    // RESEND OTP (🔥 TAMBAHAN)
    // =========================
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $data = Cache::get('register_'.$request->email);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Session habis, ulangi register'
            ], 400);
        }

        $otp = random_int(100000, 999999);

        $data['otp'] = Hash::make($otp);

        Cache::put('register_'.$request->email, $data, now()->addMinutes(5));

        BrevoMailService::send(
            $request->email,
            'OTP Baru',
            "<h1>$otp</h1><p>Berlaku 5 menit</p>"
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP dikirim ulang'
        ]);
    }

    // =========================
    // LOGIN
    // =========================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $alumni = alumniModel::with('user')
            ->where('email', $request->email)
            ->first();

        if (!$alumni || !$alumni->user || !Hash::check($request->password, $alumni->user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        if (!$alumni->user->is_verified) {
            return response()->json([
                'status' => false,
                'message' => 'Akun belum verifikasi OTP'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'user' => [
                'user_id' => $alumni->user->id,
                'alumni_id' => $alumni->id,
                'nim' => $alumni->nim,
                'name' => $alumni->nama_alumni,
                'email' => $alumni->email,
            ],
        ]);
    }
}