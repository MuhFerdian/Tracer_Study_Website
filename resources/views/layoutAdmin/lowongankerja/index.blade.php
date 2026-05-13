@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Lowongan Pekerjaan</h1>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Daftar Lowongan</span>
            <button class="btn btn-success btn-sm" onclick="tambah()">
                <i class="fas fa-plus-circle me-1"></i>Tambah Loker
            </button>
        </div>

        <div class="card-body">
            <table class="table table-bordered" id="table-loker">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Posisi</th>
                        <th>Perusahaan</th>
                        <th>Lokasi</th>
                        <th>Gaji</th>
                        <th>Batas Lamaran</th>
                        <th>Kontak</th>
                        <th>Link Lamaran</th>
                        <th>Role</th>
                        <th>Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
let table;

$(document).ready(function(){
    table = $('#table-loker').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/admin/loker/list",
        columns: [
            { data: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'posisi' },
            { data: 'nama_perusahaan' },
            { data: 'lokasi' },
            { data: 'gaji' },
            { data: 'batas_lamaran' },
            { data: 'kontak' },
            { data: 'link_lamaran', orderable:false, searchable:false },
            { data: 'role' },
            { data: 'aktif', orderable:false },
            { data: 'aksi', orderable:false, searchable:false }
        ]
    });
});

function tambah(){
    modalAction("/admin/loker/create_ajax");
}
function edit(id){
    modalAction("/admin/loker/" + id + "/edit");
}

function hapus(id){
    modalAction("/admin/loker/" + id + "/delete");
}
</script>
@endpush