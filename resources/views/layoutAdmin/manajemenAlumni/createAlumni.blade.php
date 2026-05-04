<form action="{{ url('/admin/alumni/store') }}" method="POST" id="form-tambah-alumni">
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Alumni</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>NIM</label>
                    <input type="text" name="nim" id="nim" class="form-control" required>
                    <small id="error-nim" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama_alumni" id="nama_alumni" class="form-control" required>
                    <small id="error-nama_alumni" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Program Studi</label>
                    <input type="text" name="prodi" id="prodi" class="form-control">
                    <small id="error-prodi" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" id="no_hp" class="form-control">
                    <small id="error-no_hp" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                    <small id="error-email" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Tahun Lulus</label>
                    <input type="number" name="tanggal_lulus" id="tanggal_lulus" class="form-control" min="1900" max="2100">
                    <small id="error-tanggal_lulus" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Status Pekerjaan</label>
                    <input type="text" name="status_pekerjaan" id="status_pekerjaan" class="form-control">
                    <small id="error-status_pekerjaan" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama Instansi</label>
                    <input type="text" name="nama_instansi" id="nama_instansi" class="form-control">
                    <small id="error-nama_instansi" class="error-text form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Posisi</label>
                    <input type="text" name="posisi" id="posisi" class="form-control">
                    <small id="error-posisi" class="error-text form-text text-danger"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        // Close modal
        $('#myModal .btn-close, #myModal .btn-warning').on('click', function () {
            $('#myModal').modal('hide');
        });

        // Validasi dan submit via AJAX
        $("#form-tambah-alumni").validate({
            rules: {
                    prodi: { required: true },
                    nim: { required: true, minlength: 5 },
                    nama_alumni: { required: true, minlength: 3 },
                    // email: { email: true },
                    tanggal_lulus: { required: true, digits: true, min: 1900, max: 2100 }
                },
            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataUser.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function (prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
