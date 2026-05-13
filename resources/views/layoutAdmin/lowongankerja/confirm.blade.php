<div class="modal-header">
    <h5 class="modal-title">Hapus Lowongan</h5>
</div>

<div class="modal-body">
    Yakin ingin menghapus <b>{{ $loker->posisi }}</b> di <b>{{ $loker->nama_perusahaan }}</b>?
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    <button onclick="hapusData({{ $loker->id }})" class="btn btn-danger">Hapus</button>
</div>

<script>
function hapusData(id){
    $.ajax({
        url: "/admin/loker/" + id + "/delete",
        type: "DELETE",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res){
            $('#myModal').modal('hide');
            $('#table-loker').DataTable().ajax.reload();

            Swal.fire('Berhasil', 'Lowongan berhasil dihapus', 'success');
        },
        error: function(){
            Swal.fire('Error', 'Gagal hapus data', 'error');
        }
    });
}
</script>