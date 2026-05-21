@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Dosen</h1>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Daftar Dosen</span>
            {{-- <button onclick="tambah()" class="btn btn-primary">Tambah Dosen</button> --}}
            <button class="btn btn-success btn-sm" onclick="tambah()"><i class="fas fa-plus-circle me-1"></i>Tambah Dosen</button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="table-dosen">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
let table;

$(document).ready(function(){
    table = $('#table-dosen').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/admin/manajemen-dosen/list",
        columns: [
            { data: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'username' },
            { data: 'name' },
            { data: 'email' },
            { data: 'status', orderable:false },
            { data: 'aksi', orderable:false, searchable:false }
        ]
    });
});

// 🔥 PAKAI modalAction (PENTING)
function tambah(){
    modalAction("/admin/manajemen-dosen/create_ajax");
}


function edit(id){
    modalAction("/admin/manajemen-dosen/" + id + "/edit_ajax");
}

function hapus(id){
    modalAction("/admin/manajemen-dosen/" + id + "/delete_ajax");
}


</script>
@endpush