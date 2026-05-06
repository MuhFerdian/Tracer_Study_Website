<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Politeknik Negeri Jember</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Animated gradient background for body */
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Gradient colors: original blue #3A4A5C, slightly lighter blues #506B8D and #648ABF */
            background: linear-gradient(270deg, #3A4A5C, #506B8D, #648ABF, #506B8D, #3A4A5C);
            background-size: 1000% 1000%;
            animation: gradientShift 30s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .login-title-bg {
            position: absolute;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 48px;
            font-weight: 300;
            letter-spacing: 4px;
            color: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
            z-index: 2;
            position: relative;
        }

        .logo-section {
            background: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: white;
            /* border-radius: 50%; */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            box-shadow: 0 8px 25px rgba(58, 74, 92, 0.3);
            overflow: hidden;
        }

        .logo-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .institution-name {
            font-size: 24px;
            font-weight: bold;
            color: #3A4A5C;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .institution-subtitle {
            font-size: 16px;
            color: #666;
            font-weight: 300;
        }

        .form-section {
            background: #F8F9FA;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-box {
            background: rgba(58, 74, 92, 0.05);
            border: 1px solid rgba(58, 74, 92, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.4;
        }

        .alert-error {
            background: #fdecea;
            border: 1px solid #f5c2c7;
            color: #842029;
        }

        .alert-success {
            background: #e8f5e9;
            border: 1px solid #badbcc;
            color: #0f5132;
        }

        .info-text {
            color: #3A4A5C;
            font-size: 14px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            z-index: 2;
        }

        .form-input {
            width: 100%;
            padding: 15px 50px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            z-index: 1;
        }

        .form-input.password-input {
            padding-right: 45px;
        }

        .form-input:focus {
            outline: none;
            border-color: #3A4A5C;
            box-shadow: 0 0 10px rgba(58, 74, 92, 0.2);
        }

        .username-dropdown {
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .username-dropdown.show {
            display: block;
        }

        .username-option {
            padding: 12px 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .username-option:hover {
            background-color: #f0f0f0;
        }

        .remember-password {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #3A4A5C;
        }

        .remember-text {
            font-size: 14px;
            color: #555;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: white;
            color: #3A4A5C;
            border: 2px solid #3A4A5C;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            background: #3A4A5C;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(58, 74, 92, 0.3);
        }

        .form-footer {
            /* Removed forgot password link, so keep this simple */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-message {
            color: #f44336;
            font-size: 12px;
            margin-top: 5px;
            display: none;
            padding-left: 50px;
        }

        .error-message.show {
            display: block;
        }

        .form-input.error {
            border-color: #f44336;
        }

        .success-message {
            color: #4CAF50;
            font-size: 12px;
            margin-top: 5px;
            display: none;
            padding-left: 50px;
        }

        .success-message.show {
            display: block;
        }

        /* Eye icon for password toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            color: #999;
            user-select: none;
            z-index: 3;
        }
        .password-toggle:hover {
            color: #3A4A5C;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 450px;
            }

            .logo-section {
                padding: 40px 30px;
            }

            .form-section {
                padding: 40px 30px;
            }

            .login-title-bg {
                font-size: 36px;
                top: 40px;
            }

            .logo {
                width: 100px;
                height: 100px;
                font-size: 14px;
            }

            .institution-name {
                font-size: 20px;
            }

            .form-footer {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="login-title-bg">LOGIN</div>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo">
                <img src="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/Logo_Polije.png') }}" alt="Logo Politeknik Negeri Jember" width="150" />
            </div>
            <div class="institution-name">POLITEKNIK NEGERI JEMBER</div>
            <div class="institution-subtitle">Portal Akademik</div>
        </div>

        <div class="form-section">
            <div class="info-box">
                <div class="info-text">
                    Silakan masuk menggunakan username dan password yang telah ditetapkan.
                </div>
            </div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        @if ($errors->has('login'))
            {{ $errors->first('login') }}
        @else
            {{ $errors->first() }}
        @endif
    </div>
@endif

<form id="loginForm" method="POST" action="{{ url('login') }}">
    @csrf
    
    <!-- Role Selection -->
    <div class="form-group">
        <label style="font-weight: bold; color: #3A4A5C; display: block; margin-bottom: 10px;">Pilih Tipe User:</label>
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required style="width: 18px; height: 18px; cursor: pointer; accent-color: #3A4A5C;">
                <span style="color: #555;">Admin</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="role" value="dosen" {{ old('role') === 'dosen' ? 'checked' : '' }} required style="width: 18px; height: 18px; cursor: pointer; accent-color: #3A4A5C;">
                <span style="color: #555;">Dosen</span>
            </label>
        </div>
        <div class="error-message" id="roleError">Mohon pilih tipe user terlebih dahulu</div>
    </div>

    <div class="form-group">
        <div class="input-group">
            <span class="input-icon">👤</span>
            <input
                type="text"
                class="form-input"
                placeholder="Username"
                id="usernameInput"
                name="username"
                value="{{ old('username') }}"
                autocomplete="off"
                required
            />
            <div class="username-dropdown" id="usernameDropdown"></div>
        </div>
        <div class="error-message" id="usernameError">Mohon isi username terlebih dahulu</div>
        <div class="success-message" id="usernameSuccess"></div>
    </div>

    <div class="form-group">
        <div class="input-group">
            <span class="input-icon">🔒</span>
            <input
                type="password"
                class="form-input password-input"
                placeholder="Password"
                id="passwordInput"
                name="password"
                autocomplete="new-password"
                required
            />
            <span
                class="password-toggle"
                id="passwordToggle"
                title="Tampilkan / sembunyikan password"
            >👁️</span>
        </div>
        <div class="error-message" id="passwordError">Mohon isi password terlebih dahulu</div>
    </div>

    <div class="remember-password">
        <input
            type="checkbox"
            class="remember-checkbox"
            id="rememberCheck"
            name="remember"
        />
        <label for="rememberCheck" class="remember-text">Ingat password</label>
    </div>

    <button type="submit" class="login-btn">Login</button>

    <div class="form-footer">
        <!-- Forgot password link removed as requested -->
    </div>
</form>

        </div>
    </div>

    <script>
        // Data username yang mungkin digunakan
        const availableUsernames = ['admin'];

        // Menyimpan username yang pernah digunakan
        // let savedUsernames = JSON.parse(sessionStorage.getItem('adminUsernames')) || [];
        let savedUsernames = []; // Sebelumnya: JSON.parse(sessionStorage.getItem('adminUsernames')) || [];


        const usernameInput = document.getElementById('usernameInput');
        const passwordInput = document.getElementById('passwordInput');
        const usernameError = document.getElementById('usernameError');
        const passwordError = document.getElementById('passwordError');
        const usernameSuccess = document.getElementById('usernameSuccess');
        const roleError = document.getElementById('roleError');
        const usernameDropdown = document.getElementById('usernameDropdown');
        const loginForm = document.getElementById('loginForm');
        const passwordToggle = document.getElementById('passwordToggle');
        const roleInputs = document.querySelectorAll('input[name="role"]');

        // Track jika field sudah pernah diklik/diinteraksi sebelumnya
        let usernameTouched = false;
        let passwordTouched = false;
        let roleTouched = false;

        // Fungsi untuk menampilkan dropdown username
        function showUsernameDropdown() {
            usernameDropdown.innerHTML = '';

            // Gabungkan savedUsernames dan availableUsernames, hilangkan duplikat
            // const allUsernames = [...new Set([...savedUsernames, ...availableUsernames])];

            // if (allUsernames.length > 0) {
            //     allUsernames.forEach(username => {
            //         const option = document.createElement('div');
            //         option.className = 'username-option';
            //         option.textContent = username;
            //         option.addEventListener('mousedown', (e) => {
            //             e.preventDefault(); // Mencegah kehilangan fokus pada input
            //             usernameInput.value = username;
            //             usernameInput.focus(); // Tetap fokus ke input
            //             hideUsernameDropdown();
            //             hideUsernameError();
            //         });
            //         usernameDropdown.appendChild(option);
            //     });
            //     usernameDropdown.classList.add('show');
            // }
        }

        // Fungsi untuk menyembunyikan dropdown username
        function hideUsernameDropdown() {
            usernameDropdown.classList.remove('show');
        }

        // Event listener untuk input username - focus
        usernameInput.addEventListener('focus', function () {
            showUsernameDropdown();
            if (passwordTouched && !passwordInput.value.trim()) {
                showPasswordError();
            }
        });

        // Event listener untuk input username - blur
        usernameInput.addEventListener('blur', function () {
            setTimeout(() => {
                if (!usernameDropdown.contains(document.activeElement)) {
                    hideUsernameDropdown();
                }
            }, 200);
        });

        // Event listener untuk input username - input/typing
        usernameInput.addEventListener('input', function () {
            usernameTouched = true;
            if (this.value.trim()) {
                hideUsernameError();
                hideUsernameSuccess();
            }
        });

        // Event listener untuk input password - focus
        passwordInput.addEventListener('focus', function () {
            if (usernameTouched && !usernameInput.value.trim()) {
                showUsernameError();
            }
        });

        // Event listener untuk input password - input/typing
        passwordInput.addEventListener('input', function () {
            passwordTouched = true;
            if (this.value.trim()) {
                hidePasswordError();
            }
        });

        // Event listeners untuk role selection
        roleInputs.forEach(input => {
            input.addEventListener('change', function () {
                roleTouched = true;
                hideRoleError();
            });
        });

        // Fungsi untuk menampilkan error username
        function showUsernameError() {
            usernameInput.classList.add('error');
            usernameError.classList.add('show');
        }

        // Fungsi untuk menyembunyikan error username
        function hideUsernameError() {
            usernameInput.classList.remove('error');
            usernameError.classList.remove('show');
        }

        // Fungsi untuk menampilkan success username
        function showUsernameSuccess(message) {
            usernameSuccess.textContent = message;
            usernameSuccess.classList.add('show');
        }

        // Fungsi untuk menyembunyikan success username
        function hideUsernameSuccess() {
            usernameSuccess.classList.remove('show');
        }

        // Fungsi untuk menampilkan error password
        function showPasswordError() {
            passwordInput.classList.add('error');
            passwordError.classList.add('show');
        }

        // Fungsi untuk menyembunyikan error password
        function hidePasswordError() {
            passwordInput.classList.remove('error');
            passwordError.classList.remove('show');
        }

        // Fungsi untuk menampilkan error role
        function showRoleError() {
            roleError.classList.add('show');
        }

        // Fungsi untuk menyembunyikan error role
        function hideRoleError() {
            roleError.classList.remove('show');
        }

        // Fungsi untuk menyimpan username baru
        function saveUsername(username) {
            // if (username && !savedUsernames.includes(username)) {
            //     savedUsernames.push(username);
            //     sessionStorage.setItem('adminUsernames', JSON.stringify(savedUsernames));
            // }
            return;
        }

        // Toggle visibility password
        passwordToggle.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.textContent = '🙈';
                passwordToggle.title = 'Sembunyikan password';
            } else {
                passwordInput.type = 'password';
                passwordToggle.textContent = '👁️';
                passwordToggle.title = 'Tampilkan password';
            }
            passwordInput.focus();
        });

        // Event listener untuk form submit
        loginForm.addEventListener('submit', function (e) {
            const username = usernameInput.value.trim();
            const password = passwordInput.value.trim();
            const roleSelected = Array.from(roleInputs).some(input => input.checked);

            // Reset error messages
            hideUsernameError();
            hidePasswordError();
            hideRoleError();

            let hasError = false;

            // Validasi role
            if (!roleSelected) {
                showRoleError();
                hasError = true;
            }

            // Validasi username
            if (!username) {
                showUsernameError();
                hasError = true;
            }

            // Validasi password
            if (!password) {
                showPasswordError();
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Simpan username untuk penggunaan selanjutnya
            saveUsername(username);
        });

        // Tutup dropdown ketika klik di luar area username
        document.addEventListener('click', function (e) {
            if (!usernameInput.contains(e.target) && !usernameDropdown.contains(e.target)) {
                hideUsernameDropdown();
            }
        });

        // Add interactive effects untuk input fields
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function () {
                if (!this.classList.contains('error')) {
                    this.parentElement.style.transform = 'scale(1.02)';
                }
            });

            input.addEventListener('blur', function () {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>

