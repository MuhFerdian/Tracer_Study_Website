<!-- @extends('layoutAdmin.app') -->

@section('content')

<div class="container-fluid px-4">

    <h1 class="mt-4 fw-bold text-primary">
        Tambah Lowongan Pekerjaan
    </h1>

    <div class="card shadow border-0 rounded-4 mt-4">

        <div class="card-body p-4">

            {{-- Tambahkan enctype untuk upload file --}}
            <form action="{{ url('admin/lowongan-pekerjaan/store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    {{-- POSISI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Posisi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="posisi"
                               class="form-control rounded-3"
                               placeholder="Contoh: Web Developer"
                               required>
                    </div>

                    {{-- PERUSAHAAN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nama Perusahaan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="nama_perusahaan"
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
                               class="form-control rounded-3">
                    </div>

                    {{-- KONTAK --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Kontak
                        </label>
                        <input type="text"
                               name="kontak"
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
                               class="form-control rounded-3"
                               placeholder="https://example.com/apply">
                    </div>

                    {{-- LOGO PERUSAHAAN --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Logo Perusahaan
                        </label>
                        
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
                        <div id="logoPreview" class="mt-3 d-none">
                            <img src="" 
                                 id="logoImage"
                                 width="100" 
                                 height="100"
                                 class="rounded-3 border shadow-sm object-fit-contain bg-light p-2"
                                 alt="Preview logo">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger ms-2"
                                    onclick="resetLogo()">
                                <i class="fas fa-times"></i> Hapus
                            </button>
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
                                  placeholder="Tulis deskripsi lowongan, kualifikasi, dan tanggung jawab..."></textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ url('admin/lowongan-pekerjaan') }}"
                       class="btn btn-light border rounded-3 px-4">

                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-3 px-4">

                        <i class="fas fa-save me-1"></i> Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- Script Preview Logo --}}
<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
    const preview = document.getElementById('logoPreview');
    const image = document.getElementById('logoImage');
    const file = e.target.files[0];
    
    if (file) {
        // Validasi ukuran file (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB!');
            this.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // Validasi tipe file
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak didukung!');
            this.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // Tampilkan preview
        const reader = new FileReader();
        reader.onload = function(e) {
            image.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    }
});

function resetLogo() {
    const input = document.getElementById('logoInput');
    const preview = document.getElementById('logoPreview');
    input.value = '';
    preview.classList.add('d-none');
}
</script>

{{-- CSS Tambahan --}}
<style>
.object-fit-contain {
    object-fit: contain !important;
}
</style>

@endsection
