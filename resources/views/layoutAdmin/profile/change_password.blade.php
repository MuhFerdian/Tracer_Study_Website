<div class="modal-header border-0 pb-0">
    <h5 class="modal-title fw-semibold">
        <i class="fas fa-lock me-2 text-primary"></i> Ganti Password
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form id="formGantiPassword">
    @csrf
    <div class="modal-body px-4 py-3">

        <div id="alertGantiPassword"></div>

        {{-- Password Baru --}}
        <div class="mb-3">
            <label class="form-label fw-medium">Password Baru</label>
            <div class="position-relative">
                <input type="password" class="form-control pe-5" id="password_baru" name="password_baru"
                    placeholder="Minimal 6 karakter">
                <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer; z-index:10;"
                    id="toggle_pw_baru">
                    <i class="fas fa-eye text-secondary" id="icon_pw_baru"></i>
                </span>
            </div>
            <div class="text-danger small mt-1" id="err_password_baru"></div>
        </div>

        {{-- Konfirmasi Password Baru --}}
        <div class="mb-3">
            <label class="form-label fw-medium">Konfirmasi Password Baru</label>
            <div class="position-relative">
                <input type="password" class="form-control pe-5" id="password_baru_confirmation"
                    name="password_baru_confirmation" placeholder="Ulangi password baru">
                <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer; z-index:10;"
                    id="toggle_pw_konfirmasi">
                    <i class="fas fa-eye text-secondary" id="icon_pw_konfirmasi"></i>
                </span>
            </div>
            <div class="text-danger small mt-1" id="err_password_baru_confirmation"></div>
        </div>

    </div>

    <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSimpanPassword">
            <span id="btnSimpanText"><i class="fas fa-save me-1"></i> Simpan</span>
            <span id="btnSimpanLoading" class="d-none">
                <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
            </span>
        </button>
    </div>
</form>

<script>
(function () {
    // Toggle password baru
    document.getElementById('toggle_pw_baru').addEventListener('click', function () {
        var inp  = document.getElementById('password_baru');
        var icon = document.getElementById('icon_pw_baru');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Toggle konfirmasi
    document.getElementById('toggle_pw_konfirmasi').addEventListener('click', function () {
        var inp  = document.getElementById('password_baru_confirmation');
        var icon = document.getElementById('icon_pw_konfirmasi');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Submit
    document.getElementById('formGantiPassword').addEventListener('submit', function (e) {
        e.preventDefault();

        document.getElementById('err_password_baru').textContent = '';
        document.getElementById('err_password_baru_confirmation').textContent = '';
        document.getElementById('alertGantiPassword').innerHTML = '';

        document.getElementById('btnSimpanText').classList.add('d-none');
        document.getElementById('btnSimpanLoading').classList.remove('d-none');
        document.getElementById('btnSimpanPassword').disabled = true;

        fetch('{{ route("profile.change-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(this)
        })
        .then(function (res) {
            if (!res.ok) return res.json().then(function (d) { return Promise.reject({ status: res.status, data: d }); });
            return res.json();
        })
        .then(function (data) {
            document.getElementById('alertGantiPassword').innerHTML =
                '<div class="alert alert-success py-2"><i class="fas fa-check-circle me-1"></i> ' + data.message + '</div>';
            document.getElementById('formGantiPassword').reset();
            setTimeout(function () {
                bootstrap.Modal.getInstance(document.getElementById('modalGantiPassword')).hide();
            }, 1500);
        })
        .catch(function (err) {
            if (err && err.status === 422 && err.data) {
                if (err.data.errors) {
                    Object.keys(err.data.errors).forEach(function (field) {
                        var el = document.getElementById('err_' + field);
                        if (el) el.textContent = err.data.errors[field][0];
                    });
                }
                if (err.data.message) {
                    document.getElementById('alertGantiPassword').innerHTML =
                        '<div class="alert alert-danger py-2"><i class="fas fa-exclamation-circle me-1"></i> ' + err.data.message + '</div>';
                }
            } else {
                document.getElementById('alertGantiPassword').innerHTML =
                    '<div class="alert alert-danger py-2"><i class="fas fa-exclamation-circle me-1"></i> Terjadi kesalahan, coba lagi.</div>';
            }
        })
        .finally(function () {
            document.getElementById('btnSimpanText').classList.remove('d-none');
            document.getElementById('btnSimpanLoading').classList.add('d-none');
            document.getElementById('btnSimpanPassword').disabled = false;
        });
    });
})();
</script>
