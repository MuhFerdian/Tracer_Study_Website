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
            <div class="col-lg-4 text-lg-end">
                <span class="badge rounded-pill bg-light text-primary px-3 py-2">
                    <i class="fas fa-chart-line me-2"></i>Data monitoring
                </span>
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
                    <button class="btn btn-sm btn-outline-secondary" id="btnKelolaAktif" style="display:none">
                        <i class="fas fa-cog me-1"></i>Kelola Aktif
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
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i>Grafik Sebaran Profesi Lulusan</div>
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
     TOAST NOTIFICATION
============================================ --}}
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
                            <input type="date" name="tanggal_buka" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Tutup <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_tutup" class="form-control" required>
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
    .badge-aktif { background-color: #28a745; color: #fff; }
    .badge-tutup { background-color: #6c757d; color: #fff; }
    .badge-draft { background-color: #ffc107; color: #333; }
    #modalKonfirmasiPeriode .modal-content { border-radius: 1rem; }
    #dashboardToast { min-width: 280px; }
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

        var newBtn = btnYa.cloneNode(true);
        btnYa.parentNode.replaceChild(newBtn, btnYa);
        newBtn.addEventListener('click', function () {
            bootstrap.Modal.getInstance(document.getElementById('modalKonfirmasiPeriode')).hide();
            if (typeof opts.onConfirm === 'function') opts.onConfirm();
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKonfirmasiPeriode')).show();
    }

    // =============================================
    // LOAD DAFTAR PERIODE
    // =============================================
    function loadPeriodes() {
        $.get("{{ url('/admin/survey-period') }}", function (res) {
            var $sel = $('#selectPeriode');
            $sel.find('option:not(:first)').remove();

            if (!res.data || res.data.length === 0) {
                $('#infoPeriodeAktif').html(
                    '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Belum ada periode survei. Klik "Buat Periode" untuk memulai.</span>'
                );
                loadAllDashboardData();
                return;
            }

            res.data.forEach(function (p) {
                var icon = p.status === 'aktif' ? ' 🟢' : (p.status === 'draft' ? ' 🟡' : '');
                $sel.append('<option value="' + p.id + '" data-status="' + p.status + '" data-obj=\'' + JSON.stringify(p) + '\'>'
                    + p.nama + icon + '</option>');
            });

            var aktif = res.data.find(function (p) { return p.status === 'aktif'; });
            if (aktif) {
                $sel.val(aktif.id);
                selectedPeriodId = aktif.id;
                showInfoPeriode(aktif);
            } else {
                $('#infoPeriodeAktif').html('<span class="text-muted"><i class="fas fa-info-circle me-1"></i>Tidak ada periode aktif. Pilih periode lalu klik "Kelola Aktif".</span>');
                $('#btnKelolaAktif').hide();
            }

            loadAllDashboardData();
        });
    }

    function showInfoPeriode(p) {
        if (!p) { $('#btnKelolaAktif').hide(); return; }
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

    function renderPie(canvasId, labels, values) {
        var el = document.getElementById(canvasId);
        if (!el) return;
        if (el._chart) el._chart.destroy();
        el._chart = new Chart(el.getContext('2d'), {
            type: 'pie',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: COLORS }] },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    function showEmpty(id, msg) {
        $('#' + id).html('<p class="text-muted text-center my-3">' + (msg || 'Tidak ada data') + '</p>');
    }

    function loadKompetensi(url, canvasId, wrapperId) {
        $.get(url + periodParam())
            .done(function (res) {
                if (!res || !res.length) { showEmpty(wrapperId); return; }
                var order = ['Sangat Baik','Baik','Cukup','Kurang'];
                var map   = {'Sangat Baik':0,'Baik':0,'Cukup':0,'Kurang':0};
                res.forEach(function (r) { if (r.tingkat_kepuasan in map) map[r.tingkat_kepuasan] = r.jumlah_responden_per_tingkat; });
                renderPie(canvasId, order, order.map(function (l) { return map[l]; }));
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

        $.get("{{ url('/admin/dashboard/instansi-chart') }}" + p, function (res) {
            if (!res || !res.length) { showEmpty('instansiChartWrap'); return; }
            renderPie('instansiChart', res.map(function (i) { return i.jenis_instansi; }), res.map(function (i) { return i.total; }));
        }).fail(function () { showEmpty('instansiChartWrap', 'Gagal memuat data'); });

        $.get("{{ url('/admin/dashboard/profesi-chart') }}" + p, function (res) {
            if (!res || !res.length) { showEmpty('profesiChartWrap'); return; }
            renderPie('profesiChart', res.map(function (x) { return x.profesi; }), res.map(function (x) { return x.total; }));
        }).fail(function () { showEmpty('profesiChartWrap', 'Gagal memuat data'); });

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
    // FORM BUAT PERIODE
    // =============================================
    $('#formTambahPeriode').on('submit', function (e) {
        e.preventDefault();
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
});
</script>
@endpush
