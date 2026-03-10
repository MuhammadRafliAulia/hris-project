<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Hasil - {{ $bank->title }}</title>
 <style>
 body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; padding:0; }
 .layout { display:flex; min-height:100vh; }
 .sidebar { width:280px; min-width:280px; flex-shrink:0; }
 .main { flex:1; padding:24px; min-width:0; overflow-x:auto; }
 .header { margin-bottom:24px; }
 .header a { color:#0f172a; text-decoration:none; font-size:14px; }
 .header a:hover { text-decoration:underline; }
 h1 { font-size:22px; color:#0f172a; margin:8px 0 0 0; }
 h2 { font-size:14px; color:#64748b; margin:4px 0 0 0; font-weight:400; }
 .card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:20px; margin-bottom:16px; }
 .stats { display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
 .stat-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:16px 20px; flex:1; min-width:140px; }
 .stat-card .number { font-size:24px; font-weight:700; color:#003e6f; }
 .stat-card .label { font-size:12px; color:#64748b; margin-top:4px; }
 table { width:100%; border-collapse:collapse; }
 th, td { padding:12px; text-align:left; border-bottom:1px solid #e2e8f0; font-size:13px; }
 th { background:#f1f5f9; color:#334155; font-weight:600; position:sticky; top:0; }
 tr:last-child td { border-bottom:none; }
 tr:hover td { background:#f8fafc; }
 .score-good { background:#d1fae5; color:#065f46; padding:3px 8px; border-radius:4px; font-size:12px; }
 .score-ok { background:#fef08a; color:#713f12; padding:3px 8px; border-radius:4px; font-size:12px; }
 .score-poor { background:#fecaca; color:#991b1b; padding:3px 8px; border-radius:4px; font-size:12px; }
 .empty { text-align:center; color:#64748b; padding:40px; }
 .btn { color:#fff; border:none; padding:8px 14px; border-radius:6px; font-size:13px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
 .btn-primary { background:#003e6f; }
 .btn-primary:hover { background:#002a4f; }
 .btn-pdf { background:#dc2626; }
 .btn-pdf:hover { background:#b91c1c; }
 .btn-success { background:#10b981; }
 .btn-success:hover { background:#059669; }
 .btn-warning { background:#f59e0b; }
 .btn-warning:hover { background:#d97706; }
 .actions { display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-bottom:20px; }
 .success-msg { background:#d1fae5; color:#065f46; padding:12px; border-radius:6px; margin-bottom:16px; font-size:13px; }
 .filter-form { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; margin-bottom:20px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:16px 20px; }
 .filter-form .field { display:flex; flex-direction:column; gap:4px; }
 .filter-form label { font-size:12px; color:#64748b; font-weight:600; }
 .filter-form input, .filter-form select { padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px; color:#0f172a; }
 .filter-form .btn-filter { background:#003e6f; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-size:13px; cursor:pointer; height:fit-content; }
 .filter-form .btn-filter:hover { background:#002a4f; }
 .filter-form .btn-reset { background:#64748b; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-size:13px; cursor:pointer; text-decoration:none; height:fit-content; }
 .filter-form .btn-reset:hover { background:#475569; }
 .checkbox-cell { width:40px; text-align:center; }
 .checkbox-cell input[type="checkbox"] { cursor:pointer; width:16px; height:16px; }
 .btn-danger { background:#dc2626; }
 .btn-danger:hover { background:#b91c1c; }
 .bulk-delete-actions { display:none; margin-bottom:16px; padding:16px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; align-items:center; gap:12px; }
 .bulk-delete-actions.show { display:flex; }
 .bulk-delete-info { font-size:13px; color:#64748b; flex:1; }
 .bulk-delete-info strong { color:#0f172a; }
 </style>
 <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
 <div class="layout">
 @include('layouts.sidebar')
 <div class="main">
 <div class="header">
 <a href="{{ route('banks.index') }}">&larr; Kembali ke Daftar Bank Soal</a>
 <h1>Hasil Tes</h1>
 <h2>{{ $bank->title }}</h2>
 @if(request()->hasAny(['nama', 'bulan', 'tanggal']))
 <p style="font-size:13px; color:#003e6f; margin-top:8px;">
 Filter aktif:
 @if(request('nama')) <strong>Nama:</strong> {{ request('nama') }} @endif
 @if(request('bulan')) <strong>Bulan:</strong> {{ ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][request('bulan')-1] ?? '' }} @endif
 @if(request('tanggal')) <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') }} @endif
 </p>
 @endif
 </div>

 @if(session('success'))
 <div class="success-msg">✓ {{ session('success') }}</div>
 @endif

 {{-- Statistics --}}
 @php
 $completedResponses = $responses->where('completed', true);
 $totalParticipants = $completedResponses->count();
 $avgScore = $totalParticipants > 0 ? round($completedResponses->avg('score'), 1) : 0;
 $totalQ = $questions->count();
 $scoreableQ = $questions->whereNotIn('type', ['narrative', 'survey'])->count();
 $avgPct = $scoreableQ > 0 && $totalParticipants > 0 ? round(($avgScore / $scoreableQ) * 100, 1) : 0;
 @endphp
 <div class="stats">
 <div class="stat-card">
 <div class="number">{{ $totalParticipants }}</div>
 <div class="label">Total Peserta Selesai</div>
 </div>
 <div class="stat-card">
 <div class="number">{{ $scoreableQ }}</div>
 <div class="label">Soal Dinilai{{ $totalQ > $scoreableQ ? ' (+ ' . ($totalQ - $scoreableQ) . ' narasi/survei)' : '' }}</div>
 </div>
 <div class="stat-card">
 <div class="number">{{ $avgScore }}</div>
 <div class="label">Rata-rata Skor</div>
 </div>
 <div class="stat-card">
 <div class="number">{{ $avgPct }}%</div>
 <div class="label">Rata-rata Persentase</div>
 </div>
 </div>

 {{-- Filter --}}
 <form method="GET" action="{{ route('banks.results', $bank) }}" class="filter-form">
 <div class="field">
 <label>Nama Peserta</label>
 <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama...">
 </div>
 <div class="field">
 <label>Bulan</label>
 <select name="bulan">
 <option value="">-- Semua Bulan --</option>
 @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $namaBulan)
 <option value="{{ $i + 1 }}" {{ request('bulan') == ($i + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
 @endforeach
 </select>
 </div>
 <div class="field">
 <label>Tanggal</label>
 <input type="date" name="tanggal" value="{{ request('tanggal') }}">
 </div>
 <button type="submit" class="btn-filter"> Filter</button>
 <a href="{{ route('banks.results', $bank) }}" class="btn-reset">Reset</a>
 </form>

 {{-- Results Table --}}
 <form id="bulkDeleteForm" method="POST" action="{{ route('banks.bulk-delete-responses', $bank) }}" style="margin-bottom:16px;">
 @csrf
 <div id="bulkDeleteActions" class="bulk-delete-actions">
 <span class="bulk-delete-info">
 <span id="selectedCount">0</span> peserta dipilih
 </span>
 <button type="button" class="btn btn-danger" onclick="if(confirm('Yakin hapus peserta terpilih? Data tidak dapat dikembalikan!')) { document.getElementById('bulkDeleteForm').submit(); }">
 Hapus Peserta Terpilih
 </button>
 <button type="button" class="btn" style="background:#64748b;" onclick="clearAllCheckboxes()" title="Batalkan Pemilihan">
 Batal
 </button>
 </div>

 <div class="card">
 @if($completedResponses->count() > 0)
 <div style="overflow-x:auto;">
 <table>
 <thead>
 <tr>
 <th class="checkbox-cell"><input type="checkbox" id="selectAll" title="Pilih semua peserta di halaman ini"></th>
 <th>No</th>
 <th>Nama Peserta</th>
 <th>NIK</th>
 <th>Email</th>
 <th>No. Telp</th>
 <th>Departemen</th>
 <th>Jabatan</th>
 <th>Skor</th>
 <th>Persentase</th>
 <th>Pelanggaran</th>
 <th>Durasi</th>
 <th>Waktu Selesai</th>
 <th>Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($completedResponses as $index => $resp)
 @php
 $pct = $scoreableQ > 0 ? round(($resp->score / $scoreableQ) * 100, 2) : 0;
 $duration = ($resp->started_at && $resp->completed_at)
 ? $resp->started_at->diff($resp->completed_at)->format('%H:%I:%S')
 : '-';
 @endphp
 <tr>
 <td class="checkbox-cell"><input type="checkbox" name="response_ids[]" value="{{ $resp->id }}" class="response-checkbox" data-name="{{ $resp->participant_name }}"></td>
 <td>{{ $index + 1 }}</td>
 <td><strong>{{ $resp->participant_name }}</strong></td>
 <td>{{ $resp->nik ?? '-' }}</td>
 <td>{{ $resp->participant_email ?? '-' }}</td>
 <td>{{ $resp->phone ?? '-' }}</td>
 <td>{{ $resp->department ?? '-' }}</td>
 <td>{{ $resp->position ?? '-' }}</td>
 <td><strong>{{ $resp->score }} / {{ $scoreableQ }}</strong></td>
 <td>
 @if($pct >= 70)
 <span class="score-good">{{ $pct }}%</span>
 @elseif($pct >= 50)
 <span class="score-ok">{{ $pct }}%</span>
 @else
 <span class="score-poor">{{ $pct }}%</span>
 @endif
 </td>
 <td>
 @if(($resp->violation_count ?? 0) >= 3)
 <span style="background:#dc2626;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">{{ $resp->violation_count }}x</span>
 @elseif(($resp->violation_count ?? 0) > 0)
 <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">{{ $resp->violation_count }}x</span>
 @else
 <span style="color:#94a3b8;font-size:11px;">-</span>
 @endif
 </td>
 <td>{{ $duration }}</td>
 <td>{{ $resp->completed_at ? $resp->completed_at->format('d/m/Y H:i') : '-' }}</td>
 <td>
 <a href="{{ route('banks.export-report-pdf', [$bank, $resp]) }}" class="btn btn-pdf" title="Download Laporan PDF">
 Laporan
 </a>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @else
 <div class="empty">Belum ada peserta yang menyelesaikan tes ini.</div>
 @endif
 </div>
 </form>

 {{-- KRAEPELIN RESULTS SECTION --}}
 @php
  $kraepelinSubTests = $subTests->where('type', 'kraepelin');
 @endphp
 @if($kraepelinSubTests->count() > 0 && $completedResponses->count() > 0)
 @foreach($kraepelinSubTests as $kst)
 @php $kc = $kst->kraepelin_config ?? []; @endphp
 <div class="card" style="margin-top:16px;">
 <h2 style="font-size:16px;font-weight:700;color:#5b21b6;margin:0 0 16px 0;">Hasil Kraepelin — {{ $kst->title }}</h2>

 <div style="overflow-x:auto;">
 <table>
 <thead>
 <tr>
  <th>No</th>
  <th>Nama</th>
  <th>Kecepatan</th>
  <th>Ketelitian</th>
  <th>Ketahanan</th>
  <th>Stabilitas</th>
  <th>Semangat</th>
  <th>Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($completedResponses as $kIdx => $resp)
 @php
  $kData = $resp->responses['kraepelin_' . $kst->id] ?? null;
  $cols = $kData['columns'] ?? [];
  $colCount = count($cols);
  // Metrics calculation
  $correctPerCol = array_map(fn($c) => $c['correct_count'] ?? 0, $cols);
  $attemptedPerCol = array_map(fn($c) => $c['attempted'] ?? 0, $cols);
  $totalCorrect = array_sum($correctPerCol);
  $totalAttempted = array_sum($attemptedPerCol);
  // Kecepatan: avg correct per column
  $speed = $colCount > 0 ? round(array_sum($correctPerCol) / $colCount, 1) : 0;
  // Ketelitian: accuracy %
  $accuracy = $totalAttempted > 0 ? round(($totalCorrect / $totalAttempted) * 100, 1) : 0;
  // Ketahanan: first 1/3 vs last 1/3
  $third = max(1, intval($colCount / 3));
  $firstThird = $colCount > 0 ? array_sum(array_slice($correctPerCol, 0, $third)) / $third : 0;
  $lastThird = $colCount > 0 ? array_sum(array_slice($correctPerCol, -$third)) / $third : 0;
  $endurance = $firstThird > 0 ? round(($lastThird / $firstThird) * 100, 0) : 0;
  // Stabilitas: std deviation (lower = more stable)
  $mean = $speed;
  $variance = $colCount > 0 ? array_sum(array_map(fn($v) => pow($v - $mean, 2), $correctPerCol)) / $colCount : 0;
  $stdDev = round(sqrt($variance), 1);
  // Semangat: last 1/3 avg > first 1/3 avg?
  $motivation = $lastThird > $firstThird ? 'Positif' : ($lastThird < $firstThird ? 'Menurun' : 'Stabil');
 @endphp
 <tr>
  <td>{{ $kIdx + 1 }}</td>
  <td><strong>{{ $resp->participant_name }}</strong></td>
  <td>{{ $speed }} / kolom</td>
  <td>
   @if($accuracy >= 80) <span class="score-good">{{ $accuracy }}%</span>
   @elseif($accuracy >= 60) <span class="score-ok">{{ $accuracy }}%</span>
   @else <span class="score-poor">{{ $accuracy }}%</span>
   @endif
  </td>
  <td>
   @if($endurance >= 85) <span class="score-good">{{ $endurance }}%</span>
   @elseif($endurance >= 65) <span class="score-ok">{{ $endurance }}%</span>
   @else <span class="score-poor">{{ $endurance }}%</span>
   @endif
  </td>
  <td>SD {{ $stdDev }}</td>
  <td>
   @if($motivation === 'Positif') <span class="score-good">{{ $motivation }}</span>
   @elseif($motivation === 'Stabil') <span class="score-ok">{{ $motivation }}</span>
   @else <span class="score-poor">{{ $motivation }}</span>
   @endif
  </td>
  <td>
   @if($colCount > 0)
   <button type="button" class="btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="toggleKpGraph('{{ $resp->id }}_{{ $kst->id }}')">Lihat</button>
   <a href="{{ route('banks.export-kraepelin-pdf', [$bank, $resp, $kst]) }}" class="btn btn-pdf" style="padding:4px 10px;font-size:11px;">PDF</a>
   @else - @endif
  </td>
 </tr>
 @if($colCount > 0)
 <tr id="kpGraphRow_{{ $resp->id }}_{{ $kst->id }}" style="display:none;">
  <td colspan="8" style="padding:16px;">
   <div style="max-width:100%;overflow-x:auto;">
    <canvas id="kpChart_{{ $resp->id }}_{{ $kst->id }}" height="200" data-correct="{{ json_encode($correctPerCol) }}"></canvas>
   </div>
   <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:12px;">
    <div style="background:#ede9fe;padding:8px;border-radius:6px;text-align:center;">
     <div style="font-size:18px;font-weight:700;color:#5b21b6;">{{ $speed }}</div>
     <div style="font-size:10px;color:#64748b;">Kecepatan (avg/kolom)</div>
    </div>
    <div style="background:#{{ $accuracy >= 80 ? 'd1fae5' : ($accuracy >= 60 ? 'fef08a' : 'fecaca') }};padding:8px;border-radius:6px;text-align:center;">
     <div style="font-size:18px;font-weight:700;">{{ $accuracy }}%</div>
     <div style="font-size:10px;color:#64748b;">Ketelitian</div>
    </div>
    <div style="background:#{{ $endurance >= 85 ? 'd1fae5' : ($endurance >= 65 ? 'fef08a' : 'fecaca') }};padding:8px;border-radius:6px;text-align:center;">
     <div style="font-size:18px;font-weight:700;">{{ $endurance }}%</div>
     <div style="font-size:10px;color:#64748b;">Ketahanan Kerja</div>
    </div>
    <div style="background:#e0f2fe;padding:8px;border-radius:6px;text-align:center;">
     <div style="font-size:18px;font-weight:700;color:#003e6f;">{{ $stdDev }}</div>
     <div style="font-size:10px;color:#64748b;">Stabilitas (SD)</div>
    </div>
    <div style="background:#{{ $motivation === 'Positif' ? 'd1fae5' : ($motivation === 'Stabil' ? 'fef08a' : 'fecaca') }};padding:8px;border-radius:6px;text-align:center;">
     <div style="font-size:18px;font-weight:700;">{{ $motivation }}</div>
     <div style="font-size:10px;color:#64748b;">Semangat Kerja</div>
    </div>
   </div>
  </td>
 </tr>
 @endif
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endforeach
 @endif

 {{-- DISC RESULTS SECTION --}}
 @php
  $discSubTests = $subTests->where('type', 'disc');
 @endphp
 @if($discSubTests->count() > 0 && $completedResponses->count() > 0)
 @foreach($discSubTests as $dst)
 <div class="card" style="margin-top:16px;">
 <h2 style="font-size:16px;font-weight:700;color:#0d9488;margin:0 0 16px 0;">Hasil DISC — {{ $dst->title }}</h2>

 <div style="overflow-x:auto;">
 <table>
 <thead>
 <tr>
  <th>No</th>
  <th>Nama</th>
  <th style="color:#dc2626;">D</th>
  <th style="color:#f59e0b;">I</th>
  <th style="color:#059669;">S</th>
  <th style="color:#2563eb;">C</th>
  <th>Profil</th>
  <th>Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($completedResponses as $dIdx => $resp)
 @php
  $dData = $resp->responses['disc_' . $dst->id] ?? null;
  $dScores = $dData['scores'] ?? ['D' => ['most' => 0], 'I' => ['most' => 0], 'S' => ['most' => 0], 'C' => ['most' => 0]];
  $dProfile = $dData['profile_type'] ?? '-';
  $dMost = ['D' => $dScores['D']['most'] ?? 0, 'I' => $dScores['I']['most'] ?? 0, 'S' => $dScores['S']['most'] ?? 0, 'C' => $dScores['C']['most'] ?? 0];
  $dTotal = max(array_sum($dMost), 1);
  $hasDiscData = !empty($dData['answers']);
 @endphp
 <tr>
  <td>{{ $dIdx + 1 }}</td>
  <td><strong>{{ $resp->participant_name }}</strong></td>
  <td><span style="background:#fef2f2;color:#dc2626;padding:2px 8px;border-radius:4px;font-weight:700;">{{ $dMost['D'] }}</span></td>
  <td><span style="background:#fffbeb;color:#d97706;padding:2px 8px;border-radius:4px;font-weight:700;">{{ $dMost['I'] }}</span></td>
  <td><span style="background:#ecfdf5;color:#059669;padding:2px 8px;border-radius:4px;font-weight:700;">{{ $dMost['S'] }}</span></td>
  <td><span style="background:#eff6ff;color:#2563eb;padding:2px 8px;border-radius:4px;font-weight:700;">{{ $dMost['C'] }}</span></td>
  <td><strong style="font-size:14px;">{{ $dProfile }}</strong></td>
  <td>
   @if($hasDiscData)
   <button type="button" class="btn btn-primary" style="padding:4px 10px;font-size:11px;background:#0d9488;" onclick="toggleDiscGraph('{{ $resp->id }}_{{ $dst->id }}')">Lihat</button>
   <a href="{{ route('banks.export-disc-pdf', [$bank, $resp, $dst]) }}" class="btn btn-pdf" style="padding:4px 10px;font-size:11px;">PDF</a>
   @else - @endif
  </td>
 </tr>
 @if($hasDiscData)
 <tr id="discGraphRow_{{ $resp->id }}_{{ $dst->id }}" style="display:none;">
  <td colspan="8" style="padding:16px;">
   <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">
    <div style="max-width:300px;margin:0 auto;">
     <canvas id="discChart_{{ $resp->id }}_{{ $dst->id }}" height="280"
      data-d="{{ $dMost['D'] }}" data-i="{{ $dMost['I'] }}" data-s="{{ $dMost['S'] }}" data-c="{{ $dMost['C'] }}"></canvas>
    </div>
    <div>
     <div style="font-size:14px;font-weight:700;color:#0d9488;margin-bottom:12px;">Profil: {{ $dProfile }}</div>
     <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
      <div style="background:#fef2f2;padding:10px;border-radius:8px;text-align:center;">
       <div style="font-size:22px;font-weight:800;color:#dc2626;">{{ $dMost['D'] }}</div>
       <div style="font-size:10px;color:#64748b;">Dominance</div>
       <div style="background:#e2e8f0;height:4px;border-radius:2px;margin-top:4px;"><div style="background:#dc2626;height:100%;border-radius:2px;width:{{ round(($dMost['D'] / 24) * 100) }}%"></div></div>
      </div>
      <div style="background:#fffbeb;padding:10px;border-radius:8px;text-align:center;">
       <div style="font-size:22px;font-weight:800;color:#d97706;">{{ $dMost['I'] }}</div>
       <div style="font-size:10px;color:#64748b;">Influence</div>
       <div style="background:#e2e8f0;height:4px;border-radius:2px;margin-top:4px;"><div style="background:#f59e0b;height:100%;border-radius:2px;width:{{ round(($dMost['I'] / 24) * 100) }}%"></div></div>
      </div>
      <div style="background:#ecfdf5;padding:10px;border-radius:8px;text-align:center;">
       <div style="font-size:22px;font-weight:800;color:#059669;">{{ $dMost['S'] }}</div>
       <div style="font-size:10px;color:#64748b;">Steadiness</div>
       <div style="background:#e2e8f0;height:4px;border-radius:2px;margin-top:4px;"><div style="background:#059669;height:100%;border-radius:2px;width:{{ round(($dMost['S'] / 24) * 100) }}%"></div></div>
      </div>
      <div style="background:#eff6ff;padding:10px;border-radius:8px;text-align:center;">
       <div style="font-size:22px;font-weight:800;color:#2563eb;">{{ $dMost['C'] }}</div>
       <div style="font-size:10px;color:#64748b;">Compliance</div>
       <div style="background:#e2e8f0;height:4px;border-radius:2px;margin-top:4px;"><div style="background:#2563eb;height:100%;border-radius:2px;width:{{ round(($dMost['C'] / 24) * 100) }}%"></div></div>
      </div>
     </div>
    </div>
   </div>
  </td>
 </tr>
 @endif
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endforeach
 @endif

 {{-- PAPIKOSTIK RESULTS SECTION --}}
 @php
  $papiSubTests = $subTests->where('type', 'papikostik');
  $papiDims = ['N','G','A','L','P','I','T','V','S','O','B','R','D','C','X','Z','E','K','F','W'];
  $papiDimNames = [
   'N' => 'Need to Finish', 'G' => 'Hard Worker', 'A' => 'Need to Achieve', 'L' => 'Leadership Role',
   'P' => 'Need to Control', 'I' => 'Ease in Decision', 'T' => 'Pace', 'V' => 'Vigorous',
   'S' => 'Social Extension', 'R' => 'Theoretical', 'D' => 'Interest in Detail', 'C' => 'Organized',
   'X' => 'Need for Change', 'B' => 'Need for Rules', 'O' => 'Emotional', 'Z' => 'Need for Achievement',
   'E' => 'Role of Educator', 'K' => 'Need for Closeness', 'F' => 'Need to Support', 'W' => 'Need for Belonging'
  ];
 @endphp
 @if($papiSubTests->count() > 0 && $completedResponses->count() > 0)
 @foreach($papiSubTests as $pst)
 <div class="card" style="margin-top:16px;">
 <h2 style="font-size:16px;font-weight:700;color:#7c3aed;margin:0 0 16px 0;">Hasil PAPIKOSTIK — {{ $pst->title }}</h2>

 <div style="overflow-x:auto;">
 <table>
 <thead>
 <tr>
  <th>No</th>
  <th>Nama</th>
  @foreach($papiDims as $dim)
  <th style="font-size:10px;color:#7c3aed;">{{ $dim }}</th>
  @endforeach
  <th>Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($completedResponses as $pIdx => $resp)
 @php
  $pData = $resp->responses['papikostik_' . $pst->id] ?? null;
  $pScores = $pData['scores'] ?? [];
  $hasPapiData = !empty($pData['answers']);
 @endphp
 <tr>
  <td>{{ $pIdx + 1 }}</td>
  <td><strong>{{ $resp->participant_name }}</strong></td>
  @foreach($papiDims as $dim)
  <td><span style="background:#f5f3ff;color:#7c3aed;padding:2px 6px;border-radius:4px;font-weight:700;font-size:12px;">{{ $pScores[$dim] ?? 0 }}</span></td>
  @endforeach
  <td>
   @if($hasPapiData)
   <button type="button" class="btn btn-primary" style="padding:4px 10px;font-size:11px;background:#7c3aed;" onclick="togglePapiGraph('{{ $resp->id }}_{{ $pst->id }}')">Lihat</button>
   <a href="{{ route('banks.export-papikostik-pdf', [$bank, $resp, $pst]) }}" class="btn btn-pdf" style="padding:4px 10px;font-size:11px;">PDF</a>
   @else - @endif
  </td>
 </tr>
 @if($hasPapiData)
 <tr id="papiGraphRow_{{ $resp->id }}_{{ $pst->id }}" style="display:none;">
  <td colspan="{{ 3 + count($papiDims) }}" style="padding:16px;">
   <div style="max-width:700px;margin:0 auto;">
    <canvas id="papiChart_{{ $resp->id }}_{{ $pst->id }}" height="300"
     data-scores="{{ json_encode($pScores) }}"
     data-dims="{{ json_encode($papiDims) }}"></canvas>
   </div>
  </td>
 </tr>
 @endif
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endforeach
 @endif

 {{-- Actions --}}
 <div class="actions">
 @if($completedResponses->count() > 0)
 <a href="{{ route('banks.export-excel', array_merge([$bank], request()->only(['nama', 'bulan', 'tanggal']))) }}" class="btn" style="background:#059669;"> Export Excel{{ request()->hasAny(['nama', 'bulan', 'tanggal']) ? ' (Hasil Filter)' : '' }}</a>
 @endif
 @if($bank->is_active)
 <form method="POST" action="{{ route('banks.toggle', $bank) }}" style="display:inline;">
 @csrf
 <button type="submit" class="btn btn-warning"> Tutup Link Soal</button>
 </form>
 @else
 <form method="POST" action="{{ route('banks.toggle', $bank) }}" style="display:inline;">
 @csrf
 <button type="submit" class="btn btn-success"> Buka Link Soal</button>
 </form>
 @endif
 <a href="{{ route('banks.index') }}" class="btn btn-primary">Kembali ke Daftar Bank</a>
 </div>
 </div>
 </div>
<script>
const kpCharts = {};
function toggleKpGraph(key) {
 const row = document.getElementById('kpGraphRow_' + key);
 if (!row) return;
 if (row.style.display === 'none') {
  row.style.display = '';
  if (!kpCharts[key]) renderKpChart(key);
 } else {
  row.style.display = 'none';
 }
}
function renderKpChart(key) {
 const canvas = document.getElementById('kpChart_' + key);
 if (!canvas) return;
 const dataAttr = canvas.getAttribute('data-correct');
 if (!dataAttr) return;
 const data = JSON.parse(dataAttr);
 const labels = data.map((_, i) => 'K' + (i + 1));
 kpCharts[key] = new Chart(canvas, {
  type: 'line',
  data: {
   labels: labels,
   datasets: [{
    label: 'Jawaban Benar per Kolom',
    data: data,
    borderColor: '#7c3aed',
    backgroundColor: 'rgba(124,58,237,0.1)',
    fill: true,
    tension: 0.3,
    pointRadius: 2,
    pointBackgroundColor: '#7c3aed'
   }]
  },
  options: {
   responsive: true,
   plugins: { legend: { display: false } },
   scales: {
    y: { beginAtZero: true, title: { display: true, text: 'Benar' } },
    x: { title: { display: true, text: 'Kolom' } }
   }
  }
 });
}

const discCharts = {};
function toggleDiscGraph(key) {
 const row = document.getElementById('discGraphRow_' + key);
 if (!row) return;
 if (row.style.display === 'none') {
  row.style.display = '';
  if (!discCharts[key]) renderDiscChart(key);
 } else {
  row.style.display = 'none';
 }
}
function renderDiscChart(key) {
 const canvas = document.getElementById('discChart_' + key);
 if (!canvas) return;
 const d = parseInt(canvas.getAttribute('data-d')) || 0;
 const i = parseInt(canvas.getAttribute('data-i')) || 0;
 const s = parseInt(canvas.getAttribute('data-s')) || 0;
 const c = parseInt(canvas.getAttribute('data-c')) || 0;
 discCharts[key] = new Chart(canvas, {
  type: 'radar',
  data: {
   labels: ['Dominance (D)', 'Influence (I)', 'Steadiness (S)', 'Compliance (C)'],
   datasets: [{
    label: 'Skor DISC',
    data: [d, i, s, c],
    borderColor: '#0d9488',
    backgroundColor: 'rgba(13,148,136,0.15)',
    borderWidth: 2,
    pointBackgroundColor: ['#dc2626', '#f59e0b', '#059669', '#2563eb'],
    pointBorderColor: ['#dc2626', '#f59e0b', '#059669', '#2563eb'],
    pointRadius: 5,
    pointHoverRadius: 7
   }]
  },
  options: {
   responsive: true,
   plugins: { legend: { display: false } },
   scales: {
    r: { beginAtZero: true, max: 24, ticks: { stepSize: 4, font: { size: 10 } }, pointLabels: { font: { size: 11, weight: 'bold' } } }
   }
  }
 });
}

const papiCharts = {};
function togglePapiGraph(key) {
 const row = document.getElementById('papiGraphRow_' + key);
 if (!row) return;
 if (row.style.display === 'none') {
  row.style.display = '';
  if (!papiCharts[key]) renderPapiChart(key);
 } else {
  row.style.display = 'none';
 }
}
function renderPapiChart(key) {
 const canvas = document.getElementById('papiChart_' + key);
 if (!canvas) return;
 const scores = JSON.parse(canvas.getAttribute('data-scores') || '{}');
 const dims = JSON.parse(canvas.getAttribute('data-dims') || '[]');
 const data = dims.map(d => scores[d] || 0);
 papiCharts[key] = new Chart(canvas, {
  type: 'radar',
  data: {
   labels: dims,
   datasets: [{
    label: 'Skor PAPIKOSTIK',
    data: data,
    backgroundColor: 'rgba(124,58,237,0.2)',
    borderColor: '#7c3aed',
    borderWidth: 2,
    pointBackgroundColor: '#7c3aed',
    pointBorderColor: '#fff',
    pointRadius: 4,
    pointHoverRadius: 6
   }]
  },
  options: {
   responsive: true,
   plugins: { legend: { display: false } },
   scales: {
    r: {
     beginAtZero: true,
     max: 9,
     ticks: { stepSize: 1, font: { size: 8 }, backdropColor: 'transparent' },
     pointLabels: { font: { size: 11, weight: 'bold' }, color: '#1e293b' },
     grid: { color: '#e2e8f0' },
     angleLines: { color: '#e2e8f0' }
    }
   }
  }
 });
}

// Checkbox selection handling
document.addEventListener('DOMContentLoaded', function() {
 const selectAllCheckbox = document.getElementById('selectAll');
 const responseCheckboxes = document.querySelectorAll('.response-checkbox');
 const bulkDeleteActions = document.getElementById('bulkDeleteActions');
 const selectedCountSpan = document.getElementById('selectedCount');

 // Select/Deselect all
 if (selectAllCheckbox) {
  selectAllCheckbox.addEventListener('change', function() {
   responseCheckboxes.forEach(cb => cb.checked = this.checked);
   updateBulkDeleteUI();
  });
 }

 // Update UI when individual checkboxes change
 responseCheckboxes.forEach(cb => {
  cb.addEventListener('change', function() {
   updateSelectAllCheckbox();
   updateBulkDeleteUI();
  });
 });

 function updateSelectAllCheckbox() {
  const allChecked = Array.from(responseCheckboxes).every(cb => cb.checked);
  const someChecked = Array.from(responseCheckboxes).some(cb => cb.checked);
  if (selectAllCheckbox) {
   selectAllCheckbox.checked = allChecked;
   selectAllCheckbox.indeterminate = someChecked && !allChecked;
  }
 }

 function updateBulkDeleteUI() {
  const checkedCount = Array.from(responseCheckboxes).filter(cb => cb.checked).length;
  if (checkedCount > 0) {
   bulkDeleteActions.classList.add('show');
   selectedCountSpan.textContent = checkedCount;
  } else {
   bulkDeleteActions.classList.remove('show');
  }
 }
});

function clearAllCheckboxes() {
 document.getElementById('selectAll').checked = false;
 document.querySelectorAll('.response-checkbox').forEach(cb => cb.checked = false);
 document.getElementById('bulkDeleteActions').classList.remove('show');
}
</script>
</body>
</html>
