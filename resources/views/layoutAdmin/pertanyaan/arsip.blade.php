@extends('layoutAdmin.app')

@section('title', 'Arsip Pertanyaan')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Arsip Pertanyaan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/admin/pertanyaan') }}">Pertanyaan</a></li>
        <li class="breadcrumb-item active">Arsip</li>
    </ol>

    <div class="alert alert-secondary d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-archive fa-lg"></i>
        <div>
            Pertanyaan yang diarsip <strong>tidak akan tampil</strong> di survei alumni.
            Anda dapat memulihkan pertanyaan kapan saja.
        </div>
    </div>

    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-exclamation me-1"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-archive me-1"></i> Daftar Pertanyaan Diarsip</span>
            <div class="d-flex gap-2">
                <a href="{{ route('pertanyaan.arsip.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
                <a href="{{ url('/admin/pertanyaan') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableArsip" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Soal</th>
                            <th>Pertanyaan</th>
                            <th>Tipe</th>
                            <th>Opsi</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal container -->
<div id="modalContainer"></div>
@endsection

@push('css')
<style>
    .table th, .table td { vertical-align: middle; }
    .card-header { background-color: #f4f6f9; }
    table.dataTable thead th { border-top: 2px solid #dee2e6; }
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {
    $('#tableArsip').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '{{ url("/admin/pertanyaan/arsip/list") }}',
            error: function (xhr) {
                console.error('Ajax error:', xhr.responseText);
            }
        },
        columns: [
            { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
            { data: 'kode_soal', name: 'kode_soal' },
            { data: 'question_display', name: 'question_text' },
            { data: 'type', name: 'type' },
            { data: 'options_display', name: 'options', orderable: false },
            { data: 'urutan', name: 'urutan', className: 'text-center' },
            { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
        ]
    });
});

function restorePertanyaan(id) {
    Swal.fire({
        title: 'Pulihkan Pertanyaan?',
        text: 'Pertanyaan akan dikembalikan ke daftar aktif dan tampil di survei.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#aaa',
        confirmButtonText: '<i class="fas fa-undo me-1"></i>Ya, Pulihkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/pertanyaan/' + id + '/restore',
                type: 'PATCH',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                        $('#tableArsip').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Terjadi kesalahan.', 'error');
                }
            });
        }
    });
}

function modalDelete(url) {
    $.ajax({
        url: url,
        type: 'GET',
        success: function (html) {
            $('#modalContainer').html(html);
            $('#modalDelete').modal('show');
        },
        error: function () {
            Swal.fire('Gagal', 'Tidak dapat memuat konfirmasi hapus.', 'error');
        }
    });
}
</script>
@endpush
