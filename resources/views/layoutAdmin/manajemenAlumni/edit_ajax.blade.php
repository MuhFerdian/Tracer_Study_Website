<form id="form-edit-alumni" method="POST" action="{{ url('/admin/alumni/update/' . $alumni->id) }}">
    @csrf
    @method('PUT')
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Alumni</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>NIM</label>
                    <input type="text" name="nim" value="{{ $alumni->nim }}" class="form-control" required>
                    <small id="error-nim" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama_alumni" value="{{ $alumni->nama }}" class="form-control" required>
                    <small id="error-nama_alumni" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Program Studi</label>
                    <input type="text" name="prodi" value="{{ $alumni->prodi }}" class="form-control">
                    <small id="error-prodi" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" value="{{ $alumni->no_hp }}" class="form-control">
                    <small id="error-no_hp" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $alumni->email }}" id="email" class="form-control">
                    <small id="error-email" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Angkatan</label>
                    <input type="number" name="angkatan" value="{{ $alumni->angkatan }}" class="form-control" min="1900" max="2100">
                    <small id="error-angkatan" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Tahun Lulus</label>
                    <input type="number" name="tanggal_lulus" value="{{ $alumni->tahun_lulus }}" class="form-control" min="1900" max="2100">
                    <small id="error-tanggal_lulus" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Status Pekerjaan</label>
                    <input type="text" name="status_pekerjaan" value="{{ $alumni->status_pekerjaan }}" class="form-control">
                    <small id="error-status_pekerjaan" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama Instansi</label>
                    <input type="text" name="nama_instansi" value="{{ $alumni->nama_instansi }}" class="form-control">
                    <small id="error-nama_instansi" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Posisi</label>
                    <input type="text" name="posisi" value="{{ $alumni->posisi }}" class="form-control">
                    <small id="error-posisi" class="error-text text-danger"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(function() {
    // Close modal
        $('#myModal .btn-close, #myModal .btn-warning').on('click', function () {
            $('#myModal').modal('hide');
        });

    $('#form-edit-alumni').validate({
        rules: {
            prodi: { required: true },
            nim: { required: true, minlength: 5 },
            nama_alumni: { required: true, minlength: 3 },
            angkatan: { required: true, digits: true, min: 1900, max: 2100 },
            tanggal_lulus: { required: true, digits: true, min: 1900, max: 2100 }
        },
        submitHandler: function(form) {
            $.ajax({
                url: $(form).attr('action'),
                method: 'PUT',
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        dataUser.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgField, function(key, val) {
                            $('#error-' + key).text(val[0]);
                        });
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                }
            });
            return false;
        }
    });
});
</script>