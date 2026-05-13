<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\alumniModel as Alumni;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user_id = $request->query('user_id');

        $alumni = Alumni::where('user_id', $user_id)->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'debug_user_id' => $user_id
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $alumni
        ]);
    }

    /**
     * Tampilkan modal ganti password (AJAX)
     */
    public function changePasswordForm()
    {
        return view('layoutAdmin.profile.change_password');
    }

    /**
     * Proses ganti password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password_baru'  => 'required|min:6|confirmed',
        ], [
            'password_baru.required'  => 'Password baru wajib diisi.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = \App\Models\User::findOrFail(Auth::id());
        $user->password = Hash::make($request->password_baru);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}