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
    // REGISTER 
    // =========================
    public function register(Request $request)
    {
        $request->validate([
            'nim'      => 'required|string|unique:users,nim',
            'email'    => 'required|email|unique:users,email|regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i',
            'username' => 'required|regex:/^[a-z0-9]+$/|unique:users,username',
            'no_hp'    => 'required|string|unique:users,no_hp',
            'password' => 'required|min:6',
        ], [
            'email.regex' => 'Email harus menggunakan domain @gmail.com.',
        ]);

        $alumni = alumniModel::where('nim', $request->nim)->first();

        if (!$alumni) {
            return response()->json(['status' => false, 'message' => 'NIM tidak terdaftar'], 404);
        }

        if (!empty($alumni->user_id)) {
            return response()->json(['status' => false, 'message' => 'Akun sudah terdaftar'], 400);
        }

        $otp = random_int(100000, 999999);

        Cache::put('register_'.$request->email, [
            'nim' => $request->nim,
            'email' => $request->email,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'otp' => Hash::make($otp),
        ], now()->addMinutes(5));

        BrevoMailService::send(
            $request->email,
            'Kode OTP Registrasi',
            "<h1>$otp</h1><p>Berlaku 5 menit</p>"
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP dikirim'
        ]);
    }

    // =========================
    // FORGOT PASSWORD 
    // =========================
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i',
        ], [
            'email.regex' => 'Email harus menggunakan domain @gmail.com.',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email tidak ditemukan'
            ], 404);
        }

        $otp = random_int(100000, 999999);

        Cache::put('forgot_'.$request->email, [
            'email' => $request->email,
            'otp' => Hash::make($otp),
        ], now()->addMinutes(5));

        BrevoMailService::send(
            $request->email,
            'OTP Reset Password',
            "<h1>$otp</h1><p>Berlaku 5 menit</p>"
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP dikirim'
        ]);
    }

    // =========================
    // VERIFY OTP 
    // =========================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'type' => 'required|in:register,forgot'
        ]);

        $key = $request->type == 'register'
            ? 'register_'.$request->email
            : 'forgot_'.$request->email;

        $data = Cache::get($key);

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

        // ===== REGISTER FLOW =====
        if ($request->type == 'register') {

            $alumni = alumniModel::where('nim', $data['nim'])->first();

            $roleId = DB::table('role')
                ->where('role_kode', 'ALM')
                ->value('role_id');

            DB::transaction(function () use ($data, $alumni, $roleId) {

                $userId = DB::table('users')->insertGetId([
                    'role_id' => $roleId,
                    'username' => $data['username'],
                    'name' => $alumni->nama,
                    'email' => $data['email'],
                    'no_hp' => $data['no_hp'],
                    'password' => $data['password'],
                    'status' => 'active',
                    'is_verified' => true,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $alumni->update([
                    'user_id' => $userId,
                    'email' => $data['email'],
                    'no_hp' => $data['no_hp'],
                ]);
            });
        }

        Cache::forget($key);

        // Untuk forgot password: simpan token sesi agar resetPasswordOtp bisa dieksekusi
        if ($request->type == 'forgot') {
            Cache::put('reset_verified_' . $request->email, true, now()->addMinutes(10));
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP valid'
        ]);
    }

    // =========================
    // RESET PASSWORD 
    // Hanya boleh dipanggil setelah OTP forgot password diverifikasi.
    // Token sementara disimpan di Cache setelah verifyOtp berhasil.
    // =========================
    public function resetPasswordOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Cek apakah OTP sudah diverifikasi sebelumnya
        $resetKey = 'reset_verified_' . $request->email;
        if (!Cache::has($resetKey)) {
            return response()->json([
                'status'  => false,
                'message' => 'Sesi reset password tidak valid atau sudah kadaluarsa. Silakan ulangi proses lupa password.',
            ], 403);
        }

        $user = DB::table('users')->where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Email tidak ditemukan',
            ], 404);
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Hapus token sesi reset setelah digunakan
        Cache::forget($resetKey);

        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil diubah',
        ]);
    }

    // =========================
    // RESEND OTP
    // =========================
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:register,forgot'
        ]);

        $key = $request->type == 'register'
            ? 'register_'.$request->email
            : 'forgot_'.$request->email;

        $data = Cache::get($key);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Session habis'
            ], 400);
        }

        $otp = random_int(100000, 999999);
        $data['otp'] = Hash::make($otp);

        Cache::put($key, $data, now()->addMinutes(5));

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
            'email'    => 'required|email|regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i',
            'password' => 'required',
        ], [
            'email.regex' => 'Email harus menggunakan domain @gmail.com.',
        ]);

        // Cari user dari tabel users berdasarkan email
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'status'  => false,
                'message' => 'Akun belum verifikasi OTP',
            ], 403);
        }

        // Ambil data alumni yang terhubung ke user ini
        $alumni = $user->alumni;

        if (!$alumni) {
            return response()->json([
                'status'  => false,
                'message' => 'Data alumni tidak ditemukan',
            ], 404);
        }

        // Hapus token lama, buat token baru
        $user->tokens()->delete();
        $token = $user->createToken('alumni-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
            'user_id' => $alumni->user->id,
            'alumni_id' => $alumni->id,
            'nim' => $alumni->nim,
            'name' => $alumni->nama,
            'email' => $alumni->email,
            'no_hp' => $alumni->no_hp,
            'prodi' => $alumni->prodi,
            'angkatan' => $alumni->angkatan,
            'tahunLulus' => $alumni->tahun_lulus,
            'alamat' => $alumni->alamat,
            'image' => $alumni->image,
        ],
        ]);
    }
}