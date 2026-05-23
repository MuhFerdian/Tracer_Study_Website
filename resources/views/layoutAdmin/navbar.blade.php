<nav class="sb-topnav navbar navbar-expand navbar-dark top-navbar">

    {{-- BRAND --}}
    <a class="navbar-brand ps-3 d-flex align-items-center" href="{{ url('/admin') }}">

        <img class="logo-animated" src="{{ asset('assets/img/logo_tc5.png') }}"
            alt="Logo">

        <div class="brand-wrapper">
            <span class="brand-title">Tracer Study</span>
            <small class="brand-subtitle">Teknologi Informasi</small>
        </div>
    </a>

    {{-- SIDEBAR TOGGLE --}}
    <button class="btn btn-link btn-sm toggle-btn order-1 order-lg-0 me-3" id="sidebarToggle">

        <i class="fas fa-bars"></i>
    </button>

    {{-- NOTIFIKASI & PROFILE WRAPPER --}}
    <div class="nav-right-wrap ms-auto d-flex align-items-center gap-2">
        {{-- NOTIFIKASI --}}
        <div class="nav-notif-wrap position-relative">
            <button class="btn notif-btn" id="notifToggle" title="Notifikasi Alumni">
                <i class="fas fa-bell"></i>
                <span class="notif-badge d-none" id="notifBadge">0</span>
            </button>

            {{-- Dropdown Panel --}}
            <div class="notif-panel shadow" id="notifPanel">
                <div class="notif-panel-header d-flex align-items-center justify-content-between">
                    <span class="fw-bold"><i class="fas fa-bell me-2 text-warning"></i>Notifikasi Alumni</span>
                    <button class="btn btn-sm btn-link text-muted p-0" id="notifMarkAll" title="Tandai semua sudah dibaca">
                        <i class="fas fa-check-double"></i>
                    </button>
                </div>
                <div class="notif-panel-body" id="notifList">
                    <div class="notif-empty text-center py-4 text-muted">
                        <i class="fas fa-bell-slash fs-3 mb-2 d-block"></i>
                        <small>Belum ada alumni yang mengisi</small>
                    </div>
                </div>
                <div class="notif-panel-footer text-center">
                    <a href="{{ url('/admin/alumni-sudah-mengisi') }}" class="text-primary small fw-semibold">
                        Lihat semua alumni yang mengisi →
                    </a>
                </div>
            </div>
        </div>

        {{-- PROFILE --}}
        <ul class="navbar-nav">

        <li class="nav-item dropdown">

            <a class="nav-link dropdown-toggle profile-btn" href="#" data-bs-toggle="dropdown">

                <i class="fas fa-user-circle"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                <li class="dropdown-header">
                    <strong>{{ auth()->user()->nama ?? 'User' }}</strong><br>
                    <small>{{ auth()->user()->role->role_nama ?? 'Admin' }}</small>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalGantiPassword">
                        <i class="fas fa-key me-2"></i>
                        Ganti Password
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>

                    <a class="dropdown-item text-danger" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                        <i class="fas fa-right-from-bracket me-2"></i>
                        Keluar
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">

                        @csrf
                    </form>

                </li>

            </ul>

        </li>

    </ul>
    </div>
    {{-- END NOTIFIKASI & PROFILE WRAPPER --}}

</nav>

