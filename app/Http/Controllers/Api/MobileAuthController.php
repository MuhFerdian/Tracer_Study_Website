<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\alumniModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'nim' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $alumni = alumniModel::where('nim', $request->nim)->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'NIM tidak terdaftar sebagai alumni',
            ], 404);
        }

        if ($alumni->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'Akun sudah terdaftar untuk alumni ini',
            ], 400);
        }

        // cek email di users
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Email sudah digunakan',
            ], 400);
        }

        // ambil role alumni
        $roleId = DB::table('role')->where('role_kode', 'ALM')->value('role_id');

        if (!$roleId) {
            return response()->json([
                'status' => false,
                'message' => 'Role alumni tidak ditemukan',
            ], 500);
        }

        // transaction
        DB::beginTransaction();

        try {

            $user = User::create([
                'role_id' => $roleId,
                'username' => $request->email,
                'name' => $alumni->nama_alumni,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            $alumni->update([
                'user_id' => $user->id,
                'email' => $request->email,
            ]);

            // 🔥 TOKEN
            $token = $user->createToken('mobile')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil',
                'token' => $token,
                'user' => [
                    'user_id' => $user->id,
                    'alumni_id' => $alumni->alumni_id,
                    'nim' => $alumni->nim,
                    'name' => $alumni->nama_alumni,
                    'email' => $request->email,
                ],
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
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

        $user = $alumni->user;

        // 🔥 HAPUS TOKEN LAMA (optional tapi bagus)
        $user->tokens()->delete();

        // 🔥 BUAT TOKEN BARU
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'user_id' => $user->id,
                'alumni_id' => $alumni->alumni_id,
                'nim' => $alumni->nim,
                'name' => $alumni->nama_alumni,
                'email' => $alumni->email,
            ],
        ]);
    }
}