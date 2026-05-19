<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
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
        return view('auth.login'); // Pastikan file resources/views/login.blade.php tersedia
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'min:6', 'max:25', 'regex:/^[a-z0-9._]+$/'],
            'password' => ['required', 'string', 'min:8', 'max:16'],
            'role'     => ['required', 'in:admin,dosen'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min'      => 'Username minimal 6 karakter.',
            'username.max'      => 'Username terlalu panjang.',
            'username.regex'    => 'Username hanya boleh huruf kecil, angka, titik, dan underscore.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.max'      => 'Password terlalu panjang.',
            'role.required'     => 'Pilih tipe user terlebih dahulu.',
            'role.in'           => 'Tipe user tidak valid.',
        ]);

        // ── Rate limiting: maks 5 percobaan per menit per IP ──────────
        $throttleKey = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('username', 'role'));
        }

        $role     = $credentials['role'];
        $username = $credentials['username'];
        $password = $credentials['password'];

        // Attempt login dengan role yang sesuai
        $authResult = $this->attemptLogin($username, $password, $role);
        $user = $authResult['user'];

        if ($user) {
            // Login berhasil — reset rate limiter
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            Log::info("Login berhasil untuk {$role}: {$username}");

            return redirect()->intended($this->redirectPath($user, $role))
                ->with('success', 'Login berhasil sebagai ' . ucfirst($role) . '.');
        }

        // Login gagal — tambah hitungan rate limiter
        RateLimiter::hit($throttleKey, 60);
        Log::warning("Login gagal untuk {$role}: {$username}");

        return back()->withErrors([
            'login' => $authResult['message'] ?? ('Username atau password salah untuk role ' . $role . '.'),
        ])->withInput($request->only('username', 'role'));
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
        }

        return '/';
    }

    // protected function redirectPath($user, $role): string
    // {
    //     if ($role === 'admin') {
    //         return '/admin';
    //     }

    //     if ($role === 'dosen') {
    //         return '/dosen';
    //     }

    //     if ($role === 'alumni') {
    //         return '/alumni/' . $user->alumni_id;
    //     }

    //     return '/';
    // }

    public function logout(Request $request)
    {
        if (Auth::guard('alumni')->check()) {
            Auth::guard('alumni')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // return redirect('/landingpage');
        $response = redirect('/landingpage');

        return $response->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma','no-cache')
            ->header('Expires','0');
    }
}
