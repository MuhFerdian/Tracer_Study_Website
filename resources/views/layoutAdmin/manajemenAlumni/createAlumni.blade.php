<form action="{{ url('/admin/alumni/store') }}" method="POST" id="form-tambah-alumni">
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- NIM --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">NIM <span class="text-danger">*</span></label>
                    <input type="text" name="nim" id="nim" class="form-control"
                        placeholder="Contoh: E41241479" required>
                    <small class="text-muted">NIM hanya boleh huruf dan angka, minimal 5 karakter.</small>
                    <small id="error-nim" class="error-text form-text text-danger d-block"></small>
                </div>

                {{-- NAMA --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="nama_alumni" id="nama_alumni" class="form-control"
                        placeholder="Contoh: Muh Masrukhin Ferdian" required>
                    <small class="text-muted">Nama lengkap alumni sesuai ijazah.</small>
                    <small id="error-nama_alumni" class="error-text form-text text-danger d-block"></small>
                </div>

                {{-- PROGRAM STUDI --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Program Studi <span class="text-danger">*</span></label>
                    <input type="text" name="prodi" id="prodi" class="form-control"
                        placeholder="Contoh: Teknik Informatika" required>
                    <small class="text-muted">Nama program studi hanya boleh huruf.</small>
                    <small id="error-prodi" class="error-text form-text text-danger d-block"></small>
                </div>

                {{-- NO HP --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">No HP</label>
                    <input type="text" name="no_hp" id="no_hp" class="form-control"
                        placeholder="Contoh: 08123456789">
                    <small class="text-muted">Nomor HP aktif alumni, minimal 10 digit (opsional).</small>
                    <small id="error-no_hp" class="error-text form-text text-danger d-block"></small>
                </div>

                {{-- EMAIL --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        placeholder="Contoh: muh.ferdian@gmail.com">
                    <small class="text-muted">Alamat email aktif alumni (opsional).</small>
                    <small id="error-email" class="error-text form-text text-danger d-block"></small>
                </div>

                 {{-- ALAMAT --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Alamat</label>
                    <input type="text" name="alamat" id="alamat" class="form-control"
                        placeholder="Contoh: Jl. Mawar No. 5, Nganjuk">
                    <small class="text-muted">Alamat domisili alumni saat ini (opsional).</small>
                    <small id="error-alamat" class="error-text form-text text-danger d-block"></small>
                </div>

                {{-- ANGKATAN --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Angkatan <span class="text-danger">*</span></label>
                    <input type="number" name="angkatan" id="angkatan" class="form-control"
                        placeholder="Contoh: 2021" min="1900" max="2100">
                    <small class="text-muted">Tahun masuk kuliah alumni.</small>
                    <small id="error-angkatan" class="error-text form-text text-danger d-block"></small>
                </div>

                {{-- TAHUN LULUS --}}
                <div class="form-group mb-3">
                    <label class="fw-semibold">Tahun Lulus <span class="text-danger">*</span></label>
                    <input type="number" name="tanggal_lulus" id="tanggal_lulus" class="form-control"
                        placeholder="Contoh: 2025" min="1900" max="2100">
                    <small class="text-muted">Tahun lulus tidak boleh lebih kecil dari angkatan.</small>
                    <small id="error-tanggal_lulus" class="error-text form-text text-danger d-block"></small>
                </div>


            </div>

            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        // Validasi dan submit via AJAX
        $("#form-tambah-alumni").validate({
            rules: {
                nim:          { required: true, minlength: 5 },
                nama_alumni:  { required: true, minlength: 3 },
                prodi:        { required: true },
                no_hp:        { minlength: 10 },
                email:        { email: true },
                angkatan:     { digits: true, min: 1900, max: 2100 },
                tanggal_lulus:{ required: true, digits: true, min: 1900, max: 2100 },
            },
            messages: {
                nim:           { required: 'NIM wajib diisi.', minlength: 'NIM minimal 5 karakter.' },
                nama_alumni:   { required: 'Nama wajib diisi.', minlength: 'Nama minimal 3 karakter.' },
                prodi:         { required: 'Program studi wajib diisi.' },
                no_hp:         { minlength: 'No HP minimal 10 digit.' },
                email:         { email: 'Format email tidak valid.' },
                tanggal_lulus: { required: 'Tahun lulus wajib diisi.' },
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
