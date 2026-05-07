<nav class="navbar navbar-expand-lg navbar-dark landing-navbar fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3" href="{{ url('/') }}">
            <img src="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/Logo_Polije2.png') }}" alt="Logo Politeknik Negeri Jember">
            <div class="d-flex flex-column">
                <span class="landing-brand-title">Tracer Study</span>
                <small class="landing-brand-subtitle">Jurusan Teknologi Informasi</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link active" href="/">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#about-tracer">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="#manfaat">Manfaat</a></li>
                <li class="nav-item"><a class="nav-link" href="#tentangkami">Kontak</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn nav-login-btn rounded-pill px-4" href="{{ url('/login') }}">
                        <i class="fas fa-right-to-bracket me-2"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
