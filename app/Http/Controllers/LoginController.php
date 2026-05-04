<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\alumniModel;
use App\Models\User;

class LoginController extends Controller
{
    // Override agar login pakai username, bukan email
    public function username()
    {
        return 'username';
    }

    // Tampilkan form login
    public function showLoginForm()
    {
        return view('login'); // Pastikan file resources/views/login.blade.php tersedia
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:admin,dosen,alumni'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'role.required' => 'Pilih tipe user terlebih dahulu.',
            'role.in' => 'Tipe user tidak valid.',
        ]);

        $role = $credentials['role'];
        $username = $credentials['username'];
        $password = $credentials['password'];

        // Attempt login dengan role yang sesuai
        $authResult = $this->attemptLogin($username, $password, $role);
        $user = $authResult['user'];

        if ($user) {
            // Regenerate session
            $request->session()->regenerate();
            Log::info("Login berhasil untuk {$role}: {$username}");

            return redirect()->intended($this->redirectPath($user, $role))
                ->with('success', 'Login berhasil sebagai ' . ucfirst($role) . '.');
        }

        Log::warning("Login gagal untuk {$role}: {$username}");

        return back()->withErrors([
            'login' => $authResult['message'] ?? ('Username atau password salah untuk role ' . $role . '.'),
        ])->withInput();
    }

    // Attempt login berdasarkan role
    protected function attemptLogin($username, $password, $role): array
    {
        if ($role === 'admin' || $role === 'dosen') {
            // Login admin & dosen dari user table dengan role Admin atau Dosen
            $user = User::where('username', $username)
                ->whereHas('role', function ($query) use ($role) {
                    $roleName = ($role === 'admin') ? 'Admin' : 'Dosen';
                    $query->where('role_nama', $roleName);
                })
                ->first();

            if (!$user) {
                return [
                    'user' => null,
                    'message' => 'Akun tidak ditemukan atau tidak memiliki role ' . ucfirst($role) . '.',
                ];
            }

            if (!Hash::check($password, $user->password)) {
                return [
                    'user' => null,
                    'message' => 'Password yang Anda masukkan salah.',
                ];
            }

            // Cek status akun untuk dosen (hanya dosen dengan status 'active' boleh login)
            if ($role === 'dosen' && isset($user->status) && $user->status !== 'active') {
                return [
                    'user' => null,
                    'message' => 'Akun belum diaktifkan oleh administrator.'
                ];
            }

            // Set guard 'web' untuk admin dan dosen
            Auth::guard('web')->login($user);

            return [
                'user' => $user,
                'message' => null,
            ];
        } elseif ($role === 'alumni') {
            // Login alumni dari alumni table yang relate ke user
            $alumni = alumniModel::whereHas('user', function ($query) use ($username) {
                $query->where('username', $username);
            })->first();

            if (!$alumni || !$alumni->user) {
                return [
                    'user' => null,
                    'message' => 'Akun alumni tidak ditemukan atau belum terhubung dengan user login.',
                ];
            }

            if (!Hash::check($password, $alumni->user->password)) {
                return [
                    'user' => null,
                    'message' => 'Password yang Anda masukkan salah.',
                ];
            }

            // Set guard 'alumni' untuk alumni
            Auth::guard('alumni')->login($alumni);

            return [
                'user' => $alumni,
                'message' => null,
            ];
        }

        return [
            'user' => null,
            'message' => 'Tipe user tidak valid.',
        ];
    }

    // Arahkan user setelah login berhasil
    protected function redirectPath($user, $role): string
    {
        Log::info("Authenticated as {$role}");

        if ($role === 'admin' || $role === 'dosen') {
            // Both admin dan dosen go to dashboard
            return '/admin';
        } elseif ($role === 'alumni') {
            return '/alumni/' . $user->alumni_id;
        }

        return '/';
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landingpage')->with('status', 'Berhasil logout.');
    }
}
