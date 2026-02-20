<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard</title>
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

    /* Summary Cards - Compact */
    .summary-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; margin-bottom:16px; }
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
    .sc-icon-wrap.red { background:#fef2f2; }
    .sc-icon-wrap.amber { background:#fffbeb; }
    .sc-icon-wrap.green { background:#f0fdf4; }
    .sc-info { min-width:0; }
    .sc-value { font-size:20px; font-weight:700; color:#0f172a; line-height:1.1; letter-spacing:-0.5px; }
    .sc-label { font-size:10px; color:#94a3b8; font-weight:500; text-transform:uppercase; letter-spacing:0.4px; margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    /* Quick Links - Pill style */
    .quick-links { display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap; }
    .quick-link {
      background:#fff; border:1px solid #e2e8f0; border-radius:20px;
      padding:5px 12px; text-decoration:none; color:#475569;
      font-size:11px; font-weight:500; transition:all 0.15s;
      display:inline-flex; align-items:center; gap:4px;
    }
    .quick-link:hover { border-color:#003e6f; color:#003e6f; background:#f0f7ff; }
    .quick-link .ql-icon { font-size:12px; }

    /* Charts - Compact */
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
    .chart-wrap.lg { height:220px; }

    /* Recent SP Table - Compact */
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
    .sp-badge { padding:2px 6px; border-radius:3px; font-size:9px; font-weight:700; display:inline-block; letter-spacing:0.3px; }
    .sp-1 { background:#fef9c3; color:#854d0e; }
    .sp-2 { background:#ffedd5; color:#9a3412; }
    .sp-3 { background:#fee2e2; color:#991b1b; }
    .status-pill {
      display:inline-flex; align-items:center; gap:4px;
      font-size:10px; font-weight:500; padding:2px 8px; border-radius:10px;
    }
    .status-pill.pending { background:#fffbeb; color:#b45309; }
    .status-pill.pending_hr { background:#eff6ff; color:#1d4ed8; }
    .status-pill.approved { background:#f0fdf4; color:#15803d; }
    .status-pill .dot { width:5px; height:5px; border-radius:50%; }
    .status-pill.pending .dot { background:#f59e0b; }
    .status-pill.pending_hr .dot { background:#3b82f6; }
    .status-pill.approved .dot { background:#10b981; }

    .copyright { font-size:10px; color:#cbd5e1; text-align:center; margin-top:12px; padding-top:10px; border-top:1px solid #f1f5f9; letter-spacing:0.2px; }
    .empty-state { text-align:center; color:#cbd5e1; padding:24px; font-size:11px; }

    @media (max-width:1400px) {
      .summary-grid { grid-template-columns:repeat(3,1fr); }
      .row-3 { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:1100px) {
      .row-2 { grid-template-columns:1fr; }
      .row-3 { grid-template-columns:1fr; }
      .summary-grid { grid-template-columns:repeat(2,1fr); }
    }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  @include('layouts.sidebar')

  <div class="main">
    <div class="topbar">
      <div>
        <h1>Dashboard</h1>
        <span class="greeting">Selamat datang, {{ Auth::user()->name }}</span>
      </div>
      <div class="date-label">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    <div class="content">
      <div style="display:flex;gap:8px;margin-bottom:12px;align-items:center;">
        <div style="flex:1;">
            <div id="dashboardToggle" style="display:inline-flex;gap:8px;">
            <button type="button" id="btnEmp" class="quick-link" style="background:#003e6f;color:#fff;">Data Karyawan</button>
            <button type="button" id="btnSP" class="quick-link" style="background:#fff;color:#475569;border:1px solid #e2e8f0;">Data Surat Peringatan</button>
          </div>
        </div>
      </div>

      <style>
        .quick-link {
          transition: background-color 220ms ease, color 220ms ease, transform 200ms ease, box-shadow 220ms ease;
          will-change: background-color, transform;
          border-radius:20px;
        }
        .quick-link:hover { transform: translateY(-1px); }
        .quick-link.active {
          background:#003e6f;color:#fff;border-color:#003e6f;
          box-shadow: 0 8px 20px rgba(3,78,111,0.16);
          transform: translateY(-3px) scale(1.02);
        }
      </style>

      {{-- Employee Dashboard --}}
      <div id="empDashboard">
      {{-- ===== SUMMARY CARDS ===== --}}
      <div class="summary-grid">
        <div class="summary-card">
          <div class="sc-icon-wrap blue">👥</div>
          <div class="sc-info">
            <div class="sc-value">{{ $totalEmployees }}</div>
            <div class="sc-label">Karyawan</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap purple">📋</div>
          <div class="sc-info">
            <div class="sc-value">{{ $totalPsikotest }}</div>
            <div class="sc-label">Bank Soal</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap red">⚠️</div>
          <div class="sc-info">
            <div class="sc-value">{{ $totalSP }}</div>
            <div class="sc-label">Total SP</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap amber">⏳</div>
          <div class="sc-info">
            <div class="sc-value">{{ $spPending + $spPendingHR }}</div>
            <div class="sc-label">Pending</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap green">✅</div>
          <div class="sc-info">
            <div class="sc-value">{{ $spApproved }}</div>
            <div class="sc-label">Approved</div>
          </div>
        </div>
      </div>

      {{-- ===== QUICK LINKS ===== --}}
      <div class="quick-links">
        <a href="{{ route('banks.index') }}" class="quick-link"><span class="ql-icon">📋</span> Psikotest</a>
        <a href="{{ route('employees.index') }}" class="quick-link"><span class="ql-icon">👥</span> Karyawan</a>
        <a href="{{ route('warning-letters.index') }}" class="quick-link"><span class="ql-icon">⚠️</span> Surat Peringatan</a>
        <a href="{{ route('warning-letters.create') }}" class="quick-link"><span class="ql-icon">➕</span> Buat SP</a>
        <a href="{{ route('warning-letters.export') }}" class="quick-link"><span class="ql-icon">📥</span> Export Excel</a>
      </div>

      {{-- ===== SP MONTHLY TREND (FULL WIDTH) --}}
      {{-- Employee detail charts (dept / pendidikan / status) inserted here so toggling hides/shows all employee visuals --}}

      <div class="row-3">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🏢</span> Karyawan per Departemen</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="empDeptChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🎓</span> Karyawan per Pendidikan</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="empPendidikanChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📝</span> Status Karyawan</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="empStatusKaryawanChart"></canvas>
          </div>
        </div>
      </div>

      <div class="row-2" style="margin-top:12px;">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🔖</span> Karyawan per Status (Aktif/Tidak)</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="empStatusChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🏆</span> Top Departemen (5)</h3>
          </div>
          <div style="padding:10px;">
            @if($employeesByDept->count() > 0)
              <ol style="margin:0 0 0 16px;padding:0;font-size:13px;color:#374151;">
                @foreach($employeesByDept->take(5) as $dept => $count)
                  <li style="margin-bottom:6px;"><strong style="color:#0f172a">{{ $dept }}</strong> — {{ $count }} karyawan</li>
                @endforeach
              </ol>
            @else
              <div class="empty-state">Belum ada data departemen.</div>
            @endif
          </div>
        </div>
      </div>

      </div> {{-- end empDashboard --}}

      {{-- SP Dashboard --}}
      <div id="spDashboard" style="display:none;">
      <div class="row-full">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📊</span> Tren SP per Bulan</h3>
            <span class="card-badge">12 Bulan</span>
          </div>
          <div class="chart-wrap lg">
            <canvas id="spMonthlyChart"></canvas>
          </div>
        </div>
      </div>

      {{-- SP Summary Cards (quick KPIs for SP dashboard) --}}
      <div class="summary-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:12px;">
        <div class="summary-card">
          <div class="sc-icon-wrap amber">⚠️</div>
          <div class="sc-info">
            <div class="sc-value">{{ $totalSP }}</div>
            <div class="sc-label">Total SP</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap green">📈</div>
          <div class="sc-info">
            <div id="spAvgPerMonth" class="sc-value">-</div>
            <div class="sc-label">Rata-rata / Bulan (12 bln)</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="sc-icon-wrap blue">📅</div>
          <div class="sc-info">
            <div class="sc-value">{{ collect($sp1Monthly)->last() + collect($sp2Monthly)->last() + collect($sp3Monthly)->last() }}</div>
            <div class="sc-label">SP Bulan Ini</div>
          </div>
        </div>
        @php
          $topDept = $spByDept->sortDesc();
          $topDeptName = $topDept->keys()->first() ?? '-';
          $topDeptCount = $topDept->first() ?? 0;
        @endphp
        <div class="summary-card">
          <div class="sc-icon-wrap purple">🏢</div>
          <div class="sc-info">
            <div class="sc-value">{{ $topDeptCount }}</div>
            <div class="sc-label">Dept Paling Banyak SP ({{ $topDeptName }})</div>
          </div>
        </div>
      </div>

      {{-- ===== SP CHARTS ROW ===== --}}
      <div class="row-3">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">🏢</span> SP per Departemen</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="spDeptChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📌</span> Level SP</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="spLevelChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📋</span> Status SP</h3>
          </div>
          <div class="chart-wrap sm">
            <canvas id="spStatusChart"></canvas>
          </div>
        </div>
      </div>

      {{-- SP extra charts moved here: (only single copy kept above) --}}

      <div class="row-2">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📄</span> SP Terbaru</h3>
            <span class="card-badge">5 Terakhir</span>
          </div>
          @if($recentSP->count() > 0)
          <div style="overflow-x:auto;">
          <table class="recent-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Dept</th>
                <th>SP</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentSP as $sp)
              <tr>
                <td>{{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d/m/Y') : '-' }}</td>
                <td style="font-weight:500;color:#0f172a;">{{ $sp->nama }}</td>
                <td>{{ $sp->departemen }}</td>
                <td><span class="sp-badge sp-{{ $sp->sp_level }}">SP-{{ $sp->sp_level }}</span></td>
                <td>
                  <span class="status-pill {{ $sp->status }}">
                    <span class="dot"></span>
                    @if($sp->status === 'approved') Approved
                    @elseif($sp->status === 'pending_hr') HR
                    @else Pending
                    @endif
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          </div>
          @else
          <div class="empty-state">Belum ada data surat peringatan.</div>
          @endif
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><span class="icon">📊</span> Statistik SP Ringkas</h3>
          </div>
          <div style="padding:12px;">
            <div style="font-size:13px;color:#334155;font-weight:600;margin-bottom:8px;">Total SP: {{ $totalSP }}</div>
            <div>Pending: {{ $spPending + $spPendingHR }} • Approved: {{ $spApproved }}</div>
          </div>
        </div>
      </div>

      </div> {{-- end spDashboard --}}

      {{-- ===== EMPLOYEE DETAIL CHARTS ===== (moved into empDashboard) --}}

      <div class="copyright">
        &copy; 2026 Shindengen HR Internal Team
      </div>
    </div>
  </div>

  <script>
    // ===== CHART DEFAULTS =====
    Chart.defaults.font.family = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif";
    Chart.defaults.font.size = 10;
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.plugins.legend.labels.padding = 10;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    const palette = ['#003e6f','#0369a1','#0891b2','#10b981','#059669','#7c3aed','#a855f7','#ec4899','#f43f5e','#f59e0b','#eab308','#84cc16'];

    // ===== 1. SP MONTHLY TREND =====
    new Chart(document.getElementById('spMonthlyChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($monthLabels) !!},
        datasets: [
          { label: 'SP-1', data: {!! json_encode($sp1Monthly) !!}, backgroundColor: '#fbbf24', borderRadius: 3, barPercentage: 0.7, stack: 'levels' },
          { label: 'SP-2', data: {!! json_encode($sp2Monthly) !!}, backgroundColor: '#fb923c', borderRadius: 3, barPercentage: 0.7, stack: 'levels' },
          { label: 'SP-3', data: {!! json_encode($sp3Monthly) !!}, backgroundColor: '#f87171', borderRadius: 3, barPercentage: 0.7, stack: 'levels' }
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { font: { size: 10, weight: 500 }, pointStyle: 'rectRounded', padding: 12 } } },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 9 } }, stacked: true },
          y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9', drawBorder: false }, stacked: true }
        }
      }
    });

    // compute & show average SP per month (12 months)
    (function(){
      try{
        var a = {!! json_encode($sp1Monthly) !!};
        var b = {!! json_encode($sp2Monthly) !!};
        var c = {!! json_encode($sp3Monthly) !!};
        var total = 0;
        for(var i=0;i<a.length;i++){ total += (a[i]||0) + (b[i]||0) + (c[i]||0); }
        var avg = Math.round((total / (a.length||1)) * 10) / 10;
        var el = document.getElementById('spAvgPerMonth'); if(el) el.innerText = avg;
      }catch(e){ console.error(e); }
    })();

    // ===== 2. SP PER DEPARTEMEN =====
    new Chart(document.getElementById('spDeptChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($spByDept->keys()) !!},
        datasets: [{
          data: {!! json_encode($spByDept->values()) !!},
          backgroundColor: palette.slice(0, {!! $spByDept->count() !!}),
          borderRadius: 3, barPercentage: 0.6
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9', drawBorder: false } },
          y: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
      }
    });

    // ===== 3. SP LEVEL DISTRIBUTION =====
    new Chart(document.getElementById('spLevelChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($spByLevel->keys()->map(function($k){ return 'SP-'.$k; })) !!},
        datasets: [{
          data: {!! json_encode($spByLevel->values()) !!},
          backgroundColor: ['#fbbf24','#fb923c','#f87171'],
          borderWidth: 2, borderColor: '#fff', hoverOffset: 4
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 10 } } }
      }
    });

    // ===== 4. SP STATUS =====
    new Chart(document.getElementById('spStatusChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($spByStatus->keys()) !!},
        datasets: [{
          data: {!! json_encode($spByStatus->values()) !!},
          backgroundColor: ['#fbbf24','#60a5fa','#34d399'],
          borderWidth: 2, borderColor: '#fff', hoverOffset: 4
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 10 } } }
      }
    });

    // ===== 5. KARYAWAN PER DEPARTEMEN =====
    new Chart(document.getElementById('empDeptChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($employeesByDept->keys()) !!},
        datasets: [{
          data: {!! json_encode($employeesByDept->values()) !!},
          backgroundColor: palette.slice(0, {!! $employeesByDept->count() !!}),
          borderRadius: 3, barPercentage: 0.6
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f1f5f9', drawBorder: false } },
          y: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
      }
    });

    // ===== 5b. KARYAWAN PER STATUS (AKTIF/TIDAK) =====
    new Chart(document.getElementById('empStatusChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($employeesByStatus->keys()) !!},
        datasets: [{
          data: {!! json_encode($employeesByStatus->values()) !!},
          backgroundColor: ['#10b981','#f59e0b','#ef4444','#60a5fa','#9ca3af'],
          borderWidth: 2, borderColor: '#fff', hoverOffset: 6
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '50%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8 } } }
      }
    });

    // ===== UI Toggle between dashboards =====
    document.addEventListener('DOMContentLoaded', function() {
      var btnEmp = document.getElementById('btnEmp');
      var btnSP = document.getElementById('btnSP');
      var emp = document.getElementById('empDashboard');
      var sp = document.getElementById('spDashboard');
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

      function showEmp() {
        emp.style.display = '';
        sp.style.display = 'none';
        setActiveButton(btnEmp, btnSP);
      }
      function showSP() {
        emp.style.display = 'none';
        sp.style.display = '';
        setActiveButton(btnSP, btnEmp);
      }

      btnEmp.addEventListener('click', showEmp);
      btnSP.addEventListener('click', showSP);
      // default
      showEmp();
    });

    // ===== 6. KARYAWAN PER PENDIDIKAN =====
    new Chart(document.getElementById('empPendidikanChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($employeesByPendidikan->keys()) !!},
        datasets: [{
          data: {!! json_encode($employeesByPendidikan->values()) !!},
          backgroundColor: palette.slice(0, {!! $employeesByPendidikan->count() !!}),
          borderWidth: 2, borderColor: '#fff', hoverOffset: 4
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '55%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, padding: 8 } } }
      }
    });

    // ===== 7. STATUS KARYAWAN =====
    new Chart(document.getElementById('empStatusKaryawanChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($employeesByStatusKaryawan->keys()) !!},
        datasets: [{
          data: {!! json_encode($employeesByStatusKaryawan->values()) !!},
          backgroundColor: ['#003e6f','#0891b2','#10b981','#7c3aed','#f59e0b','#ef4444'],
          borderWidth: 2, borderColor: '#fff', hoverOffset: 4
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '55%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, padding: 8 } } }
      }
    });
  </script>
</body>
</html>

