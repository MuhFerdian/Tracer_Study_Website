@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Lowongan Pekerjaan</h1>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Lowongan</span>
            <button type="button" class="btn btn-success btn-sm" onclick="addModal()">
                <i class="fas fa-plus-circle me-1"></i>Tambah Lowongan
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="table-loker">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Posisi</th>
                            <th>Perusahaan</th>
                            <th>Lokasi</th>
                            <th>Gaji</th>
                            <th>Batas Lamaran</th>
                            <th>Status</th>
                            <th width="150px">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form (Tambah & Edit) -->
<div class="modal fade" id="modalLoker" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 850px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Lowongan Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formLoker" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="loker_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Lowongan</label>
                        <input type="file" name="foto" id="foto" class="form-control">
                        <small class="text-muted">
                            Format: JPG, JPEG, PNG • Maksimal ukuran 5 MB
                        </small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Posisi</label>
                            <input type="text" name="posisi" id="posisi" class="form-control" placeholder="Contoh: Web Developer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control" placeholder="Nama perusahaan" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lokasi</label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Surabaya / Remote">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gaji</label>
                            <input type="text" name="gaji" id="gaji" class="form-control" placeholder="Rp 5.000.000">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Batas Lamaran</label>
                            <input type="date" name="batas_lamaran" id="batas_lamaran" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kontak</label>
                            <input type="text" name="kontak" id="kontak" class="form-control" placeholder="08123456789">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Link Lamaran</label>
                        <input type="url" name="link_lamaran" id="link_lamaran" class="form-control" placeholder="https://">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Tulis deskripsi lowongan..."></textarea>
                    </div>
                    <div class="mb-3" id="statusGroup" style="display:none;">
                        <label class="form-label fw-bold">Status Aktif</label>
                        <select name="aktif" id="aktif" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Lowongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Hapus Data Lowongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-0" style="background-color: #FFF3CD;">
                    <i class="fas fa-exclamation-circle me-2"></i><strong>Konfirmasi !!!</strong><br>
                    Apakah Anda ingin menghapus data lowongan seperti di bawah ini?
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light" width="40%">Posisi :</th>
                        <td id="hapus_posisi"></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Perusahaan :</th>
                        <td id="hapus_perusahaan"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-warning text-dark fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmHapus" class="btn btn-danger fw-bold">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let table;
$(document).ready(function(){
    table = $('#table-loker').DataTable({
        processing: true,
        ajax: {
            url: "{{ url('admin/lowongan-pekerjaan/list') }}",
            dataSrc: ""
        },
        columns: [
            { data: null, 
        sortable: false, 
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }},
            { data: 'posisi' },
            { data: 'nama_perusahaan' },
            { data: 'lokasi' },
            { data: 'gaji' },
            { data: 'batas_lamaran' },
            { 
                data: 'aktif',
                render: function(data){
                    return data ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';
                }
            },
            {
                data: 'id',
                render: function(id, type, row){
                    return `
                        <div class="d-flex gap-1">
                            <button onclick="showDetail(${id})" class="btn btn-info btn-sm text-white">Detail</button>
                            <button onclick="editModal(${id})" class="btn btn-warning btn-sm">Edit</button>
                            <button onclick="deleteData(${id}, '${row.posisi}', '${row.nama_perusahaan}')" class="btn btn-danger btn-sm">Hapus</button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Simpan
    $('#formLoker').off('submit').on('submit', function(e){
    e.preventDefault();

    let id = $('#loker_id').val();

    let url = id 
        ? "{{ url('admin/lowongan-pekerjaan') }}/" + id + "/update"
        : "{{ url('admin/lowongan-pekerjaan/store') }}";

    let form = $('#formLoker')[0];
    let formData = new FormData(form);

    if(id){
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
            $('#modalLoker').modal('hide');
            table.ajax.reload();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message ?? 'Data berhasil disimpan'
            });
        },
        error: function(xhr){
             let message = 'Terjadi kesalahan';

    if(xhr.responseJSON && xhr.responseJSON.errors){
        message = Object.values(xhr.responseJSON.errors)
                    .map(err => err[0])
                    .join('<br>');
    }

    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        html: message
    });
}
    });
});
});

function addModal() {
    $('#formLoker')[0].reset();
    $('#loker_id').val(''); 
    $('#modalTitle').text('Tambah Lowongan Pekerjaan');
    $('#statusGroup').hide();
    $('#modalLoker').modal('show');
}

function editModal(id) {
    $.get(`{{ url('admin/lowongan-pekerjaan') }}/${id}/edit`, function(data){
        $('#loker_id').val(data.id);
        $('#posisi').val(data.posisi);
        $('#nama_perusahaan').val(data.nama_perusahaan);
        $('#lokasi').val(data.lokasi);
        $('#gaji').val(data.gaji);
        $('#batas_lamaran').val(data.batas_lamaran);
        $('#kontak').val(data.kontak);
        $('#link_lamaran').val(data.link_lamaran);
        $('#deskripsi').val(data.deskripsi);
        $('#aktif').val(data.aktif);
        
        $('#modalTitle').text('Edit Lowongan Pekerjaan');
        $('#statusGroup').show();
        $('#modalLoker').modal('show');
    });
}

function showDetail(id) {
    window.location.href = `{{ url('admin/lowongan-pekerjaan') }}/${id}/show`;
}

let deleteId = null;
function deleteData(id, posisi, perusahaan) {
    deleteId = id;
    $('#hapus_posisi').text(posisi);
    $('#hapus_perusahaan').text(perusahaan);
    $('#modalHapus').modal('show');
}
$('#btnConfirmHapus').click(function() {
    $.ajax({
        url: "{{ url('admin/lowongan-pekerjaan') }}/" + deleteId + "/delete",
        type: "POST",
        data: { 
            _token: "{{ csrf_token() }}",
            _method: "DELETE" 
        },
        success: function(res) {
            $('#modalHapus').modal('hide');
            table.ajax.reload();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            });
        },
        error: function() {
            Swal.fire('Gagal', 'Data gagal dihapus', 'error');
        }
    });
});
</script>
@endpush

<style>

.card-lowongan{
    transition: all .25s ease;
}

.card-lowongan:hover{
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,.12)!important;
}

.btn-action{
    height: 42px;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    padding: 0;
}

</style>