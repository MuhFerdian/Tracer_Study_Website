@php
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Hindari warning VS Code jika $lowongan null
$lowongan = $lowongan ?? collect();

// Helper: format gaji → Rp 60.000.000
function formatGaji(string $gaji): string {
    $clean = preg_replace('/[^0-9]/', '', $gaji);
    if ($clean !== '' && is_numeric($clean)) {
        return 'Rp ' . number_format((int)$clean, 0, ',', '.');
    }
    return $gaji; // bukan angka murni, tampil apa adanya
}
@endphp
    
    <section class="benefit-section section-pad" id="lowongan">
    <div class="container">

        {{-- Section Header --}}
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="section-kicker mb-3">
                    <i class="fas fa-briefcase"></i>
                    Lowongan Terbaru
                </div>
        
                <h2 class="section-title mb-3">
                    Peluang Karir Alumni
                </h2>
        
                <p class="section-copy fs-5 mb-0">
                    Temukan peluang kerja terbaru dari perusahaan dan partner kampus.
                </p>
            </div>
        </div>
        
        {{-- Grid Lowongan --}}
        <div class="row g-4 justify-content-center">
        
            @forelse($lowongan as $item)

                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp">

                    <div class="benefit-card h-100 p-0" style="
                                        overflow:hidden;
                                        display:flex;
                                        flex-direction:column;
                                        background:#ffffff !important;
                                        border:1px solid #e2e8f0 !important;
                                        box-shadow:0 4px 24px rgba(8,34,79,.10) !important;
                                        transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease;
                                    " onmouseover="
                                        this.style.transform='translateY(-6px)';
                                        this.style.boxShadow='0 20px 50px rgba(8,34,79,.16)';
                                        this.style.borderColor='rgba(37,130,243,.28)';
                                    " onmouseout="
                                        this.style.transform='';
                                        this.style.boxShadow='0 4px 24px rgba(8,34,79,.10)';
                                        this.style.borderColor='#e2e8f0';
                                    ">

                        {{-- FOTO --}}
                        @if(!empty($item->foto))
                            @php
                                // Foto baru: path relatif ke public/ (mengandung 'startbootstrap' atau 'foto_loker')
                                // Foto lama: path di storage/app/public/ (hanya 'loker/namafile')
                                $fotoUrl = str_starts_with($item->foto, 'startbootstrap') || str_starts_with($item->foto, 'foto_loker')
                                    ? asset($item->foto)
                                    : \Illuminate\Support\Facades\Storage::url($item->foto);
                            @endphp

                            <div style="overflow:hidden; flex-shrink:0;">

                                <img src="{{ $fotoUrl }}" alt="{{ $item->posisi }}" style="
                                                        width:100%;
                                                        height:160px;
                                                        object-fit:cover;
                                                        display:block;
                                                        transition:transform .35s ease;
                                                    " onmouseover="this.style.transform='scale(1.05)'"
                                    onmouseout="this.style.transform='scale(1)'">

                            </div>

                        @else

                            <div style="
                                                    width:100%;
                                                    height:160px;
                                                    background:linear-gradient(135deg,#eff6ff,#dbeafe);
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                    color:#93c5fd;
                                                    font-size:2.4rem;
                                                    flex-shrink:0;
                                                ">
                                <i class="fas fa-briefcase"></i>
                            </div>

                        @endif

                        {{-- BODY --}}
                        <div style="
                                            padding:1.35rem;
                                            display:flex;
                                            flex-direction:column;
                                            flex:1;
                                        ">

                            {{-- Company badge --}}
                            <span style="
                                                display:inline-flex;
                                                align-items:center;
                                                gap:6px;
                                                background:rgba(37,130,243,.1);
                                                border:1px solid rgba(37,130,243,.12);
                                                color:var(--tracer-blue);
                                                border-radius:999px;
                                                padding:3px 12px;
                                                font-size:.75rem;
                                                font-weight:700;
                                                margin-bottom:8px;
                                                width:fit-content;
                                            ">
                                <i class="fas fa-building"></i>
                                {{ $item->nama_perusahaan }}
                            </span>

                            {{-- Posisi --}}
                            <h4 style="
                                                font-size:1.05rem;
                                                font-weight:800;
                                                color:var(--tracer-ink);
                                                margin-bottom:10px;
                                                line-height:1.35;
                                            ">
                                {{ $item->posisi }}
                            </h4>

                            {{-- Info chips --}}
                            <div style="margin-bottom:4px;">

                                @if(!empty($item->lokasi))
                                    <span style="
                                                            display:inline-flex;
                                                            align-items:center;
                                                            gap:5px;
                                                            background:rgba(255,255,255,.72);
                                                            border:1px solid var(--tracer-line);
                                                            border-radius:999px;
                                                            padding:3px 10px;
                                                            font-size:.74rem;
                                                            color:var(--tracer-muted);
                                                            margin-right:4px;
                                                            margin-bottom:4px;
                                                        ">
                                        <i class="fas fa-location-dot" style="color:#dc2626;"></i>
                                        {{ $item->lokasi }}
                                    </span>
                                @endif

                                @if(!empty($item->gaji))
                                    @php $gajiFormatted = formatGaji($item->gaji); @endphp
                                    <span style="
                                                            display:inline-flex;
                                                            align-items:center;
                                                            gap:5px;
                                                            background:#eff6ff;
                                                            border:1px solid #bfdbfe;
                                                            border-radius:999px;
                                                            padding:3px 10px;
                                                            font-size:.74rem;
                                                            color:var(--tracer-blue);
                                                            margin-right:4px;
                                                            margin-bottom:4px;
                                                        ">
                                        <i class="fas fa-wallet"></i>
                                        {{ $gajiFormatted }}
                                    </span>
                                @endif

                                @if(!empty($item->batas_lamaran))
                                    @php
                                        $batas      = Carbon::parse($item->batas_lamaran);
                                        $sudahLewat = $batas->isPast();
                                        $deadlineBg    = $sudahLewat ? '#fef2f2' : '#f0fdf4';
                                        $deadlineBorder= $sudahLewat ? '#fecaca' : '#bbf7d0';
                                        $deadlineColor = $sudahLewat ? '#dc2626' : '#16a34a';
                                        $deadlineIcon  = $sudahLewat ? 'fa-calendar-xmark' : 'fa-calendar-check';
                                    @endphp
                                    <span style="
                                                            display:inline-flex;
                                                            align-items:center;
                                                            gap:5px;
                                                            background:{{ $deadlineBg }};
                                                            border:1px solid {{ $deadlineBorder }};
                                                            border-radius:999px;
                                                            padding:3px 10px;
                                                            font-size:.74rem;
                                                            color:{{ $deadlineColor }};
                                                            margin-right:4px;
                                                            margin-bottom:4px;
                                                        ">
                                        <i class="fas {{ $deadlineIcon }}"></i>
                                        {{ $batas->format('d M Y') }}
                                    </span>
                                @endif

                            </div>

                            {{-- Deskripsi --}}
                            @if(!empty($item->deskripsi))

                                <p style="
                                                        color:var(--tracer-muted);
                                                        font-size:.86rem;
                                                        line-height:1.65;
                                                        margin-top:10px;
                                                        margin-bottom:16px;
                                                        flex:1;
                                                    ">
                                    {{ \Illuminate\Support\Str::limit($item->deskripsi, 100) }}
                                </p>

                            @endif

                            {{-- Button --}}
                            <a href="{{ url('/lowongan/' . $item->id) }}" style="
                                                display:inline-flex;
                                                align-items:center;
                                                gap:8px;
                                                background:linear-gradient(
                                                    135deg,
                                                    var(--tracer-blue),
                                                    var(--tracer-blue-2)
                                                );
                                                color:white;
                                                border:none;
                                                border-radius:999px;
                                                padding:9px 20px;
                                                font-size:.84rem;
                                                font-weight:700;
                                                text-decoration:none;
                                                box-shadow:0 8px 20px rgba(37,130,243,.22);
                                                transition:.25s;
                                                align-self:flex-start;
                                                margin-top:auto;
                                            " onmouseover="
                                                this.style.transform='translateY(-2px)';
                                                this.style.boxShadow='0 14px 30px rgba(37,130,243,.35)';
                                            " onmouseout="
                                                this.style.transform='';
                                                this.style.boxShadow='0 8px 20px rgba(37,130,243,.22)';
                                            ">
                                Lihat Detail
                                <i class="fas fa-arrow-right"></i>
                            </a>

                        </div>
                    </div>
                </div>

            @empty

                <div class="col-12">

                    <div class="benefit-card text-center" style="padding:48px 24px;">

                        <div class="benefit-icon" style="margin:0 auto 16px;">
                            <i class="fas fa-briefcase"></i>
                        </div>

                        <h4>Belum ada lowongan tersedia</h4>

                        <p class="section-copy mb-0">
                            Pantau terus halaman ini untuk info lowongan terbaru.
                        </p>

                    </div>

                </div>

            @endforelse
        
        </div>
        
        {{-- CTA --}}
        <div class="row justify-content-center mt-5">
        
            <div class="col-auto">
        
                <a href="{{ url('/lowongan') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                    <i class="fas fa-briefcase me-2"></i>
                    Lihat Semua Lowongan
                </a>
        
            </div>
        
        </div>
        
        </div>
        </section>