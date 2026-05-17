<nav class="navbar navbar-expand-lg navbar-dark landing-navbar fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3" href="{{ url('/landingpage') }}">
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
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#about-tracer">Tentang</a></li>
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#profil">Profil</a></li>
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#dosen">Dosen</a></li>
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#lowongan">Lowongan</a></li>
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link nav-scroll-link" href="{{ url('/landingpage') }}#tentangkami">Kontak</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn nav-login-btn rounded-pill px-4" href="{{ url('/login') }}">
                        <i class="fas fa-right-to-bracket me-2"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
(function () {
    var navbar = document.getElementById('mainNavbar');

    // ── Shadow muncul saat scroll ───────────────────────────────
    function handleScroll() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        highlightActiveLink();
    }
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // ── Smooth scroll dengan offset navbar ─────────────────────
    document.addEventListener('click', function (e) {
        var link = e.target.closest('.nav-scroll-link');
        if (!link) return;

        var href = link.getAttribute('href');
        var hashIndex = href.indexOf('#');
        if (hashIndex === -1) return;

        var hash = href.substring(hashIndex);       // "#faq"
        var targetId = hash.substring(1);           // "faq"
        var navH = navbar ? navbar.offsetHeight : 72;

        // Cek apakah section ada di halaman ini
        var target = document.getElementById(targetId);
        if (target) {
            e.preventDefault();
            var top = target.getBoundingClientRect().top + window.scrollY - navH - 16;
            window.scrollTo({ top: top, behavior: 'smooth' });
            history.pushState(null, '', hash);
        }
        // Kalau section tidak ada (beda halaman), biarkan browser navigasi biasa
    });

    // ── Saat halaman load dengan hash di URL ───────────────────
    window.addEventListener('load', function () {
        var navH = navbar ? navbar.offsetHeight : 72;
        if (window.location.hash) {
            var targetId = window.location.hash.substring(1);
            var target = document.getElementById(targetId);
            if (target) {
                setTimeout(function () {
                    var top = target.getBoundingClientRect().top + window.scrollY - navH - 16;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }, 150);
            }
        }
    });

    // ── Highlight link aktif berdasarkan posisi scroll ──────────
    function highlightActiveLink() {
        var sections = ['beranda', 'about-tracer', 'profil', 'dosen', 'lowongan', 'faq', 'tentangkami'];
        var navH = navbar ? navbar.offsetHeight : 72;
        var scrollY = window.scrollY + navH + 32;
        var current = '';

        sections.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.offsetTop <= scrollY) {
                current = id;
            }
        });

        document.querySelectorAll('.nav-scroll-link').forEach(function (link) {
            link.classList.remove('active');
            var href = link.getAttribute('href');
            if (current && href && href.endsWith('#' + current)) {
                link.classList.add('active');
            }
        });
    }
})();
</script>
