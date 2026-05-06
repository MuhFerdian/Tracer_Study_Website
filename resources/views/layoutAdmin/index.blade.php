@extends('layoutAdmin.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    {{-- Cards dan Tabel --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Alumni</h5>
                    <h3 id="totalAlumni">0</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Sudah Isi</h5>
                    <h3 id="sudahIsi">0</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Belum Isi</h5>
                    <h3 id="belumIsi">0</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Persentase</h5>
                    <h3 id="persentase">0%</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik dan Tabel --}}
    <div class="row">
        {{-- Grafik Sebaran Jenis Instansi --}}
        <div class="col-xl-6">
            <div class="card mb-4 equal-height-card"> {{-- Added equal-height-card for consistency --}}
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Sebaran Jenis Instansi
                </div>
                <div class="card-body">
                    <canvas id="instansiChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        {{-- Grafik Sebaran Profesi Lulusan --}}
        <div class="col-xl-6">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Sebaran Profesi Lulusan
                </div>
                <div class="card-body">
                    <canvas id="profesiChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- This div was mistakenly closing the row div, moved below --}}


    {{-- Tabel Sebaran Profesi --}}
    <div class="row"> {{-- Wrap tables in their own row for proper layout --}}
        <div class="col-xl-12 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>Tabel Sebaran Profesi
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelAlumni">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">Tahun Lulus</th>
                                    <th rowspan="2" class="text-center align-middle">Jumlah Lulusan</th>
                                    <th rowspan="2" class="text-center align-middle">Jumlah Lulusan yang terlacak</th>
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
                                <tr>
                                    <td colspan="8" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rata-rata Masa Tunggu --}}
        <div class="col-xl-12 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>Tabel Rata-rata Masa Tunggu
                </div>
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
                                <tr>
                                    <td colspan="4" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Penilaian Kepuasan Pengguna Lulusan --}}
        <div class="col-xl-12 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>Tabel Penilaian Kepuasan Pengguna Lulusan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabelAlumniSatisfaction">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Jenis Kemampuan</th>
                                    <th colspan="4" class="text-center">Tingkat Kepuasan Pengguna (%)</th>
                                </tr>
                                <tr>
                                    <th>Sangat Baik</th>
                                    <th>Baik</th>
                                    <th>Cukup</th>
                                    <th>Kurang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kerjasama Tim
                </div>
                <div class="card-body">
                    <canvas id="kerjaSamaChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Keahlian Dalam IT
                </div>
                <div class="card-body">
                    <canvas id="keahlian" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kemampuan Bahasa Asing(Inggris)
                </div>
                <div class="card-body">
                    <canvas id="kemampuanBahasa" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kemampuan Komunikasi
                </div>
                <div class="card-body">
                    <canvas id="kemampuanKomunikasi" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Pengembangan Diri
                </div>
                <div class="card-body">
                    <canvas id="pengembanganDiri" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <div class="card mb-4 equal-height-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Kepemimpinan
                </div>
                <div class="card-body">
                    <canvas id="kepemimpinan" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 offset-xl-3 mt-4">
    <div class="card mb-4 equal-height-card">
        <div class="card-header">
            <i class="fas fa-chart-pie me-1"></i>Grafik Ulasan Etos Kerja
        </div>
        <div class="card-body">
            <canvas id="etosKerja" width="100%" height="40"></canvas>
        </div>
    </div>
</div>
    </div> {{-- Close the row div that contains all tables --}}
</div>
@endsection

