@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4 mb-5">
    <h1 class="mt-4 fw-bold">Detail Lowongan Pekerjaan</h1>

    <div class="card shadow border-0 rounded-4 mt-4">
        <div class="card-body p-5">
            <div class="row mb-5 align-items-center">
                <div class="col-md-4">
                    @if($lowongan->foto)
                    <img src="{{ Storage::url($lowongan->foto) }}"
                    class="img-fluid rounded-4 shadow-sm"
                    onclick="openImageModal(this.src)"
                    style="
                    width: 100%;
                    height: 350px;
                    object-fit: cover;
                    border: 1px solid #dee2e6;
                    cursor:pointer;
                    transition:0.3s;"
                    onmouseover="this.style.transform='scale(1.02)'"
                    onmouseout="this.style.transform='scale(1)'">
                    @else
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-sm" 
                    style="width: 100%; height: 350px; background-color: #D9D9D9; border: 1px solid #ced4da;">
                        <div class="text-center" style="color: #adb5bd;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                            </svg>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-md-8 ps-md-5">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <h2 class="fw-bold mb-0">{{ $lowongan->posisi }}</h2>
                        <span class="badge {{ $lowongan->aktif ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                            {{ $lowongan->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-muted fs-5"><i class="fas fa-building me-2"></i>{{ $lowongan->nama_perusahaan }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1">Posisi</label>
                    <div class="p-3 border rounded-3 bg-light">{{ $lowongan->posisi }}</div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1">Nama Perusahaan</label>
                    <div class="p-3 border rounded-3 bg-light">{{ $lowongan->nama_perusahaan }}</div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1">Lokasi</label>
                    <div class="p-3 border rounded-3 bg-light">{{ $lowongan->lokasi ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1">Gaji</label>
                    <div class="p-3 border rounded-3 bg-light">{{ $lowongan->gaji ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1">Batas Lamaran</label>
                    <div class="p-3 border rounded-3 bg-light">{{ \Carbon\Carbon::parse($lowongan->batas_lamaran)->format('d M Y') }}</div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark mb-1">Kontak</label>
                    <div class="p-3 border rounded-3 bg-light">{{ $lowongan->kontak }}</div>
                </div>

                <!-- Link Lamaran -->
                <div class="col-md-12">
                    <label class="fw-bold text-dark mb-1">Link Lamaran</label>
                    <div class="p-3 border rounded-3 bg-light">
                        @if($lowongan->link_lamaran)
                        <a href="{{ $lowongan->link_lamaran }}" target="_blank" class="text-decoration-none fw-bold">
                            <i class="fas fa-external-link-alt me-2"></i>{{ $lowongan->link_lamaran }}
                        </a>
                        @else
                        <span class="text-muted">Tidak ada link lamaran</span>
                        @endif
                    </div>
                </div>

                <!-- Deskripsi Di Paling Bawah -->
                <div class="col-md-12 mb-4">
                    <label class="fw-bold text-dark mb-1">Deskripsi</label>
                    <div class="p-4 border rounded-3 bg-light" style="min-height: 150px;">
                        {!! nl2br(e($lowongan->deskripsi)) !!}
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-5">
                <a href="{{ url('admin/lowongan-pekerjaan') }}" class="btn btn-secondary px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Foto -->
<div id="imageModal"
     onclick="closeImageModal()"
     style="
        display:none;
        position:fixed;
        z-index:9999;
        left:0;
        top:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.2s;
     ">

    <!-- Tombol Close -->
    <span onclick="closeImageModal()"
          style="
            position:absolute;
            top:20px;
            right:35px;
            color:white;
            font-size:40px;
            font-weight:bold;
            cursor:pointer;
            z-index:10000;
          ">
        &times;
    </span>

    <!-- Gambar -->
    <div class="d-flex justify-content-center align-items-center h-100">
        <img id="modalImage"
             onclick="event.stopPropagation()"
             style="
                max-width:75%;
                max-height:75%;
                border-radius:20px;
                box-shadow:0 0 20px rgba(0,0,0,0.4);
                animation: zoomIn 0.2s;
             ">
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes zoomIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<script>
function openImageModal(src){
    document.getElementById("imageModal").style.display = "block";
    document.getElementById("modalImage").src = src;
}

function closeImageModal(){
    document.getElementById("imageModal").style.display = "none";
}

// Tutup modal dengan tombol ESC
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeImageModal();
    }
});
</script>
@endsection