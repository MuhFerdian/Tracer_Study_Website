<section id="lowongan" class="lowongan-section py-5">
    <div class="container">

        {{-- Header Section --}}
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center gap-2 badge-wrapper mb-3">
                <span class="badge-dot"></span>
                <span class="badge-text">LOWONGAN TERBARU</span>
            </div>
            <h2 class="section-title">Peluang Karir Alumni</h2>
            <p class="section-subtitle">
                Temukan peluang kerja terbaru dari perusahaan dan partner kampus terpercaya.
            </p>
        </div>

        {{-- Card Grid --}}
        <div class="row g-4">
            @forelse($lowongan ?? [] as $item)
            <div class="col-lg-4 col-md-6">
                <article class="job-card h-100">
                    
                    {{-- Glow effect --}}
                    <div class="card-glow"></div>
                    
                    <div class="card-content">
                        
                        {{-- Header: Logo + Title + Status --}}
                        <div class="card-header d-flex align-items-start gap-3 mb-4">
                            
                            {{-- Company Logo --}}
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
                            
                            <div class="flex-grow-1 min-width-0">
                                <h3 class="job-title text-truncate" title="{{ $item->posisi }}">
                                    {{ $item->posisi }}
                                </h3>
                                <p class="company-name text-truncate" title="{{ $item->nama_perusahaan }}">
                                    {{ $item->nama_perusahaan }}
                                </p>
                            </div>
                            
                            <span class="status-badge status-active">Aktif</span>
                        </div>

                        {{-- Info baris (lokasi, gaji, batas) --}}
<div class="job-info-list">
    <div class="info-row location">
        <i class="fas fa-location-dot"></i>
        <span>{{ $item->lokasi ?? '-' }}</span>
    </div>
    <div class="info-row salary">
        <i class="fas fa-wallet"></i>
        <span>{{ $item->gaji ?? '-' }}</span>
    </div>
    <div class="info-row deadline">
        <i class="fas fa-calendar-check"></i>
        <span>Batas: {{ $item->batas_lamaran ?? '-' }}</span>
    </div>
</div>

                        {{-- Description --}}
                        <p class="job-description">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->deskripsi), 120) }}
                        </p>

                        {{-- Footer Action --}}
                        <div class="card-footer mt-auto pt-3 border-top">
                            <a href="{{ url('/lowongan/'.$item->id) }}" class="btn-view-job">
                                Lihat Detail</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                    </div>
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
                </div>
            </div>
            @endforelse
        </div>

        {{-- View All Button --}}
        <div class="text-center mt-5">
            <a href="{{ url('/lowongan') }}" class="btn-view-all">
                Lihat Selangkapnya
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

    </div>
</section>

<style>
/* ──────────── VARIABLES & BASE ───────────── */
.lowongan-section {
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
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 12px 32px rgba(37, 99, 235, 0.12);
    --shadow-hover: 0 20px 48px rgba(37, 99, 235, 0.18);
    --radius: 20px;
    --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    
    background: var(--bg-section);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    position: relative;
    overflow: hidden;
}

/* Decorative background elements */
.lowongan-section::before,
.lowongan-section::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    z-index: 0;
    opacity: 0.5;
}
.lowongan-section::before {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
    top: -150px;
    right: -100px;
}
.lowongan-section::after {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
    bottom: -100px;
    left: -50px;
}

/* ──────────── HEADER ───────────── */
.badge-wrapper {
    background: rgba(37, 99, 235, 0.08);
    border: 1px solid rgba(37, 99, 235, 0.15);
    padding: 6px 16px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.badge-dot {
    width: 8px;
    height: 8px;
    background: var(--primary);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(0.9); }
}

.badge-text {
    font-size: 11px;
    font-weight: 700;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 16px 0 12px;
    line-height: 1.2;
}

.section-subtitle {
    color: var(--text-secondary);
    font-size: 1.05rem;
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ──────────── JOB CARD ───────────── */
.job-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
    z-index: 1;
}

.job-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: var(--radius);
    padding: 1px;
    background: linear-gradient(135deg, transparent 60%, rgba(37, 99, 235, 0.1));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
}

.card-glow {
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
    border-radius: 50%;
    top: -80px;
    right: -80px;
    transition: var(--transition);
    opacity: 0;
    z-index: 0;
}

.job-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
    border-color: rgba(37, 99, 235, 0.3);
}

.job-card:hover .card-glow {
    opacity: 1;
    transform: scale(1.1);
}

.card-content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* ──────────── LOGO & HEADER ───────────── */
.logo-wrapper {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    transition: var(--transition);
}

.logo-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 6px;
}

.logo-wrapper i {
    font-size: 20px;
    color: var(--primary);
}

.job-card:hover .logo-wrapper {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.job-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px;
    line-height: 1.3;
}

.company-name {
    font-size: 0.9rem;
    color: var(--primary);
    font-weight: 600;
    margin: 0;
    line-height: 1.4;
}

/* Status Badge */
.status-badge {
    padding: 5px 14px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    flex-shrink: 0;
}

.status-active {
    background: rgba(16, 185, 129, 0.12);
    color: var(--success);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

/* ───────────── INFO LIST ───────────── */
.job-info-list {
    position: relative;
    z-index: 1;
    margin-bottom: 18px;
    display: grid;
    gap: 12px;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-secondary);
    font-size: 0.93rem;
    padding: 8px 12px;
    border-radius: 10px;
    transition: var(--transition);
}

.info-row:hover {
    background: rgba(37, 99, 235, 0.04);
}

.info-row i {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}

/* 📍 Location - Red/Rose */
.info-row.location i {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
    padding: 4px;
    border-radius: 6px;
}

/* 💰 Salary - Emerald/Teal (Modern Green) */
.info-row.salary i {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
    padding: 4px;
    border-radius: 6px;
}

/* ⏰ Deadline - Biru (Kembali ke warna asli) */
.info-row.deadline i {
    color: #2563eb;
    background: rgba(37, 99, 235, 0.1);
    padding: 4px;
    border-radius: 6px;
}

/* Alternative: Gunakan warna Amber/Orange untuk deadline */
/*
.info-row.deadline i {
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
}
*/

/* ──────────── DESCRIPTION ───────────── */
.job-description {
    color: var(--text-secondary);
    font-size: 0.92rem;
    line-height: 1.65;
    margin: 0 0 20px;
    flex-grow: 1;
}

/* ──────────── FOOTER & BUTTONS ───────────── */
.card-footer {
    border-color: var(--border);
}

.btn-view-job {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--primary);
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--transition);
    border: none;
    width: 100%;
}

.btn-view-job:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
    color: white;
}

.btn-view-job i {
    transition: transform 0.2s ease;
}

.btn-view-job:hover i {
    transform: translateX(4px);
}

/* View All Button */
.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: white;
    color: var(--primary);
    padding: 14px 32px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    border: 2px solid var(--primary);
    transition: var(--transition);
}

.btn-view-all:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.btn-view-all i {
    transition: transform 0.2s ease;
}

.btn-view-all:hover i {
    transform: translateX(4px);
}

/* ──────────── EMPTY STATE ───────────── */
.empty-state {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}

.empty-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    background: var(--primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 28px;
}

.empty-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px;
}

.empty-text {
    color: var(--text-muted);
    font-size: 0.95rem;
    margin: 0;
}

/* ──────────── RESPONSIVE ───────────── */
@media (max-width: 768px) {
    .section-title {
        font-size: 1.6rem;
    }
    
    .section-subtitle {
        font-size: 1rem;
    }
    
    .job-card {
        padding: 20px;
        border-radius: 16px;
    }
    
    .logo-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
    }
    
    .job-title {
        font-size: 1.05rem;
    }
    
    .btn-view-all {
        padding: 12px 28px;
        font-size: 0.95rem;
    }
}

@media (max-width: 576px) {
    .card-header {
        flex-wrap: wrap;
    }
    
    .status-badge {
        margin-left: auto;
        margin-top: 8px;
    }
    
    .job-details {
        gap: 8px;
    }
    
    .detail-item {
        font-size: 0.88rem;
    }
}

/* ──────────── UTILITIES ───────────── */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.min-width-0 {
    min-width: 0;
}

.border-top {
    border-top: 1px solid var(--border) !important;
}

.mt-auto {
    margin-top: auto;
}
</style>

{{-- Optional: Add Google Font Inter for better typography --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">