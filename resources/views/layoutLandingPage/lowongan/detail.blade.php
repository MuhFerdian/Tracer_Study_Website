<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $lowongan->posisi }}</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

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
            width: 420px;
            height: 420px;
            background: rgba(37,99,235,.10);
            border-radius: 50%;
            top: -180px;
            right: -100px;
            filter: blur(90px);
            z-index: -1;
        }

        body::after{
            content: '';
            position: fixed;
            width: 350px;
            height: 350px;
            background: rgba(59,130,246,.08);
            border-radius: 50%;
            bottom: -150px;
            left: -100px;
            filter: blur(90px);
            z-index: -1;
        }

        /*
        |--------------------------------------------------------------------------
        | CONTAINER
        |--------------------------------------------------------------------------
        */

        .custom-container{
            max-width: 980px;
        }

        /*
        |--------------------------------------------------------------------------
        | HERO HEADER
        |--------------------------------------------------------------------------
        */

        .hero-detail{
            background: linear-gradient(135deg,#2563eb,#1e40af);
            border-radius: 26px;
            padding: 28px 26px;
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
            box-shadow: 0 18px 40px rgba(37,99,235,.18);
        }

        .hero-detail::before{
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
            top: -110px;
            right: -60px;
        }

        .hero-detail::after{
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            bottom: -70px;
            left: -30px;
        }

        .hero-content{
            position: relative;
            z-index: 2;
        }

        .hero-title{
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .hero-subtitle{
            color: rgba(255,255,255,.82);
            margin-bottom: 0;
            font-size: .95rem;
        }

        /*
        |--------------------------------------------------------------------------
        | BUTTON BACK
        |--------------------------------------------------------------------------
        */

        .btn-back{
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.22);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 999px;
            padding: 10px 18px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: .3s;
            font-size: .92rem;
        }

        .btn-back:hover{
            background: white;
            color: #2563eb;
            transform: translateY(-2px);
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL CARD
        |--------------------------------------------------------------------------
        */

        .detail-card{
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(16px);
            border-radius: 26px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.5);
            box-shadow: 0 14px 35px rgba(0,0,0,.05);
            position: relative;
        }

        .detail-card::before{
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(37,99,235,.05);
            border-radius: 50%;
            top: -70px;
            right: -40px;
        }

        .card-content{
            position: relative;
            z-index: 2;
            padding: 34px;
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY INFO
        |--------------------------------------------------------------------------
        */

        .company-name{
            color: #2563eb;
            font-weight: 600;
            font-size: 1rem;
        }

        .job-title{
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #0f172a;
        }

        /*
        |--------------------------------------------------------------------------
        | INFO BOX
        |--------------------------------------------------------------------------
        */

        .info-box{
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            transition: .3s;
            height: 100%;
            border: 1px solid rgba(0,0,0,.04);
        }

        .info-box:hover{
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(0,0,0,.05);
        }

        .info-icon{
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg,#2563eb,#3b82f6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            font-size: .95rem;
        }

        /*
        |--------------------------------------------------------------------------
        | DESKRIPSI
        |--------------------------------------------------------------------------
        */

        .deskripsi-title{
            font-size: 1.15rem;
        }

        .deskripsi-box{
            background: #f8fafc;
            border-radius: 22px;
            padding: 24px;
            border: 1px solid rgba(0,0,0,.04);
        }

        .deskripsi-box p{
            line-height: 1.8;
            color: #475569;
            margin-bottom: 0;
            font-size: .95rem;
        }

        /*
        |--------------------------------------------------------------------------
        | BUTTON LAMAR
        |--------------------------------------------------------------------------
        */

        .btn-lamar{
            background: linear-gradient(135deg,#2563eb,#1d4ed8);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: .3s;
            box-shadow: 0 12px 25px rgba(37,99,235,.20);
            font-size: .95rem;
        }

        .btn-lamar:hover{
            transform: translateY(-3px);
            color: white;
            box-shadow: 0 18px 40px rgba(37,99,235,.30);
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media(max-width:768px){

            .hero-title{
                font-size: 1.6rem;
            }

            .hero-detail{
                padding: 24px 22px;
            }

            .card-content{
                padding: 24px;
            }

            .job-title{
                font-size: 1.35rem;
            }

        }

    </style>

</head>
<body>

<section class="py-4 py-lg-5">

    <div class="container custom-container">

        {{-- HERO --}}
        <div class="hero-detail">

            <div class="hero-content">

                <a href="{{ url('/lowongan') }}"
                   class="btn-back mb-4">

                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Lowongan

                </a>

                <h1 class="hero-title">
                    {{ $lowongan->posisi }}
                </h1>

                <p class="hero-subtitle">
                    Detail informasi lowongan pekerjaan
                </p>

            </div>

        </div>

        {{-- DETAIL --}}
        <div class="detail-card">

            <div class="card-content">

                {{-- COMPANY --}}
                <div class="mb-4">

                    <h2 class="job-title">
                        {{ $lowongan->posisi }}
                    </h2>

                    <h5 class="company-name mb-0">
                        <i class="fas fa-building me-2"></i>
                        {{ $lowongan->nama_perusahaan }}
                    </h5>

                </div>

                {{-- INFO --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-4">

                        <div class="info-box">

                            <div class="info-icon">
                                <i class="fas fa-location-dot"></i>
                            </div>

                            <small class="text-muted d-block mb-2">
                                Lokasi
                            </small>

                            <strong>
                                {{ $lowongan->lokasi ?? '-' }}
                            </strong>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="info-box">

                            <div class="info-icon">
                                <i class="fas fa-wallet"></i>
                            </div>

                            <small class="text-muted d-block mb-2">
                                Gaji
                            </small>

                            <strong class="text-success">
                                {{ $lowongan->gaji ?? '-' }}
                            </strong>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="info-box">

                            <div class="info-icon">
                                <i class="fas fa-calendar-days"></i>
                            </div>

                            <small class="text-muted d-block mb-2">
                                Batas Lamaran
                            </small>

                            <strong>
                                {{ $lowongan->batas_lamaran ?? '-' }}
                            </strong>

                        </div>

                    </div>

                </div>

                {{-- DESKRIPSI --}}
                <div class="mb-4">

                    <h4 class="fw-bold deskripsi-title mb-3">
                        Deskripsi Pekerjaan
                    </h4>

                    <div class="deskripsi-box">

                        <p>
                            {{ $lowongan->deskripsi }}
                        </p>

                    </div>

                </div>

                {{-- BUTTON --}}
                @if($lowongan->link_lamaran)

                <a href="{{ $lowongan->link_lamaran }}"
                   target="_blank"
                   class="btn-lamar">

                    <i class="fas fa-paper-plane"></i>
                    Lamar Sekarang

                </a>

                @endif

            </div>

        </div>

    </div>

</section>

</body>
</html>