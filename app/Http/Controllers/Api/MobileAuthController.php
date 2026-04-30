<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\alumniModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        if (!empty($alumni->user_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Akun untuk NIM ini sudah terdaftar',
            ], 400);
        }

        $existingEmail = alumniModel::where('email', $request->email)->where('alumni_id', '!=', $alumni->alumni_id)->exists();
        if ($existingEmail) {
            return response()->json([
                'status' => false,
                'message' => 'Email sudah terdaftar untuk alumni lain',
            ], 400);
        }

        $existingUsername = DB::table('users')->where('username', $request->email)->exists();
        if ($existingUsername) {
            return response()->json([
                'status' => false,
                'message' => 'Email sudah digunakan sebagai akun login',
            ], 400);
        }

        $roleId = DB::table('role')->where('role_kode', 'ALM')->value('role_id');
        if (!$roleId) {
            return response()->json([
                'status' => false,
                'message' => 'Role Alumni belum tersedia di database',
            ], 500);
        }

        $userId = null;
        DB::transaction(function () use ($request, $alumni, $roleId, &$userId) {
            $userId = DB::table('users')->insertGetId([
                'role_id' => $roleId,
                'username' => $request->email,
                'name' => $alumni->nama_alumni,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $alumni->update([
                'user_id' => $userId,
                'email' => $request->email,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'user' => [
                'user_id' => $userId,
                'alumni_id' => $alumni->alumni_id,
                'nim' => $alumni->nim,
                'name' => $alumni->nama_alumni,
                'email' => $request->email,
            ],
        ]);
    }

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

