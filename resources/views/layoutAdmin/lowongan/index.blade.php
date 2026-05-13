@extends('layoutAdmin.app')

@section('content')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">

        <div>
            <h1 class="fw-bold text-primary mb-1">
                Lowongan Pekerjaan
            </h1>

            <p class="text-muted mb-0">
                Kelola data lowongan pekerjaan alumni
            </p>
        </div>

        <a href="{{ url('admin/lowongan-pekerjaan/create') }}"
           class="btn btn-primary rounded-3 shadow-sm">

            <i class="fas fa-plus me-2"></i>
            Tambah Lowongan
        </a>

    </div>

    <div class="row">

        @forelse($lowongan as $item)

        <div class="col-md-6 col-lg-4 mb-4">

            <div class="card border-0 shadow-sm rounded-4 h-100 card-lowongan">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h5 class="fw-bold mb-1">
                                {{ $item->posisi }}
                            </h5>

                            <p class="text-muted mb-2">
                                <i class="fas fa-building me-1"></i>
                                {{ $item->nama_perusahaan }}
                            </p>

                        </div>

                        <span class="badge bg-primary rounded-pill">
                            {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-location-dot me-1"></i>
                            {{ $item->lokasi ?? 'Tidak disebutkan' }}
                        </small>

                        <small class="text-success fw-semibold">
                            <i class="fas fa-wallet me-1"></i>
                            {{ $item->gaji ?? '-' }}
                        </small>

                    </div>

                    <p class="text-secondary small">
                        {{ \Illuminate\Support\Str::limit($item->deskripsi, 120) }}
                    </p>

                    <div class="mt-3">

                        <small class="text-danger">
                            <i class="fas fa-calendar-days me-1"></i>

                            Batas:
                            {{ $item->batas_lamaran ?? '-' }}
                        </small>

                    </div>

                </div>

             <div class="d-flex gap-2">

    {{-- EDIT --}}
    <a href="{{ url('admin/lowongan-pekerjaan/'.$item->id.'/edit') }}"
       class="btn btn-warning btn-action w-100">

        Edit
    </a>

    {{-- HAPUS --}}
    <form action="{{ url('admin/lowongan-pekerjaan/'.$item->id.'/delete') }}"
          method="POST"
          class="w-100">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-action w-100">

            Hapus
        </button>

    </form>

</div>

            </div>

        </div>

        @empty

        <div class="col-12">

            <div class="alert alert-info rounded-4 shadow-sm">

                Belum ada lowongan pekerjaan tersedia.

            </div>

        </div>

        @endforelse

    </div>

</div>

@endsection

<style>

.card-lowongan{
    transition: all .25s ease;
}

.card-lowongan:hover{
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,.12)!important;
}

.btn-action{
    height: 42px;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    padding: 0;
}

</style>