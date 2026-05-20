<div class="modal-header">
    <h5 class="modal-title">Tambah Dosen</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="formTambah">
    @csrf

    <div class="modal-body">

        <div class="mb-3">
            <label class="fw-semibold">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" id="username" class="form-control"
                placeholder="Contoh: ulfa.emi">
            <small class="text-muted">Hanya boleh huruf, angka, titik, dan underscore. Minimal 4 karakter.</small>
            <small id="error-username" class="text-danger d-block"></small>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control"
                placeholder="Contoh: Ulfa Emi Rahmawati, S.Kom., M.Kom.">
            <small class="text-muted">Nama lengkap dosen sesuai identitas.</small>
            <small id="error-name" class="text-danger d-block"></small>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                placeholder="Contoh: ulfa.emi@polije.ac.id">
            <small class="text-muted">Email aktif dosen (opsional).</small>
            <small id="error-email" class="text-danger d-block"></small>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Minimal 6 karakter">
                <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                    <i class="fas fa-eye" id="iconPassword"></i>
                </button>
            </div>
            <small class="text-muted">Password untuk login dosen ke sistem.</small>
            <small id="error-password" class="text-danger d-block"></small>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-control">
                <option value="pending">Pending</option>
                <option value="active">Active</option>
            </select>
            <small class="text-muted">Active: dosen bisa login. Pending: akun belum aktif.</small>
            <small id="error-status" class="text-danger d-block"></small>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
// Toggle password
$('#togglePassword').on('click', function () {
    var inp  = $('#password');
    var icon = $('#iconPassword');
    if (inp.attr('type') === 'password') {
        inp.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        inp.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

$('#formTambah').submit(function (e) {
    e.preventDefault();

    // Reset error
    $('[id^="error-"]').text('');

    $.post('/admin/manajemen-dosen/store', $(this).serialize(), function (res) {
        if (res.status) {
            $('#myModal').modal('hide');
            $('#table-dosen').DataTable().ajax.reload();
            Swal.fire('Berhasil', res.message, 'success');
        } else {
            if (res.msgField) {
                $.each(res.msgField, function (field, messages) {
                    $('#error-' + field).text(messages[0]);
                });
            }
            Swal.fire('Gagal', res.message, 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
    });
});
</script>
