@extends('layoutAdmin.app')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Data Alumni Belum Mengisi Tracer Study</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard / Alumni Belum Mengisi</li>
        </ol>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fs-5"><i class="fas fa-users me-2"></i> Daftar Alumni Belum Mengisi TS</span>
                <a href="{{ route('export.alumni.belum') }}" class="btn btn-success px-4 py-2 fs-6">
                    <i class="fas fa-file-export me-2"></i> Export
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle" id="table_alumni_belum">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Alumni</th>
                                <th>NIM</th>
                                <th>Program Studi</th>
                                <th>Angkatan</th>
                                <th>Tahun Lulus</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($alumni->isNotEmpty())
                                @foreach ($alumni as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->nim }}</td>
                                        <td>{{ $item->prodi }}</td>
                                                <td>
                                                    @php
                                                        $angk = $item->angkatan;
                                                    @endphp
                                                    @if (empty($angk))
                                                        -
                                                    @elseif (preg_match('/^\d{4}$/', (string) $angk))
                                                        {{ $angk }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($angk)->format('Y') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $th = $item->tahun_lulus;
                                                    @endphp
                                                    @if (empty($th))
                                                        -
                                                    @elseif (preg_match('/^\d{4}$/', (string) $th))
                                                        {{ $th }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($th)->format('Y') }}
                                                    @endif
                                                </td>
                                        <td class="text-center">
                                            <a href="{{ url('/admin/alumni/' . $item->id . '/answers') }}" 
                                               class="btn btn-sm btn-secondary" 
                                               title="Lihat Detail Jawaban (Belum Terjawab)">
                                                <i class="fas fa-eye me-1"></i>Cek Jawaban
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if ($alumni->isEmpty())
                        <div class="alert alert-info mt-3 mb-0 text-center">
                            Tidak ada data alumni yang belum mengisi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .card-header {
            background-color: #f4f6f9;
        }

        #table_alumni_belum th {
            text-align: center;
        }

        table.dataTable thead th {
            border-top: 2px solid #dee2e6;
        }

        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter {
            margin-bottom: 20px;
        }

        table.dataTable {
            margin-top: 10px;
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function () {
            $('#table_alumni_belum').DataTable({
                responsive: true,
                autoWidth: false,
                // tambahkan opsi lain jika perlu
            });
        });
    </script>
@endpush