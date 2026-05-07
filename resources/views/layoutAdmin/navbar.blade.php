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
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-gear me-2"></i>
                        Pengaturan
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-clock-rotate-left me-2"></i>
                        Aktivitas
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
