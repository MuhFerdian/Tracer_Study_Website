<div class="modal-header">
    <h5 class="modal-title">Edit Dosen</h5>
</div>

<form id="formEdit">
    @csrf
    @method('PUT')

    <div class="modal-body">
        <input type="text" name="username" value="{{ $user->username }}" class="form-control mb-2" required>
        <input type="text" name="name" value="{{ $user->name }}" class="form-control mb-2" required>
        <input type="email" name="email" value="{{ $user->email }}" class="form-control mb-2">

        <input type="password" name="password" class="form-control mb-2" placeholder="Kosongkan jika tidak diubah">

        <select name="status" class="form-control mb-2">
            <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
        </select>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Update</button>
    </div>
</form>

<script>
$('#formEdit').submit(function(e){
    e.preventDefault();

    $.ajax({
        url: "/admin/manajemen-dosen/{{ $user->id }}/update",
        type: "PUT",
        data: $(this).serialize(),
        success: function(res){
            $('#myModal').modal('hide');
            $('#table-dosen').DataTable().ajax.reload();

            Swal.fire('Berhasil', res.message, 'success');
        }
    });
});
</script>