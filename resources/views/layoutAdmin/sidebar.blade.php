<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-header d-flex flex-column align-items-center"
            style="padding-top: 0.3rem; padding-bottom: 0.3rem;">
            <img class="logo-animated" src="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/logoTC.png') }}"
                alt="Logo" style="height: 80px; margin-right: 5px;">
            {{-- <div class="text-white fw-semibold" style="font-size: 1.1rem;">Admin Panel</div> --}}
            @php
                $role = auth()->user()->role->role_nama ?? '';
            @endphp

            <div class="text-white fw-semibold" style="font-size: 1.1rem;">
                {{ $role == 'Admin' ? 'Admin Panel' : 'Dosen Panel' }}
            </div>

            <div class="text-muted" style="font-size: 0.85rem;">Teknologi Informasi</div>
        </div>
        <div class="sb-sidenav-menu">
            <div class="nav">

                {{-- HEADING --}}
                <div class="sb-sidenav-menu-heading">Utama</div>

                {{-- DASHBOARD --}}
                <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-house"></i>
                    </div>

                    Dashboard
                </a>

                {{-- SURVEI ALUMNI --}}
                <a class="nav-link collapsed
        {{ request()->is('admin/alumni-belum-mengisi') || request()->is('admin/alumni-sudah-mengisi') ? '' : 'collapsed' }}"
                    href="#" data-bs-toggle="collapse" data-bs-target="#collapseSurvey"
                    aria-expanded="{{ request()->is('admin/alumni-belum-mengisi') || request()->is('admin/alumni-sudah-mengisi') ? 'true' : 'false' }}"
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
                <div class="collapse
        {{ request()->is('admin/alumni-belum-mengisi') || request()->is('admin/alumni-sudah-mengisi') ? 'show' : '' }}"
                    id="collapseSurvey" data-bs-parent="#sidenavAccordion">

                    <nav class="sb-sidenav-menu-nested nav">

                        {{-- BELUM MENGISI --}}
                        <a class="nav-link
                {{ request()->is('admin/alumni-belum-mengisi') ? 'active' : '' }}"
                            href="{{ url('/admin/alumni-belum-mengisi') }}">

                            <i class="fas fa-user-clock me-2"></i>
                            Alumni Belum Mengisi
                        </a>

                        {{-- SUDAH MENGISI --}}
                        <a class="nav-link
                {{ request()->is('admin/alumni-sudah-mengisi') ? 'active' : '' }}"
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

                            <a class="nav-link
                    {{ request()->is('admin/manajemen-dosen') ? 'active' : '' }}" href="{{ url('admin/manajemen-dosen') }}">

                                <div class="sb-nav-link-icon">
                                    <i class="fas fa-chalkboard-user"></i>
                                </div>

                                Manajemen Dosen
                            </a>

                @endif

                {{-- MANAJEMEN ALUMNI --}}
                <a class="nav-link
        {{ request()->is('admin/alumni') ? 'active' : '' }}" href="{{ url('admin/alumni') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-users"></i>
                    </div>

                    Manajemen Alumni
                </a>

                {{-- PERTANYAAN --}}
                <a class="nav-link
        {{ request()->is('admin/pertanyaan') ? 'active' : '' }}" href="{{ url('admin/pertanyaan') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-circle-question"></i>
                    </div>

                    Pertanyaan
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

    .sb-sidenav {
        font-family: 'Poppins', sans-serif;
        overflow-y: auto;
    }

    .sb-sidenav-dark {
        background-color: #0f2b66 !important;
    }

    .sb-sidenav-header {
        border-bottom: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 0.5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.8rem;
    }

    .logo-animated {
        transition: all 0.3s ease;
    }

    .logo-animated:hover {
        transform: scale(1.05);
    }

    .sb-sidenav-menu-heading {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.46);
        text-transform: uppercase;
        padding: 1rem 1rem 0.5rem;
    }

    .sb-sidenav .nav-link {
        font-family: 'Poppins', sans-serif;
        font-size: 0.92rem;
        font-weight: 600;
        color: rgba(255,255,255,0.76) !important;
        border-left: 4px solid transparent;
        transition: all 0.25s ease;
        border-radius: 8px;
        margin: 0.25rem 0.6rem;
        padding: 0.78rem 0.9rem;
        white-space: normal !important;
        overflow-wrap: anywhere;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sb-sidenav .nav-link:hover {
        background-color: rgba(255,255,255,0.08);
        color: #ffffff !important;
        transform: none;
        border-left-color: transparent;
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

    .sb-sidenav-menu-nested .nav-link {
        padding-left: 2.25rem !important;
        font-size: 0.82rem;
        line-height: 1.3;
        margin: 0.2rem 0.6rem;
        background-color: rgba(255,255,255,0.04);
    }

    .sb-sidenav-collapse-arrow i {
        transition: transform 0.3s ease;
        color: rgba(255,255,255,0.72);
    }

    .collapsed .sb-sidenav-collapse-arrow i {
        transform: rotate(0deg);
    }

    .sb-sidenav-collapse-arrow i {
        transform: rotate(-180deg);
    }

    .sb-sidenav-footer {
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        color: rgba(255,255,255,0.62);
        padding: 1rem;
        border-top: 1px solid rgba(255,255,255,0.08);
        background-color: rgba(0,0,0,0.2);
    }

    .sb-sidenav-menu::-webkit-scrollbar {
        width: 4px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-track {
        background: #0f2b66;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.28);
        border-radius: 10px;
    }
</style>
