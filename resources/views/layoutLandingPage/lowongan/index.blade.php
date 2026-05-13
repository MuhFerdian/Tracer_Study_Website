<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lowongan Pekerjaan</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <style>

        *{
            font-family: 'Poppins', sans-serif;
        }

        body{
            background: #f4f7fb;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

        /*
        |--------------------------------------------------------------------------
        | BACKGROUND ORNAMEN
        |--------------------------------------------------------------------------
        */

        body::before{
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            background: rgba(37,99,235,.10);
            border-radius: 50%;
            top: -180px;
            right: -120px;
            filter: blur(90px);
            z-index: -1;
        }

        body::after{
            content: '';
            position: fixed;
            width: 420px;
            height: 420px;
            background: rgba(59,130,246,.08);
            border-radius: 50%;
            bottom: -180px;
            left: -120px;
            filter: blur(90px);
            z-index: -1;
        }

        /*
        |--------------------------------------------------------------------------
        | HERO HEADER
        |--------------------------------------------------------------------------
        */

        .hero-header{
            background: linear-gradient(135deg,#2563eb,#1e40af);
            border-radius: 32px;
            padding: 38px 30px;
            position: relative;
            overflow: hidden;
            margin-bottom: 45px;
            box-shadow: 0 20px 50px rgba(37,99,235,.20);
        }

        .hero-header::before{
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
            top: -120px;
            right: -70px;
        }

        .hero-header::after{
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            bottom: -80px;
            left: -40px;
        }

        .hero-content{
            position: relative;
            z-index: 2;
        }

        .hero-title{
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 12px;
        }

        .hero-subtitle{
            color: rgba(255,255,255,.82);
            font-size: 1rem;
            margin-bottom: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | BUTTON KEMBALI
        |--------------------------------------------------------------------------
        */

        .btn-kembali{
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.25);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 999px;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: .3s;
        }

        .btn-kembali:hover{
            background: white;
            color: #2563eb;
            transform: translateY(-3px);
        }

        /*
        |--------------------------------------------------------------------------
        | CARD LOWONGAN
        |--------------------------------------------------------------------------
        */

        .card-lowongan{
            background: rgba(255,255,255,.82);
            backdrop-filter: blur(16px);
            border-radius: 28px;
            padding: 28px;
            transition: .35s ease;
            height: 100%;
            border: 1px solid rgba(255,255,255,.5);
            box-shadow: 0 15px 35px rgba(0,0,0,.05);
            position: relative;
            overflow: hidden;
        }

        .card-lowongan::before{
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            background: rgba(37,99,235,.07);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        .card-lowongan:hover{
            transform: translateY(-10px);
            box-shadow: 0 25px 55px rgba(37,99,235,.14);
        }

        .card-content{
            position: relative;
            z-index: 2;
        }

        /*
        |--------------------------------------------------------------------------
        | ICON BOX
        |--------------------------------------------------------------------------
        */

        .icon-box{
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: linear-gradient(135deg,#2563eb,#3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 10px 25px rgba(37,99,235,.25);
        }

        /*
        |--------------------------------------------------------------------------
        | TEXT
        |--------------------------------------------------------------------------
        */

        .company-name{
            color: #2563eb;
            font-weight: 600;
            margin-bottom: 0;
        }

        .job-title{
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .info-item{
            font-size: .92rem;
            color: #64748b;
            margin-bottom: 10px;
        }

        .job-desc{
            color: #475569;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        /*
        |--------------------------------------------------------------------------
        | BUTTON DETAIL
        |--------------------------------------------------------------------------
        */

        .btn-detail{
            background: linear-gradient(135deg,#2563eb,#1d4ed8);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: .3s;
            font-weight: 600;
            box-shadow: 0 12px 24px rgba(37,99,235,.22);
        }

        .btn-detail:hover{
            transform: translateY(-3px);
            color: white;
            box-shadow: 0 18px 35px rgba(37,99,235,.35);
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        .pagination{
            gap: 8px;
        }

        .page-link{
            border: none;
            border-radius: 14px !important;
            padding: 10px 16px;
            color: #2563eb;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(0,0,0,.05);
        }

        .page-item.active .page-link{
            background: linear-gradient(135deg,#2563eb,#1d4ed8);
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media(max-width:768px){

            .hero-title{
                font-size: 2rem;
            }

            .hero-header{
                padding: 32px 24px;
            }

            .card-lowongan{
                padding: 24px;
            }

        }

    </style>

</head>

<body>

<section class="py-5">

    <div class="container">

        {{-- HERO --}}
        <div class="hero-header text-center">

            <div class="hero-content">

                <a href="{{ url('/') }}"
                   class="btn-kembali mb-4">

                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Landing Page

                </a>

                <h1 class="hero-title">
                    Lowongan Pekerjaan
                </h1>

                <p class="hero-subtitle">
                    Temukan peluang karir terbaik untuk alumni dan mahasiswa
                </p>

            </div>

        </div>

        {{-- LIST LOWONGAN --}}
        <div class="row">

            @forelse($lowongan as $item)

            <div class="col-lg-4 col-md-6 mb-4">

                <div class="card-lowongan">

                    <div class="card-content">

                        <div class="d-flex align-items-start gap-3 mb-4">

                            <div class="icon-box">
                                <i class="fas fa-briefcase"></i>
                            </div>

                            <div>

                                <h4 class="job-title">
                                    {{ $item->posisi }}
                                </h4>

                                <p class="company-name">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $item->nama_perusahaan }}
                                </p>

                            </div>

                        </div>

                        <div class="mb-3">

                            <div class="info-item">
                                <i class="fas fa-location-dot text-danger me-2"></i>
                                {{ $item->lokasi ?? '-' }}
                            </div>

                            <div class="info-item">
                                <i class="fas fa-wallet text-success me-2"></i>
                                {{ $item->gaji ?? '-' }}
                            </div>

                        </div>

                        <p class="job-desc">
                            {{ \Illuminate\Support\Str::limit($item->deskripsi, 120) }}
                        </p>

                        <a href="{{ url('/lowongan/'.$item->id) }}"
                           class="btn-detail">

                            Detail Lowongan
                            <i class="fas fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>

            @empty

            <div class="col-12">

                <div class="alert alert-light rounded-4 shadow-sm border-0 p-4 text-center">

                    Belum ada lowongan pekerjaan tersedia.

                </div>

            </div>

            @endforelse

        </div>

        {{-- PAGINATION --}}
        <div class="mt-5 d-flex justify-content-center">

            {{ $lowongan->links() }}

        </div>

    </div>

</section>

</body>
</html>