{{-- MODAL GANTI PASSWORD --}}
<div class="modal fade" id="modalGantiPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0 rounded-4">

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
                            <input type="password" class="form-control pe-5"
                                id="password_baru" name="password_baru"
                                placeholder="Minimal 8 karakter">
                            <span id="toggle_pw_baru"
                                class="position-absolute top-50 end-0 translate-middle-y me-3"
                                style="cursor:pointer; z-index:10;">
                                <i class="fas fa-eye text-secondary" id="icon_pw_baru"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="err_password_baru"></div>
                    </div>

                    {{-- Konfirmasi --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Konfirmasi Password Baru</label>
                        <div class="position-relative">
                            <input type="password" class="form-control pe-5"
                                id="password_baru_confirmation" name="password_baru_confirmation"
                                placeholder="Ulangi password baru">
                            <span id="toggle_pw_konfirmasi"
                                class="position-absolute top-50 end-0 translate-middle-y me-3"
                                style="cursor:pointer; z-index:10;">
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

        </div>
    </div>
</div>

<script>
    // Toggle password baru
    document.getElementById('toggle_pw_baru').addEventListener('click', function () {
        var inp  = document.getElementById('password_baru');
        var icon = document.getElementById('icon_pw_baru');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Toggle konfirmasi
    document.getElementById('toggle_pw_konfirmasi').addEventListener('click', function () {
        var inp  = document.getElementById('password_baru_confirmation');
        var icon = document.getElementById('icon_pw_konfirmasi');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Reset form saat modal ditutup
    document.getElementById('modalGantiPassword').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formGantiPassword').reset();
        document.getElementById('alertGantiPassword').innerHTML = '';
        document.getElementById('err_password_baru').textContent = '';
        document.getElementById('err_password_baru_confirmation').textContent = '';
        // Reset icon mata
        document.getElementById('icon_pw_baru').classList.replace('fa-eye-slash', 'fa-eye');
        document.getElementById('icon_pw_konfirmasi').classList.replace('fa-eye-slash', 'fa-eye');
        document.getElementById('password_baru').type = 'password';
        document.getElementById('password_baru_confirmation').type = 'password';
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
</script>

<style>
    /* =========================
       TOP NAVBAR
    ========================== */
    .top-navbar,
    .sb-topnav.navbar {
        height: var(--admin-topbar-height, 64px);
        background: linear-gradient(90deg, #1e3a8a, #2563eb);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        padding: 0 1rem;
        z-index: 1039;
        position: fixed;
        top: 0;
        width: 100%;
    }

    /* =========================
       BRAND
    ========================== */
    .top-navbar .navbar-brand,
    .sb-topnav .navbar-brand {
        width: var(--admin-sidebar-width, 260px);
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .logo-animated {
        height: 42px;
        transition: all 0.3s ease;
    }

    .logo-animated:hover {
        transform: rotate(10deg) scale(1.05);
    }

    .sb-topnav .logo-animated {
        height: 42px;
        transition: all 0.3s ease;
    }

    .sb-topnav .logo-animated:hover {
        transform: rotate(10deg) scale(1.05);
    }

    .brand-wrapper {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .brand-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
    }

    .brand-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.75);
        display: block !important;
        visibility: visible !important;
    }

    .sb-topnav .brand-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
    }

    .sb-topnav .brand-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.75);
        display: block !important;
        visibility: visible !important;
    }

    /* =========================
       SIDEBAR TOGGLE
    ========================== */
    .toggle-btn {
        color: white !important;
        font-size: 1.1rem;
        border: none;
        box-shadow: none !important;
    }

    .toggle-btn:hover {
        color: #dbeafe !important;
    }

    .sb-topnav .toggle-btn {
        color: white !important;
    }

    .sb-topnav .toggle-btn:hover {
        color: #dbeafe !important;
    }

    /* =========================
       SEARCH BOX (dihapus)
    ========================== */

    /* =========================
       RIGHT SIDE NAV WRAPPER
    ========================== */
    .nav-right-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-right: 1rem;
    }

    .sb-topnav .nav-right-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-right: 1rem;
    }

    /* =========================
       NOTIFIKASI BELL
    ========================== */
    .nav-notif-wrap {
        position: relative;
    }

    .notif-btn {
        position: relative;
        color: white !important;
        font-size: 1.25rem;
        background: transparent;
        border: none;
        padding: 0.4rem 0.6rem;
        border-radius: 50%;
        transition: background 0.2s;
        box-shadow: none !important;
    }

    .notif-btn:hover {
        background: rgba(255,255,255,0.15) !important;
    }

    .sb-topnav .notif-btn {
        color: white !important;
    }

    .sb-topnav .notif-btn:hover {
        background: rgba(255,255,255,0.15) !important;
    }

    .notif-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
        pointer-events: none;
    }

    .notif-panel {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 340px;
        max-height: 420px;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        z-index: 2000;
        border: 1px solid rgba(0,0,0,0.08);
        flex-direction: column;
    }

    .notif-panel.open {
        display: flex;
    }

    .notif-panel-header {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .notif-panel-body {
        overflow-y: auto;
        flex: 1;
    }

    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
        text-decoration: none;
        color: inherit;
    }

    .notif-item:hover {
        background: #f0f9ff;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-avatar {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #38bdf8);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .notif-item-body {
        flex: 1;
        min-width: 0;
    }

    .notif-item-name {
        font-weight: 700;
        font-size: 0.88rem;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .notif-item-meta {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 2px;
    }

    .notif-item-time {
        font-size: 0.72rem;
        color: #94a3b8;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .notif-panel-footer {
        padding: 0.7rem 1rem;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .notif-empty {
        color: #94a3b8;
    }

    .sb-topnav .form-inline {
        display: none !important;
    }

    /* Ensure nav components are visible */
    .top-navbar .navbar-nav,
    .sb-topnav .navbar-nav {
        margin: 0;
        padding: 0;
        gap: 0;
        display: flex !important;
    }

    .top-navbar .nav-item,
    .sb-topnav .nav-item {
        display: flex;
        align-items: center;
    }

    /* =========================
       PROFILE BUTTON
    ========================== */
    .profile-btn {
        color: white !important;
        font-size: 1.3rem;
        transition: all 0.2s ease;
    }

    .profile-btn:hover {
        color: #dbeafe !important;
    }

    .sb-topnav .profile-btn {
        color: white !important;
    }

    .sb-topnav .profile-btn:hover {
        color: #dbeafe !important;
    }

    /* =========================
       DROPDOWN
    ========================== */
    .top-navbar .dropdown-menu,
    .sb-topnav .dropdown-menu {
        border-radius: 14px;
        padding: 0.5rem;
        min-width: 220px;
        font-family: 'Poppins', sans-serif;
    }

    .top-navbar .dropdown-header,
    .sb-topnav .dropdown-header {
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
    }

    .top-navbar .dropdown-item,
    .sb-topnav .dropdown-item {
        border-radius: 10px;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .top-navbar .dropdown-item:hover,
    .sb-topnav .dropdown-item:hover {
        background-color: #eff6ff;
    }

    /* =========================
       RESPONSIVE
    ========================== */
    /* Override tablet breakpoint (991px down) dari tracer-ui.css */
    @media (max-width: 991px) {
        .sb-topnav .navbar-brand {
            width: auto !important;
            min-width: 0 !important;
        }

        .sb-topnav .navbar-brand .brand-title {
            font-size: 1rem;
        }

        .sb-topnav .navbar-brand .brand-subtitle {
            font-size: 0.62rem !important;
            display: block !important;
        }

        .top-navbar .navbar-brand,
        .sb-topnav .navbar-brand {
            gap: 10px;
        }
    }

    @media (max-width: 768px) {

        .brand-subtitle {
            font-size: 0.62rem !important;
            display: block !important;
            visibility: visible !important;
        }

        .brand-title {
            font-size: 0.95rem;
        }

        .top-navbar .navbar-brand,
        .sb-topnav .navbar-brand {
            width: auto !important;
            gap: 10px;
            padding-left: 0.75rem !important;
        }

        .logo-animated {
            height: 38px;
            flex-shrink: 0;
        }

        .toggle-btn {
            margin-left: 0.3rem;
            margin-right: 0;
        }

        .nav-right-wrap {
            gap: 0.5rem;
            padding-right: 0.5rem;
        }

        .notif-panel {
            width: 300px;
            right: -10px;
        }
    }

    @media (max-width: 576px) {
        .top-navbar,
        .sb-topnav.navbar {
            padding: 0 0.5rem !important;
            height: auto !important;
            min-height: 60px !important;
        }

        .top-navbar .navbar-brand,
        .sb-topnav .navbar-brand {
            width: auto !important;
            padding: 0.5rem 0 !important;
            gap: 8px !important;
        }

        .brand-wrapper {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .brand-title {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .brand-subtitle {
            font-size: 0.58rem !important;
            display: block !important;
            visibility: visible !important;
            white-space: nowrap;
        }

        .logo-animated {
            height: 32px;
            flex-shrink: 0;
        }

        .toggle-btn {
            font-size: 1rem;
            padding: 0.25rem;
            margin-left: 0.3rem;
            margin-right: 0;
        }

        .nav-right-wrap {
            gap: 0.4rem;
            padding-right: 0;
        }

        .notif-btn {
            font-size: 1.1rem;
            padding: 0.3rem 0.4rem;
        }

        .top-navbar .dropdown-menu,
        .sb-topnav .dropdown-menu {
            min-width: 160px;
            font-size: 0.85rem;
        }

        .notif-panel {
            width: 280px;
            right: -40px;
        }
    }

    /* Override untuk 600px ke bawah dari tracer-ui.css */
    @media (max-width: 600px) {
        .sb-topnav .navbar-brand {
            width: auto !important;
            min-width: 0 !important;
            padding-left: 0.75rem !important;
        }

        .sb-topnav .navbar-brand .brand-title {
            font-size: 0.85rem !important;
        }

        .sb-topnav .navbar-brand .brand-subtitle {
            font-size: 0.58rem !important;
            display: block !important;
        }
    }
</style>

<script>
(function () {
    var btn      = document.getElementById('notifToggle');
    var panel    = document.getElementById('notifPanel');
    var badge    = document.getElementById('notifBadge');
    var list     = document.getElementById('notifList');
    var markAll  = document.getElementById('notifMarkAll');
    var loaded   = false;

    // ── Toggle panel ───────────────────────────────────────────
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        panel.classList.toggle('open');
        if (panel.classList.contains('open') && !loaded) {
            loadNotif();
        }
    });

    // Tutup saat klik di luar
    document.addEventListener('click', function (e) {
        if (!panel.contains(e.target) && e.target !== btn) {
            panel.classList.remove('open');
        }
    });

    // ── Load data alumni yang mengisi ──────────────────────────
    function loadNotif() {
        list.innerHTML = '<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm"></span></div>';
        fetch('{{ route("notif.alumni.mengisi") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            loaded = true;
            renderList(res.data);
            updateBadge(res.unread);
        })
        .catch(function () {
            list.innerHTML = '<div class="text-center py-4 text-danger small">Gagal memuat notifikasi</div>';
        });
    }

    // ── Render item list ───────────────────────────────────────
    function renderList(data) {
        if (!data || data.length === 0) {
            list.innerHTML = '<div class="notif-empty text-center py-4"><i class="fas fa-bell-slash fs-3 mb-2 d-block"></i><small>Belum ada alumni yang mengisi</small></div>';
            return;
        }
        var html = '';
        data.forEach(function (item) {
            var initial = (item.nama || 'A').charAt(0).toUpperCase();
            html += '<a href="{{ url("/admin/alumni") }}/' + item.alumni_id + '/answers" class="notif-item">' +
                '<div class="notif-avatar">' + initial + '</div>' +
                '<div class="notif-item-body">' +
                    '<div class="notif-item-name">' + escHtml(item.nama) + '</div>' +
                    '<div class="notif-item-meta">' + escHtml(item.nim) + ' &bull; ' + escHtml(item.prodi) + '</div>' +
                '</div>' +
                '<div class="notif-item-time">' + escHtml(item.submitted_ago) + '</div>' +
            '</a>';
        });
        list.innerHTML = html;
    }

    // ── Update badge ───────────────────────────────────────────
    function updateBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    // ── Mark all read ──────────────────────────────────────────
    markAll.addEventListener('click', function () {
        fetch('{{ route("notif.mark.all.read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function () {
            badge.classList.add('d-none');
        });
    });

    // ── Escape HTML helper ─────────────────────────────────────
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Auto-load badge count saat halaman load ────────────────
    fetch('{{ route("notif.unread.count") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json(); })
    .then(function (res) { updateBadge(res.count); })
    .catch(function () {});
})();
</script>
