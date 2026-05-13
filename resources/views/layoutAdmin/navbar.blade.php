<nav class="sb-topnav navbar navbar-expand navbar-dark top-navbar">

    {{-- BRAND --}}
    <a class="navbar-brand ps-3 d-flex align-items-center" href="{{ url('/admin') }}">

        <img class="logo-animated" src="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/logoTC.png') }}"
            alt="Logo">

        <div class="brand-wrapper">
            <span class="brand-title">Tracer Study</span>
            <small class="brand-subtitle">Teknologi Informasi</small>
        </div>
    </a>

    {{-- SIDEBAR TOGGLE --}}
    <button class="btn btn-link btn-sm toggle-btn order-1 order-lg-0 me-3" id="sidebarToggle">

        <i class="fas fa-bars"></i>
    </button>

    {{-- SEARCH --}}
    <form class="d-none d-md-inline-block form-inline ms-auto me-3">

        <div class="input-group search-box">

            <input class="form-control" type="text" placeholder="Cari data..." />

            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>

        </div>

    </form>

    {{-- PROFILE --}}
    <ul class="navbar-nav me-3">

        <li class="nav-item dropdown">

            <a class="nav-link dropdown-toggle profile-btn" href="#" data-bs-toggle="dropdown">

                <i class="fas fa-user-circle"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                <li class="dropdown-header">
                    <strong>{{ auth()->user()->nama ?? 'User' }}</strong><br>
                    <small>{{ auth()->user()->role->role_nama ?? 'Admin' }}</small>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalGantiPassword">
                        <i class="fas fa-key me-2"></i>
                        Ganti Password
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>

                    <a class="dropdown-item text-danger" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                        <i class="fas fa-right-from-bracket me-2"></i>
                        Keluar
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">

                        @csrf
                    </form>

                </li>

            </ul>

        </li>

    </ul>

</nav>

{{-- MODAL GANTI PASSWORD --}}
<div class="modal fade" id="modalGantiPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0 rounded-4">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-lock me-2 text-primary"></i> Ganti Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formGantiPassword">
                @csrf
                <div class="modal-body px-4 py-3">

                    <div id="alertGantiPassword"></div>

                    {{-- Password Baru --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Password Baru</label>
                        <div class="position-relative">
                            <input type="password" class="form-control pe-5"
                                id="password_baru" name="password_baru"
                                placeholder="Minimal 6 karakter">
                            <span id="toggle_pw_baru"
                                class="position-absolute top-50 end-0 translate-middle-y me-3"
                                style="cursor:pointer; z-index:10;">
                                <i class="fas fa-eye text-secondary" id="icon_pw_baru"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="err_password_baru"></div>
                    </div>

                    {{-- Konfirmasi --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Konfirmasi Password Baru</label>
                        <div class="position-relative">
                            <input type="password" class="form-control pe-5"
                                id="password_baru_confirmation" name="password_baru_confirmation"
                                placeholder="Ulangi password baru">
                            <span id="toggle_pw_konfirmasi"
                                class="position-absolute top-50 end-0 translate-middle-y me-3"
                                style="cursor:pointer; z-index:10;">
                                <i class="fas fa-eye text-secondary" id="icon_pw_konfirmasi"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="err_password_baru_confirmation"></div>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPassword">
                        <span id="btnSimpanText"><i class="fas fa-save me-1"></i> Simpan</span>
                        <span id="btnSimpanLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Toggle password baru
    document.getElementById('toggle_pw_baru').addEventListener('click', function () {
        var inp  = document.getElementById('password_baru');
        var icon = document.getElementById('icon_pw_baru');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Toggle konfirmasi
    document.getElementById('toggle_pw_konfirmasi').addEventListener('click', function () {
        var inp  = document.getElementById('password_baru_confirmation');
        var icon = document.getElementById('icon_pw_konfirmasi');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Reset form saat modal ditutup
    document.getElementById('modalGantiPassword').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formGantiPassword').reset();
        document.getElementById('alertGantiPassword').innerHTML = '';
        document.getElementById('err_password_baru').textContent = '';
        document.getElementById('err_password_baru_confirmation').textContent = '';
        // Reset icon mata
        document.getElementById('icon_pw_baru').classList.replace('fa-eye-slash', 'fa-eye');
        document.getElementById('icon_pw_konfirmasi').classList.replace('fa-eye-slash', 'fa-eye');
        document.getElementById('password_baru').type = 'password';
        document.getElementById('password_baru_confirmation').type = 'password';
    });

    // Submit
    document.getElementById('formGantiPassword').addEventListener('submit', function (e) {
        e.preventDefault();

        document.getElementById('err_password_baru').textContent = '';
        document.getElementById('err_password_baru_confirmation').textContent = '';
        document.getElementById('alertGantiPassword').innerHTML = '';

        document.getElementById('btnSimpanText').classList.add('d-none');
        document.getElementById('btnSimpanLoading').classList.remove('d-none');
        document.getElementById('btnSimpanPassword').disabled = true;

        fetch('{{ route("profile.change-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(this)
        })
        .then(function (res) {
            if (!res.ok) return res.json().then(function (d) { return Promise.reject({ status: res.status, data: d }); });
            return res.json();
        })
        .then(function (data) {
            document.getElementById('alertGantiPassword').innerHTML =
                '<div class="alert alert-success py-2"><i class="fas fa-check-circle me-1"></i> ' + data.message + '</div>';
            document.getElementById('formGantiPassword').reset();
            setTimeout(function () {
                bootstrap.Modal.getInstance(document.getElementById('modalGantiPassword')).hide();
            }, 1500);
        })
        .catch(function (err) {
            if (err && err.status === 422 && err.data) {
                if (err.data.errors) {
                    Object.keys(err.data.errors).forEach(function (field) {
                        var el = document.getElementById('err_' + field);
                        if (el) el.textContent = err.data.errors[field][0];
                    });
                }
                if (err.data.message) {
                    document.getElementById('alertGantiPassword').innerHTML =
                        '<div class="alert alert-danger py-2"><i class="fas fa-exclamation-circle me-1"></i> ' + err.data.message + '</div>';
                }
            } else {
                document.getElementById('alertGantiPassword').innerHTML =
                    '<div class="alert alert-danger py-2"><i class="fas fa-exclamation-circle me-1"></i> Terjadi kesalahan, coba lagi.</div>';
            }
        })
        .finally(function () {
            document.getElementById('btnSimpanText').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-none');
            document.getElementById('btnSimpanPassword').disabled = false;
        });
    });
</script>

<style>
    /* =========================
       TOP NAVBAR
    ========================== */
    .top-navbar {
        height: var(--admin-topbar-height, 64px);
        background: linear-gradient(90deg, #1e3a8a, #2563eb);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        padding: 0 1rem;
        z-index: 1039;
        position: fixed;
        top: 0;
        width: 100%;
    }

    /* =========================
       BRAND
    ========================== */
    .top-navbar .navbar-brand {
        width: var(--admin-sidebar-width, 260px);
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .logo-animated {
        height: 42px;
        transition: all 0.3s ease;
    }

    .logo-animated:hover {
        transform: rotate(10deg) scale(1.05);
    }

    .brand-wrapper {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .brand-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
    }

    .brand-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.75);
    }

    /* =========================
       SIDEBAR TOGGLE
    ========================== */
    .toggle-btn {
        color: white !important;
        font-size: 1.1rem;
        border: none;
        box-shadow: none !important;
    }

    .toggle-btn:hover {
        color: #dbeafe !important;
    }

    /* =========================
       SEARCH BOX
    ========================== */
    .search-box {
        width: 280px;
        overflow: hidden;
        border-radius: 12px;
    }

    .search-box .form-control {
        border: none;
        box-shadow: none !important;
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        padding-left: 15px;
    }

    .search-box .btn {
        border: none;
        width: 50px;
    }

    /* =========================
       PROFILE BUTTON
    ========================== */
    .profile-btn {
        color: white !important;
        font-size: 1.3rem;
        transition: all 0.2s ease;
    }

    .profile-btn:hover {
        color: #dbeafe !important;
    }

    /* =========================
       DROPDOWN
    ========================== */
    .top-navbar .dropdown-menu {
        border-radius: 14px;
        padding: 0.5rem;
        min-width: 220px;
        font-family: 'Poppins', sans-serif;
    }

    .top-navbar .dropdown-header {
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
    }

    .top-navbar .dropdown-item {
        border-radius: 10px;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .top-navbar .dropdown-item:hover {
        background-color: #eff6ff;
    }

    /* =========================
       RESPONSIVE
    ========================== */
    @media (max-width: 768px) {

        .brand-subtitle {
            display: none;
        }

        .search-box {
            width: 140px;
        }

        .brand-title {
            font-size: 1rem;
        }

        .top-navbar .navbar-brand {
            width: 180px;
            gap: 8px;
        }

        .logo-animated {
            height: 36px;
        }

        .toggle-btn {
            order: 2;
            margin-left: auto;
            margin-right: 1rem;
        }
    }

    @media (max-width: 576px) {
        .top-navbar {
            padding: 0 0.5rem;
        }

        .top-navbar .navbar-brand {
            width: 140px;
            padding: 0;
        }

        .brand-title {
            font-size: 0.9rem;
        }

        .logo-animated {
            height: 30px;
        }

        .search-box {
            display: none !important;
        }

        .toggle-btn {
            font-size: 1rem;
            padding: 0.25rem;
        }

        .top-navbar .dropdown-menu {
            min-width: 160px;
            font-size: 0.85rem;
        }
    }
</style>
