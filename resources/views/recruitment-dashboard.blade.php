<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard Recruitment</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * { box-sizing:border-box; }
    body { font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background:#f8fafc; margin:0; display:flex; height:100vh; color:#1e293b; }
    .sidebar { width:260px; min-width:260px; flex-shrink:0; }
    .main { flex:1; display:flex; flex-direction:column; min-width:0; }
    .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; }
    .topbar h1 { margin:0; font-size:16px; font-weight:600; color:#0f172a; letter-spacing:-0.3px; }
    .topbar .greeting { font-size:12px; color:#94a3b8; font-weight:400; }
    .topbar .date-label { font-size:11px; color:#94a3b8; background:#f1f5f9; padding:4px 10px; border-radius:6px; font-weight:500; }
    .content { flex:1; padding:16px 20px; overflow-y:auto; }

    /* Summary Cards */
    .summary-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:16px; }
    .summary-card {
      background:#fff; border-radius:8px; padding:12px 14px;
      box-shadow:0 1px 2px rgba(0,0,0,0.04);
      border:1px solid #f1f5f9;
      display:flex; align-items:center; gap:10px;
      transition:box-shadow 0.15s, transform 0.15s;
    }
    .summary-card:hover { box-shadow:0 3px 8px rgba(0,0,0,0.06); transform:translateY(-1px); }
    .sc-icon-wrap {
      width:34px; height:34px; border-radius:8px;
      display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0;
    }
    .sc-icon-wrap.blue { background:#eff6ff; }
    .sc-icon-wrap.purple { background:#f5f3ff; }
    .sc-icon-wrap.green { background:#f0fdf4; }
    .sc-icon-wrap.amber { background:#fffbeb; }
    .sc-icon-wrap.cyan { background:#ecfeff; }
    .sc-icon-wrap.rose { background:#fff1f2; }
    .sc-info { min-width:0; }
    .sc-value { font-size:20px; font-weight:700; color:#0f172a; line-height:1.1; letter-spacing:-0.5px; }
    .sc-label { font-size:10px; color:#94a3b8; font-weight:500; text-transform:uppercase; letter-spacing:0.4px; margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    /* Stats row */
    .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:16px; }
    .stat-mini {
      background:#fff; border-radius:8px; padding:10px 14px; border:1px solid #f1f5f9;
      box-shadow:0 1px 2px rgba(0,0,0,0.04); text-align:center;
    }
    .stat-mini .sm-value { font-size:22px; font-weight:700; color:#0f172a; }
    .stat-mini .sm-label { font-size:10px; color:#94a3b8; font-weight:500; text-transform:uppercase; letter-spacing:0.3px; margin-top:2px; }
    .stat-mini.today .sm-value { color:#2563eb; }
    .stat-mini.month .sm-value { color:#7c3aed; }
    .stat-mini.rate .sm-value { color:#059669; }

    /* Charts */
    .row-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
    .row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px; }
    .row-full { margin-bottom:12px; }
    .card {
      background:#fff; border-radius:8px; padding:14px;
      box-shadow:0 1px 2px rgba(0,0,0,0.04);
      border:1px solid #f1f5f9;
    }
    .card-header {
      display:flex; align-items:center; justify-content:space-between;
      margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #f8fafc;
    }
    .card-title {
      margin:0; font-size:12px; color:#334155; font-weight:600;
      display:flex; align-items:center; gap:6px; letter-spacing:-0.1px;
    }
    .card-title .icon { font-size:13px; }
    .card-badge {
      font-size:9px; background:#eff6ff; color:#2563eb; padding:2px 7px;
      border-radius:10px; font-weight:600; letter-spacing:0.3px; text-transform:uppercase;
    }
    .chart-wrap { position:relative; height:200px; }
    .chart-wrap.sm { height:170px; }

    /* Recent table */
    .recent-table { width:100%; border-collapse:separate; border-spacing:0; }
    .recent-table th {
      padding:6px 8px; text-align:left; font-size:9px; font-weight:600;
      color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;
      border-bottom:1px solid #f1f5f9;
    }
    .recent-table td {
      padding:7px 8px; font-size:11px; border-bottom:1px solid #f8fafc; color:#475569;
    }
    .recent-table tbody tr { transition:background 0.1s; }
    .recent-table tbody tr:hover { background:#f8fafc; }
    .recent-table tbody tr:last-child td { border-bottom:none; }
    .status-pill {
      display:inline-flex; align-items:center; gap:4px;
      font-size:10px; font-weight:500; padding:2px 8px; border-radius:10px;
    }
    .status-pill.completed { background:#f0fdf4; color:#15803d; }
    .status-pill.ongoing { background:#fffbeb; color:#b45309; }
    .status-pill .dot { width:5px; height:5px; border-radius:50%; }
    .status-pill.completed .dot { background:#10b981; }
    .status-pill.ongoing .dot { background:#f59e0b; }

    .copyright { font-size:10px; color:#cbd5e1; text-align:center; margin-top:12px; padding-top:10px; border-top:1px solid #f1f5f9; letter-spacing:0.2px; }
    .empty-state { text-align:center; color:#cbd5e1; padding:24px; font-size:11px; }

    @media (max-width:1400px) {
      .summary-grid { grid-template-columns:repeat(2,1fr); }
      .row-3 { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:1100px) {
      .row-2 { grid-template-columns:1fr; }
      .row-3 { grid-template-columns:1fr; }
      .stats-row { grid-template-columns:1fr; }
    }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  @include('layouts.sidebar')

  <div class="main">
    <div class="topbar">
      <div>
        <h1>Dashboard Recruitment</h1>
        <span class="greeting">Selamat datang, {{ Auth::user()->name }}</span>
      </div>
      <div class="date-label">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    <div class="content">
      <div style="display:flex;gap:8px;margin-bottom:12px;align-items:center;">
        <div style="flex:1;">
          <div id="recruitmentToggle" style="display:inline-flex;gap:8px;">
            <button type="button" id="btnParticipants" class="quick-link" style="background:#003e6f;color:#fff;">Dashboard Peserta</button>
            <button type="button" id="btnCheat" class="quick-link" style="background:#fff;color:#475569;border:1px solid #e2e8f0;">Dashboard Kecurangan</button>
          </div>
        </div>
      </div>

      <style>
        .quick-link { transition: background-color 220ms ease, color 220ms ease, transform 200ms ease, box-shadow 220ms ease; will-change: background-color, transform; border-radius:20px; padding:6px 12px; border:1px solid #e2e8f0; }
        .quick-link:hover { transform: translateY(-1px); }
        .quick-link.active { background:#003e6f;color:#fff;border-color:#003e6f; box-shadow: 0 8px 20px rgba(3,78,111,0.16); transform: translateY(-3px) scale(1.02); }

      {{-- Top Violators Table --}}
      </style>

      {{-- Participants Dashboard / Cheat Dashboard toggle wrappers --}}
      <div id="participantsDashboard">

      {{-- ===== SUMMARY CARDS ===== --}}
      <div class="summary-grid">
        <div class="summary-card">
          <div class="sc-icon-wrap purple">📋</div>
          <div class="sc-info">
            <div class="sc-value">{{ $totalBanks }}</div>
            <div class="sc-label">Bank Soal</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap green">✅</div>
          <div class="sc-info">
            <div class="sc-value">{{ $activeBanks }}</div>
            <div class="sc-label">Bank Aktif</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap blue">👥</div>
          <div class="sc-info">
            <div class="sc-value">{{ $totalParticipants }}</div>
            <div class="sc-label">Total Peserta</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap amber">🏁</div>
          <div class="sc-info">
            <div class="sc-value">{{ $completedParticipants }}</div>
            <div class="sc-label">Selesai Tes</div>
          </div>
        </div>
      </div>

      {{-- ===== MINI STATS ===== --}}
      <div class="stats-row">
        <div class="stat-mini today">
          <div class="sm-value">{{ $participantsToday }}</div>
          <div class="sm-label">Peserta Hari Ini</div>
        </div>
        <div class="stat-mini month">
          <div class="sm-value">{{ $participantsThisMonth }}</div>
          <div class="sm-label">Peserta Bulan Ini</div>
        </div>
        <div class="stat-mini rate">
          <div class="sm-value">{{ $completionRate }}%</div>
          <div class="sm-label">Tingkat Penyelesaian</div>
        </div>
      </div>

      {{-- ===== CHART ROW 1: Trend + By Position ===== --}}
      <div class="row-2">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📈</span> Tren Peserta per Bulan</h3>
            <span class="card-badge">6 Bulan</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartMonthly"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">💼</span> Peserta per Posisi</h3>
            <span class="card-badge">Distribusi</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartPosition"></canvas>
          </div>
        </div>
      </div>

      {{-- ===== CHART ROW 2: Per Bank Soal + Completion ===== --}}
      <div class="row-2">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📊</span> Peserta per Bank Soal</h3>
            <span class="card-badge">Top</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartPerBank"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">✅</span> Status Penyelesaian</h3>
            <span class="card-badge">Overall</span>
          </div>
          <div class="chart-wrap sm">
            <canvas id="chartCompletion"></canvas>
          </div>
        </div>
      </div>

      {{-- ===== RECENT PARTICIPANTS TABLE ===== --}}
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><span class="icon">🕐</span> Peserta Terbaru</h3>
          <span class="card-badge">10 Terakhir</span>
        </div>
        @if($recentParticipants->count() > 0)
        <div style="overflow-x:auto;">
        <table class="recent-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Posisi</th>
              <th>Bank Soal</th>
              <th>Skor</th>
              <th>Status</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentParticipants as $p)
            <tr>
              <td style="font-weight:500;color:#0f172a;">{{ $p->participant_name }}</td>
              <td>{{ $p->nik ?? '-' }}</td>
              <td>{{ $p->department ?? $p->position ?? '-' }}</td>
              <td>{{ $p->bank->title ?? '-' }}</td>
              <td style="font-weight:600;">{{ $p->completed ? $p->score : '-' }}</td>
              <td>
                @if($p->completed)
                  <span class="status-pill completed"><span class="dot"></span> Selesai</span>
                @else
                  <span class="status-pill ongoing"><span class="dot"></span> Berlangsung</span>
                @endif
              </td>
              <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        </div>
        @else
        <div class="empty-state">Belum ada peserta yang mengikuti tes.</div>
        @endif
      </div>

      </div> {{-- end participantsDashboard --}}

      <div id="cheatDashboard" style="display:none;">
      {{-- ===== SECTION: ANALISIS KECURANGAN ===== --}}
      <div style="margin-top:20px; margin-bottom:12px; padding-top:16px; border-top:2px solid #fecaca;">
        <h2 style="margin:0 0 4px 0; font-size:15px; color:#dc2626; font-weight:700; display:flex; align-items:center; gap:8px;">
          <span>🛡️</span> Analisis Tingkat Kecurangan
        </h2>
        <p style="margin:0; font-size:11px; color:#94a3b8;">Monitoring pelanggaran anti-cheat selama ujian berlangsung</p>
      </div>

      {{-- Cheat Summary Cards --}}
      <div class="summary-grid">
        <div class="summary-card">
          <div class="sc-icon-wrap rose">🚨</div>
          <div class="sc-info">
            <div class="sc-value" style="color:#dc2626;">{{ $totalCheaters }}</div>
            <div class="sc-label">Peserta Curang</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap green">✅</div>
          <div class="sc-info">
            <div class="sc-value" style="color:#059669;">{{ $totalClean }}</div>
            <div class="sc-label">Peserta Bersih</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap amber">📊</div>
          <div class="sc-info">
            <div class="sc-value" style="color:#d97706;">{{ $cheatRate }}%</div>
            <div class="sc-label">Tingkat Kecurangan</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap" style="background:#fef2f2;">⛔</div>
          <div class="sc-info">
            <div class="sc-value" style="color:#991b1b;">{{ $forcedSubmits }}</div>
            <div class="sc-label">Auto-Submit Paksa</div>
          </div>
        </div>
      </div>

      {{-- Mini stats for cheat --}}
      <div class="stats-row">
        <div class="stat-mini">
          <div class="sm-value" style="color:#dc2626;">{{ $avgViolation }}</div>
          <div class="sm-label">Rata-rata Pelanggaran</div>
        </div>
        <div class="stat-mini">
          <div class="sm-value" style="color:#ea580c;">{{ $cheatLevelData[2] + $cheatLevelData[3] }}</div>
          <div class="sm-label">Pelanggaran Sedang-Berat</div>
        </div>
        <div class="stat-mini">
          <div class="sm-value" style="color:#059669;">{{ $totalClean > 0 && ($totalClean + $totalCheaters) > 0 ? round(($totalClean / ($totalClean + $totalCheaters)) * 100, 1) : 0 }}%</div>
          <div class="sm-label">Tingkat Integritas</div>
        </div>
      </div>

      {{-- Chart Row: Distribusi Level + Jenis Pelanggaran --}}
      <div class="row-2">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🎯</span> Distribusi Level Kecurangan</h3>
            <span class="card-badge" style="background:#fef2f2;color:#dc2626;">Level</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartCheatLevel"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📋</span> Jenis Pelanggaran</h3>
            <span class="card-badge" style="background:#fef2f2;color:#dc2626;">Breakdown</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartViolationType"></canvas>
          </div>
        </div>
      </div>

      {{-- Chart Row: Tren Kecurangan + Korelasi Skor --}}
      <div class="row-2">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📈</span> Tren Kecurangan per Bulan</h3>
            <span class="card-badge" style="background:#fef2f2;color:#dc2626;">6 Bulan</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartCheatTrend"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🔗</span> Korelasi Pelanggaran vs Skor</h3>
            <span class="card-badge" style="background:#fef2f2;color:#dc2626;">Scatter</span>
          </div>
          <div class="chart-wrap">
            <canvas id="chartScatter"></canvas>
          </div>
        </div>
      </div>

      {{-- Top Violators Table --}}
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><span class="icon">⚠️</span> Top Pelanggar Terbanyak</h3>
          <span class="card-badge" style="background:#fef2f2;color:#dc2626;">Top 10</span>
        </div>
        @if($topViolators->count() > 0)
        <div style="overflow-x:auto;">
        <table class="recent-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Peserta</th>
              <th>NIK</th>
              <th>Departemen</th>
              <th>Jabatan</th>
              <th>Pelanggaran</th>
              <th>Skor</th>
              <th>Status</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($topViolators as $idx => $v)
            @php
              $vLevel = $v->violation_count >= 5 ? 'berat' : ($v->violation_count >= 3 ? 'sedang' : 'ringan');
            @endphp
            <tr>
              <td>{{ $idx + 1 }}</td>
              <td style="font-weight:500;color:#0f172a;">{{ $v->participant_name }}</td>
              <td>{{ $v->nik ?? '-' }}</td>
              <td>{{ $v->department ?? '-' }}</td>
              <td>{{ $v->position ?? '-' }}</td>
              <td>
                @if($v->violation_count >= 5)
                  <span style="background:#dc2626;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">{{ $v->violation_count }}x</span>
                @elseif($v->violation_count >= 3)
                  <span style="background:#ea580c;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">{{ $v->violation_count }}x</span>
                @else
                  <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">{{ $v->violation_count }}x</span>
                @endif
              </td>
              <td style="font-weight:600;">{{ $v->score }}</td>
              <td>
                @if($vLevel === 'berat')
                  <span style="background:#fef2f2;color:#dc2626;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">Berat</span>
                @elseif($vLevel === 'sedang')
                  <span style="background:#fff7ed;color:#ea580c;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">Sedang</span>
                @else
                  <span style="background:#fffbeb;color:#b45309;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">Ringan</span>
                @endif
              </td>
              <td style="font-size:10px;color:#64748b;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $v->anti_cheat_note ?? '-' }}">
                {{ $v->anti_cheat_note ?? '-' }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        </div>
        @else
        <div class="empty-state">Belum ada pelanggaran terdeteksi.</div>
        @endif
      </div> {{-- end cheatDashboard --}}

  <script>
    // Toggle logic for recruitment dashboard (participants <-> cheat)
    document.addEventListener('DOMContentLoaded', function() {
      var btnParticipants = document.getElementById('btnParticipants');
      var btnCheat = document.getElementById('btnCheat');
      var participants = document.getElementById('participantsDashboard');
      var cheat = document.getElementById('cheatDashboard');

      function setActiveButton(activeBtn, inactiveBtn) {
        activeBtn.classList.add('active');
        activeBtn.setAttribute('aria-pressed', 'true');
        activeBtn.style.background = '#003e6f';
        activeBtn.style.color = '#fff';
        activeBtn.style.border = '1px solid #003e6f';

        inactiveBtn.classList.remove('active');
        inactiveBtn.setAttribute('aria-pressed', 'false');
        inactiveBtn.style.background = '#fff';
        inactiveBtn.style.color = '#475569';
        inactiveBtn.style.border = '1px solid #e2e8f0';
      }

      function showParticipants(){ participants.style.display = ''; cheat.style.display = 'none'; setActiveButton(btnParticipants, btnCheat); }
      function showCheat(){ participants.style.display = 'none'; cheat.style.display = ''; setActiveButton(btnCheat, btnParticipants); }

      btnParticipants.addEventListener('click', showParticipants);
      btnCheat.addEventListener('click', showCheat);
      // default
      showParticipants();
    });
  </script>

      <div class="copyright">copyright &copy;2026 Shindengen HR Internal Team</div>
    </div>
  </div>

  <script>
    const chartColors = {
      blue: 'rgba(59,130,246,0.8)',
      blueBg: 'rgba(59,130,246,0.1)',
      purple: 'rgba(124,58,237,0.8)',
      purpleBg: 'rgba(124,58,237,0.1)',
      green: 'rgba(16,185,129,0.8)',
      amber: 'rgba(245,158,11,0.8)',
      rose: 'rgba(244,63,94,0.8)',
      roseBg: 'rgba(244,63,94,0.1)',
      cyan: 'rgba(6,182,212,0.8)',
      slate: 'rgba(100,116,139,0.8)',
      red: 'rgba(220,38,38,0.8)',
      redBg: 'rgba(220,38,38,0.1)',
      orange: 'rgba(234,88,12,0.8)',
      indigo: 'rgba(79,70,229,0.8)',
    };
    const defaultFont = { family:"'Inter',sans-serif", size:10, weight:'500' };
    Chart.defaults.font = defaultFont;
    Chart.defaults.plugins.legend.labels.boxWidth = 10;
    Chart.defaults.plugins.legend.labels.padding = 8;
    Chart.defaults.plugins.legend.labels.font = { size:9, family:"'Inter',sans-serif" };

    // 1) Monthly trend (line)
    new Chart(document.getElementById('chartMonthly'), {
      type: 'line',
      data: {
        labels: {!! json_encode($monthLabels) !!},
        datasets: [{
          label: 'Peserta',
          data: {!! json_encode($monthlyData) !!},
          borderColor: chartColors.blue,
          backgroundColor: chartColors.blueBg,
          fill: true,
          tension: 0.3,
          pointRadius: 3,
          pointHoverRadius: 5,
          borderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9' } },
          x: { ticks: { font: { size: 9 } }, grid: { display: false } }
        }
      }
    });

    // 2) By Position (doughnut)
    new Chart(document.getElementById('chartPosition'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($positionLabels) !!},
        datasets: [{
          data: {!! json_encode($positionData) !!},
          backgroundColor: [chartColors.blue, chartColors.purple, chartColors.green, chartColors.amber, chartColors.rose, chartColors.cyan],
          borderWidth: 1,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { position: 'right', labels: { font: { size: 10 }, padding: 6, boxWidth: 10 } }
        }
      }
    });

    // 3) Per Bank Soal (horizontal bar)
    new Chart(document.getElementById('chartPerBank'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($bankLabels) !!},
        datasets: [{
          label: 'Peserta',
          data: {!! json_encode($bankData) !!},
          backgroundColor: chartColors.blue,
          borderRadius: 4,
          barThickness: 18,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9' } },
          y: { ticks: { font: { size: 9 } }, grid: { display: false } }
        }
      }
    });

    // 4) Completion (pie)
    new Chart(document.getElementById('chartCompletion'), {
      type: 'pie',
      data: {
        labels: ['Selesai', 'Belum Selesai'],
        datasets: [{
          data: [{{ $completedParticipants }}, {{ $totalParticipants - $completedParticipants }}],
          backgroundColor: [chartColors.green, chartColors.amber],
          borderWidth: 1,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8, boxWidth: 10 } }
        }
      }
    });

    // ===== CHEAT ANALYTICS CHARTS =====

    // 5) Distribusi Level Kecurangan (doughnut)
    new Chart(document.getElementById('chartCheatLevel'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($cheatLevelLabels) !!},
        datasets: [{
          data: {!! json_encode($cheatLevelData) !!},
          backgroundColor: [chartColors.green, chartColors.amber, chartColors.orange, chartColors.red],
          borderWidth: 2,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '55%',
        plugins: {
          legend: { position: 'right', labels: { font: { size: 10 }, padding: 6, boxWidth: 12 } },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                var pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                return ctx.label + ': ' + ctx.raw + ' (' + pct + '%)';
              }
            }
          }
        }
      }
    });

    // 6) Jenis Pelanggaran (horizontal bar)
    new Chart(document.getElementById('chartViolationType'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($violationTypeLabels) !!},
        datasets: [{
          label: 'Jumlah',
          data: {!! json_encode($violationTypeData) !!},
          backgroundColor: [
            chartColors.red, chartColors.rose, chartColors.orange, chartColors.amber,
            chartColors.purple, chartColors.indigo, chartColors.cyan, chartColors.blue,
            chartColors.slate, chartColors.green
          ],
          borderRadius: 4,
          barThickness: 16,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9' } },
          y: { ticks: { font: { size: 9 } }, grid: { display: false } }
        }
      }
    });

    // 7) Tren Kecurangan per Bulan (stacked bar + line)
    new Chart(document.getElementById('chartCheatTrend'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($cheatTrendLabels) !!},
        datasets: [
          {
            label: 'Total Peserta',
            data: {!! json_encode($cheatTrendTotal) !!},
            backgroundColor: chartColors.blueBg,
            borderColor: chartColors.blue,
            borderWidth: 1,
            borderRadius: 4,
            order: 2,
          },
          {
            label: 'Peserta Curang',
            data: {!! json_encode($cheatTrendCheaters) !!},
            backgroundColor: chartColors.redBg,
            borderColor: chartColors.red,
            borderWidth: 2,
            type: 'line',
            tension: 0.3,
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            order: 1,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top', labels: { font: { size: 9 }, padding: 8, boxWidth: 10 } }
        },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9' } },
          x: { ticks: { font: { size: 9 } }, grid: { display: false } }
        }
      }
    });

    // 8) Korelasi Pelanggaran vs Skor (scatter)
    new Chart(document.getElementById('chartScatter'), {
      type: 'scatter',
      data: {
        datasets: [{
          label: 'Peserta',
          data: {!! json_encode($scatterData) !!},
          backgroundColor: chartColors.rose,
          pointRadius: 5,
          pointHoverRadius: 7,
          borderColor: 'rgba(244,63,94,0.4)',
          borderWidth: 1,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                return 'Pelanggaran: ' + ctx.raw.x + ', Skor: ' + ctx.raw.y;
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            title: { display: true, text: 'Jumlah Pelanggaran', font: { size: 10 } },
            ticks: { stepSize: 1, font: { size: 9 } },
            grid: { color: '#f1f5f9' }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Skor', font: { size: 10 } },
            ticks: { font: { size: 9 } },
            grid: { color: '#f1f5f9' }
          }
        }
      }
    });
  </script>
</body>
</html>
