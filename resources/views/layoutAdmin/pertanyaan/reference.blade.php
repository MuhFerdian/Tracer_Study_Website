@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="mt-4">📋 Panduan Pertanyaan Wajib Kemendikbud</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/pertanyaan') }}">Manajemen Pertanyaan</a></li>
            <li class="breadcrumb-item active">Panduan Referensi</li>
        </ol>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info border-start border-4 border-info" role="alert">
        <h5 class="alert-heading">
            <i class="fas fa-lightbulb me-2"></i> Gunakan halaman ini sebagai referensi
        </h5>
        <p class="mb-0">
            Daftar di bawah menunjukkan semua pertanyaan <strong>wajib</strong> dari Kemendikbud yang harus Anda buat. 
            Anda dapat:
        </p>
        <ul class="mb-0 mt-2">
            <li>Membaca detail setiap pertanyaan</li>
            <li><strong>Download template Excel</strong> untuk bulk import</li>
            <li>Atau <strong>tambah manual</strong> via form CRUD</li>
        </ul>
    </div>

    <!-- FILTER & ACTION -->
    <div class="card mb-5">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

        <!-- BAGIAN KIRI: Filter, Download, & Upload -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Filter -->
            <input type="text"
                class="form-control form-control-sm"
                id="filterKategori"
                placeholder="🔍 Filter berdasarkan kategori"
                style="width: 260px;">

            <!-- Download Template -->
            <button
                class="btn btn-success btn-sm"
                onclick="downloadTemplate()"
                title="Download template Excel untuk bulk import">
                <i class="fas fa-download me-1"></i>
                Download Template
            </button>

            <!-- Upload File -->
            <button
                class="btn btn-warning btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#uploadModal"
                title="Upload file Excel untuk import pertanyaan">
                <i class="fas fa-upload me-1"></i>
                Upload File
            </button>
        </div>

        <!-- BAGIAN KANAN: Tombol Kembali -->
        <div>
            <a href="{{ url('/admin/pertanyaan') }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

    </div>
