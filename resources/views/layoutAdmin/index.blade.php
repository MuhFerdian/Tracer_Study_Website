@extends('layoutAdmin.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">

    {{-- Hero --}}
    <div class="dashboard-hero">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <p class="text-uppercase fw-bold small mb-2 text-white-50">Dashboard Tracer Study</p>
                <h1 class="mb-2">Ringkasan Data Alumni</h1>
                <p class="mb-0">Pantau progres pengisian survey, sebaran pekerjaan, dan indikator kepuasan pengguna lulusan.</p>
            </div>
            <div class="col-lg-4 text-lg-end d-flex flex-wrap gap-2 justify-content-lg-end">
                <span class="badge rounded-pill bg-light text-primary px-4 py-2" style="font-size:.85rem; font-weight:700;">
                    <i class="fas fa-chart-line me-2"></i>Data monitoring
                </span>
                <a href="#" id="btnExportPdf"
                   class="badge rounded-pill px-4 py-2 text-decoration-none"
                   style="background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:.85rem;font-weight:700;display:inline-flex;align-items:center;gap:.4rem;transition:all .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.28)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.18)'"
                   title="Export laporan ringkasan jawaban alumni ke PDF">
                    <i class="fas fa-file-pdf"></i>Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================
         PANEL PERIODE SURVEI
    ============================================ --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold mb-1 small text-muted">FILTER PERIODE SURVEI</label>
                    <select id="selectPeriode" class="form-select form-select-sm">
                        <option value="">— Semua Periode —</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div id="infoPeriodeAktif" class="small text-muted mt-3">Memuat info periode...</div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#modalTambahPeriode">
                        <i class="fas fa-plus me-1"></i>Buat Periode
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-1" id="btnKelolaAktif" style="display:none">
                        <i class="fas fa-cog me-1"></i>Kelola Aktif
                    </button>
                    <button class="btn btn-sm btn-outline-danger" id="btnHapusPeriode" style="display:none; border: 2px solid #000000;">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Total Alumni</div>
                        <div class="metric-value" id="totalAlumni">0</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Sudah Isi</div>
                        <div class="metric-value" id="sudahIsi">0</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Belum Isi</div>
                        <div class="metric-value" id="belumIsi">0</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-user-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Persentase</div>
                        <div class="metric-value" id="persentase">0%</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-percent"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rata-rata Penghasilan Card --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Rata-rata Penghasilan/Bulan</div>
                        <div class="metric-value fs-5" id="rataRataPenghasilan">-</div>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Penghasilan Tertinggi</div>
                        <div class="metric-value fs-5" id="penghasilanTertinggi">-</div>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="metric-label mb-2">Penghasilan Terendah</div>
                        <div class="metric-value fs-5" id="penghasilanTerendah">-</div>
                    </div>
                    <div class="metric-icon">
                        <i class="fas fa-arrow-trend-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Pie --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Sebaran Jenis Instansi</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="instansiChartWrap">
                    <canvas id="instansiChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-bar me-1"></i>Grafik Sebaran Profesi Lulusan</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="profesiChartWrap">
                    <canvas id="profesiChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Sebaran Profesi --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fas fa-table me-1"></i>Tabel Sebaran Profesi</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelAlumni">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">Tahun Lulus</th>
                                    <th rowspan="2" class="text-center align-middle">Jumlah Lulusan</th>
                                    <th rowspan="2" class="text-center align-middle">Terlacak</th>
                                    <th colspan="2" class="text-center">Profesi Kerja</th>
                                    <th colspan="3" class="text-center">Lingkup Tempat Kerja</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Bidang Infokom</th>
                                    <th class="text-center">Bidang Non Infokom</th>
                                    <th class="text-center">Multinasional/Internasional</th>
                                    <th class="text-center">Nasional</th>
                                    <th class="text-center">Wirausaha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="8" class="text-center">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rata-rata Masa Tunggu --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fas fa-table me-1"></i>Tabel Rata-rata Masa Tunggu</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelAverageWaitingTime">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Tahun Lulus</th>
                                    <th>Jumlah Lulusan</th>
                                    <th>Jumlah Lulusan yang Terlacak</th>
                                    <th>Rata-rata Waktu Tunggu (Bulan)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rata-rata Penghasilan per Tahun Lulus --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fas fa-money-bill-wave me-1"></i>Tabel Rata-rata Penghasilan per Tahun Lulus</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelPenghasilan">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Tahun Lulus</th>
                                    <th>Jumlah Responden</th>
                                    <th>Rata-rata Penghasilan/Bulan</th>
                                    <th>Tertinggi</th>
                                    <th>Terendah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="5" class="text-center">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Kompetensi --}}    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kerjasama Tim</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="kerjaSamaChartWrap">
                    <canvas id="kerjaSamaChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Keahlian Dalam IT</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="keahlianWrap">
                    <canvas id="keahlian"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kemampuan Bahasa Asing (Inggris)</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="kemampuanBahasaWrap">
                    <canvas id="kemampuanBahasa"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kemampuan Komunikasi</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="kemampuanKomunikasiWrap">
                    <canvas id="kemampuanKomunikasi"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Pengembangan Diri</div>
                <div class="card-body d-flex align-items-center justify-content-center" id="pengembanganDiriWrap">
                    <canvas id="pengembanganDiri"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 d-none d-xl-block"></div>
    </div>

