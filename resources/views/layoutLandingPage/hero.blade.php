@extends('layoutLandingPage.app')

@section('content')
<section class="hero-section" id="beranda">
    <div class="container hero-content">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-white">
                <div class="hero-badge mb-4 animate__animated animate__fadeInDown">
                    <i class="fas fa-chart-line"></i>
                    Portal pelacakan alumni
                </div>

                <h1 class="hero-title fw-bold mb-4 animate__animated animate__fadeInDown">
                    Tracer Study <span class="text-gradient-blue">Politeknik Negeri Jember</span>
                </h1>

                <p class="hero-copy mb-4 animate__animated animate__fadeIn animate__delay-1s">
                    Bantu kampus membaca perjalanan alumni, kebutuhan industri, dan kualitas kurikulum melalui data
                    yang rapi, cepat, dan mudah diakses.
                </p>

                <div class="hero-actions mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="{{ url('/login') }}" class="btn btn-blue-glow rounded-pill px-4 py-3">
                        <i class="fas fa-user-graduate me-2"></i>Mulai Isi Tracer
                    </a>
                    <a href="#about-tracer" class="btn btn-outline-glass rounded-pill px-4 py-3">
                        <i class="fas fa-circle-info me-2"></i>Lihat Detail
                    </a>
                </div>

                <div class="hero-stat-grid animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="hero-stat hero-stat--hover">
                        <strong>Alumni</strong>
                        <span>Riwayat lulusan lebih mudah dilacak</span>
                    </div>
                    <div class="hero-stat hero-stat--hover">
                        <strong>Data</strong>
                        <span>Rekap survey tersusun otomatis</span>
                    </div>
                    <div class="hero-stat hero-stat--hover">
                        <strong>Mutu</strong>
                        <span>Evaluasi kurikulum lebih terukur</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 position-relative">
                <div class="hero-visual animate__animated animate__fadeInRight animate__delay-1s">
                    <img src="{{ asset('assets/img/ilustrasi.png') }}"
                        alt="Ilustrasi Tracer Study">
                    <div class="hero-visual-note">
                        <i class="fas fa-shield-halved"></i>
                        <div>
                            <strong>Portal resmi JTI Polije</strong>
                            <div class="small text-muted">Data alumni, dosen, dan rekap survey dalam satu sistem.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layoutLandingPage.about')
@include('layoutLandingPage.profil')
@include('layoutLandingPage.dosen')
@include('layoutLandingPage.lowongan', ['lowongan' => $lowongan])
@include('layoutLandingPage.faq')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const animateElements = document.querySelectorAll('.animate__animated');
        const animateOnScroll = function () {
            animateElements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                if (elementPosition < windowHeight - 100) {
                    const animationClass = element.classList.item(1);
                    element.classList.add(animationClass);
                }
            });
        };
        window.addEventListener('scroll', animateOnScroll);
        animateOnScroll();
    });
</script>
@endpush
