<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lowongan->posisi }} - {{ $lowongan->nama_perusahaan }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --success: #10b981;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --bg-card: #ffffff;
            --bg-section: #f8fafc;
            --border: #e2e8f0;
            --navy-darkest: #0f172a;
            --navy-dark: #1e3a8a;
        }

        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #dbeafe 100%);
            min-height: 100vh;
        }

        .detail-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 20px 16px;
        }

        /* HERO SECTION - Compact */
        .hero-section {
            background: linear-gradient(135deg, var(--navy-darkest) 0%, var(--navy-dark) 100%);
            border-radius: 16px;
            padding: 24px 28px;
            margin-bottom: 16px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
            margin: 0;
        }

        /* MAIN CARD - Compact */
        .detail-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        /* COMPANY HEADER - Compact */
        .company-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 24px;
        }

        .company-logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border: 1px solid var(--border);
            object-fit: contain;
            padding: 10px;
            flex-shrink: 0;
        }

        .company-info h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0 0 4px;
        }

        .company-name {
            color: var(--primary);
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }

        /* STATS GRID - Compact */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
        }

        /* INFO ROW - Compact */
        .info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
            font-size: 0.88rem;
        }

        .info-row i {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            border-radius: 8px;
        }

        .info-row.location i {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .info-row.salary i {
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .info-row.deadline i {
            color: #2563eb;
            background: rgba(37, 99, 235, 0.1);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            display: block;
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .stat-value {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-value.salary {
            color: var(--success);
        }

        /* SECTIONS - Compact */
        .section {
            margin-bottom: 20px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 12px;
        }

        .description-box {
            background: var(--bg-section);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid var(--border);
            max-height: 200px;
            overflow-y: auto;
        }

        .description-text {
            color: var(--text-secondary);
            line-height: 1.6;
            font-size: 0.9rem;
            margin: 0;
            white-space: pre-wrap;
        }

        /* CONTACT - Compact */
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--bg-section);
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-bottom: 10px;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .contact-icon i {
            font-size: 0.9rem;
        }

        .contact-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 2px;
        }

        .contact-value {
            font-size: 0.9rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .contact-value a {
            color: var(--primary);
            text-decoration: none;
        }

        /* APPLY BUTTON - Compact */
        .btn-apply {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 10px 24px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: all 0.3s ease;
            width: auto;
            margin-top: 12px;
        }

        .btn-apply:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
            color: white;
        }

        .btn-apply i {
            font-size: 0.85rem;
            transition: transform 0.2s ease;
        }

        .btn-apply:hover i {
            transform: translateX(3px);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .detail-container {
                padding: 16px 12px;
            }

            .hero-section {
                padding: 20px;
            }

            .detail-card {
                padding: 20px;
            }

            .company-header {
                flex-direction: column;
                text-align: center;
                align-items: center;
                gap: 12px;
            }

            .company-info h1 {
                font-size: 1.3rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .stat-card {
                padding: 14px;
            }

            .btn-apply {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="detail-container">

    {{-- HERO --}}
    <div class="hero-section">
        <a href="{{ url('/lowongan') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <p class="hero-subtitle">Detail informasi lowongan pekerjaan</p>
    </div>

    {{-- MAIN CARD --}}
    <div class="detail-card">

        {{-- COMPANY HEADER --}}
        <div class="company-header">
            @if($lowongan->logo)
                <img src="{{ asset('storage/' . $lowongan->logo) }}" class="company-logo" alt="Logo">
            @else
                <div class="company-logo d-flex align-items-center justify-content-center">
                    <i class="fas fa-building" style="color: var(--primary); font-size: 24px;"></i>
                </div>
            @endif
            
            <div class="company-info">
                <h1>{{ $lowongan->posisi }}</h1>
                <p class="company-name">{{ $lowongan->nama_perusahaan }}</p>
            </div>
        </div>

        {{-- STATS GRID --}}
        <div class="stats-grid">
            
            {{-- Lokasi --}}
            <div class="stat-card">
                <div class="info-row location">
                    <i class="fas fa-location-dot"></i>
                    <div class="stat-content">
                        <span class="stat-label">Lokasi</span>
                        <span class="stat-value">{{ $lowongan->lokasi ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Gaji --}}
            <div class="stat-card">
                <div class="info-row salary">
                    <i class="fas fa-wallet"></i>
                    <div class="stat-content">
                        <span class="stat-label">Gaji</span>
                        <span class="stat-value salary">{{ $lowongan->gaji ?? 'Negosiasi' }}</span>
                    </div>
                </div>
            </div>

            {{-- Batas --}}
            <div class="stat-card">
                <div class="info-row deadline">
                    <i class="fas fa-calendar-check"></i>
                    <div class="stat-content">
                        <span class="stat-label">Batas</span>
                        <span class="stat-value">
                            {{ $lowongan->batas_lamaran ? \Carbon\Carbon::parse($lowongan->batas_lamaran)->format('d M Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- DESKRIPSI --}}
        <div class="section">
            <h3 class="section-title">Deskripsi Pekerjaan</h3>
            <div class="description-box">
                <p class="description-text">{!! nl2br(e($lowongan->deskripsi)) !!}</p>
            </div>
        </div>

        {{-- KONTAK & APPLY --}}
        <div class="section">
            <h3 class="section-title">Kontak & Informasi</h3>
            
            @if($lowongan->kontak)
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div>
                    <div class="contact-label">Kontak</div>
                    <div class="contact-value">{{ $lowongan->kontak }}</div>
                </div>
            </div>
            @endif
            
            @if($lowongan->link_lamaran)
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-link"></i>
                </div>
                <div>
                    <div class="contact-label">Link</div>
                    <div class="contact-value">
                        <a href="{{ $lowongan->link_lamaran }}" target="_blank">
                            {{ Str::limit($lowongan->link_lamaran, 40) }}
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ $lowongan->link_lamaran }}" target="_blank" class="btn-apply">
                <i class="fas fa-paper-plane"></i>
                <span>Lamar Sekarang</span>
            </a>
            @endif
        </div>

    </div>

</div>

</body>
</html>