</div>
    

    <!-- Daftar Pertanyaan -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i> {{ count($pertanyaanWajib) }} Pertanyaan Wajib
                <span class="badge bg-warning text-dark ms-2">* Wajib dibuat</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Kategori</th>
                            <th>Kode</th>
                            <th>Pertanyaan</th>
                            <th>Tipe</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pertanyaanTable">
                        @foreach($pertanyaanWajib as $item)
                        <tr class="kategoriBaris" data-kategori="{{ strtolower($item['kategori']) }}">
                            <td>
                                <span class="badge bg-primary">{{ $item['no'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-secondary">
                                    {{ $item['kategori'] }}
                                </span>
                            </td>
                            <td>
                                <code class="text-danger fw-bold">{{ $item['kode'] }}</code>
                            </td>
                            <td>
                                <strong>{{ $item['pertanyaan'] }}</strong>
                                <br>
                                <small class="text-muted">{{ $item['deskripsi'] }}</small>
                            </td>
                            <td>
                                @if(strpos($item['tipe'], '*Wajib') !== false)
                                    <span class="badge bg-danger">{{ str_replace('*Wajib', '', $item['tipe']) }}</span>
                                @elseif(strpos($item['tipe'], '*') !== false)
                                    <span class="badge bg-warning text-dark">{{ str_replace('*', '', $item['tipe']) }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $item['tipe'] }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#detailModal" onclick="showDetail(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-xs btn-primary" 
                                        onclick="createQuestion('{{ $item['kode'] }}', '{{ addslashes($item['pertanyaan']) }}')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Stats -->
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="text-danger">{{ count($pertanyaanWajib) }}</h3>
                        <p class="mb-0 text-muted">Total Pertanyaan Wajib</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="text-warning">
                            {{ collect($pertanyaanWajib)->filter(fn($p) => strpos($p['tipe'], '*Wajib') !== false)->count() }}
                        </h3>
                        <p class="mb-0 text-muted">Prioritas Tinggi (*Wajib)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h3 class="text-success">+5</h3>
                        <p class="mb-0 text-muted">Kategori Pertanyaan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="card mt-4 bg-light">
        <div class="card-body">
            <h6 class="card-title">
                <i class="fas fa-key me-2"></i> Keterangan Badge
            </h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <span class="badge bg-danger me-2">Wajib</span>
                    <small>Pertanyaan yang harus ada</small>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-warning text-dark me-2">Conditional</span>
                    <small>Bergantung jawaban pertanyaan lain</small>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-secondary me-2">Optional</span>
                    <small>Dapat disesuaikan kebutuhan</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Panduan:</strong>
                        <ol>
                            <li>Download template Excel terlebih dahulu</li>
                            <li>Isi kolom sesuai panduan</li>
                            <li>Upload file untuk import</li>
                            <li>Tunggu proses import selesai</li>
                        </ol>
                    </div>
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Pilih File Excel (.xlsx/.xls)</label>
                        <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Maksimal ukuran: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpload">
                        <i class="fas fa-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Isi dari JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnBuatPertanyaan">
                    <i class="fas fa-plus me-1"></i> Buat Pertanyaan Ini
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    function showDetail(btn) {
        const row = btn.closest('tr');
        const no = row.cells[0].textContent.trim();
        const kategori = row.cells[1].textContent.trim();
        const kode = row.cells[2].textContent.trim();
        const pertanyaan = row.cells[3].querySelector('strong').textContent;
        const deskripsi = row.cells[3].querySelector('small').textContent;
        const tipe = row.cells[4].textContent.trim();

        const html = `
            <div class="detail-item mb-3">
                <label class="fw-bold text-muted">Nomor</label>
                <p>${no}</p>
            </div>
            <div class="detail-item mb-3">
                <label class="fw-bold text-muted">Kategori</label>
                <p><span class="badge bg-light text-dark">${kategori}</span></p>
            </div>
            <div class="detail-item mb-3">
                <label class="fw-bold text-muted">Kode</label>
                <p><code class="text-danger fs-5">${kode}</code></p>
            </div>
            <div class="detail-item mb-3">
                <label class="fw-bold text-muted">Pertanyaan</label>
                <p><strong>${pertanyaan}</strong></p>
            </div>
            <div class="detail-item mb-3">
                <label class="fw-bold text-muted">Tipe Input</label>
                <p>${tipe}</p>
            </div>
            <div class="detail-item">
                <label class="fw-bold text-muted">Deskripsi</label>
                <p class="text-muted">${deskripsi}</p>
            </div>
        `;

        document.getElementById('detailContent').innerHTML = html;
        
        // Store data untuk digunakan saat click "Buat Pertanyaan Ini"
        document.getElementById('btnBuatPertanyaan').dataset.kode = kode;
        document.getElementById('btnBuatPertanyaan').dataset.pertanyaan = pertanyaan;
    }

    // Handle click button "Buat Pertanyaan Ini"
    document.getElementById('btnBuatPertanyaan').addEventListener('click', function() {
        const kode = this.dataset.kode;
        const pertanyaan = this.dataset.pertanyaan;
        
        // Close detail modal
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        if (detailModal) {
            detailModal.hide();
        }
        
        // Open create form dengan pre-fill data
        setTimeout(function() {
            createQuestion(kode, pertanyaan);
        }, 300);
    });

    // Filter fungsi
    document.getElementById('filterKategori').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.kategoriBaris').forEach(row => {
            const kategori = row.dataset.kategori;
            if (kategori.includes(searchTerm) || searchTerm === '') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function createQuestion(kode, pertanyaan) {
        // Load form CREATE via AJAX
        $.get("{{ url('/admin/pertanyaan/create_ajax') }}", function (data) {
            // Cari atau buat container modal jika belum ada
            if ($('#referenceModalContainer').length === 0) {
                $('body').append('<div id="referenceModalContainer"></div>');
            }
            
            // Load form ke container
            $('#referenceModalContainer').html(data);
            
            // Pre-fill form dengan data dari reference
            setTimeout(function() {
                document.getElementById('kode_soal').value = kode;
                document.getElementById('question_text').value = pertanyaan;
                document.getElementById('hint').value = '';
                document.getElementById('type').value = '';
                document.getElementById('urutan').value = '1';
                document.getElementById('options').value = '';
                document.getElementById('matrixItemsList').innerHTML = '';
                
                // Trigger modal create untuk terbuka
                var modal = new bootstrap.Modal(document.getElementById('modalCreate'));
                modal.show();
            }, 100);
        });
    }

    // Download template function
    function downloadTemplate() {
        window.location.href = "{{ route('pertanyaan.download-template') }}";
    }

    // Handle upload form
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const btnUpload = document.getElementById('btnUpload');
        const originalText = btnUpload.innerHTML;
        btnUpload.disabled = true;
        btnUpload.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

        $.ajax({
            url: "{{ route('pertanyaan.import-questions') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: `<strong>${response.message}</strong><br><br>` +
                          (response.errors.length > 0 ? 
                              '<strong>Peringatan:</strong><br>' + response.errors.join('<br>') : 
                              'Semua data berhasil diimport!'),
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                    document.getElementById('uploadForm').reset();
                    
                    // Reload halaman atau table
                    if (window.tablePertanyaan) {
                        window.tablePertanyaan.ajax.reload();
                    }
                });
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message || 'Terjadi kesalahan saat import file'
                });
            },
            complete: function() {
                btnUpload.disabled = false;
                btnUpload.innerHTML = originalText;
            }
        });
    });
</script>
@endpush

@push('css')
<style>
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .kategoriBaris:hover {
        background-color: #f8f9fa;
    }

    .detail-item label {
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .modern-filter-input {
    height: 42px;

    border-radius: 12px;

    border: 1px solid #dbe4f0;

    padding-left: 14px;

    font-size: 0.92rem;
    font-weight: 500;

    transition: all 0.25s ease;

    box-shadow: none;
    }

    .modern-filter-input:focus {
        border-color: #2563eb;

        box-shadow:
            0 0 0 4px rgba(37,99,235,0.12);

        outline: none;
    }

    .modern-filter-input::placeholder {
        color: #94a3b8;
    }

    /* MOBILE */
    @media (max-width: 768px) {

        .modern-filter-input {
            width: 100%;
        }

        .card-header .btn {
            width: 100%;
        }
    }
</style>
@endpush
