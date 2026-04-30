<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-header d-flex flex-column align-items-center"
            style="padding-top: 0.3rem; padding-bottom: 0.3rem;">
            <img class="logo-animated" src="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/logoTC.png') }}"
                alt="Logo" style="height: 80px; margin-right: 5px;">
            <div class="text-white fw-semibold" style="font-size: 1.1rem;">Admin Panel</div>
            <div class="text-muted" style="font-size: 0.85rem;">Teknologi Informasi</div>
        </div>
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link" href="{{ url('/admin') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseRekapData"
                    aria-expanded="false" aria-controls="collapseRekapData">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    Rekap Data
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="collapseRekapData" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="accordionRekap">

                        {{-- Belum Mengisi --}}
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseBelumMengisi" aria-expanded="false"
                            aria-controls="collapseBelumMengisi">
                            <i class="fas fa-clock me-2"></i> Belum Mengisi
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseBelumMengisi" data-bs-parent="#accordionRekap">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="{{ url('/admin/alumni-belum-mengisi') }}">
                                    <i class="fas fa-user-graduate me-2"></i> Rekap Alumni
                                </a>
                            </nav>
                        </div>

                        {{-- Sudah Mengisi --}}
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseSudahMengisi" aria-expanded="false"
                            aria-controls="collapseSudahMengisi">
                            <i class="fas fa-check-circle me-2"></i> Sudah Mengisi
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseSudahMengisi" data-bs-parent="#accordionRekap">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="{{ url('/admin/alumni-sudah-mengisi') }}">
                                    <i class="fas fa-user-check me-2"></i> Survei Alumni
                                </a>
                            </nav>
                        </div>

                    </nav>
                </div>

                <div class="sb-sidenav-menu-heading">Manajemen Data</div>

                <a class="nav-link" href="{{ url('admin/manajemen-dosen') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chalkboard-user"></i></div>
                    Manajemen Dosen
                </a>
                <a class="nav-link" href="{{ url('admin/alumni') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                    Manajemen Alumni
                </a>
                <a class="nav-link" href="{{ url('admin/pertanyaan') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-question-circle"></i></div>
                    Pertanyaan
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Tracer Study Admin
        </div>
    </nav>
</div>

<style>
    /* Tambahan CSS untuk UI yang lebih bagus */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    .sb-nav-fixed .sb-topnav {
        z-index: 1000;
        position: fixed;
        top: 0;
        width: 100%;
    }

    #layoutSidenav #layoutSidenav_nav {
        z-index: 1050;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
    }

    /* Terapkan font ke seluruh sidebar */
    .sb-sidenav {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
    }

    .sb-sidenav-dark {
        background-color: #0f172a !important;
    }

    .sb-sidenav-menu-heading {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: #94a3b8;
        text-transform: uppercase;
        padding: 1rem 1.5rem 0.5rem;
    }

    .nav-link {
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        color: #cbd5e0;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        margin: 0.25rem 0.75rem;
        padding: 0.7rem 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nav-link:hover {
        background: linear-gradient(90deg, #2d3748, #1e293b);
        color: #ffffff;
        border-left: 4px solid #3b82f6;
        transform: translateX(4px);
    }

    .nav-link.active {
        background: linear-gradient(90deg, #1e293b, #0f172a);
        color: #ffffff;
        font-weight: 600;
        border-left: 4px solid #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Icon styling */
    .nav-link i {
        width: 1.75rem;
        font-size: 1.1rem;
        transition: transform 0.2s ease;
    }

    .nav-link:hover i {
        transform: scale(1.05);
    }

    /* Nested menu styling */
    .sb-sidenav-menu-nested .nav-link {
        padding-left: 2.5rem !important;
        font-size: 0.85rem;
        margin: 0.15rem 0.75rem;
    }

    .sb-sidenav-menu-nested .nav-link:hover {
        transform: translateX(2px);
    }

    /* Collapse arrow animation */
    .sb-sidenav-collapse-arrow i {
        transition: transform 0.3s ease;
    }

    .collapsed .sb-sidenav-collapse-arrow i {
        transform: rotate(0deg);
    }

    .sb-sidenav-collapse-arrow i {
        transform: rotate(-180deg);
    }

    /* Header area */
    .sb-sidenav-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 0.5rem;
    }

    .logo-animated {
        transition: transform 0.3s ease;
    }

    .logo-animated:hover {
        transform: scale(1.05);
    }

    /* Footer */
    .sb-sidenav-footer {
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        color: #94a3b8;
        padding: 1rem 1.2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background-color: rgba(0, 0, 0, 0.2);
    }

    /* Scrollbar styling */
    .sb-sidenav-menu::-webkit-scrollbar {
        width: 4px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-track {
        background: #1e293b;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 10px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    /* Badge / icon tambahan untuk menu baru */
    .fa-answers {
        font-family: 'Font Awesome 6 Free';
        content: "\f0e6";
    }

    /* Efek glassmorphism pada sidebar */
    @supports (backdrop-filter: blur(10px)) {
        .sb-sidenav {
            background: linear-gradient(135deg, #0f172acc 0%, #1e293bcc 100%);
            backdrop-filter: blur(10px);
        }
    }
</style>