</div>

{{-- ============================================
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="dashboardToast" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="dashboardToastBody">
                <i class="fas fa-check-circle" id="dashboardToastIcon"></i>
                <span id="dashboardToastMsg"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

{{-- ============================================
     MODAL KONFIRMASI KELOLA PERIODE
============================================ --}}
<div class="modal fade" id="modalKonfirmasiPeriode" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-body text-center p-4">
                <div id="konfirmasiIcon" class="mb-3" style="font-size: 2.5rem;"></div>
                <h6 class="fw-bold mb-2" id="konfirmasiTitle"></h6>
                <p class="text-muted small mb-0" id="konfirmasiDesc"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm px-4" id="btnKonfirmasiYa">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     MODAL BUAT PERIODE SURVEI
============================================ --}}
<div class="modal fade" id="modalTambahPeriode" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambahPeriode">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Buat Periode Survei</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Tracer Study 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" min="2000" max="2100" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Buka <span class="text-danger">*</span></label>
                            <input type="date" id="inputTanggalBuka" name="tanggal_buka" class="form-control" required>
                            <small class="text-muted">Mulai survey dari tanggal ini</small>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Tutup <span class="text-danger">*</span></label>
                            <input type="date" id="inputTanggalTutup" name="tanggal_tutup" class="form-control" required>
                            <small id="warningTanggal" class="text-danger" style="display:none;">❌ Tanggal tutup harus lebih besar dari buka</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Opsional"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="langsung_aktifkan" name="langsung_aktifkan" value="1">
                        <label class="form-check-label" for="langsung_aktifkan">
                            Langsung aktifkan periode ini
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPeriode">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    /* ── Badge status periode ── */
    .badge-aktif { background-color: #28a745; color: #fff; }
    .badge-tutup { background-color: #dc2626; color: #fff; }
    .badge-draft { background-color: #ffc107; color: #333; }
    #modalKonfirmasiPeriode .modal-content { border-radius: 1rem; }
    #dashboardToast { min-width: 280px; }

    /* ── Panel filter periode ── */
    .card.mb-4.border-0.shadow-sm:has(#selectPeriode) {
        background: linear-gradient(135deg, #f0f6ff 0%, #ffffff 100%) !important;
        border-left: 4px solid #1557c0 !important;
        border-radius: 12px !important;
    }

    /* ── Metric cards — warna berbeda per posisi ── */
    .col-xl-3:nth-child(1) .metric-card::before { background: linear-gradient(90deg, #1557c0, #2582f3); }
    .col-xl-3:nth-child(2) .metric-card::before { background: linear-gradient(90deg, #059669, #34d399); }
    .col-xl-3:nth-child(3) .metric-card::before { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .col-xl-3:nth-child(4) .metric-card::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

    .col-xl-3:nth-child(1) .metric-icon { background: linear-gradient(135deg, #1557c0, #2582f3); }
    .col-xl-3:nth-child(2) .metric-icon { background: linear-gradient(135deg, #059669, #34d399); }
    .col-xl-3:nth-child(3) .metric-icon { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .col-xl-3:nth-child(4) .metric-icon { background: linear-gradient(135deg, #7c3aed, #a78bfa); }

    /* ── Metric cards penghasilan ── */
    .col-xl-4:nth-child(1) .metric-card::before { background: linear-gradient(90deg, #0891b2, #22d3ee); }
    .col-xl-4:nth-child(2) .metric-card::before { background: linear-gradient(90deg, #059669, #34d399); }
    .col-xl-4:nth-child(3) .metric-card::before { background: linear-gradient(90deg, #dc2626, #f87171); }

    .col-xl-4:nth-child(1) .metric-icon { background: linear-gradient(135deg, #0891b2, #22d3ee); }
    .col-xl-4:nth-child(2) .metric-icon { background: linear-gradient(135deg, #059669, #34d399); }
    .col-xl-4:nth-child(3) .metric-icon { background: linear-gradient(135deg, #dc2626, #f87171); }

    /* ── Card grafik — left border accent ── */
    .card:has(#instansiChart)    { border-left: 4px solid #1557c0 !important; }
    .card:has(#profesiChart)     { border-left: 4px solid #0891b2 !important; }
    .card:has(#kerjaSamaChart)   { border-left: 4px solid #059669 !important; }
    .card:has(#keahlian)         { border-left: 4px solid #7c3aed !important; }
    .card:has(#kemampuanBahasa)  { border-left: 4px solid #d97706 !important; }
    .card:has(#kemampuanKomunikasi) { border-left: 4px solid #dc2626 !important; }
    .card:has(#pengembanganDiri) { border-left: 4px solid #0f766e !important; }

    /* ── Card tabel — left border accent ── */
    .card:has(#tabelAlumni)           { border-left: 4px solid #1557c0 !important; }
    .card:has(#tabelAverageWaitingTime) { border-left: 4px solid #0891b2 !important; }
    .card:has(#tabelPenghasilan)      { border-left: 4px solid #059669 !important; }

    /* ── Hover effect metric card ── */
    .metric-card {
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 50px rgba(8, 34, 79, 0.14) !important;
    }

    /* ── Tombol periode ── */
    .btn-primary {
        background: linear-gradient(135deg, #1557c0, #2582f3) !important;
        border: none !important;
        box-shadow: 0 6px 18px rgba(21, 87, 192, 0.28);
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(21, 87, 192, 0.38) !important;
    }
    .btn-outline-secondary {
        border-color: #94a3b8 !important;
        color: #475569 !important;
        transition: all 0.2s ease;
    }
    .btn-outline-secondary:hover {
        background: #f1f5f9 !important;
        border-color: #64748b !important;
        transform: translateY(-1px);
    }

    /* ── Table summary row ── */
    .table-summary-row td {
        background: linear-gradient(135deg, #eff6ff, #f0fdf4) !important;
        color: #1e3a5f !important;
        font-weight: 700;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {

    var selectedPeriodId = null;

    // =============================================
    // TOAST HELPER
    // =============================================
    function showToast(msg, type) {
        var toast   = document.getElementById('dashboardToast');
        var body    = document.getElementById('dashboardToastMsg');
        var icon    = document.getElementById('dashboardToastIcon');
        var colors  = { success: 'bg-success text-white', danger: 'bg-danger text-white', warning: 'bg-warning text-dark' };
        var icons   = { success: 'fa-check-circle', danger: 'fa-times-circle', warning: 'fa-exclamation-triangle' };

        toast.className = 'toast align-items-center border-0 shadow ' + (colors[type] || colors.success);
        icon.className  = 'fas ' + (icons[type] || icons.success);
        body.textContent = msg;

        var bsToast = bootstrap.Toast.getOrCreateInstance(toast, { delay: 3500 });
        bsToast.show();
    }

    // =============================================
    // MODAL KONFIRMASI HELPER
    // =============================================
    function showKonfirmasi(opts) {
        document.getElementById('konfirmasiTitle').textContent = opts.title || '';
        document.getElementById('konfirmasiDesc').textContent  = opts.desc  || '';
        document.getElementById('konfirmasiIcon').textContent  = opts.icon  || '❓';

        var btnYa = document.getElementById('btnKonfirmasiYa');
        btnYa.className = 'btn btn-sm px-4 ' + (opts.btnClass || 'btn-primary');
        btnYa.textContent = opts.btnText || 'Ya, Lanjutkan';

        // Tampilkan/sembunyikan tombol batal sesuai kebutuhan
        var btnBatal = document.querySelector('#modalKonfirmasiPeriode [data-bs-dismiss="modal"]');
        if (btnBatal) {
            if (opts.hideCancel) {
                btnBatal.style.setProperty('display', 'none', 'important');
            } else {
                btnBatal.style.setProperty('display', 'inline-block', 'important');
            }
        }

        var newBtn = btnYa.cloneNode(true);
        btnYa.parentNode.replaceChild(newBtn, btnYa);
        newBtn.addEventListener('click', function () {
            bootstrap.Modal.getInstance(document.getElementById('modalKonfirmasiPeriode')).hide();
            if (typeof opts.onConfirm === 'function') opts.onConfirm();
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKonfirmasiPeriode')).show();
    }

    // =============================================
    // HAPUS PERIODE — ATTACH HANDLER
    // =============================================
    function attachDeleteHandler() {
        $('#btnHapusPeriode').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            var p = $(this).data('period');
            console.log('Delete button clicked, period:', p);
            
            if (!p || !p.id) {
                showToast('❌ Data periode tidak ditemukan.', 'danger');
                return false;
            }

            // Jika periode aktif, tampilkan peringatan/blokir
            if (p.status === 'aktif') {
                showKonfirmasi({
                    title     : '⚠️ Periode Sedang Aktif!',
                    desc      : 'Periode "' + p.nama + '" saat ini sedang aktif untuk survey alumni. Periode aktif tidak dapat dihapus. Silakan tutup periode ini terlebih dahulu sebelum menghapus.',
                    icon      : '⚠️',
                    btnClass  : 'btn-secondary',
                    btnText   : 'Mengerti',
                    hideCancel: true,
                    onConfirm : function () {
                        // Tidak melakukan apa-apa, hanya menutup modal
                    }
                });
                return false;
            }

            showKonfirmasi({
                title    : 'Hapus Periode?',
                desc     : 'Periode "' + p.nama + '" akan dihapus permanen. Tindakan ini tidak bisa dibatalkan. Lanjutkan?',
                icon     : '⚠️',
                btnClass : 'btn-danger',
                onConfirm: function () {
                    var deleteUrl = "{{ url('/admin/survey-period') }}/" + p.id + "/delete";
                    console.log('Confirming delete, URL:', deleteUrl);
                    
                    $.ajax({
                        url    : deleteUrl,
                        method : 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        dataType: 'json',
                        timeout: 5000,
                        success: function (res) {
                            console.log('Delete success:', res);
                            if (res && res.status) {
                                showToast('✓ ' + (res.message || 'Periode berhasil dihapus.'), 'success');
                                setTimeout(function() { loadPeriodes(); }, 500);
                            } else {
                                showToast('❌ ' + (res.message || 'Gagal menghapus periode.'), 'danger');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log('Delete error:', status, error, xhr);
                            
                            var msg = 'Terjadi kesalahan saat menghapus periode.';
                            if (xhr.status === 422) {
                                try {
                                    var res = xhr.responseJSON;
                                    msg = res.message || msg;
                                } catch(e) {}
                            }
                            
                            showToast('❌ ' + msg, 'danger');
                        }
                    });
                }
            });
            
            return false;
        });
    }

    // =============================================
    // LOAD DAFTAR PERIODE (dengan Real-time refresh)
    // =============================================
    function loadPeriodes() {
        $.get("{{ url('/admin/survey-period') }}", function (res) {
            var $sel = $('#selectPeriode');
            var currentVal = $sel.val(); // Simpan nilai yang dipilih saat ini
            $sel.find('option:not(:first)').remove();

            if (!res.data || res.data.length === 0) {
                $('#infoPeriodeAktif').html(
                    '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Belum ada periode survei. Klik "Buat Periode" untuk memulai.</span>'
                );
                if (!currentVal) loadAllDashboardData();
                return;
            }

            res.data.forEach(function (p) {
                var icon = p.status === 'aktif' ? ' 🟢' : (p.status === 'draft' ? ' 🟡' : ' 🔴');
                $sel.append('<option value="' + p.id + '" data-status="' + p.status + '" data-obj=\'' + JSON.stringify(p) + '\'>'
                    + p.nama + icon + '</option>');
            });

            var aktif = res.data.find(function (p) { return p.status === 'aktif'; });
            
            // Jika ada periode aktif dan belum ada pilihan, pilih otomatis
            if (aktif && !currentVal) {
                $sel.val(aktif.id);
                selectedPeriodId = aktif.id;
                showInfoPeriode(aktif);
                loadAllDashboardData();
            } else if (currentVal) {
                // Maintain pilihan yang sebelumnya
                $sel.val(currentVal);
                var selectedPeriod = res.data.find(function (p) { return p.id == currentVal; });
                if (selectedPeriod) {
                    showInfoPeriode(selectedPeriod);
                }
            } else if (aktif) {
                showInfoPeriode(aktif);
            } else {
                $('#infoPeriodeAktif').html('<span class="text-muted"><i class="fas fa-info-circle me-1"></i>Tidak ada periode aktif. Pilih periode lalu klik "Kelola Aktif".</span>');
                $('#btnKelolaAktif').hide();
            }
        });
    }

    function showInfoPeriode(p) {
        if (!p) { 
            $('#btnKelolaAktif').hide();
            $('#btnHapusPeriode').hide();
            return;
        }
        var badges = { aktif: 'badge-aktif', tutup: 'badge-tutup', draft: 'badge-draft' };
        var label  = { aktif: 'Aktif', tutup: 'Tutup', draft: 'Draft' };
        $('#infoPeriodeAktif').html(
            '<span class="badge ' + (badges[p.status] || '') + ' me-1">' + (label[p.status] || p.status) + '</span>'
            + '<strong>' + p.nama + '</strong>'
            + (p.tanggal_buka ? ' &nbsp;|&nbsp; ' + p.tanggal_buka.substring(0,10) + ' s/d ' + p.tanggal_tutup.substring(0,10) : '')
        );
        var btnLabel = p.status === 'aktif'
            ? '<i class="fas fa-stop-circle me-1"></i>Tutup Periode'
            : '<i class="fas fa-play-circle me-1"></i>Aktifkan Periode';
        $('#btnKelolaAktif').show().html(btnLabel).data('period', p);
        
        // Tombol Hapus selalu muncul agar user bisa klik & melihat peringatan jika periode aktif
        $('#btnHapusPeriode').show().data('period', p);
        attachDeleteHandler();
    }

    // =============================================
    // GANTI PERIODE
    // =============================================
    $('#selectPeriode').on('change', function () {
        selectedPeriodId = $(this).val() || null;
        var opt = $(this).find('option:selected');
        if (selectedPeriodId) {
            try { showInfoPeriode(JSON.parse(opt.attr('data-obj'))); } catch(e) {}
        } else {
            $('#infoPeriodeAktif').html('<span class="text-muted">Menampilkan semua periode.</span>');
            $('#btnKelolaAktif').hide();
            $('#btnHapusPeriode').hide(); // Sembunyikan tombol hapus saat "Semua Periode" terpilih
        }
        loadAllDashboardData();
    });

    function periodParam() {
        return selectedPeriodId ? '?period_id=' + selectedPeriodId : '';
    }

    // =============================================
    // CHART HELPERS
    // =============================================
    var COLORS = ['#007bff','#ffc107','#28a745','#dc3545','#6610f2','#fd7e14','#20c997','#6f42c1'];
    var KOMPETENSI_COLORS = {
        'Sangat Baik': '#1557c0',
        'Baik':        '#ffc107',
        'Cukup':       '#28a745',
        'Kurang':      '#dc3545'
    };

    // Pie chart — untuk Sebaran Jenis Instansi
    function renderPie(canvasId, labels, values) {
        var el = document.getElementById(canvasId);
        if (!el) return;
        if (el._chart) el._chart.destroy();
        el._chart = new Chart(el.getContext('2d'), {
            type: 'pie',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: COLORS }] },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed; } } }
                }
            }
        });
    }

    // Bar chart vertikal — untuk Sebaran Profesi Lulusan
    // function renderBar(canvasId, labels, values) {
    //     var el = document.getElementById(canvasId);
    //     if (!el) return;
    //     if (el._chart) el._chart.destroy();

    //     var maxVal = Math.max.apply(null, values);
    //     var suggestedMax = maxVal < 3 ? maxVal + 1 : Math.ceil(maxVal * 1.15);

    //     // Tinggi dinamis agar label tidak miring: minimal 250px, 50px per bar
    //     var dynHeight = Math.max(250, labels.length * 50);
    //     el.style.height = dynHeight + 'px';
    //     el.parentElement.style.alignItems = 'flex-start';

    //     el._chart = new Chart(el.getContext('2d'), {
    //         type: 'bar',
    //         data: {
    //             labels: labels,
    //             datasets: [{
    //                 label: 'Jumlah Alumni',
    //                 data: values,
    //                 backgroundColor: COLORS,
    //                 borderRadius: 6,
    //                 borderSkipped: false
    //             }]
    //         },
    //         options: {
    //             indexAxis: 'y',
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 legend: { position: 'top' },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: function(ctx) { return ' ' + ctx.parsed.x + ' alumni'; }
    //                     }
    //                 }
    //             },
    //             scales: {
    //                 x: {
    //                     beginAtZero: true,
    //                     min: 0,
    //                     suggestedMax: suggestedMax,
    //                     ticks: {
    //                         stepSize: 1,
    //                         precision: 0,
    //                         callback: function(value) {
    //                             return Number.isInteger(value) ? value : null;
    //                         },
    //                         maxRotation: 0,
    //                         minRotation: 0
    //                     },
    //                     grid: { color: 'rgba(0,0,0,0.06)' }
    //                 },
    //                 y: {
    //                     ticks: {
    //                         font: { size: 11 },
    //                         maxRotation: 0,
    //                         minRotation: 0,
    //                         autoSkip: false
    //                     },
    //                     grid: { display: false }
    //                 }
    //             }
    //         }
    //     });
    // }

    function renderBar(canvasId, labels, values) {

    var el = document.getElementById(canvasId);

    if (!el) return;

    if (el._chart) el._chart.destroy();

    var maxVal = Math.max.apply(null, values);

    var suggestedMax = maxVal < 3
        ? maxVal + 1
        : Math.ceil(maxVal * 1.15);

    // tinggi chart
    el.style.height = '320px';

    el._chart = new Chart(el.getContext('2d'), {

        type: 'bar',

        data: {
            labels: labels,

            datasets: [{
                label: 'Jumlah Alumni',
                data: values,
                backgroundColor: COLORS,
                borderRadius: 8,
                borderSkipped: false
            }]
        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'top'
                },

                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.parsed.y + ' alumni';
                        }
                    }
                }
            },

            scales: {

                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        font: {
                            size: 11
                        }
                    },

                    grid: {
                        display: false
                    }
                },

                y: {
                    beginAtZero: true,
                    min: 0,
                    suggestedMax: suggestedMax,

                    ticks: {
                        stepSize: 1,
                        precision: 0,

                        callback: function(value) {
                            return Number.isInteger(value)
                                ? value
                                : null;
                        }
                    },

                    grid: {
                        color: 'rgba(0,0,0,0.06)'
                    }
                }
            }
        }
    });
}

    // Doughnut chart — untuk grafik kompetensi
    function renderDoughnut(canvasId, labels, values, bgColors) {
        var el = document.getElementById(canvasId);
        if (!el) return;
        if (el._chart) el._chart.destroy();
        el._chart = new Chart(el.getContext('2d'), {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: bgColors, hoverOffset: 8 }] },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed; } } }
                }
            }
        });
    }

    function showEmpty(id, msg) {
        $('#' + id).html('<p class="text-muted text-center my-3">' + (msg || 'Tidak ada data') + '</p>');
    }

    function loadKompetensi(url, canvasId, wrapperId) {
        $.get(url + periodParam())
            .done(function (res) {
                if (!res || !res.length) { showEmpty(wrapperId); return; }
                var order  = ['Sangat Baik','Baik','Cukup','Kurang'];
                var map    = {'Sangat Baik':0,'Baik':0,'Cukup':0,'Kurang':0};
                res.forEach(function (r) { if (r.tingkat_kepuasan in map) map[r.tingkat_kepuasan] = r.jumlah_responden_per_tingkat; });
                var vals   = order.map(function (l) { return map[l]; });
                var colors = order.map(function (l) { return KOMPETENSI_COLORS[l]; });
                renderDoughnut(canvasId, order, vals, colors);
            })
            .fail(function () { showEmpty(wrapperId, 'Gagal memuat data'); });
    }

    // =============================================
    // LOAD SEMUA DATA DASHBOARD
    // =============================================
    function loadAllDashboardData() {
        var p = periodParam();

        $.get("{{ url('/admin/dashboard/summary') }}" + p, function (res) {
            $('#totalAlumni').text(res.total_alumni);
            $('#sudahIsi').text(res.sudah_isi);
            $('#belumIsi').text(res.belum_isi);
            $('#persentase').text(res.total_alumni > 0 ? (res.sudah_isi / res.total_alumni * 100).toFixed(1) + '%' : '0%');
        });

        // Pie chart — Sebaran Jenis Instansi
        $.get("{{ url('/admin/dashboard/instansi-chart') }}" + p, function (res) {
            if (!res || !res.length) { showEmpty('instansiChartWrap'); return; }
            renderPie('instansiChart', res.map(function (i) { return i.jenis_instansi; }), res.map(function (i) { return i.total; }));
        }).fail(function () { showEmpty('instansiChartWrap', 'Gagal memuat data'); });

        // Bar chart horizontal — Sebaran Profesi Lulusan
        $.get("{{ url('/admin/dashboard/profesi-chart') }}" + p, function (res) {
            if (!res || !res.length) { showEmpty('profesiChartWrap'); return; }
            renderBar('profesiChart', res.map(function (x) { return x.profesi; }), res.map(function (x) { return x.total; }));
        }).fail(function () { showEmpty('profesiChartWrap', 'Gagal memuat data'); });

        // Doughnut chart — Grafik Kompetensi
        loadKompetensi('/admin/dashboard/kerjasama-chart',           'kerjaSamaChart',      'kerjaSamaChartWrap');
        loadKompetensi('/admin/dashboard/keahlian-chart',            'keahlian',            'keahlianWrap');
        loadKompetensi('/admin/dashboard/kemampuan-bahasa-chart',    'kemampuanBahasa',     'kemampuanBahasaWrap');
        loadKompetensi('/admin/dashboard/kemampuan-komunikasi-chart','kemampuanKomunikasi', 'kemampuanKomunikasiWrap');
        loadKompetensi('/admin/dashboard/pengembangan-diri-chart',   'pengembanganDiri',    'pengembanganDiriWrap');

        $.get("{{ url('/admin/dashboard/rekap-alumni') }}" + p, function (res) {
            var tbody = '', t = {l:0,tr:0,i:0,n:0,m:0,na:0,w:0};
            res.forEach(function (r) {
                tbody += '<tr><td>' + (r.tahunlulus||'-') + '</td><td>' + r.jumlahlulusan + '</td><td>' + r.terlacaklulusan
                    + '</td><td>' + r.infokom + '</td><td>' + r.noninfokom + '</td><td>' + r.multinasional
                    + '</td><td>' + r.nasional + '</td><td>' + r.wirausaha + '</td></tr>';
                t.l+=+r.jumlahlulusan; t.tr+=+r.terlacaklulusan; t.i+=+r.infokom;
                t.n+=+r.noninfokom; t.m+=+r.multinasional; t.na+=+r.nasional; t.w+=+r.wirausaha;
            });
            tbody += '<tr class="table-summary-row fw-bold"><td>Jumlah</td><td>'+t.l+'</td><td>'+t.tr+'</td><td>'+t.i+'</td><td>'+t.n+'</td><td>'+t.m+'</td><td>'+t.na+'</td><td>'+t.w+'</td></tr>';
            $('#tabelAlumni tbody').html(tbody);
        });

        $.get("{{ url('/admin/dashboard/average-waiting-time') }}" + p, function (res) {
            var tbody = '', tl = 0, tt = 0;
            res.forEach(function (r) {
                tbody += '<tr><td>' + (r.tahunlulus||'-') + '</td><td>' + r.jumlahlulusan + '</td><td>' + r.terlacaklulusan + '</td><td>' + r.rata_rata_waktu_tunggu_bulan + '</td></tr>';
                tl += +r.jumlahlulusan; tt += +r.terlacaklulusan;
            });
            tbody += '<tr class="table-summary-row fw-bold"><td>Jumlah</td><td>'+tl+'</td><td>'+tt+'</td><td>-</td></tr>';
            $('#tabelAverageWaitingTime tbody').html(tbody);
        });

        // Penghasilan Alumni
        $.get("{{ url('/admin/dashboard/penghasilan-alumni') }}" + p, function (res) {
            // Summary cards
            var fmt = function (n) {
                if (!n || n == 0) return '-';
                return 'Rp ' + parseInt(n).toLocaleString('id-ID');
            };
            var s = res.summary;
            $('#rataRataPenghasilan').text(s && s.rata_rata   ? fmt(Math.round(s.rata_rata))   : '-');
            $('#penghasilanTertinggi').text(s && s.tertinggi  ? fmt(s.tertinggi)               : '-');
            $('#penghasilanTerendah').text(s && s.terendah    ? fmt(s.terendah)                : '-');

            // Tabel per tahun
            if (!res.per_tahun || !res.per_tahun.length) {
                $('#tabelPenghasilan tbody').html('<tr><td colspan="5" class="text-center text-muted">Belum ada data penghasilan (pastikan pertanyaan f505 sudah ada)</td></tr>');
                return;
            }
            var tbody = '';
            res.per_tahun.forEach(function (r) {
                tbody += '<tr>'
                    + '<td>' + (r.tahun_lulus || '-') + '</td>'
                    + '<td class="text-center">' + r.total_responden + '</td>'
                    + '<td>' + fmt(r.rata_rata) + '</td>'
                    + '<td>' + fmt(r.tertinggi) + '</td>'
                    + '<td>' + fmt(r.terendah)  + '</td>'
                    + '</tr>';
            });
            $('#tabelPenghasilan tbody').html(tbody);
        }).fail(function () {
            $('#rataRataPenghasilan, #penghasilanTertinggi, #penghasilanTerendah').text('-');
            $('#tabelPenghasilan tbody').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>');
        });
    }

    // =============================================
    // REAL-TIME VALIDASI TANGGAL
    // =============================================
    function validateTanggal() {
        var tanggalBuka = $('#inputTanggalBuka').val();
        var tanggalTutup = $('#inputTanggalTutup').val();
        var $warning = $('#warningTanggal');
        var $btnSimpan = $('#btnSimpanPeriode');
        
        if (tanggalBuka && tanggalTutup) {
            if (new Date(tanggalTutup) <= new Date(tanggalBuka)) {
                $warning.show();
                $btnSimpan.prop('disabled', true);
                $('#inputTanggalTutup').addClass('is-invalid');
            } else {
                $warning.hide();
                $btnSimpan.prop('disabled', false);
                $('#inputTanggalTutup').removeClass('is-invalid');
            }
        }
    }
    
    $('#inputTanggalBuka, #inputTanggalTutup').on('change', validateTanggal);

    // =============================================
    // FORM BUAT PERIODE
    // =============================================
    $('#formTambahPeriode').on('submit', function (e) {
        e.preventDefault();
        
        // Validasi tanggal frontend
        var tanggalBuka = new Date($('input[name=tanggal_buka]').val());
        var tanggalTutup = new Date($('input[name=tanggal_tutup]').val());
        
        // Cek apakah field kosong
        if (!$('input[name=tanggal_buka]').val()) {
            showToast('⚠️ Tanggal Buka harus diisi!', 'warning');
            return;
        }
        if (!$('input[name=tanggal_tutup]').val()) {
            showToast('⚠️ Tanggal Tutup harus diisi!', 'warning');
            return;
        }
        
        // Cek tanggal buka dan tutup sama
        if (tanggalBuka.getTime() === tanggalTutup.getTime()) {
            showToast('❌ Tanggal Buka dan Tutup tidak boleh sama!', 'danger');
            $('input[name=tanggal_tutup]').focus();
            return;
        }
        
        // Cek tanggal tutup lebih besar dari buka
        if (tanggalTutup <= tanggalBuka) {
            showToast('❌ Tanggal Tutup harus lebih besar dari Tanggal Buka!', 'danger');
            $('input[name=tanggal_tutup]').focus();
            return;
        }
        
        var $btn = $('#btnSimpanPeriode').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        $.ajax({
            url    : "{{ url('/admin/survey-period/store') }}",
            method : 'POST',
            data   : $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        }).done(function (res) {
            if (!res.status) {
                showToast(res.message || 'Gagal menyimpan periode.', 'danger');
                return;
            }

            var periodId = res.data.id;
            var aktifkan = $('input[name=langsung_aktifkan]').is(':checked');

            if (aktifkan) {
                $.ajax({
                    url    : "{{ url('/admin/survey-period') }}/" + periodId + "/aktifkan",
                    method : 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                }).always(function () {
                    $('#modalTambahPeriode').modal('hide');
                    $('#formTambahPeriode')[0].reset();
                    showToast('Periode berhasil dibuat dan diaktifkan.', 'success');
                    loadPeriodes();
                });
            } else {
                $('#modalTambahPeriode').modal('hide');
                $('#formTambahPeriode')[0].reset();
                showToast('Periode berhasil dibuat.', 'success');
                loadPeriodes();
            }
        }).fail(function (xhr) {
            var err = xhr.responseJSON;
            var msg = err && err.msgField
                ? 'Validasi gagal: ' + Object.values(err.msgField).flat().join(', ')
                : 'Terjadi kesalahan server.';
            showToast(msg, 'danger');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
        });
    });

    // =============================================
    // KELOLA AKTIF / TUTUP
    // =============================================
    $('#btnKelolaAktif').on('click', function () {
        var p = $(this).data('period');
        if (!p) return;
        var isAktif = p.status === 'aktif';

        showKonfirmasi({
            title    : (isAktif ? 'Tutup' : 'Aktifkan') + ' Periode?',
            desc     : '"' + p.nama + '" akan ' + (isAktif ? 'ditutup' : 'diaktifkan') + '. Lanjutkan?',
            icon     : isAktif ? '🔴' : '🟢',
            btnClass : isAktif ? 'btn-danger' : 'btn-success',
            onConfirm: function () {
                $.ajax({
                    url    : "{{ url('/admin/survey-period') }}/" + p.id + (isAktif ? '/tutup' : '/aktifkan'),
                    method : 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                }).done(function (res) {
                    if (res.status) {
                        showToast('Periode berhasil ' + (isAktif ? 'ditutup.' : 'diaktifkan.'), 'success');
                        loadPeriodes();
                    } else {
                        showToast(res.message || 'Gagal memperbarui periode.', 'danger');
                    }
                }).fail(function () {
                    showToast('Terjadi kesalahan. Coba lagi.', 'danger');
                });
            }
        });
    });

    // =============================================
    // INIT
    // =============================================
    loadPeriodes();

    // =============================================
    // SMART POLLING — Hanya saat tab FOKUS
    // =============================================
    var pollInterval = null;
    var POLL_INTERVAL = 10000; // 10 detik

    function startPolling() {
        if (!pollInterval) {
            pollInterval = setInterval(function() {
                loadPeriodes();
            }, POLL_INTERVAL);
            console.log('🟢 Polling DIMULAI (tab aktif)');
        }
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
            console.log('🔴 Polling DIHENTIKAN (tab tidak fokus)');
        }
    }

    // Mulai polling saat halaman pertama kali load
    startPolling();

    // Deteksi saat user fokus/tidak fokus ke tab ini
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Tab tidak fokus (user ke tab lain)
            stopPolling();
        } else {
            // Tab kembali fokus
            console.log('🟢 Tab kembali fokus → Load data sekarang');
            loadPeriodes(); // Load sekali langsung
            startPolling();
        }
    });

    // Bonus: Deteksi saat window blur (user pergi ke app lain)
    window.addEventListener('blur', stopPolling);
    window.addEventListener('focus', function() {
        console.log('🟢 Window fokus → Resume polling');
        loadPeriodes();
        startPolling();
    });

        // =============================================
    // TOMBOL EXPORT PDF � buka di tab baru
    // =============================================
    $('#btnExportPdf').on('click', function (e) {
        e.preventDefault();
        var url = '{{ route("laporan.pdf") }}';
        if (selectedPeriodId) {
            url += '?period_id=' + selectedPeriodId;
        }
        window.open(url, '_blank');
    });
});
</script>
@endpush