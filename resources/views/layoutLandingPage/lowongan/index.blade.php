{{-- resources/views/layoutLandingPage/lowongan/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Lowongan Pekerjaan - Tracer Study</title>
    
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* 🎨 BLUE THEME PALETTE */
            --primary: #1e40af;           /* Biru Tua - Primary */
            --primary-dark: #1e3a8a;      /* Biru Lebih Tua - Hover */
            --primary-darker: #172554;    /* Biru Navy - Accent */
            --primary-light: #bfdbfe;     /* Biru Muda - Background */
            --primary-lighter: #eff6ff;   /* Biru Sangat Muda - Highlight */
            
            --success: #059669;           /* Emerald - Success */
            --success-light: #d1fae5;
            
            --text-primary: #1e293b;      /* Dark Blue-Gray - Heading */
            --text-secondary: #475569;    /* Medium Gray - Body */
            --text-muted: #94a3b8;        /* Light Gray - Muted */
            --text-white: #ffffff;        /* White Text */
            
            --bg-card: #ffffff;           /* White - Card Background */
            --bg-page: #f8fafc;           /* Very Light Blue - Page Background */
            --bg-section: #eff6ff;        /* Light Blue - Section Background */
            
            --border: #cbd5e1;            /* Light Blue-Gray - Border */
            --border-light: #e2e8f0;      /* Lighter Border */
            
            --shadow-sm: 0 1px 2px rgba(30, 64, 175, 0.08);
            --shadow-md: 0 4px 12px rgba(30, 64, 175, 0.12);
            --shadow-lg: 0 8px 30px rgba(30, 64, 175, 0.18);
            --shadow-hover: 0 20px 48px rgba(30, 64, 175, 0.25);
            
            --radius: 20px;
            --radius-sm: 12px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg-page) 0%, var(--primary-lighter) 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: var(--text-secondary);
        }

        /* Decorative Background Blobs */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.5;
            pointer-events: none;
        }
        body::before {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
            top: -250px;
            right: -200px;
        }
        body::after {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.25) 0%, transparent 70%);
            bottom: -200px;
            left: -150px;
        }

        .custom-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 20px;
            position: relative;
            z-index: 1;
        }

        /* ========== HEADER ========== */
        .page-header {
            text-align: center;
            padding: 48px 24px 36px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .page-header > * {
            position: relative;
            z-index: 1;
        }

        .badge-new {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
            backdrop-filter: blur(4px);
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            background: #60a5fa;
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(96, 165, 250, 0.6);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(96, 165, 250, 0.6); }
            50% { opacity: 0.8; transform: scale(0.95); box-shadow: 0 0 0 10px rgba(96, 165, 250, 0); }
        }

        .badge-text {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-white);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .page-title {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--text-white);
            margin: 0 0 12px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.05rem;
            max-width: 580px;
            margin: 0 auto;
            font-weight: 400;
        }

        /* Tombol Kembali - White Style */
        .btn-back {
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary);
            border: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }

        .btn-back:hover {
            background: #ffffff;
            color: var(--primary-dark);
            transform: translateX(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18);
        }

        /* ========== SEARCH BOX ========== */
        .search-box {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
        }

        .search-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 14px 18px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            background: var(--bg-page);
            color: var(--text-primary);
            transition: var(--transition);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--text-white);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .search-btn {
            background: var(--primary);
            color: var(--text-white);
            border: none;
            padding: 14px 28px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.25);
        }

        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.35);
        }

        .search-btn:active {
            transform: translateY(0);
        }

        /* Tombol Reset */
        .btn-reset {
            border-color: var(--border);
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .btn-reset:hover {
            background: var(--bg-section);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* ========== JOB CARD ========== */
        .job-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            padding: 28px;
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            opacity: 0;
            transition: var(--transition);
        }

        .job-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .job-card:hover::before {
            opacity: 1;
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-light);
        }

        .logo-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary-lighter), var(--primary-light));
            border: 2px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .logo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }

        .logo-wrapper i {
            font-size: 24px;
            color: var(--primary);
        }

        .job-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 6px;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .job-title:hover {
            color: var(--primary);
        }

        .company-name {
            font-size: 0.92rem;
            color: var(--primary);
            font-weight: 600;
            margin: 0;
        }

        .status-badge {
            margin-left: auto;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background: var(--success-light);
            color: var(--success);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        /* ========== INFO LIST ========== */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-secondary);
            font-size: 0.92rem;
        }

        .info-item i {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 14px;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .job-card:hover .info-item i {
            transform: scale(1.05);
        }

        .info-item.location i { 
            color: #dc2626; 
            background: rgba(220, 38, 38, 0.1); 
        }
        .info-item.salary i { 
            color: var(--success); 
            background: rgba(5, 150, 105, 0.12); 
        }
        .info-item.deadline i { 
            color: var(--primary); 
            background: var(--primary-light); 
        }

        /* ========== DESCRIPTION ========== */
        .job-description {
            color: var(--text-secondary);
            font-size: 0.93rem;
            line-height: 1.65;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 72px 32px;
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 2px dashed var(--border);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-lighter));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 32px;
            border: 3px solid var(--border-light);
        }

        .empty-title {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0 0 10px;
        }

        .empty-text {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0;
        }

        /* ========== PAGINATION ========== */
        .pagination-wrapper {
            margin-top: 48px;
            display: flex;
            justify-content: center;
        }

        .pagination { 
            gap: 8px; 
            flex-wrap: wrap;
        }
        
        .page-item .page-link {
            border: 2px solid var(--border);
            color: var(--text-secondary);
            padding: 10px 18px;
            border-radius: 12px;
            background: var(--bg-card);
            font-weight: 500;
            transition: var(--transition);
        }
        
        .page-item .page-link:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-lighter);
        }
        
        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--text-white);
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.3);
        }
        
        .page-item.disabled .page-link {
            color: var(--text-muted);
            background: var(--bg-page);
            cursor: not-allowed;
        }

        /* ========== VIEW ALL BUTTON ========== */
        .view-all-wrapper {
            text-align: center;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid var(--border-light);
        }

        .btn-view-all {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            color: var(--primary);
            padding: 14px 36px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid var(--primary);
            transition: var(--transition);
            box-shadow: 0 4px 16px rgba(30, 64, 175, 0.15);
        }

        .btn-view-all:hover {
            background: var(--primary);
            color: var(--text-white);
            transform: translateY(-3px);
            box-shadow: 0 12px 36px rgba(30, 64, 175, 0.3);
        }

        .btn-view-all i {
            transition: transform 0.2s ease;
        }

        .btn-view-all:hover i {
            transform: translateX(4px);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .page-title { font-size: 1.7rem; }
            .page-subtitle { font-size: 1rem; }
            .search-form { flex-direction: column; }
            .search-btn { width: 100%; justify-content: center; }
            .job-card { padding: 22px; }
            .card-header { gap: 12px; }
            .logo-wrapper { width: 52px; height: 52px; }
        }

        @media (max-width: 480px) {
            .custom-container { padding: 16px 12px; }
            .page-header { padding: 36px 20px 28px; border-radius: 16px; }
            .page-title { font-size: 1.5rem; }
            .search-box { padding: 18px; }
            .job-card { padding: 20px; border-radius: 16px; }
        }

        /* ========== UTILITIES ========== */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Highlight keyword search */
        mark {
            background: linear-gradient(120deg, rgba(96, 165, 250, 0.3), rgba(96, 165, 250, 0.15));
            color: var(--primary-dark);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="custom-container">

    {{-- PAGE HEADER --}}
    <header class="page-header">
        {{-- Tombol Kembali --}}
        <div class="mb-3 text-start">
            <a href="{{ url('/') }}" class="btn btn-back btn-sm rounded-pill px-4 d-inline-flex align-items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Landing Page
            </a>
        </div>
        
    
        
        <h1 class="page-title">Lowongan Pekerjaan</h1>
        <p class="page-subtitle">Temukan peluang karir terbaik untuk alumni dan mahasiswa</p>
    </header>

    {{-- SEARCH BOX --}}
    <div class="search-box">
        <form class="search-form" action="{{ url('/lowongan') }}" method="GET">
            <input type="text" 
                   name="search" 
                   class="search-input" 
                   placeholder="Cari posisi, perusahaan, atau lokasi..." 
                   value="{{ old('search', request('search')) }}">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Cari Lowongan
            </button>
            {{-- Tombol Reset --}}
            @if(request('search'))
            <a href="{{ url('/lowongan') }}" class="btn btn-reset px-4 rounded-3 d-flex align-items-center" title="Reset Pencarian">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    {{-- JOB CARDS GRID --}}
    <div class="row g-4">
        @forelse($lowongan as $item)
        <div class="col-lg-4 col-md-6">
            <article class="job-card">
                
                {{-- Card Header --}}
                <div class="card-header">
                    {{-- Logo --}}
                    <div class="logo-wrapper">
                        @if($item->logo)
                            <img src="{{ asset('storage/' . $item->logo) }}" 
                                 alt="{{ $item->nama_perusahaan }}"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.closest('.logo-wrapper').innerHTML='<i class=\'fas fa-building\'></i>'">
                        @else
                            <i class="fas fa-building"></i>
                        @endif
                    </div>
                    
                    {{-- Title & Company --}}
                    <div class="flex-grow-1 min-width-0">
                        <h3 class="job-title text-truncate-2" title="{{ $item->posisi }}">
                            {{-- Highlight keyword jika ada search --}}
                            @if(request('search'))
                                {!! str_ireplace(
                                    request('search'), 
                                    '<mark>'.request('search').'</mark>', 
                                    e($item->posisi)
                                ) !!}
                            @else
                                {{ $item->posisi }}
                            @endif
                        </h3>
                        <p class="company-name text-truncate" title="{{ $item->nama_perusahaan }}">
                            {{ $item->nama_perusahaan }}
                        </p>
                    </div>
                    
                    {{-- Status --}}
                    <span class="status-badge">
                        <i class="fas fa-circle" style="font-size:6px;vertical-align:middle;margin-right:4px"></i>
                        Aktif
                    </span>
                </div>

                {{-- Info List --}}
                <div class="info-list">
                    <div class="info-item location">
                        <i class="fas fa-location-dot"></i>
                        <span>{{ $item->lokasi ?? 'Tidak disebutkan' }}</span>
                    </div>
                    <div class="info-item salary">
                        <i class="fas fa-wallet"></i>
                        <span>{{ $item->gaji ?? 'Negosiasi' }}</span>
                    </div>
                    <div class="info-item deadline">
                        <i class="fas fa-calendar-check"></i>
                        <span>Batas: {{ $item->batas_lamaran ? \Carbon\Carbon::parse($item->batas_lamaran)->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                {{-- Description --}}
                <p class="job-description">
                    {{ \Illuminate\Support\Str::limit(strip_tags($item->deskripsi), 120) }}
                </p>

            </article>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h4 class="empty-title">Belum Ada Lowongan</h4>
                <p class="empty-text">Silakan cek kembali nanti untuk peluang karir terbaru.</p>
                <a href="{{ url('/lowongan') }}" class="btn btn-outline-primary mt-3 rounded-pill px-4">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Halaman
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($lowongan->hasPages())
    <div class="pagination-wrapper">
        {{ $lowongan->links() }}
    </div>
    @endif

    {{-- VIEW ALL BUTTON --}}
    <div class="view-all-wrapper">
        <a href="{{ url('/lowongan') }}" class="btn-view-all">
            <i class="fas fa-list-ul"></i>
            Lihat Semua Lowongan
        </a>
    </div>

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Optional: Smooth scroll untuk anchor links --}}
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href'))?.scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>

</body>
</html>