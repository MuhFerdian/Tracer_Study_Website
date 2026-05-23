<!-- @extends('layoutAdmin.app') -->

@section('content')

<div class="container-fluid px-4">

    <h1 class="mt-4 fw-bold text-warning">
        Edit Lowongan
    </h1>

    <div class="card shadow border-0 rounded-4 mt-4">

        <div class="card-body p-4">

            <form action="{{ url('admin/lowongan-pekerjaan/'.$lowongan->id.'/update') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Posisi
                        </label>

                        <input type="text"
                               name="posisi"
                               value="{{ $lowongan->posisi }}"
                               class="form-control rounded-3"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nama Perusahaan
                        </label>

                        <input type="text"
                               name="nama_perusahaan"
                               value="{{ $lowongan->nama_perusahaan }}"
                               class="form-control rounded-3"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Lokasi
                        </label>

                        <input type="text"
                               name="lokasi"
                               value="{{ $lowongan->lokasi }}"
                               class="form-control rounded-3">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Gaji
                        </label>

                        <input type="text"
                               name="gaji"
                               value="{{ $lowongan->gaji }}"
                               class="form-control rounded-3">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Batas Lamaran
                        </label>

                        <input type="date"
                               name="batas_lamaran"
                               value="{{ $lowongan->batas_lamaran }}"
                               class="form-control rounded-3">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Kontak
                        </label>

                        <input type="text"
                               name="kontak"
                               value="{{ $lowongan->kontak }}"
                               class="form-control rounded-3">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Link Lamaran
                        </label>

                        <input type="text"
                               name="link_lamaran"
                               value="{{ $lowongan->link_lamaran }}"
                               class="form-control rounded-3">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea name="deskripsi"
                                  rows="5"
                                  class="form-control rounded-3">{{ $lowongan->deskripsi }}</textarea>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">
                            Foto
                        </label>

                        <input type="file"
                            name="foto"
                            class="form-control rounded-3">

                        @if($lowongan->foto)
                            <img src="{{ asset('storage/' . $lowongan->foto) }}"
                                width="200"
                                class="mt-3 rounded shadow-sm">
                        @endif
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ url('admin/lowongan-pekerjaan') }}"
                       class="btn btn-light border rounded-3 px-4">

                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-warning text-white rounded-3 px-4">

                        Update
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection