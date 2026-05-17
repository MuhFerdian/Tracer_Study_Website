@extends('layoutAdmin.app')

@section('content')

<div class="container-fluid px-4">

    <h1 class="mt-4 fw-bold text-warning">
        Edit Lowongan
    </h1>

    <div class="card shadow border-0 rounded-4 mt-4">

        <div class="card-body p-4">

            <form action="{{ url('admin/lowongan-pekerjaan/'.$lowongan->id.'/update') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row">

                    {{-- POSISI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Posisi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="posisi"
                               value="{{ old('posisi', $lowongan->posisi) }}"
                               class="form-control rounded-3"
                               placeholder="Contoh: Web Developer"
                               required>
                    </div>

                    {{-- NAMA PERUSAHAAN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nama Perusahaan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="nama_perusahaan"
                               value="{{ old('nama_perusahaan', $lowongan->nama_perusahaan) }}"
                               class="form-control rounded-3"
                               placeholder="Nama perusahaan"
                               required>
                    </div>

                    {{-- LOKASI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Lokasi
                        </label>
                        <input type="text"
                               name="lokasi"
                               value="{{ old('lokasi', $lowongan->lokasi) }}"
                               class="form-control rounded-3"
                               placeholder="Surabaya / Remote">
                    </div>

                    {{-- GAJI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Gaji
                        </label>
                        <input type="text"
                               name="gaji"
                               value="{{ old('gaji', $lowongan->gaji) }}"
                               class="form-control rounded-3"
                               placeholder="Rp 5.000.000 - Rp 8.000.000">
                    </div>

                    {{-- BATAS LAMARAN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Batas Lamaran
                        </label>
                        <input type="date"
                               name="batas_lamaran"
                               value="{{ old('batas_lamaran', $lowongan->batas_lamaran) }}"
                               class="form-control rounded-3">
                    </div>

                    {{-- KONTAK --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Kontak
                        </label>
                        <input type="text"
                               name="kontak"
                               value="{{ old('kontak', $lowongan->kontak) }}"
                               class="form-control rounded-3"
                               placeholder="08123456789 / hr@perusahaan.com">
                    </div>

                    {{-- LINK LAMARAN --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Link Lamaran
                        </label>
                        <input type="url"
                               name="link_lamaran"
                               value="{{ old('link_lamaran', $lowongan->link_lamaran) }}"
                               class="form-control rounded-3"
                               placeholder="https://example.com/apply">
                    </div>

                    {{-- LOGO PERUSAHAAN --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Logo Perusahaan
                        </label>

                        {{-- Input File --}}
                        <input type="file"
                               name="logo"
                               id="logoInput"
                               class="form-control rounded-3"
                               accept="image/png, image/jpeg, image/jpg, image/gif, image/svg+xml">

                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle"></i> 
                            Format: PNG, JPG, GIF, SVG. Maksimal 2MB.
                        </small>

                        {{-- Preview Logo --}}
                        <div class="mt-3 d-flex align-items-center gap-3" id="logoContainer">
                            
                            {{-- Logo Lama --}}
                            @if($lowongan->logo)
                                <div id="existingLogo">
                                    <img src="{{ asset('storage/'.$lowongan->logo) }}" 
                                         id="currentLogoImg"
                                         width="100" 
                                         height="100"
                                         class="rounded-3 border shadow-sm object-fit-contain bg-light p-2"
                                         alt="Logo {{ $lowongan->nama_perusahaan }}">
                                    <br>
                                    <small class="text-muted">Logo saat ini</small>
                                </div>
                            @endif

                            {{-- Preview Logo Baru (hidden by default) --}}
                            <div id="newLogoPreview" class="d-none">
                                <img src="" 
                                     id="previewImg"
                                     width="100" 
                                     height="100"
                                     class="rounded-3 border shadow-sm object-fit-contain bg-light p-2"
                                     alt="Preview logo baru">
                                <br>
                                <small class="text-success">Preview baru</small>
                            </div>

                            {{-- Tombol Hapus Logo --}}
                            @if($lowongan->logo)
                                <div id="deleteLogoBtn">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger mt-2"
                                            onclick="removeLogo()">
                                        <i class="fas fa-trash-alt"></i> Hapus Logo
                                    </button>
                                    {{-- Hidden input untuk menandai hapus logo --}}
                                    <input type="hidden" name="hapus_logo" id="hapusLogoInput" value="0">
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                                  rows="5"
                                  class="form-control rounded-3"
                                  placeholder="Tulis deskripsi lowongan, kualifikasi, dan tanggung jawab...">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ url('admin/lowongan-pekerjaan') }}"
                       class="btn btn-light border rounded-3 px-4">

                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-warning text-white rounded-3 px-4">

                        <i class="fas fa-save me-1"></i> Update
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- Script Preview & Hapus Logo --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logoInput');
    const existingLogo = document.getElementById('existingLogo');
    const newLogoPreview = document.getElementById('newLogoPreview');
    const previewImg = document.getElementById('previewImg');
    const deleteLogoBtn = document.getElementById('deleteLogoBtn');
    const hapusLogoInput = document.getElementById('hapusLogoInput');

    // Preview logo baru saat dipilih
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validasi ukuran (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('⚠️ Ukuran file maksimal 2MB!');
                this.value = '';
                newLogoPreview.classList.add('d-none');
                return;
            }
            
            // Validasi tipe file
            const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/svg+xml'];
            if (!validTypes.includes(file.type)) {
                alert('⚠️ Format file tidak didukung! Gunakan PNG, JPG, GIF, atau SVG.');
                this.value = '';
                newLogoPreview.classList.add('d-none');
                return;
            }
            
            // Tampilkan preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                newLogoPreview.classList.remove('d-none');
                
                // Sembunyikan logo lama secara visual (tapi tidak menghapus)
                if (existingLogo) {
                    existingLogo.style.opacity = '0.5';
                }
            }
            reader.readAsDataURL(file);
        }
    });
});

// Fungsi hapus logo
function removeLogo() {
    if (confirm('Yakin ingin menghapus logo perusahaan?')) {
        // Tandai untuk dihapus di controller
        const hapusInput = document.getElementById('hapusLogoInput');
        if (hapusInput) {
            hapusInput.value = '1';
        }
        
        // Sembunyikan preview logo
        const existingLogo = document.getElementById('existingLogo');
        const deleteBtn = document.getElementById('deleteLogoBtn');
        const newLogoPreview = document.getElementById('newLogoPreview');
        
        if (existingLogo) existingLogo.style.display = 'none';
        if (deleteBtn) deleteBtn.style.display = 'none';
        if (newLogoPreview) newLogoPreview.classList.add('d-none');
        
        // Reset input file
        document.getElementById('logoInput').value = '';
        
        alert('✅ Logo akan dihapus saat update disimpan.');
    }
}
</script>

{{-- CSS Tambahan --}}
<style>
.object-fit-contain {
    object-fit: contain !important;
}
#logoContainer img {
    transition: opacity 0.3s ease;
}
</style>

@endsection