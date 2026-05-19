@php use Illuminate\Support\Facades\Storage; use Carbon\Carbon;

// Helper: format gaji → Rp 60.000.000
function formatGajiDetail(string $gaji): string {
    $clean = preg_replace('/[^0-9]/', '', $gaji);
    if ($clean !== '' && is_numeric($clean)) {
        return 'Rp ' . number_format((int)$clean, 0, ',', '.');
    }
    return $gaji;
}
@endphp

@extends('layoutLandingPage.app')

@push('styles')
<style>
    .loker-detail-wrap { padding-top: 80px; }

    /* ── HERO ── */
    .loker-detail-hero {
        background: linear-gradient(135deg, #071a42 0%, #1557c0 60%, #17a4df 100%);
        border-radius: var(--tracer-radius);
        padding: 36px 32px;
        position: relative;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 20px 55px rgba(8,34,79,.22);
    }
    .loker-detail-hero::before {
        content: '';
        position: absolute;
        width: 240px; height: 240px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
        top: -120px; right: -70px;
    }
    .loker-detail-hero::after {
        content: '';
        position: absolute;
        width: 170px; height: 170px;
        background: rgba(255,255,255,.04);
        border-radius: 50%;
        bottom: -80px; left: -40px;
    }
    .loker-detail-hero-inner { position: relative; z-index: 2; }

    /* ── FOTO BANNER ── */
    .loker-foto-wrap { overflow: hidden; border-radius: var(--tracer-radius) var(--tracer-radius) 0 0; }
    .loker-foto-banner {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
        cursor: pointer;
        transition: transform .35s ease;
    }
    .loker-foto-banner:hover { transform: scale(1.02); }
    .loker-foto-placeholder {
        width: 100%;
        height: 220px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #93c5fd;
        font-size: 4rem;
        border-radius: var(--tracer-radius) var(--tracer-radius) 0 0;
    }

    /* ── MAIN CARD ── */
    .loker-main-card {
        padding: 0;
        overflow: hidden;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 24px rgba(8,34,79,.10) !important;
    }
    .loker-main-body { padding: 1.75rem 2rem; }

    /* ── COMPANY BADGE ── */
    .loker-company-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(37,130,243,.1);
        border: 1px solid rgba(37,130,243,.12);
        color: var(--tracer-blue);
        border-radius: 999px;
        padding: 4px 14px;
        font-size: .78rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .loker-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        border-radius: 999px;
        padding: 5px 14px;
        font-size: .78rem;
        font-weight: 700;
    }

    /* ── INFO GRID ── */
    .loker-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin: 20px 0;
    }
    .loker-info-box {
        background: #f8fafc !important;
        border-radius: 14px;
        padding: 16px;
        border: 1px solid #e2e8f0 !important;
        transition: .25s;
    }
    .loker-info-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(8,34,79,.08);
    }
    .loker-info-label {
        font-size: .7rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }
    .loker-info-value {
        font-size: .9rem;
        font-weight: 700;
        color: var(--tracer-ink);
    }
    .loker-info-value.salary { color: #16a34a; }
    .loker-info-value.deadline { color: #dc2626; }

    /* ── DIVIDER ── */
    .loker-divider {
        border: none;
        border-top: 1px solid var(--tracer-line);
        margin: 22px 0;
    }

    /* ── SECTION TITLE ── */
    .loker-sub-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--tracer-ink);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* ── DESKRIPSI ── */
    .loker-deskripsi {
        background: rgba(248,250,252,.9);
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid var(--tracer-line);
        color: var(--tracer-muted);
        line-height: 1.85;
        font-size: .9rem;
        white-space: pre-line;
    }

    /* ── LINK BOX ── */
    .loker-link-box {
        background: rgba(248,250,252,.9);
        border-radius: 14px;
        padding: 14px 18px;
        border: 1px solid var(--tracer-line);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    /* ── BUTTONS ── */
    .btn-lamar {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: linear-gradient(135deg, var(--tracer-blue), var(--tracer-blue-2));
        color: white;
        border: none;
        border-radius: 999px;
        padding: 12px 28px;
        font-size: .9rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 12px 28px rgba(37,130,243,.25);
        transition: .25s;
    }
    .btn-lamar:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 18px 40px rgba(37,130,243,.35);
    }
    .btn-secondary-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,.72);
        color: var(--tracer-muted);
        border: 1px solid var(--tracer-line);
        border-radius: 999px;
        padding: 12px 22px;
        font-size: .88rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        backdrop-filter: blur(10px);
        transition: .25s;
    }
    .btn-secondary-action:hover {
        background: rgba(37,130,243,.08);
        border-color: rgba(37,130,243,.2);
        color: var(--tracer-blue);
    }

    /* ── LIGHTBOX ── */
    .lb-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.82);
        backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .lb-overlay.show { display: flex; animation: lbIn .2s ease; }
    .lb-img {
        max-width: 88vw;
        max-height: 85vh;
        border-radius: 16px;
        box-shadow: 0 0 60px rgba(0,0,0,.5);
        animation: lbZoom .2s ease;
    }
    .lb-close {
        position: absolute;
        top: 20px; right: 28px;
        color: white;
        font-size: 2.2rem;
        cursor: pointer;
        opacity: .85;
        line-height: 1;
        transition: opacity .2s;
    }
    .lb-close:hover { opacity: 1; }
    @keyframes lbIn   { from { opacity: 0; } to { opacity: 1; } }
    @keyframes lbZoom { from { transform: scale(.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    /* ── RESPONSIVE ── */
    @media(max-width: 768px) {
        .loker-main-body { padding: 1.25rem 1.1rem; }
        .loker-foto-banner { height: 200px; }
        .loker-info-grid { grid-template-columns: 1fr 1fr; }
    }
    @media(max-width: 480px) {
        .loker-info-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="loker-detail-wrap">

{{-- ── HERO ── --}}
<section class="section-pad" style="padding-top:0; padding-bottom:0;">
    <div class="container">
        <div class="loker-detail-hero animate__animated animate__fadeInDown">
            <div class="loker-detail-hero-inner">

                <a href="{{ url('/lowongan') }}"
                   class="btn btn-outline-glass rounded-pill px-4 py-2 mb-4 d-inline-flex align-items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Lowongan
                </a>

                <h1 class="section-title text-white mb-2">{{ $lowongan->posisi }}</h1>
                <p class="mb-0" style="color:rgba(255,255,255,.82); font-size:.95rem;">
                    <i class="fas fa-building me-2"></i>{{ $lowongan->nama_perusahaan }}
                </p>

            </div>
        </div>
    </div>
</section>

{{-- ── DETAIL ── --}}
<section class="benefit-section section-pad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 animate__animated animate__fadeInUp">

                <div class="benefit-card loker-main-card">

                    {{-- FOTO BANNER --}}
                    @if($lowongan->foto)
                    @php
                        $fotoUrl = str_starts_with($lowongan->foto, 'startbootstrap') || str_starts_with($lowongan->foto, 'foto_loker')
                            ? asset($lowongan->foto)
                            : \Illuminate\Support\Facades\Storage::url($lowongan->foto);
                    @endphp
                    <div class="loker-foto-wrap">
                        <img
                            src="{{ $fotoUrl }}"
                            alt="{{ $lowongan->posisi }}"
                            class="loker-foto-banner"
                            onclick="openLb(this.src)"
                            title="Klik untuk perbesar"
                        >
                    </div>
                    @else
                    <div class="loker-foto-placeholder">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    @endif

                    <div class="loker-main-body">

                        {{-- HEADER --}}
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-2">
                            <div>
                                <span class="loker-company-tag">
                                    <i class="fas fa-building"></i>
                                    {{ $lowongan->nama_perusahaan }}
                                </span>
                                <h2 style="font-size:1.6rem; font-weight:800; color:var(--tracer-ink); margin-bottom:0; line-height:1.3;">
                                    {{ $lowongan->posisi }}
                                </h2>
                            </div>
                            @php
                                $batasHeader = $lowongan->batas_lamaran ? Carbon::parse($lowongan->batas_lamaran) : null;
                                $isOpen = !$batasHeader || !$batasHeader->isPast();
                            @endphp
                            <span class="loker-status-badge" style="{{ $isOpen ? '' : 'background:#fef2f2; color:#dc2626; border-color:#fecaca;' }}">
                                <i class="fas fa-circle" style="font-size:.45rem;"></i>
                                {{ $isOpen ? 'Lowongan Aktif' : 'Sudah Ditutup' }}
                            </span>
                        </div>

                        {{-- INFO GRID --}}
                        <div class="loker-info-grid">

                            <div class="loker-info-box">
                                <div class="benefit-icon" style="width:36px;height:36px;border-radius:11px;font-size:.82rem;margin-bottom:10px;">
                                    <i class="fas fa-location-dot"></i>
                                </div>
                                <div class="loker-info-label">Lokasi</div>
                                <div class="loker-info-value">{{ $lowongan->lokasi ?? '-' }}</div>
                            </div>

                            <div class="loker-info-box">
                                <div class="benefit-icon" style="width:36px;height:36px;border-radius:11px;font-size:.82rem;margin-bottom:10px;">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="loker-info-label">Gaji</div>
                                <div class="loker-info-value" style="color:var(--tracer-blue);">
                                    {{ $lowongan->gaji ? formatGajiDetail($lowongan->gaji) : 'Negosiasi' }}
                                </div>
                            </div>

                            <div class="loker-info-box">
                                @php
                                    $batas      = $lowongan->batas_lamaran ? Carbon::parse($lowongan->batas_lamaran) : null;
                                    $sudahLewat = $batas ? $batas->isPast() : false;
                                    $deadlineColor = $sudahLewat ? '#dc2626' : '#16a34a';
                                    $deadlineIcon  = $sudahLewat ? 'fa-calendar-xmark' : 'fa-calendar-check';
                                    $deadlineBg    = $sudahLewat
                                        ? 'background:linear-gradient(135deg,#dc2626,#ef4444)'
                                        : 'background:linear-gradient(135deg,#16a34a,#22c55e)';
                                @endphp
                                <div class="benefit-icon" style="width:36px;height:36px;border-radius:11px;font-size:.82rem;margin-bottom:10px;{{ $deadlineBg }};">
                                    <i class="fas {{ $deadlineIcon }}"></i>
                                </div>
                                <div class="loker-info-label">Batas Lamaran</div>
                                <div class="loker-info-value" style="color:{{ $deadlineColor }};">
                                    {{ $batas ? $batas->format('d M Y') : '-' }}
                                    @if($batas)
                                    <div style="font-size:.72rem; font-weight:500; margin-top:2px; color:{{ $deadlineColor }}; opacity:.8;">
                                        {{ $sudahLewat ? 'Sudah ditutup' : 'Masih buka' }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @if($lowongan->kontak)
                            <div class="loker-info-box">
                                <div class="benefit-icon" style="width:36px;height:36px;border-radius:11px;font-size:.82rem;margin-bottom:10px;background:linear-gradient(135deg,#16a34a,#22c55e);">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="loker-info-label">Kontak</div>
                                <div class="loker-info-value">{{ $lowongan->kontak }}</div>
                            </div>
                            @endif

                        </div>

                        <hr class="loker-divider">

                        {{-- DESKRIPSI --}}
                        @if($lowongan->deskripsi)
                        <div class="mb-4">
                            <h5 class="loker-sub-title">
                                <span class="benefit-icon" style="width:30px;height:30px;border-radius:9px;font-size:.75rem;">
                                    <i class="fas fa-align-left"></i>
                                </span>
                                Deskripsi Pekerjaan
                            </h5>
                            <div class="loker-deskripsi">{{ $lowongan->deskripsi }}</div>
                        </div>
                        @endif

                        {{-- LINK LAMARAN --}}
                        @if($lowongan->link_lamaran)
                        <div class="mb-4">
                            <h5 class="loker-sub-title">
                                <span class="benefit-icon" style="width:30px;height:30px;border-radius:9px;font-size:.75rem;background:linear-gradient(135deg,#16a34a,#22c55e);">
                                    <i class="fas fa-link"></i>
                                </span>
                                Link Lamaran
                            </h5>
                            <div class="loker-link-box">
                                <div class="benefit-icon" style="width:40px;height:40px;border-radius:13px;font-size:.9rem;background:linear-gradient(135deg,#16a34a,#22c55e);flex-shrink:0;">
                                    <i class="fas fa-external-link-alt"></i>
                                </div>
                                <div>
                                    <div class="loker-info-label mb-1">Klik untuk melamar</div>
                                    <a href="{{ $lowongan->link_lamaran }}" target="_blank"
                                       class="text-primary fw-semibold text-break" style="font-size:.88rem;">
                                        {{ $lowongan->link_lamaran }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <hr class="loker-divider">

                        {{-- ACTION BUTTONS --}}
                        <div class="d-flex flex-wrap gap-3 align-items-center">

                            @if($lowongan->link_lamaran)
                            <a href="{{ $lowongan->link_lamaran }}" target="_blank" class="btn-lamar">
                                <i class="fas fa-paper-plane"></i>
                                Lamar Sekarang
                            </a>
                            @endif

                            <!-- <button class="btn-secondary-action" id="btnShare" onclick="shareLoker()">
                                <i class="fas fa-share-nodes"></i>
                                Bagikan
                            </button> -->

                            <a href="{{ url('/lowongan') }}" class="btn-secondary-action">
                                <i class="fas fa-arrow-left"></i>
                                Lowongan Lain
                            </a>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

</div>

{{-- Lightbox --}}
<div class="lb-overlay" id="lbOverlay" onclick="closeLb()">
    <span class="lb-close" onclick="closeLb()">&times;</span>
    <img id="lbImg" class="lb-img" src="" alt="Preview foto lowongan" onclick="event.stopPropagation()">
</div>
@endsection

@push('scripts')
<script>
function openLb(src) {
    document.getElementById('lbImg').src = src;
    document.getElementById('lbOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeLb() {
    document.getElementById('lbOverlay').classList.remove('show');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLb(); });

function shareLoker() {
    var btn = document.getElementById('btnShare');
    navigator.clipboard.writeText(window.location.href).then(function() {
        btn.innerHTML = '<i class="fas fa-check"></i> Link Disalin!';
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-share-nodes"></i> Bagikan';
        }, 2000);
    });
}
</script>
@endpush
