<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Tracer Study Polije</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tracer-ui.css') }}">
</head>

<body class="auth-page">
    <main class="login-shell">
        <section class="login-brand-panel" aria-label="Informasi Tracer Study">
            <div>
                <div class="login-logo-wrap">
                    <img src="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/Logo_Polije.png') }}"
                        alt="Logo Politeknik Negeri Jember">
                </div>

                <h1>Tracer Study JTI Polije</h1>
                <p class="mb-0">
                    Portal admin dan dosen untuk memantau data alumni, hasil survey, dan indikator mutu lulusan.
                </p>
            </div>

            <div class="login-mini-stats mt-4">
                <div class="login-mini-stat">
                    <strong>Admin</strong>
                    <span>Kelola data dan laporan</span>
                </div>
                <div class="login-mini-stat">
                    <strong>Dosen</strong>
                    <span>Pantau rekap tracer</span>
                </div>
            </div>
        </section>

        <section class="login-panel">
            <div class="mb-4">
                <p class="text-uppercase fw-bold text-primary small mb-2">Masuk ke sistem</p>
                <h2 class="mb-2">Selamat datang kembali</h2>
                <p class="subtitle mb-0">Pilih tipe user, lalu masukkan username dan password akun Anda.</p>
            </div>

            @if (session('success'))
                <div class="alert-soft alert-soft-success mb-3">
                    <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-soft alert-soft-danger mb-3">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    @if ($errors->has('login'))
                        {{ $errors->first('login') }}
                    @else
                        {{ $errors->first() }}
                    @endif
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ url('login') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tipe User</label>
                    <div class="role-grid">
                        <label class="role-option">
                            <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required>
                            <i class="fas fa-user-shield"></i>
                            <span class="fw-bold">Admin</span>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="dosen" {{ old('role') === 'dosen' ? 'checked' : '' }} required>
                            <i class="fas fa-chalkboard-user"></i>
                            <span class="fw-bold">Dosen</span>
                        </label>
                    </div>
                    <div class="error-message" id="roleError">Mohon pilih tipe user terlebih dahulu.</div>
                </div>

                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Username</label>
                    <div class="field-wrap">
                        <i class="fas fa-user field-icon"></i>
                        <input type="text" class="auth-input" placeholder="Masukkan username" id="usernameInput"
                            name="username" value="{{ old('username') }}" autocomplete="username" required>
                    </div>
                    <div class="error-message" id="usernameError">Mohon isi username terlebih dahulu.</div>
                </div>

                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input type="password" class="auth-input" placeholder="Masukkan password" id="passwordInput"
                            name="password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" id="passwordToggle"
                            aria-label="Tampilkan password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="passwordError">Mohon isi password terlebih dahulu.</div>
                </div>

                <div class="remember-row mb-4">
                    <label class="d-inline-flex align-items-center gap-2 mb-0" for="rememberCheck">
                        <input type="checkbox" id="rememberCheck" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="{{ url('/') }}" class="text-decoration-none fw-bold">Kembali</a>
                </div>

                <button type="submit" class="login-submit">
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </section>
    </main>

    <script>
        const loginForm = document.getElementById('loginForm');
        const usernameInput = document.getElementById('usernameInput');
        const passwordInput = document.getElementById('passwordInput');
        const passwordToggle = document.getElementById('passwordToggle');
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const roleError = document.getElementById('roleError');
        const usernameError = document.getElementById('usernameError');
        const passwordError = document.getElementById('passwordError');

        function setError(input, errorElement, show) {
            if (input) {
                input.classList.toggle('is-invalid', show);
            }
            errorElement.classList.toggle('show', show);
        }

        roleInputs.forEach((input) => {
            input.addEventListener('change', () => {
                roleError.classList.remove('show');
            });
        });

        usernameInput.addEventListener('input', () => {
            setError(usernameInput, usernameError, !usernameInput.value.trim());
        });

        passwordInput.addEventListener('input', () => {
            setError(passwordInput, passwordError, !passwordInput.value.trim());
        });

        passwordToggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            passwordToggle.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            passwordInput.focus();
        });

        loginForm.addEventListener('submit', (event) => {
            const roleSelected = Array.from(roleInputs).some((input) => input.checked);
            const usernameEmpty = !usernameInput.value.trim();
            const passwordEmpty = !passwordInput.value.trim();

            roleError.classList.toggle('show', !roleSelected);
            setError(usernameInput, usernameError, usernameEmpty);
            setError(passwordInput, passwordError, passwordEmpty);

            if (!roleSelected || usernameEmpty || passwordEmpty) {
                event.preventDefault();
            }
        });
    </script>
</body>

</html>
