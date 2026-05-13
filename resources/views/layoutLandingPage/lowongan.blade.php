<section id="lowongan" class="lowongan-section py-5">

    <div class="container">

        <div class="text-center mb-5">

            <span class="badge bg-primary px-4 py-2 rounded-pill mb-3">
                LOWONGAN TERBARU
            </span>

            <h2 class="fw-bold text-black mb-3">
                Peluang Karir Alumni
            </h2>

            <p class="text-black-emphasis mb-0">
                Temukan peluang kerja terbaru dari perusahaan dan partner kampus.
            </p>

        </div>

        <div class="row">

            @forelse($lowongan ?? [] as $item)

            <div class="col-lg-4 mb-4">

                <div class="card-lowongan-landing h-100">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <div>

                            <h4 class="fw-bold text-black mb-1">
                                {{ $item->posisi }}
                            </h4>

                            <p class="text-info mb-0">
                                {{ $item->nama_perusahaan }}
                            </p>

                        </div>

                        <span class="badge bg-success rounded-pill">
                            Aktif
                        </span>

                    </div>

                    <div class="mb-3">

                        <p class="small text-black mb-2">
                            📍 {{ $item->lokasi ?? '-' }}
                        </p>

                        <p class="small text-success mb-0">
                            💰 {{ $item->gaji ?? '-' }}
                        </p>

                    </div>

                    <p class="text-black-emphasis">
                        {{ \Illuminate\Support\Str::limit($item->deskripsi, 100) }}
                    </p>

                    <div class="mt-4">

                        <a href="{{ $item->link_lamaran }}"
                           target="_blank"
                           class="btn btn-blue-glow rounded-pill px-4">

                            Lihat Selengkapnya

                        </a>

                    </div>

                </div>

            </div>

            @empty

            <div class="col-12">

                <div class="alert alert-light rounded-4">

                    Belum ada lowongan pekerjaan tersedia.

                </div>

            </div>

            @endforelse

        </div>

        <div class="text-center mt-4">

            <a href="{{ url('/lowongan') }}"
               class="btn btn-primary rounded-pill px-4 py-2">

                Lihat Selengkapnya

            </a>

        </div>

    </div>

</section>

<style>

.lowongan-section{
    position: relative;
}

.card-lowongan-landing{
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(14px);
    border-radius: 24px;
    padding: 28px;
    transition: .3s;
}

.card-lowongan-landing:hover{
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,.18);
}

</style>