<div class="modal-header">
    <h5 class="modal-title">Hapus Dosen</h5>
</div>

<div class="modal-body">
    Yakin ingin menghapus <b>{{ $user->name }}</b>?
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    <button onclick="hapusData({{ $user->id }})" class="btn btn-danger">Hapus</button>
</div>

<script>
function hapusData(id){
    $.ajax({
        url: "/admin/manajemen-dosen/" + id + "/delete",
        type: "DELETE",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res){
            $('#myModal').modal('hide');
            $('#table-dosen').DataTable().ajax.reload();

            Swal.fire('Berhasil', res.message, 'success');
        },
        error: function(){
            Swal.fire('Error', 'Gagal hapus data', 'error');
        }
    });
}
</script>