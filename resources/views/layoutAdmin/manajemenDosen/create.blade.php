<div class="modal-header" aria-hidden="true">
    <h5 class="modal-title">Tambah Dosen</h5>
</div>

<form id="formTambah">
    @csrf

    <div class="modal-body">
        <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
        <input type="text" name="name" class="form-control mb-2" placeholder="Nama" required>
        <input type="email" name="email" class="form-control mb-2" placeholder="Email">
        <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

        <select name="status" class="form-control mb-2">
            <option value="pending">Pending</option>
            <option value="active">Active</option>
        </select>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$('#formTambah').submit(function(e){
    e.preventDefault();

    $.post("/admin/manajemen-dosen/store", $(this).serialize(), function(res){

        $('#myModal').modal('hide');
        $('#table-dosen').DataTable().ajax.reload();

        Swal.fire('Berhasil', res.message, 'success');
    });
});
</script>