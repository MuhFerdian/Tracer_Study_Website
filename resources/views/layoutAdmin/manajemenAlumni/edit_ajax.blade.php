<form id="form-edit-alumni" method="POST" action="{{ url('/admin/alumni/update/' . $alumni->id) }}">
    @csrf
    @method('PUT')
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- NIM --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">NIM <span class="text-danger">*</span></label>
                    <input type="text" name="nim" value="{{ $alumni->nim }}" class="form-control"
                        placeholder="Contoh: E41241479" required>
                    <small class="text-muted">NIM hanya boleh huruf dan angka, minimal 5 karakter.</small>
                    <small id="error-nim" class="error-text text-danger d-block"></small>
                </div>

                {{-- NAMA --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="nama_alumni" value="{{ $alumni->nama }}" class="form-control"
                        placeholder="Contoh: Muhammad Masrukhin Ferdian" required>
                    <small class="text-muted">Nama lengkap alumni sesuai ijazah.</small>
                    <small id="error-nama_alumni" class="error-text text-danger d-block"></small>
                </div>

                {{-- PROGRAM STUDI --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Program Studi <span class="text-danger">*</span></label>
                    <input type="text" name="prodi" value="{{ $alumni->prodi }}" class="form-control"
                        placeholder="Contoh: Teknik Informatika" required>
                    <small class="text-muted">Nama program studi hanya boleh huruf.</small>
                    <small id="error-prodi" class="error-text text-danger d-block"></small>
                </div>

                {{-- NO HP --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">No HP</label>
                    <input type="text" name="no_hp" value="{{ $alumni->no_hp }}" class="form-control"
                        placeholder="Contoh: 08123456789">
                    <small class="text-muted">Nomor HP aktif alumni, minimal 10 digit (opsional).</small>
                    <small id="error-no_hp" class="error-text text-danger d-block"></small>
                </div>

                {{-- EMAIL --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Email</label>
                    <input type="email" name="email" value="{{ $alumni->email }}" class="form-control"
                        placeholder="Contoh: muh.ferdian@gmail.com">
                    <small class="text-muted">Alamat email aktif alumni (opsional).</small>
                    <small id="error-email" class="error-text text-danger d-block"></small>
                </div>

                {{-- ALAMAT --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Alamat</label>
                    <input type="text" name="alamat" value="{{ $alumni->alamat }}" class="form-control"
                        placeholder="Contoh: Jl. Mawar No. 5, Nganjuk">
                    <small class="text-muted">Alamat domisili alumni saat ini (opsional).</small>
                    <small id="error-alamat" class="error-text text-danger d-block"></small>
                </div>

                {{-- ANGKATAN --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Angkatan <span class="text-danger">*</span></label>
                    <input type="number" name="angkatan" value="{{ $alumni->angkatan }}" class="form-control"
                        placeholder="Contoh: 2021" min="1900" max="2100">
                    <small class="text-muted">Tahun masuk kuliah alumni.</small>
                    <small id="error-angkatan" class="error-text text-danger d-block"></small>
                </div>

                {{-- TAHUN LULUS --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Tahun Lulus <span class="text-danger">*</span></label>
                    <input type="number" name="tanggal_lulus" value="{{ $alumni->tahun_lulus }}" class="form-control"
                        placeholder="Contoh: 2025" min="1900" max="2100">
                    <small class="text-muted">Tahun lulus tidak boleh lebih kecil dari angkatan.</small>
                    <small id="error-tanggal_lulus" class="error-text text-danger d-block"></small>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(function () {
    $('#form-edit-alumni').validate({
        rules: {
            nim:           { required: true, minlength: 5 },
            nama_alumni:   { required: true, minlength: 3 },
            prodi:         { required: true },
            no_hp:         { minlength: 10 },
            angkatan:      { digits: true, min: 1900, max: 2100 },
            tanggal_lulus: { required: true, digits: true, min: 1900, max: 2100 },
        },
        messages: {
            nim:           { required: 'NIM wajib diisi.', minlength: 'NIM minimal 5 karakter.' },
            nama_alumni:   { required: 'Nama wajib diisi.', minlength: 'Nama minimal 3 karakter.' },
            prodi:         { required: 'Program studi wajib diisi.' },
            no_hp:         { minlength: 'No HP minimal 10 digit.' },
            tanggal_lulus: { required: 'Tahun lulus wajib diisi.' },
        },
        submitHandler: function (form) {
            $.ajax({
                url: $(form).attr('action'),
                method: 'PUT',
                data: $(form).serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        dataUser.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgField, function (key, val) {
                            $('#error-' + key).text(val[0]);
                        });
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
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
