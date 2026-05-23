<style>
    /* ── FOOTER HOVER ENHANCEMENTS ── */

    /* Social icons */
    .footer-social {
        transition: transform .25s ease, background .25s ease, box-shadow .25s ease, color .25s ease;
    }
    .footer-social:hover {
        transform: translateY(-4px) scale(1.12);
        background: #ffffff !important;
        color: var(--tracer-navy) !important;
        box-shadow: 0 10px 24px rgba(255,255,255,.22);
    }

    /* Footer nav links */
    .footer-link {
        transition: color .2s ease, transform .2s ease, padding-left .2s ease;
        display: inline-flex;
        align-items: center;
    }
    .footer-link:hover {
        color: #ffffff !important;
        transform: translateX(5px);
        padding-left: 2px;
    }
    .footer-link:hover i {
        color: var(--tracer-cyan);
    }

    /* Contact info rows */
    .footer-contact-row {
        transition: color .2s ease, transform .2s ease;
        cursor: default;
    }
    .footer-contact-row:hover {
        transform: translateX(4px);
    }
    .footer-contact-row:hover p,
    .footer-contact-row:hover i {
        color: rgba(255,255,255,.9) !important;
    }

    /* External links (Tautan Lainnya) */
    .footer-ext-link {
        transition: color .2s ease, transform .2s ease, gap .2s ease;
        border-radius: 8px;
        padding: 6px 8px;
        margin: -6px -8px;
    }
    .footer-ext-link:hover {
        color: #ffffff !important;
        transform: translateX(5px);
        background: rgba(255,255,255,.07);
    }
    .footer-ext-link:hover i {
        color: var(--tracer-cyan);
    }

    /* CTA button */
    .footer-cta .btn-blue-glow {
        transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
    }
    .footer-cta .btn-blue-glow:hover {
        transform: translateY(-3px) scale(1.04);
        box-shadow: 0 18px 40px rgba(98,217,255,.38);
    }

    /* Footer CTA box */
    .footer-cta {
        transition: box-shadow .3s ease;
    }
    .footer-cta:hover {
        box-shadow: 0 0 0 1px rgba(255,255,255,.12), 0 24px 60px rgba(0,0,0,.18);
    }
</style>

<section id="tentangkami">
    <footer class="site-footer pt-5 mt-5">
        <div class="container">
            <div class="footer-cta mb-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">Mari sukseskan pelaksanaan Tracer Study Politeknik Negeri Jember.</h3>
                        <p class="mb-0 text-white-50">
                            Partisipasi alumni membantu kampus melihat kualitas lulusan secara nyata.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ url('/login') }}" class="btn btn-blue-glow rounded-pill px-4 py-3">
                            <i class="fas fa-clipboard-check me-2"></i>Isi Survey
                        </a>
                    </div>
                </div>
            </div>

            <div class="row gy-4 text-white pb-4">
                <div class="col-md-3">
                    <img src="{{ asset('assets/img/logo-jti-alt.png') }}"
                        alt="Logo Polije" width="300" class="mb-3">
                    <h5 class="fw-bold mb-3">Tracer Study</h5>
                    <p class="text-white-50 mb-4">
                        Portal pelacakan alumni Jurusan Teknologi Informasi Politeknik Negeri Jember.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="https://www.tiktok.com/@tif.nganjuk" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="https://www.youtube.com/@TIFNganjuk" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.linkedin.com/school/politeknik-negeri-jember/" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://www.instagram.com/tif.nganjuk/" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom border-white border-opacity-25">Tentang Kami</h6>
                    <div class="d-flex mb-3 footer-contact-row">
                        <i class="fas fa-university mt-1 me-3"></i>
                        <p class="mb-0 text-white-50">Politeknik Negeri Jember</p>
                    </div>
                    <div class="d-flex mb-3 footer-contact-row">
                        <i class="fas fa-map-marker-alt mt-1 me-3"></i>
                        <p class="mb-0 text-white-50">Jl. Gatot Subroto No 02, Kauman, Kecamatan Nganjuk, Kabupaten Nganjuk, Jawa Timur</p>
                    </div>
                    <div class="d-flex footer-contact-row">
                        <i class="fas fa-envelope mt-1 me-3"></i>
                        <p class="mb-0 text-white-50">
                            <a href="mailto:tracer.study@polije.ac.id" class="footer-link">tracerstudy.ts@gmail.com</a>
                        </p>
                    </div>
                </div>

                <div class="col-md-2">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom border-white border-opacity-25">Tautan</h6>
                    <ul class="list-unstyled d-grid gap-2">
                        <li>
                            <a href="{{ url('/landingpage') }}#beranda" class="footer-link d-inline-flex align-items-center">
                                <i class="fas fa-home me-2"></i>Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/landingpage') }}#about-tracer" class="footer-link d-inline-flex align-items-center">
                                <i class="fas fa-circle-info me-2"></i>Tentang
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/landingpage') }}#profil" class="footer-link d-inline-flex align-items-center">
                                <i class="fas fa-university me-2"></i>Profil
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/landingpage') }}#dosen" class="footer-link d-inline-flex align-items-center">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Dosen
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/landingpage') }}#lowongan" class="footer-link d-inline-flex align-items-center">
                                <i class="fas fa-briefcase me-2"></i>Lowongan
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/landingpage') }}#faq" class="footer-link d-inline-flex align-items-center">
                                <i class="fas fa-question-circle me-2"></i>FAQ
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom border-white border-opacity-25">Tautan Lainnya</h6>
                    <a href="https://polije.ac.id" target="_blank" rel="noopener noreferrer" class="footer-link footer-ext-link d-flex gap-3 mb-3">
                        <i class="fas fa-globe mt-1"></i>
                        <span>Politeknik Negeri Jember</span>
                    </a>
                    <a href="https://jti.polije.ac.id" target="_blank" rel="noopener noreferrer" class="footer-link footer-ext-link d-flex gap-3">
                        <i class="fas fa-laptop-code mt-1"></i>
                        <span>Jurusan Teknologi Informasi</span>
                    </a>
                </div>
            </div>

            <hr class="border-light border-opacity-25">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 py-3 text-white-50 small">
                <span>&copy; 2026 Tracer Study Politeknik Negeri Jember. All Rights Reserved.</span>
                <span>Jurusan Teknologi Informasi</span>
            </div>
        </div>

        <a href="https://wa.me/6289603902466?text=Halo%20Admin%20Prodi%20TIF%20Nganjuk%2C%20saya%20ingin%20bertanya%20mengenai%20Tracer%20Study."
            class="wa-float"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Chat WhatsApp Admin Prodi">
            <span class="wa-tooltip">Hubungi Admin!</span>
            <i class="fab fa-whatsapp"></i>
        </a>

        <a href="#" class="scroll-top" aria-label="Kembali ke atas">
            <i class="fas fa-arrow-up"></i>
        </a>
    </footer>
</section>