@push('css')
<style>
    .card-header {
        background-color: #f4f6f9;
    }
    .btn {
        transition: all 0.3s ease-in-out;
    }
    .btn:hover {
        transform: scale(1.05);
    }
    table.dataTable thead th {
        border-top: 2px solid #dee2e6;
    }
    .equal-height-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .equal-height-card .card-body {
        flex-grow: 1; /* Allow card body to grow and fill available space */
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {

    // =========================
    // 🔥 GLOBAL CHART FUNCTION
    // =========================
    function renderPieChart(canvasId, labels, dataValues, titleText) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        // destroy chart lama (biar tidak numpuk)
        if (canvas.chart) {
            canvas.chart.destroy();
        }

        canvas.chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        '#007bff','#ffc107','#28a745','#dc3545',
                        '#6610f2','#fd7e14','#20c997','#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                legend: {
                    display: true,
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: titleText
                }
            }
        });
    }

    // =========================
    // 🔥 LOAD PIE GENERIC
    // =========================
    function loadChart(url, canvasId, title) {
        $.get(url, function(res){

            if (!res || res.length === 0) {
                $('#' + canvasId).parent().html(`
                    <div class="text-center text-muted">
                        Tidak ada data
                    </div>
                `);
                return;
            }

            const labels = ['Sangat Baik','Baik','Cukup','Kurang'];

            let map = {
                'Sangat Baik': 0,
                'Baik': 0,
                'Cukup': 0,
                'Kurang': 0
            };

            res.forEach(item => {
                if(map.hasOwnProperty(item.tingkat_kepuasan)){
                    map[item.tingkat_kepuasan] = item.jumlah_responden_per_tingkat;
                }
            });

            renderPieChart(
                canvasId,
                labels,
                labels.map(l => map[l]),
                title
            );
        })
        .fail(function(){
            $('#' + canvasId).parent().html(`
                <div class="text-danger text-center">
                    Gagal load data
                </div>
            `);
        });
    }

    // ==============================
    // 🔥 Total Alumni & Presentasi
    // ===============================
    $.get("{{ url('/admin/dashboard/summary') }}", function(res){

        $('#totalAlumni').text(res.total_alumni);
        $('#sudahIsi').text(res.sudah_isi);
        $('#belumIsi').text(res.belum_isi);

        let persen = 0;
        if(res.total_alumni > 0){
            persen = (res.sudah_isi / res.total_alumni * 100).toFixed(1);
        }

        $('#persentase').text(persen + "%");
    });

    // =========================
    // 🔥 INSTANSI CHART
    // =========================
    $.get("{{ url('admin/dashboard/instansi-chart') }}", function(res){
        renderPieChart(
            'instansiChart',
            res.map(i => i.jenis_instansi),
            res.map(i => i.total),
            'Sebaran Instansi'
        );
    });

    // =========================
    // 🔥 PROFESI CHART
    // =========================
    $.get("{{ url('/admin/dashboard/profesi-chart') }}", function(res){

        const labels = res.map(x => x.profesi);
        const values = res.map(x => x.total);

        new Chart(document.getElementById('profesiChart'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#007bff','#ffc107','#28a745','#dc3545']
                }]
            }
        });

    });

    // =========================
    // 🔥 LOAD SEMUA CHART
    // =========================
    loadChart('/admin/dashboard/kerjasama-chart','kerjaSamaChart','Kerjasama Tim');
    loadChart('/admin/dashboard/keahlian-chart','keahlian','Keahlian IT');
    loadChart('/admin/dashboard/kemampuan-bahasa-chart','kemampuanBahasa','Bahasa Inggris');
    loadChart('/admin/dashboard/kemampuan-komunikasi-chart','kemampuanKomunikasi','Komunikasi');
    loadChart('/admin/dashboard/pengembangan-diri-chart','pengembanganDiri','Pengembangan Diri');
    loadChart('/admin/dashboard/kepemimpinan-chart','kepemimpinan','Kepemimpinan');
    loadChart('/admin/dashboard/etos-kerja-chart','etosKerja','Etos Kerja');

    // =========================
    // 🔥 TABEL REKAP ALUMNI
    // =========================
    $.get("{{ url('/admin/dashboard/rekap-alumni') }}", function(res){

        let tbody = '';
        let total = {
            lulus:0, terlacak:0, infokom:0,
            non:0, multi:0, nasional:0, wirausaha:0
        };

        res.forEach(r=>{
            tbody += `
                <tr>
                    <td>${r.tahunlulus}</td>
                    <td>${r.jumlahlulusan}</td>
                    <td>${r.terlacaklulusan}</td>
                    <td>${r.infokom}</td>
                    <td>${r.noninfokom}</td>
                    <td>${r.multinasional}</td>
                    <td>${r.nasional}</td>
                    <td>${r.wirausaha}</td>
                </tr>
            `;

            total.lulus += +r.jumlahlulusan;
            total.terlacak += +r.terlacaklulusan;
            total.infokom += +r.infokom;
            total.non += +r.noninfokom;
            total.multi += +r.multinasional;
            total.nasional += +r.nasional;
            total.wirausaha += +r.wirausaha;
        });

        tbody += `
            <tr style="font-weight:bold;background:#eee">
                <td>Jumlah</td>
                <td>${total.lulus}</td>
                <td>${total.terlacak}</td>
                <td>${total.infokom}</td>
                <td>${total.non}</td>
                <td>${total.multi}</td>
                <td>${total.nasional}</td>
                <td>${total.wirausaha}</td>
            </tr>
        `;

        $('#tabelAlumni tbody').html(tbody);
    });


    // =========================
    // 🔥 TABEL WAITING TIME
    // =========================
    $.get("{{ url('/admin/dashboard/average-waiting-time') }}", function(res){

        let tbody = '';
        let totalLulus = 0;
        let totalTerlacak = 0;

        res.forEach(r=>{
            tbody += `
                <tr>
                    <td>${r.tahunlulus}</td>
                    <td>${r.jumlahlulusan}</td>
                    <td>${r.terlacaklulusan}</td>
                    <td>${r.rata_rata_waktu_tunggu_bulan}</td>
                </tr>
            `;

            totalLulus += +r.jumlahlulusan;
            totalTerlacak += +r.terlacaklulusan;
        });

        tbody += `
            <tr style="font-weight:bold;background:#eee">
                <td>Jumlah</td>
                <td>${totalLulus}</td>
                <td>${totalTerlacak}</td>
                <td>-</td>
            </tr>
        `;

        $('#tabelAverageWaitingTime tbody').html(tbody);
    });


    // =========================
    // 🔥 TABEL KEPUASAN
    // =========================
    $.get("{{ url('/admin/dashboard/alumni-satisfaction') }}", function(res){

        if (!res || res.length === 0) {
            $('#tabelAlumniSatisfaction tbody').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Tidak ada data
                    </td>
                </tr>
            `);
            return;
        }

        let tbody = '';
        let no = 1;

        res.forEach(r=>{
            tbody += `
                <tr>
                    <td>${no++}</td>
                    <td>${r.jenis_kemampuan}</td>
                    <td>${r.sangat_baik}</td>
                    <td>${r.baik}</td>
                    <td>${r.cukup}</td>
                    <td>${r.kurang}</td>
                </tr>
            `;
        });

        $('#tabelAlumniSatisfaction tbody').html(tbody);
    });

});
</script>
@endpush