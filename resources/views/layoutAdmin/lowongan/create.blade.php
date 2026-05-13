<!-- @extends('layoutAdmin.app') -->

@section('content')

<div class="container-fluid px-4">

    <h1 class="mt-4 fw-bold text-primary">
        Tambah Lowongan Pekerjaan
    </h1>

    <div class="card shadow border-0 rounded-4 mt-4">

        <div class="card-body p-4">

            <form action="{{ url('admin/lowongan-pekerjaan/store') }}"
                  method="POST">

                @csrf

                <div class="row">

                    {{-- POSISI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Posisi
                        </label>

                        <input type="text"
                               name="posisi"
                               class="form-control rounded-3"
                               placeholder="Contoh: Web Developer"
                               required>
                    </div>

                    {{-- PERUSAHAAN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nama Perusahaan
                        </label>

                        <input type="text"
                               name="nama_perusahaan"
                               class="form-control rounded-3"
                               placeholder="Nama perusahaan"
                               required>
                    </div>

                    {{-- LOKASI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Lokasi
                        </label>

                        <input type="text"
                               name="lokasi"
                               class="form-control rounded-3"
                               placeholder="Surabaya / Remote">
                    </div>

                    {{-- GAJI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Gaji
                        </label>

                        <input type="text"
                               name="gaji"
                               class="form-control rounded-3"
                               placeholder="Rp 5.000.000">
                    </div>

                    {{-- BATAS LAMARAN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Batas Lamaran
                        </label>

                        <input type="date"
                               name="batas_lamaran"
                               class="form-control rounded-3">
                    </div>

                    {{-- KONTAK --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Kontak
                        </label>

                        <input type="text"
                               name="kontak"
                               class="form-control rounded-3"
                               placeholder="08123456789">
                    </div>

                    {{-- LINK --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Link Lamaran
                        </label>

                        <input type="text"
                               name="link_lamaran"
                               class="form-control rounded-3"
                               placeholder="https://">
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea name="deskripsi"
                                  rows="5"
                                  class="form-control rounded-3"
                                  placeholder="Tulis deskripsi lowongan..."></textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ url('admin/lowongan-pekerjaan') }}"
                       class="btn btn-light border rounded-3 px-4">

                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-3 px-4">

                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection