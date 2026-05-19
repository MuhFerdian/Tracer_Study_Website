<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Tracer Study — Tracer Study JTI Polije</title>
    <link rel="icon" type="image/png" href="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/Logo_Polije2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('startbootstrap-sb-admin-gh-pages/assets/img/Logo_Polije2.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --navy:  #08224f;
            --blue:  #1557c0;
            --blue2: #2582f3;
            --cyan:  #62d9ff;
            --ink:   #10233f;
            --muted: #64748b;
            --bg:    #f0f4f8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        /* ── TOOLBAR ── */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 999;
            background: linear-gradient(90deg, var(--navy), var(--blue));
            padding: .65rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(8,34,79,.3);
        }
        .toolbar-title { color:#fff; font-weight:700; font-size:.95rem; display:flex; align-items:center; gap:.5rem; }
        .toolbar-actions { display:flex; gap:.5rem; }
        .btn-print {
            background:#fff; color:var(--navy); border:none; border-radius:999px;
            padding:.4rem 1.1rem; font-weight:700; font-size:.82rem; cursor:pointer;
            display:inline-flex; align-items:center; gap:.35rem; transition:all .2s;
        }
        .btn-print:hover { background:#dff2ff; transform:translateY(-1px); }
        .btn-back {
            background:rgba(255,255,255,.15); color:#fff;
            border:1px solid rgba(255,255,255,.3); border-radius:999px;
            padding:.4rem 1.1rem; font-weight:600; font-size:.82rem;
            text-decoration:none; display:inline-flex; align-items:center; gap:.35rem;
        }
        .btn-back:hover { background:rgba(255,255,255,.25); color:#fff; }

        /* ── WRAPPER ── */
        .wrap { max-width: 860px; margin: 1.5rem auto; padding: 0 1rem 3rem; }

        /* ── COVER ── */
        .cover {
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 55%, #17a4df 100%);
            color: #fff;
            border-radius: 16px;
            padding: 2rem 2rem 1.75rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .cover::before {
            content:'';
            position:absolute; inset:0;
            background:
                linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),
                linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);
            background-size:40px 40px;
        }
        .cover-inner { position:relative; z-index:1; }
        .cover-badge {
            display:inline-flex; align-items:center; gap:.4rem;
            background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.2);
            border-radius:999px; padding:.25rem .75rem;
            font-size:.72rem; font-weight:700; letter-spacing:.05em;
            text-transform:uppercase; margin-bottom:.85rem;
        }
        .cover h1 { font-size:clamp(1.5rem,4vw,2.2rem); font-weight:800; line-height:1.1; margin-bottom:.4rem; }
        .cover-meta { color:rgba(255,255,255,.75); font-size:.85rem; margin-bottom:1.25rem; }
        .cover-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.65rem; }
        .cover-stat {
            background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18);
            border-radius:10px; padding:.75rem 1rem; backdrop-filter:blur(8px);
        }
        .cover-stat strong { display:block; font-size:1.5rem; font-weight:800; line-height:1; }
        .cover-stat span { font-size:.75rem; color:rgba(255,255,255,.72); }

        /* ── QUESTION CARD ── */
        .q-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(8,34,79,.08);
            margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .q-head {
            padding: .85rem 1.1rem .65rem;
            border-bottom: 1px solid #e8edf5;
            display: flex;
            align-items: flex-start;
            gap: .65rem;
        }
        .q-num {
            flex-shrink: 0;
            width: 26px; height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--blue), var(--blue2));
            color: #fff;
            font-size: .72rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .q-title { font-weight: 600; font-size: .9rem; line-height: 1.45; color: var(--ink); }
        .q-code {
            display:inline-block;
            font-size:.68rem; font-weight:700; color:var(--blue);
            background:rgba(21,87,192,.1); border-radius:999px;
            padding:.1rem .45rem; margin-left:.35rem; vertical-align:middle;
        }
        .q-meta { font-size:.75rem; color:var(--muted); margin-top:.2rem; }
        .q-body { padding: .85rem 1.1rem 1.1rem; }

        /* ── CHART CONTAINER ── */
        .chart-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            align-items: center;
        }
        .chart-wrap.full { grid-template-columns: 1fr; }
        .chart-canvas-wrap {
            position: relative;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chart-canvas-wrap canvas { max-height: 220px; }

        /* ── HORIZONTAL BAR (CSS) ── */
        .hbar-list { display:grid; gap:.45rem; }
        .hbar-row { display:grid; grid-template-columns:1fr 3fr 52px; align-items:center; gap:.5rem; }
        .hbar-label { font-size:.78rem; font-weight:500; color:var(--ink); text-align:right; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .hbar-track { background:#e8edf5; border-radius:999px; height:20px; overflow:hidden; }
        .hbar-fill { height:100%; border-radius:999px; min-width:3px; }
        .hbar-val { font-size:.75rem; font-weight:700; color:var(--ink); }
        .hbar-pct { font-size:.68rem; color:var(--muted); font-weight:400; }

        /* ── TEXT ANSWERS ── */
        .text-list { display:grid; gap:.4rem; }
        .text-item {
            background:#f8faff; border:1px solid #e2e8f0;
            border-radius:8px; padding:.5rem .8rem;
            font-size:.83rem; color:var(--ink);
            display:flex; align-items:flex-start; gap:.4rem;
        }
        .text-item::before { content:'›'; color:var(--blue); font-weight:700; flex-shrink:0; }
        .text-more { font-size:.75rem; color:var(--muted); text-align:center; padding:.35rem; }

        /* ── EMPTY ── */
        .q-empty { text-align:center; color:var(--muted); font-size:.82rem; padding:.75rem 0; }

        /* ── MATRIX TABLE ── */
        .matrix-table { width:100%; border-collapse:collapse; font-size:.8rem; }
        .matrix-table th { background:linear-gradient(135deg,var(--blue),var(--blue2)); color:#fff; padding:.5rem .7rem; text-align:center; font-weight:600; }
        .matrix-table th:first-child { text-align:left; }
        .matrix-table td { padding:.45rem .7rem; border-bottom:1px solid #e8edf5; color:var(--ink); text-align:center; }
        .matrix-table td:first-child { text-align:left; }
        .matrix-table tr:nth-child(even) td { background:#f8faff; }

        /* ── COLORS ── */
        .c0 { background:linear-gradient(90deg,#1557c0,#2582f3); }
        .c1 { background:linear-gradient(90deg,#0891b2,#22d3ee); }
        .c2 { background:linear-gradient(90deg,#059669,#34d399); }
        .c3 { background:linear-gradient(90deg,#d97706,#fbbf24); }
        .c4 { background:linear-gradient(90deg,#7c3aed,#a78bfa); }
        .c5 { background:linear-gradient(90deg,#dc2626,#f87171); }
        .c6 { background:linear-gradient(90deg,#0f766e,#2dd4bf); }
        .c7 { background:linear-gradient(90deg,#9333ea,#c084fc); }

        /* ── PRINT ── */
        @media print {
            @page { size:A4; margin:12mm 10mm; }
            body { background:#fff !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .toolbar { display:none !important; }
            .wrap { max-width:100%; margin:0; padding:0; }
            .cover { border-radius:0; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .q-card { box-shadow:none; border:1px solid #e2e8f0; break-inside:avoid; page-break-inside:avoid; }
            .hbar-fill, .cover, .cover-stat, .q-num, .section-card-header, .matrix-table th {
                -webkit-print-color-adjust:exact; print-color-adjust:exact;
            }
        }

        /* hide toolbar when in iframe */
        body.in-iframe .toolbar { display:none !important; }
        body.in-iframe .wrap { margin-top:.5rem; }
    </style>
</head>
<body>

<div class="toolbar">
    <div class="toolbar-title">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path fill="#fff" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 13h8v1.5H8V13zm0 3h5v1.5H8V16zm0-6h3v1.5H8V10z"/></svg>
        Laporan Tracer Study @if($periode)— {{ $periode->nama }}@endif
    </div>
    <div class="toolbar-actions">
        <a href="{{ url('/admin') }}" class="btn-back">← Kembali</a>
        <button class="btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    </div>
</div>

<div class="wrap">

    {{-- COVER --}}
    <div class="cover">
        <div class="cover-inner">
            <div class="cover-badge">📊 Laporan Hasil Tracer Study</div>
            <h1>Ringkasan Jawaban Alumni</h1>
            <div class="cover-meta">
                @if($periode)
                    <strong>{{ $periode->nama }}</strong> &nbsp;·&nbsp;
                    {{ \Carbon\Carbon::parse($periode->tanggal_buka)->format('d M Y') }}
                    s/d {{ \Carbon\Carbon::parse($periode->tanggal_tutup)->format('d M Y') }}
                    &nbsp;·&nbsp;
                @endif
                Dicetak: {{ now()->format('d M Y, H:i') }}
            </div>
            <div class="cover-stats">
                <div class="cover-stat">
                    <strong>{{ $totalAlumni }}</strong>
                    <span>Total Alumni</span>
                </div>
                <div class="cover-stat">
                    <strong>{{ $totalResponden }}</strong>
                    <span>Responden</span>
                </div>
                <div class="cover-stat">
                    <strong>{{ $totalAlumni > 0 ? round($totalResponden / $totalAlumni * 100) : 0 }}%</strong>
                    <span>Tingkat Respons</span>
                </div>
            </div>
        </div>
    </div>

    {{-- PERTANYAAN --}}
    @php
        $no = 1;
        $chartColors = [
            'rgba(21,87,192,0.85)',
            'rgba(8,145,178,0.85)',
            'rgba(5,150,105,0.85)',
            'rgba(217,119,6,0.85)',
            'rgba(124,58,237,0.85)',
            'rgba(220,38,38,0.85)',
            'rgba(15,118,110,0.85)',
            'rgba(147,51,234,0.85)',
        ];
        $chartBorders = [
            '#1557c0','#0891b2','#059669','#d97706',
            '#7c3aed','#dc2626','#0f766e','#9333ea',
        ];
    @endphp

    @forelse($questions as $q)
        @php
            $chartType = match(($no - 1) % 3) {
                0 => 'doughnut',
                1 => 'pie',
                2 => 'bar',
            };
        @endphp
        <div class="q-card">
            <div class="q-head">
                <div class="q-num">{{ $no++ }}</div>
                <div>
                    <div class="q-title">
                        {{ $q->pertanyaan }}
                        @if($q->kode_soal)<span class="q-code">{{ $q->kode_soal }}</span>@endif
                    </div>
                    <div class="q-meta">
                        Tipe: {{ ucfirst($q->type) }}
                        &nbsp;·&nbsp; {{ $q->totalResponden }} responden menjawab
                    </div>
                </div>
            </div>
            <div class="q-body">

                @if($q->totalResponden === 0)
                    <div class="q-empty">Belum ada jawaban untuk pertanyaan ini.</div>

                @elseif(in_array($q->type, ['single', 'multiple']))
                    @php
                        $labels  = $q->distribution->pluck('label')->toArray();
                        $counts  = $q->distribution->pluck('count')->map(fn($v) => (int)$v)->toArray();
                        $total   = array_sum($counts);
                        $hasData = $total > 0;
                        $cId     = 'chart_' . $q->id;
                        $colors  = array_slice($chartColors, 0, count($labels));
                        $borders = array_slice($chartBorders, 0, count($labels));
                        while(count($colors) < count($labels)) {
                            $colors[]  = $chartColors[count($colors) % count($chartColors)];
                            $borders[] = $chartBorders[count($borders) % count($chartBorders)];
                        }
                    @endphp

                    @if($hasData)
                        @if($chartType === 'bar')
                            {{-- BAR VERTIKAL — tanpa canvas kiri, full width --}}
                            <div class="chart-canvas-wrap full" style="height:200px;">
                                <canvas id="{{ $cId }}" style="max-height:200px;"></canvas>
                            </div>
                            <script>
                            (function(){
                                var ctx = document.getElementById('{{ $cId }}');
                                if(!ctx) return;
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: {!! json_encode($labels) !!},
                                        datasets: [{
                                            label: 'Jumlah',
                                            data: {!! json_encode($counts) !!},
                                            backgroundColor: {!! json_encode($colors) !!},
                                            borderColor: {!! json_encode($borders) !!},
                                            borderWidth: 1,
                                            borderRadius: 6
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: { callbacks: { label: function(c){ return ' '+c.parsed.y+' responden'; } } }
                                        },
                                        scales: {
                                            y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                            x: { ticks: { font: { size: 10 }, maxRotation: 30 } }
                                        }
                                    }
                                });
                            })();
                            </script>
                            {{-- Hbar list di bawah --}}
                            <div class="hbar-list" style="margin-top:.85rem;">
                                @foreach($q->distribution as $idx => $item)
                                    @php $pct = $total > 0 ? round((int)$item->count / $total * 100) : 0; @endphp
                                    <div class="hbar-row">
                                        <div class="hbar-label" title="{{ $item->label }}">{{ Str::limit($item->label, 28) }}</div>
                                        <div class="hbar-track">
                                            <div class="hbar-fill c{{ $idx % 8 }}" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <div class="hbar-val">{{ $item->count }} <span class="hbar-pct">({{ $pct }}%)</span></div>
                                    </div>
                                @endforeach
                            </div>

                        @else
                            {{-- PIE atau DOUGHNUT — layout 2 kolom --}}
                            <div class="chart-wrap">
                                <div class="chart-canvas-wrap">
                                    <canvas id="{{ $cId }}" width="200" height="200"></canvas>
                                </div>
                                <div class="hbar-list">
                                    @foreach($q->distribution as $idx => $item)
                                        @php $pct = $total > 0 ? round((int)$item->count / $total * 100) : 0; @endphp
                                        <div class="hbar-row">
                                            <div class="hbar-label" title="{{ $item->label }}">{{ Str::limit($item->label, 28) }}</div>
                                            <div class="hbar-track">
                                                <div class="hbar-fill c{{ $idx % 8 }}" style="width:{{ $pct }}%"></div>
                                            </div>
                                            <div class="hbar-val">{{ $item->count }} <span class="hbar-pct">({{ $pct }}%)</span></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <script>
                            (function(){
                                var ctx = document.getElementById('{{ $cId }}');
                                if(!ctx) return;
                                new Chart(ctx, {
                                    type: '{{ $chartType }}',
                                    data: {
                                        labels: {!! json_encode($labels) !!},
                                        datasets: [{
                                            data: {!! json_encode($counts) !!},
                                            backgroundColor: {!! json_encode($colors) !!},
                                            borderColor: {!! json_encode($borders) !!},
                                            borderWidth: 2,
                                            hoverOffset: 6
                                        }]
                                    },
                                    options: {
                                        responsive: false,
                                        @if($chartType === 'doughnut') cutout: '55%', @endif
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: { callbacks: { label: function(ctx) {
                                                var t = ctx.dataset.data.reduce(function(a,b){return a+b;},0);
                                                var p = t > 0 ? Math.round(ctx.parsed/t*100) : 0;
                                                return ctx.label+': '+ctx.parsed+' ('+p+'%)';
                                            }}}
                                        }
                                    }
                                });
                            })();
                            </script>
                        @endif

                    @else
                        <div class="hbar-list">
                            @foreach($q->distribution as $idx => $item)
                                <div class="hbar-row">
                                    <div class="hbar-label">{{ Str::limit($item->label, 28) }}</div>
                                    <div class="hbar-track"><div class="hbar-fill c{{ $idx % 8 }}" style="width:0%"></div></div>
                                    <div class="hbar-val">0 <span class="hbar-pct">(0%)</span></div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                @elseif($q->type === 'scale')
                    @php
                        $labels  = $q->distribution->pluck('label')->toArray();
                        $counts  = $q->distribution->pluck('count')->map(fn($v) => (int)$v)->toArray();
                        $total   = array_sum($counts);
                        $cId     = 'chart_' . $q->id;
                    @endphp
                    @if($total > 0)
                        <div class="chart-canvas-wrap full" style="height:180px;">
                            <canvas id="{{ $cId }}" style="max-height:180px;"></canvas>
                        </div>
                        @if($q->avgValue !== null)
                            <div style="text-align:right;font-size:.78rem;color:var(--muted);margin-top:.4rem;">
                                Rata-rata: <strong style="color:var(--blue)">{{ number_format($q->avgValue,1) }}</strong>
                            </div>
                        @endif
                        <script>
                        (function(){
                            var ctx = document.getElementById('{{ $cId }}');
                            if(!ctx) return;
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: {!! json_encode($labels) !!},
                                    datasets: [{
                                        label: 'Jumlah',
                                        data: {!! json_encode($counts) !!},
                                        backgroundColor: 'rgba(21,87,192,0.8)',
                                        borderColor: '#1557c0',
                                        borderWidth: 1,
                                        borderRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                                    }
                                }
                            });
                        })();
                        </script>
                    @else
                        <div class="q-empty">Belum ada jawaban untuk pertanyaan ini.</div>
                    @endif

                @elseif($q->type === 'text')
                    @if($q->textAnswers->count() > 0)
                        <div class="text-list">
                            @foreach($q->textAnswers->take(12) as $ans)
                                <div class="text-item">{{ $ans }}</div>
                            @endforeach
                        </div>
                        @if($q->textAnswers->count() > 12)
                            <div class="text-more">+ {{ $q->textAnswers->count() - 12 }} jawaban lainnya</div>
                        @endif
                    @else
                        <div class="q-empty">Belum ada jawaban untuk pertanyaan ini.</div>
                    @endif

                @elseif($q->type === 'matrix')
                    <div style="overflow-x:auto;">
                        <table class="matrix-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    @foreach($q->matrixOptions as $opt)
                                        <th>{{ $opt->label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($q->matrixRows as $row)
                                    <tr>
                                        <td>{{ $row->item_label }}</td>
                                        @foreach($q->matrixOptions as $opt)
                                            @php
                                                $cnt = $q->matrixData[$row->id][$opt->id] ?? 0;
                                                $pct = ($q->matrixRowTotal[$row->id] ?? 0) > 0
                                                    ? round($cnt / $q->matrixRowTotal[$row->id] * 100) : 0;
                                            @endphp
                                            <td>{{ $cnt }} <span style="font-size:.68rem;color:var(--muted)">({{ $pct }}%)</span></td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    @empty
        <div class="q-card" style="padding:2rem;text-align:center;color:var(--muted);">
            Belum ada pertanyaan aktif.
        </div>
    @endforelse

</div>

<script>
    if (window.self !== window.top) {
        document.body.classList.add('in-iframe');
    }
</script>
</body>
</html>
