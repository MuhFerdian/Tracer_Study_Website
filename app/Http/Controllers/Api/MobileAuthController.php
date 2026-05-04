<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\alumniModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class MobileAuthController extends Controller
{
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

    public function register(Request $request)
{
    // VALIDASI INPUT
    $request->validate([
        'nim' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    // CEK NIM ADA DI ALUMNI
    $alumni = alumniModel::where('nim', $request->nim)->first();
    if (!$alumni) {
        return response()->json([
            'status' => false,
            'message' => 'NIM tidak terdaftar sebagai alumni',
        ], 404);
    }

    // CEK SUDAH PUNYA AKUN
    if (!empty($alumni->user_id)) {
        return response()->json([
            'status' => false,
            'message' => 'Akun untuk NIM ini sudah terdaftar',
        ], 400);
    }

    // CEK EMAIL DI ALUMNI (JANGAN DOUBLE)
    $existingEmail = alumniModel::where('email', $request->email)
        ->where('alumni_id', '!=', $alumni->alumni_id)
        ->exists();

    if ($existingEmail) {
        return response()->json([
            'status' => false,
            'message' => 'Email sudah terdaftar untuk alumni lain',
        ], 400);
    }

    // CEK EMAIL DI USERS
    $existingUsername = DB::table('users')
        ->where('username', $request->email)
        ->exists();

    if ($existingUsername) {
        return response()->json([
            'status' => false,
            'message' => 'Email sudah digunakan sebagai akun login',
        ], 400);
    }

    // AMBIL ROLE ALUMNI
    $roleId = DB::table('role')
        ->where('role_kode', 'ALM')
        ->value('role_id');

    if (!$roleId) {
        return response()->json([
            'status' => false,
            'message' => 'Role Alumni belum tersedia di database',
        ], 500);
    }

    // GENERATE OTP
    $otp = rand(100000, 999999);

    $userId = null;

    // SIMPAN DATA DALAM TRANSACTION
    DB::transaction(function () use ($request, $alumni, $roleId, &$userId, $otp) {

        // INSERT KE USERS
        $userId = DB::table('users')->insertGetId([
            'role_id' => $roleId,
            'username' => $request->email,
            'name' => $alumni->nama_alumni,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            // STATUS AWAL
            'status' => 'pending',

            // OTP
            'otp_code' => $otp,
            'otp_expired_at' => now()->addMinutes(5),
            'is_verified' => false,

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // UPDATE DATA ALUMNI
        $alumni->update([
            'user_id' => $userId,
            'email' => $request->email,
        ]);
    });
    try {
    Mail::to($request->email)->send(new OtpMail($otp));
} catch (\Exception $e) {
    return response()->json([
        'status' => false,
        'message' => 'Gagal kirim email: ' . $e->getMessage()
    ]);
}

    Mail::to($request->email)->send(new OtpMail($otp));

    // RESPONSE (UNTUK TESTING DULU)
    return response()->json([
        'status' => true,
        'message' => 'Registrasi berhasil, OTP dikirim ke email',
        'data' => [
            'user_id' => $userId,
            'alumni_id' => $alumni->alumni_id,
            'nim' => $alumni->nim,
            'name' => $alumni->nama_alumni,
            'email' => $request->email,
        ],
    ]);
}



    // VERIFIKASI OTP
    public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required'
    ]);

    $user = DB::table('users')->where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan'
        ]);
    }

    if ($user->otp_code != $request->otp) {
        return response()->json([
            'status' => false,
            'message' => 'OTP salah'
        ]);
    }

    if (now()->greaterThan($user->otp_expired_at)) {
        return response()->json([
            'status' => false,
            'message' => 'OTP sudah expired'
        ]);
    }

    if ($user->is_verified) {
    return response()->json([
        'status' => false,
        'message' => 'Akun sudah diverifikasi'
    ]);
}

    DB::table('users')->where('id', $user->id)->update([
        'is_verified' => true,
        'status' => 'active',
        'otp_code' => null,
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Verifikasi berhasil'
    ]);
}


    // LOGIN
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
// CEK VERIFIKASI OTP
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
                'alumni_id' => $alumni->alumni_id,
                'nim' => $alumni->nim,
                'name' => $alumni->nama_alumni,
                'email' => $alumni->email,
            ],
        ]);
    }
}