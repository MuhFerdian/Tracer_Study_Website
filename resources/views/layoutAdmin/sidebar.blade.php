<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

        <div class="sidebar-brand">

            <img class="logo-animated"
                src="{{ asset('assets/img/logo_tc5.png') }}"
                alt="Logo">

            <div class="sidebar-brand-title">
                Tracer <span>Study</span>
            </div>

            <div class="sidebar-brand-subtitle">
                Teknologi Informasi
            </div>

            @php
                $role = auth()->user()->role->role_nama ?? '';

                // Kelompokkan kondisi route di sini agar rapi dan tidak duplikat
                $onSurvey     = request()->is('admin/alumni-belum-mengisi')
                             || request()->is('admin/alumni-sudah-mengisi');

                $onAlumni     = request()->is('admin/alumni')
                             || request()->is('admin/alumni/*');

                $onDosen      = request()->is('admin/manajemen-dosen')
                             || request()->is('admin/manajemen-dosen/*');

                $onPertanyaan = request()->is('admin/pertanyaan')
                             || request()->is('admin/pertanyaan/*');

                $onLowongan   = request()->is('admin/lowongan-pekerjaan*');

                $onDashboard  = request()->is('admin')
                             && !$onSurvey && !$onAlumni
                             && !$onDosen  && !$onPertanyaan && !$onLowongan;
            @endphp

            <div class="sidebar-role">
                {{ $role == 'Admin' ? 'Admin Panel' : 'Dosen Panel' }}
            </div>

        </div>

        <div class="sb-sidenav-menu">
            <div class="nav">

                {{-- HEADING --}}
                <div class="sb-sidenav-menu-heading">Utama</div>

                {{-- DASHBOARD --}}
                <a class="nav-link {{ $onDashboard ? 'active' : '' }}"
                    href="{{ url('/admin') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-house"></i>
                    </div>

                    Dashboard
                </a>

                {{-- SURVEI ALUMNI --}}
                {{-- BUG FIX: Hilangkan hardcoded "collapsed" di dalam class="" --}}
                {{-- Sebelumnya: class="nav-link collapsed {{ ... ? '' : 'collapsed' }}" --}}
                {{-- → selalu ada kata "collapsed" walaupun harusnya terbuka            --}}
                <a class="nav-link {{ $onSurvey ? '' : 'collapsed' }}"
                    href="#"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseSurvey"
                    aria-expanded="{{ $onSurvey ? 'true' : 'false' }}"
                    aria-controls="collapseSurvey">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-square-poll-vertical"></i>
                    </div>

                    Survei Alumni

                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>
                </a>

                {{-- ISI MENU SURVEI --}}
                <div class="collapse {{ $onSurvey ? 'show' : '' }}"
                    id="collapseSurvey"
                    data-bs-parent="#sidenavAccordion">

                    <nav class="sb-sidenav-menu-nested nav">

                        <a class="nav-link {{ request()->is('admin/alumni-belum-mengisi') ? 'active' : '' }}"
                            href="{{ url('/admin/alumni-belum-mengisi') }}">
                            <i class="fas fa-user-clock me-2"></i>
                            Alumni Belum Mengisi
                        </a>

                        <a class="nav-link {{ request()->is('admin/alumni-sudah-mengisi') ? 'active' : '' }}"
                            href="{{ url('/admin/alumni-sudah-mengisi') }}">
                            <i class="fas fa-user-check me-2"></i>
                            Alumni Sudah Mengisi
                        </a>

                    </nav>
                </div>

                {{-- HEADING --}}
                <div class="sb-sidenav-menu-heading">
                    Manajemen Data
                </div>

                {{-- KHUSUS ADMIN --}}
                @if($role == 'Admin')

                    <a class="nav-link {{ $onDosen ? 'active' : '' }}"
                        href="{{ url('admin/manajemen-dosen') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>

                        Manajemen Dosen
                    </a>

                @endif

                {{-- MANAJEMEN ALUMNI --}}
                {{-- BUG FIX: Tambah wildcard (*) agar subpage (create/edit) juga active --}}
                <a class="nav-link {{ $onAlumni ? 'active' : '' }}"
                    href="{{ url('admin/alumni') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-users"></i>
                    </div>

                    Manajemen Alumni
                </a>

                {{-- PERTANYAAN --}}
                {{-- BUG FIX: Tambah wildcard (*) agar subpage pertanyaan juga active --}}
                <a class="nav-link {{ $onPertanyaan ? 'active' : '' }}"
                    href="{{ url('admin/pertanyaan') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-circle-question"></i>
                    </div>

                    Pertanyaan
                </a>

                {{-- LOWONGAN PEKERJAAN --}}
                <a class="nav-link {{ $onLowongan ? 'active' : '' }}"
                    href="{{ url('admin/lowongan-pekerjaan') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>

                    Lowongan Pekerjaan
                </a>

            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Tracer Study {{ $role }}
        </div>
    </nav>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* ============================================================
       SIDEBAR BASE
    ============================================================ */
    .sb-sidenav {
        font-family: 'Poppins', sans-serif;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .sb-sidenav-dark {
        background-color: #0f2b66 !important;
    }

    /* ============================================================
       BRAND HEADER
    ============================================================ */
    .sidebar-brand {
        padding: 24px 12px 22px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 10px;
    }

    .sidebar-brand img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin-bottom: 16px;
        transition: transform .3s ease;
        filter: drop-shadow(0 0 20px rgba(168,85,247,.35));
    }

    .sidebar-brand img:hover {
        transform: scale(1.06);
    }

    .sidebar-brand-title {
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.1;
        color: #ffffff;
        margin-bottom: 6px;
    }

    .sidebar-brand-title span {
        color: #f5e6c8;
    }

    .sidebar-brand-subtitle {
        font-size: 0.62rem;
        color: rgba(255,255,255,0.72);
        font-weight: 500;
        letter-spacing: 1.2px;
        margin-bottom: 10px;
    }

    .sidebar-role {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.85);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: .5px;
        backdrop-filter: blur(10px);
    }

    /* ============================================================
       MENU ITEMS
    ============================================================ */
    .sb-sidenav-menu-heading {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.46);
        text-transform: uppercase;
        padding: 1rem 1rem 0.5rem;
    }

    .sb-sidenav .nav-link {
        font-family: 'Poppins', sans-serif;
        font-size: 0.92rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.76) !important;
        border-left: 4px solid transparent;
        border-radius: 8px;
        margin: 0.25rem 0.6rem;
        padding: 0.78rem 0.9rem;
        white-space: normal !important;
        overflow-wrap: anywhere;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition:
            background-color 0.2s ease,
            color 0.2s ease,
            box-shadow 0.2s ease;
        will-change: background-color;
    }

    .sb-sidenav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #ffffff !important;
        border-left-color: transparent;
        transform: none !important;
    }

    .sb-sidenav .nav-link.active {
        background: linear-gradient(90deg, #2563eb, #1d4ed8);
        color: #ffffff !important;
        border-left-color: #93c5fd;
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.28);
    }

    .sb-sidenav .nav-link.active i,
    .sb-sidenav .nav-link.active .sb-nav-link-icon {
        color: #ffffff !important;
    }

    .sb-sidenav .nav-link i {
        width: 1.4rem;
        font-size: 1rem;
    }

    /* ============================================================
       NESTED (SUBMENU)
    ============================================================ */
    .sb-sidenav-menu-nested .nav-link {
        padding-left: 2.25rem !important;
        font-size: 0.82rem;
        line-height: 1.3;
        margin: 0.2rem 0.6rem;
        background-color: rgba(255, 255, 255, 0.04);
    }

    /* ============================================================
       COLLAPSE ARROW — Override styles.css (rotate -90deg) dengan perilaku
       yang lebih intuitif: panah atas = menu terbuka, panah bawah = tertutup
    ============================================================ */

    /* Saat menu TERBUKA (:not(.collapsed)) → panah menghadap ke atas */
    .sb-sidenav .sb-sidenav-menu .nav .nav-link:not(.collapsed) .sb-sidenav-collapse-arrow {
        transform: rotate(-180deg);
        transition: transform 0.18s ease;
    }

    /* Saat menu TERTUTUP (.collapsed) → panah menghadap ke bawah */
    /* Override styles.css default rotate(-90deg) menjadi rotate(0deg) */
    .sb-sidenav .sb-sidenav-menu .nav .nav-link.collapsed .sb-sidenav-collapse-arrow {
        transform: rotate(0deg);
        transition: transform 0.18s ease;
    }

    /* ============================================================
       FOOTER
    ============================================================ */
    .sb-sidenav-footer {
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.62);
        padding: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background-color: rgba(0, 0, 0, 0.2);
    }

    /* ============================================================
       SCROLLBAR
    ============================================================ */
    .sb-sidenav-menu::-webkit-scrollbar {
        width: 4px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-track {
        background: #0f2b66;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.28);
        border-radius: 10px;
    }

    /* ============================================================
       ANTI-FLICKER / PERFORMANCE
    ============================================================ */
    #layoutSidenav_nav,
    .sb-sidenav,
    .sb-sidenav-menu,
    .sb-sidenav-menu .nav {
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        transform: translateZ(0);
    }

    .sb-sidenav * {
        box-sizing: border-box;
    }

    .sb-sidenav-menu {
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    /* Smooth Bootstrap collapse animation */
    .collapse {
        transition: height 0.16s ease-out !important;
    }

    #layoutSidenav_nav {
        contain: layout style paint;
    }

</style>