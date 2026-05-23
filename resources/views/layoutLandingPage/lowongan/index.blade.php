@php use Illuminate\Support\Facades\Storage; use Carbon\Carbon;

// Helper: format gaji → Rp 60.000.000
function formatGajiLoker(string $gaji): string {
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
    /* Offset fixed navbar */
    .loker-page-wrap { padding-top: 80px; }

    /* ── HERO (sama pola dengan hero section lain) ── */
    .loker-hero {
        background: linear-gradient(135deg, #071a42 0%, #1557c0 60%, #17a4df 100%);
        border-radius: var(--tracer-radius);
        padding: 48px 36px;
        position: relative;
        overflow: hidden;
        margin-bottom: 56px;
        box-shadow: 0 20px 55px rgba(8,34,79,.22);
    }
    .loker-hero::before {
        content: '';
        position: absolute;
        width: 280px; height: 280px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
        top: -130px; right: -80px;
    }
    .loker-hero::after {
        content: '';
        position: absolute;
        width: 190px; height: 190px;
        background: rgba(255,255,255,.04);
        border-radius: 50%;
        bottom: -90px; left: -50px;
    }
    .loker-hero-inner { position: relative; z-index: 2; }

    /* ── SEARCH ── */
    .loker-search {
        max-width: 440px;
        margin: 0 auto;
    }
    .loker-search input {
        border-radius: 999px;
        border: none;
        padding: 12px 22px;
        font-size: .93rem;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 8px 24px rgba(0,0,0,.14);
        outline: none;
        width: 100%;
    }
    .loker-search input:focus {
        box-shadow: 0 8px 28px rgba(37,130,243,.28);
    }

    /* ── CARD LOWONGAN — override benefit-card agar visible di bg terang ── */
    .loker-card {
        height: 100%;
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 24px rgba(8,34,79,.10) !important;
    }
    .loker-card:hover {
        transform: translateY(-6px) !important;
        border-color: rgba(37,130,243,.28) !important;
        box-shadow: 0 20px 50px rgba(8,34,79,.16) !important;
    }

    /* Foto thumbnail */
    .loker-card-foto-wrap { overflow: hidden; flex-shrink: 0; }
    .loker-card-foto {
        width: 100%;
        height: 170px;
        object-fit: cover;
        display: block;
        cursor: pointer;
        transition: transform .35s ease;
    }
    .loker-card-foto:hover { transform: scale(1.05); }
    .loker-card-placeholder {
        width: 100%;
        height: 170px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #93c5fd;
        font-size: 2.6rem;
    }

    /* Body */
    .loker-card-body {
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* Company badge */
    .loker-company-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(37,130,243,.1);
        border: 1px solid rgba(37,130,243,.12);
        color: var(--tracer-blue);
        border-radius: 999px;
        padding: 3px 12px;
        font-size: .75rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    /* Chips info */
    .loker-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255,255,255,.72);
        border: 1px solid var(--tracer-line);
        border-radius: 999px;
        padding: 3px 10px;
        font-size: .74rem;
        color: var(--tracer-muted);
        margin-right: 4px;
        margin-bottom: 4px;
    }
    .loker-chip.salary { color: var(--tracer-blue); background: #eff6ff; border-color: #bfdbfe; }
    .loker-chip.deadline { color: #dc2626; background: #fef2f2; border-color: #fecaca; }
    .loker-chip.open { color: #16a34a; background: #f0fdf4; border-color: #bbf7d0; }

    /* Desc */
    .loker-desc {
        color: var(--tracer-muted);
        font-size: .86rem;
        line-height: 1.65;
        margin-top: 10px;
        margin-bottom: 20px;
        flex: 1;
    }

    /* Button detail */
    .btn-loker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--tracer-blue), var(--tracer-blue-2));
        color: white;
        border: none;
        border-radius: 999px;
        padding: 9px 20px;
        font-size: .84rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(37,130,243,.22);
        transition: .25s;
        align-self: flex-start;
    }
    .btn-loker:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 14px 30px rgba(37,130,243,.35);
    }

    /* Empty state */
    .loker-empty {
        text-align: center;
        padding: 56px 24px;
    }
    .loker-empty .benefit-icon { margin: 0 auto 18px; }

    /* Pagination */
    .loker-pagination-wrap nav { display: flex; justify-content: center; }
    .loker-pagination-wrap .pagination {
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .loker-pagination-wrap .page-link {
        border: 1px solid var(--tracer-line) !important;
        border-radius: 12px !important;
        padding: 8px 14px !important;
        color: var(--tracer-blue) !important;
        font-weight: 700 !important;
        font-size: .84rem !important;
        background: rgba(255,255,255,.82) !important;
        backdrop-filter: blur(10px);
        transition: .2s;
        line-height: 1.4;
    }
    .loker-pagination-wrap .page-link:hover {
        background: rgba(37,130,243,.08) !important;
        border-color: rgba(37,130,243,.2) !important;
        color: var(--tracer-blue) !important;
    }
    .loker-pagination-wrap .page-item.active .page-link {
        background: linear-gradient(135deg, var(--tracer-blue), var(--tracer-blue-2)) !important;
        border-color: transparent !important;
        color: white !important;
        box-shadow: 0 6px 18px rgba(37,130,243,.3);
    }
    .loker-pagination-wrap .page-item.disabled .page-link {
        opacity: .45;
        pointer-events: none;
    }

    /* Lightbox */
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
</style>
@endpush

@section('content')
<div class="loker-page-wrap">

{{-- ── HERO ── --}}
<section class="section-pad" style="padding-top: 0; padding-bottom: 0;">
    <div class="container">
        <div class="loker-hero animate__animated animate__fadeInDown">
            <div class="loker-hero-inner">

                {{-- Baris atas: tombol kiri, badge tengah --}}
                <div style="position:relative; display:flex; align-items:center; justify-content:center; margin-bottom:28px;">
                    <a href="{{ url('/landingpage') }}"
                       class="btn btn-outline-glass rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2"
                       style="position:absolute; left:0;">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                    <div class="section-kicker" style="display:inline-flex; color:#dff7ff; background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.18);">
                        <i class="fas fa-briefcase"></i>
                        Karir & Lowongan
                    </div>
                </div>

                {{-- Konten tengah --}}
                <div class="text-center">
                    <h1 class="section-title text-white mb-3">Lowongan Pekerjaan</h1>
                    <p class="mb-4" style="color:rgba(255,255,255,.82);">
                        Temukan peluang karir terbaik untuk alumni JTI Polije
                    </p>
                    <div class="loker-search">
                        <form method="GET" action="{{ url('/lowongan') }}">
                            <input
                                type="text"
                                name="search"
                                placeholder="Cari posisi atau perusahaan..."
                                value="{{ request('search') }}"
                            >
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ── LIST LOWONGAN ── --}}
<section class="benefit-section section-pad">
    <div class="container">

        {{-- Filter info --}}
        @if(request('search'))
        <div class="mb-4 d-flex align-items-center gap-2 flex-wrap">
            <span class="section-copy small">Hasil pencarian untuk:</span>
            <span class="section-kicker" style="font-size:.78rem;">{{ request('search') }}</span>
            <a href="{{ url('/lowongan') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="fas fa-times me-1"></i>Reset
            </a>
        </div>
        @endif

        <div class="row g-4 justify-content-center">

            @forelse($lowongan as $item)
            <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp">
                <div class="benefit-card loker-card">

                    {{-- Foto --}}
                    @if($item->foto)
                    {{-- <!--@php-->
                    <!--    // Path lama: dimulai dengan 'startbootstrap' (disimpan di public/)-->
                    <!--    // Path baru: disimpan via Storage::disk('public'), pakai Storage::url()-->
                    <!--    $fotoUrl = str_starts_with($item->foto, 'startbootstrap')-->
                    <!--        ? asset($item->foto)-->
                    <!--        : \Illuminate\Support\Facades\Storage::url($item->foto);-->
                    <!--@endphp--> --}}
                    @php
                        $fotoUrl = asset('storage/' . $item->foto);
                    @endphp
                    <div class="loker-card-foto-wrap">
                        <img
                            src="{{ $fotoUrl }}"
                            alt="{{ $item->posisi }}"
                            class="loker-card-foto"
                            onclick="openLb(this.src)"
                        >
                    </div>
                    @else
                    <div class="loker-card-placeholder">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    @endif

                    <div class="loker-card-body">

                        <span class="loker-company-tag">
                            <i class="fas fa-building"></i>
                            {{ $item->nama_perusahaan }}
                        </span>

                        <h4 style="font-size:1.05rem; font-weight:800; color:var(--tracer-ink); margin-bottom:10px; line-height:1.35;">
                            {{ $item->posisi }}
                        </h4>

                        <div>
                            @if($item->lokasi)
                            <span class="loker-chip">
                                <i class="fas fa-location-dot text-danger"></i>
                                {{ $item->lokasi }}
                            </span>
                            @endif
                            @if($item->gaji)
                            <span class="loker-chip salary">
                                <i class="fas fa-wallet"></i>
                                {{ formatGajiLoker($item->gaji) }}
                            </span>
                            @endif
                            @if($item->batas_lamaran)
                            @php
                                $batas      = Carbon::parse($item->batas_lamaran);
                                $sudahLewat = $batas->isPast();
                            @endphp
                            <span class="loker-chip {{ $sudahLewat ? 'deadline' : 'open' }}">
                                <i class="fas {{ $sudahLewat ? 'fa-calendar-xmark' : 'fa-calendar-check' }}"></i>
                                {{ $batas->format('d M Y') }}
                            </span>
                            @endif
                        </div>

                        @if($item->deskripsi)
                        <p class="loker-desc">
                            {{ \Illuminate\Support\Str::limit($item->deskripsi, 110) }}
                        </p>
                        @endif

                        <a href="{{ url('/lowongan/'.$item->id) }}" class="btn-loker mt-auto" style="margin-top:16px !important;">
                            Lihat Detail
                            <i class="fas fa-arrow-right"></i>
                        </a>

                    </div>
                </div>
            </div>

            @empty
            <div class="col-12">
                <div class="benefit-card loker-empty">
                    <div class="benefit-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h4>
                        @if(request('search'))
                            Lowongan tidak ditemukan
                        @else
                            Belum ada lowongan tersedia
                        @endif
                    </h4>
                    <p class="section-copy mb-0">
                        @if(request('search'))
                            Coba kata kunci lain atau
                            <a href="{{ url('/lowongan') }}" class="text-primary fw-semibold">lihat semua lowongan</a>
                        @else
                            Pantau terus halaman ini untuk info lowongan terbaru
                        @endif
                    </p>
                </div>
            </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        @if($lowongan->hasPages())
        <div class="mt-5 loker-pagination-wrap">
            {{ $lowongan->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif

    </div>
</section>

</div>

{{-- Lightbox --}}
<div class="lb-overlay" id="lbOverlay" onclick="closeLb()">
    <span class="lb-close" onclick="closeLb()">&times;</span>
    <img id="lbImg" class="lb-img" src="" alt="Preview" onclick="event.stopPropagation()">
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
</script>
@endpush
