<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Kraepelin - {{ $response->participant_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
    .header { background: #5b21b6; color: #fff; padding: 18px 30px; }
    .header h1 { font-size: 16px; margin-bottom: 2px; }
    .header p { font-size: 10px; opacity: 0.85; }
    .content { padding: 20px 30px; }
    .section-title { font-size: 12px; font-weight: 700; color: #5b21b6; margin: 16px 0 8px 0; padding-bottom: 4px; border-bottom: 2px solid #7c3aed; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 5px 10px; border: 1px solid #d1d5db; font-size: 10px; }
    .info-table .label { background: #f5f3ff; font-weight: 600; width: 160px; color: #5b21b6; }
    .info-table .value { color: #1e293b; }

    .metrics-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .metrics-grid td { padding: 10px; text-align: center; border: 1px solid #e2e8f0; width: 20%; }
    .metrics-grid .metric-val { font-size: 18px; font-weight: 800; }
    .metrics-grid .metric-label { font-size: 9px; color: #64748b; margin-top: 2px; }
    .metric-good { color: #065f46; background: #d1fae5; }
    .metric-warn { color: #92400e; background: #fef3c7; }
    .metric-bad { color: #991b1b; background: #fecaca; }
    .metric-purple { color: #5b21b6; background: #ede9fe; }
    .metric-blue { color: #003e6f; background: #e0f2fe; }

    .graph-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 16px; background: #fafafe; }
    .graph-title { font-size: 11px; font-weight: 700; color: #5b21b6; margin-bottom: 8px; }

    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 9px; }
    .detail-table th { background: #5b21b6; color: #fff; padding: 5px 4px; text-align: center; font-weight: 600; }
    .detail-table td { padding: 4px; border: 1px solid #d1d5db; text-align: center; }
    .detail-table tr:nth-child(even) td { background: #f8fafc; }
    .val-good { color: #065f46; font-weight: 700; }
    .val-bad { color: #991b1b; }

    .interpretation-box { background: #f5f3ff; border: 1px solid #c4b5fd; border-radius: 6px; padding: 12px 14px; margin-bottom: 16px; }
    .interpretation-box h3 { font-size: 11px; color: #5b21b6; margin-bottom: 6px; }
    .interpretation-box p { font-size: 10px; color: #374151; margin-bottom: 4px; }
    .interp-item { padding: 3px 0; }
    .interp-label { font-weight: 700; color: #5b21b6; }
    .interp-val { font-weight: 600; }

    .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
    .page-break { page-break-before: always; }
  </style>
</head>
<body>

  <div class="header">
    <h1>LAPORAN HASIL TES KRAEPELIN</h1>
    <p>{{ $bank->title }} — {{ $subTest->title }}</p>
  </div>

  <div class="content">

    {{-- Informasi Peserta --}}
    <div class="section-title">INFORMASI PESERTA</div>
    <table class="info-table">
      <tr>
        <td class="label">Nama Peserta</td>
        <td class="value">{{ $response->participant_name }}</td>
        <td class="label">Email</td>
        <td class="value">{{ $response->participant_email ?? '-' }}</td>
      </tr>
      @if($bank->target === 'calon_karyawan')
      <tr>
        <td class="label">Tempat Lahir</td>
        <td class="value">{{ $response->birth_place ?? '-' }}</td>
        <td class="label">Tanggal Lahir</td>
        <td class="value">{{ $response->birth_date ? $response->birth_date->format('d/m/Y') : '-' }}</td>
      </tr>
      @else
      <tr>
        <td class="label">NIK</td>
        <td class="value">{{ $response->nik ?? '-' }}</td>
        <td class="label">Departemen</td>
        <td class="value">{{ $response->department ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Jabatan</td>
        <td class="value">{{ $response->position ?? '-' }}</td>
        <td class="label">No. Telepon</td>
        <td class="value">{{ $response->phone ?? '-' }}</td>
      </tr>
      @endif
      <tr>
        <td class="label">Waktu Mulai</td>
        <td class="value">{{ $response->started_at ? $response->started_at->format('d/m/Y H:i:s') : '-' }}</td>
        <td class="label">Waktu Selesai</td>
        <td class="value">{{ $response->completed_at ? $response->completed_at->format('d/m/Y H:i:s') : '-' }}</td>
      </tr>
    </table>

    {{-- Metrics Summary --}}
    @php
      $kData = ($response->responses ?? [])['kraepelin_' . $subTest->id] ?? null;
      $cols = $kData['columns'] ?? [];
      $colCount = count($cols);
      $correctPerCol = array_map(fn($c) => $c['correct_count'] ?? 0, $cols);
      $attemptedPerCol = array_map(fn($c) => $c['attempted'] ?? 0, $cols);
      $totalCorrect = array_sum($correctPerCol);
      $totalAttempted = array_sum($attemptedPerCol);

      $speed = $colCount > 0 ? round($totalCorrect / $colCount, 1) : 0;
      $accuracy = $totalAttempted > 0 ? round(($totalCorrect / $totalAttempted) * 100, 1) : 0;

      $third = max(1, intval($colCount / 3));
      $firstThirdAvg = $colCount > 0 ? array_sum(array_slice($correctPerCol, 0, $third)) / $third : 0;
      $lastThirdAvg = $colCount > 0 ? array_sum(array_slice($correctPerCol, -$third)) / $third : 0;
      $endurance = $firstThirdAvg > 0 ? round(($lastThirdAvg / $firstThirdAvg) * 100, 0) : 0;

      $mean = $speed;
      $variance = $colCount > 0 ? array_sum(array_map(fn($v) => pow($v - $mean, 2), $correctPerCol)) / $colCount : 0;
      $stdDev = round(sqrt($variance), 1);

      $motivation = $lastThirdAvg > $firstThirdAvg ? 'Positif' : ($lastThirdAvg < $firstThirdAvg ? 'Menurun' : 'Stabil');

      $maxCorrect = $colCount > 0 ? max($correctPerCol) : 0;
      $minCorrect = $colCount > 0 ? min($correctPerCol) : 0;
    @endphp

    <div class="section-title">RINGKASAN METRIK PSIKOLOGI</div>
    <table class="metrics-grid">
      <tr>
        <td class="metric-purple">
          <div class="metric-val">{{ $speed }}</div>
          <div class="metric-label">Kecepatan Kerja<br>(rata-rata benar/kolom)</div>
        </td>
        <td class="{{ $accuracy >= 80 ? 'metric-good' : ($accuracy >= 60 ? 'metric-warn' : 'metric-bad') }}">
          <div class="metric-val">{{ $accuracy }}%</div>
          <div class="metric-label">Ketelitian<br>(akurasi jawaban)</div>
        </td>
        <td class="{{ $endurance >= 85 ? 'metric-good' : ($endurance >= 65 ? 'metric-warn' : 'metric-bad') }}">
          <div class="metric-val">{{ $endurance }}%</div>
          <div class="metric-label">Ketahanan Kerja<br>(1/3 akhir vs 1/3 awal)</div>
        </td>
        <td class="metric-blue">
          <div class="metric-val">{{ $stdDev }}</div>
          <div class="metric-label">Stabilitas Emosi<br>(standar deviasi)</div>
        </td>
        <td class="{{ $motivation === 'Positif' ? 'metric-good' : ($motivation === 'Stabil' ? 'metric-warn' : 'metric-bad') }}">
          <div class="metric-val">{{ $motivation }}</div>
          <div class="metric-label">Semangat Kerja<br>(tren performa)</div>
        </td>
      </tr>
    </table>

    {{-- Graph (SVG) --}}
    @if($colCount > 0)
    <div class="section-title">GRAFIK PERFORMA PER KOLOM</div>
    <div class="graph-box">
      <div class="graph-title">Jumlah Jawaban Benar per Kolom ({{ $colCount }} kolom)</div>
      @php
        $graphW = 700;
        $graphH = 180;
        $padL = 30;
        $padR = 10;
        $padT = 10;
        $padB = 25;
        $plotW = $graphW - $padL - $padR;
        $plotH = $graphH - $padT - $padB;
        $yMax = max($maxCorrect + 2, 10);
        $stepX = $colCount > 1 ? $plotW / ($colCount - 1) : $plotW;

        // Build SVG path
        $points = [];
        $areaPoints = [];
        for ($i = 0; $i < $colCount; $i++) {
          $x = $padL + ($i * $stepX);
          $y = $padT + $plotH - (($correctPerCol[$i] / $yMax) * $plotH);
          $points[] = round($x, 1) . ',' . round($y, 1);
          $areaPoints[] = round($x, 1) . ',' . round($y, 1);
        }
        $linePath = implode(' ', $points);
        // Area fill (close path to bottom)
        $areaPath = 'M' . ($padL) . ',' . ($padT + $plotH) . ' L' . implode(' L', $areaPoints) . ' L' . round($padL + ($colCount - 1) * $stepX, 1) . ',' . ($padT + $plotH) . ' Z';

        // Mean line Y
        $meanY = $padT + $plotH - (($speed / $yMax) * $plotH);
      @endphp
      <svg width="{{ $graphW }}" height="{{ $graphH }}" viewBox="0 0 {{ $graphW }} {{ $graphH }}" style="width:100%;height:auto;">
        {{-- Y-axis grid --}}
        @for($gy = 0; $gy <= $yMax; $gy += max(1, intval($yMax / 5)))
          @php $yy = $padT + $plotH - (($gy / $yMax) * $plotH); @endphp
          <line x1="{{ $padL }}" y1="{{ $yy }}" x2="{{ $graphW - $padR }}" y2="{{ $yy }}" stroke="#e2e8f0" stroke-width="0.5"/>
          <text x="{{ $padL - 4 }}" y="{{ $yy + 3 }}" text-anchor="end" font-size="8" fill="#94a3b8">{{ $gy }}</text>
        @endfor

        {{-- Area fill --}}
        <path d="{{ $areaPath }}" fill="rgba(124,58,237,0.1)" />

        {{-- Mean line --}}
        <line x1="{{ $padL }}" y1="{{ round($meanY, 1) }}" x2="{{ $graphW - $padR }}" y2="{{ round($meanY, 1) }}" stroke="#7c3aed" stroke-width="0.8" stroke-dasharray="4,3"/>
        <text x="{{ $graphW - $padR + 2 }}" y="{{ round($meanY, 1) + 3 }}" font-size="7" fill="#7c3aed">avg</text>

        {{-- Data line --}}
        <polyline points="{{ $linePath }}" fill="none" stroke="#7c3aed" stroke-width="1.5" stroke-linejoin="round"/>

        {{-- Data points --}}
        @for($i = 0; $i < $colCount; $i++)
          @php
            $px = $padL + ($i * $stepX);
            $py = $padT + $plotH - (($correctPerCol[$i] / $yMax) * $plotH);
          @endphp
          <circle cx="{{ round($px, 1) }}" cy="{{ round($py, 1) }}" r="2" fill="#7c3aed"/>
        @endfor

        {{-- X-axis labels (show every Nth) --}}
        @php $labelEvery = max(1, intval($colCount / 20)); @endphp
        @for($i = 0; $i < $colCount; $i += $labelEvery)
          @php $lx = $padL + ($i * $stepX); @endphp
          <text x="{{ round($lx, 1) }}" y="{{ $graphH - 4 }}" text-anchor="middle" font-size="7" fill="#94a3b8">{{ $i + 1 }}</text>
        @endfor
      </svg>
    </div>

    {{-- Additional graph: attempted vs correct --}}
    <div class="graph-box">
      <div class="graph-title">Perbandingan Dijawab vs Benar per Kolom</div>
      @php
        $barW = max(4, $plotW / $colCount - 1);
        $maxAttempted = $colCount > 0 ? max(max($attemptedPerCol), $maxCorrect + 1) : 10;
      @endphp
      <svg width="{{ $graphW }}" height="{{ $graphH }}" viewBox="0 0 {{ $graphW }} {{ $graphH }}" style="width:100%;height:auto;">
        @for($gy = 0; $gy <= $maxAttempted; $gy += max(1, intval($maxAttempted / 5)))
          @php $yy = $padT + $plotH - (($gy / $maxAttempted) * $plotH); @endphp
          <line x1="{{ $padL }}" y1="{{ $yy }}" x2="{{ $graphW - $padR }}" y2="{{ $yy }}" stroke="#e2e8f0" stroke-width="0.5"/>
          <text x="{{ $padL - 4 }}" y="{{ $yy + 3 }}" text-anchor="end" font-size="8" fill="#94a3b8">{{ $gy }}</text>
        @endfor

        @for($i = 0; $i < $colCount; $i++)
          @php
            $bx = $padL + ($i * ($plotW / $colCount));
            $attH = $maxAttempted > 0 ? ($attemptedPerCol[$i] / $maxAttempted) * $plotH : 0;
            $corH = $maxAttempted > 0 ? ($correctPerCol[$i] / $maxAttempted) * $plotH : 0;
          @endphp
          <rect x="{{ round($bx, 1) }}" y="{{ round($padT + $plotH - $attH, 1) }}" width="{{ round($barW * 0.45, 1) }}" height="{{ round($attH, 1) }}" fill="rgba(148,163,184,0.4)" rx="1"/>
          <rect x="{{ round($bx + $barW * 0.5, 1) }}" y="{{ round($padT + $plotH - $corH, 1) }}" width="{{ round($barW * 0.45, 1) }}" height="{{ round($corH, 1) }}" fill="rgba(124,58,237,0.7)" rx="1"/>
        @endfor

        {{-- Legend --}}
        <rect x="{{ $graphW - 130 }}" y="4" width="8" height="8" fill="rgba(148,163,184,0.4)" rx="1"/>
        <text x="{{ $graphW - 118 }}" y="11" font-size="8" fill="#64748b">Dijawab</text>
        <rect x="{{ $graphW - 72 }}" y="4" width="8" height="8" fill="rgba(124,58,237,0.7)" rx="1"/>
        <text x="{{ $graphW - 60 }}" y="11" font-size="8" fill="#5b21b6">Benar</text>
      </svg>
    </div>
    @endif

    {{-- Interpretation --}}
    <div class="section-title">INTERPRETASI HASIL</div>
    <div class="interpretation-box">
      <h3>Analisis Psikologis Tes Kraepelin</h3>
      <div class="interp-item">
        <span class="interp-label">1. Kecepatan Kerja ({{ $speed }} benar/kolom):</span>
        <span class="interp-val">
          @if($speed >= 15) Tinggi — peserta mampu bekerja dengan cepat.
          @elseif($speed >= 8) Sedang — kecepatan kerja dalam batas normal.
          @else Rendah — peserta bekerja relatif lambat.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">2. Ketelitian ({{ $accuracy }}%):</span>
        <span class="interp-val">
          @if($accuracy >= 85) Sangat Baik — peserta sangat teliti dan akurat.
          @elseif($accuracy >= 70) Baik — tingkat ketelitian memadai.
          @elseif($accuracy >= 50) Cukup — perlu peningkatan ketelitian.
          @else Kurang — banyak kesalahan dalam perhitungan.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">3. Ketahanan Kerja ({{ $endurance }}%):</span>
        <span class="interp-val">
          @if($endurance >= 90) Sangat Baik — performa konsisten hingga akhir.
          @elseif($endurance >= 75) Baik — sedikit penurunan di akhir, masih wajar.
          @elseif($endurance >= 60) Cukup — ada penurunan performa yang perlu diperhatikan.
          @else Kurang — terjadi penurunan signifikan, stamina kerja rendah.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">4. Stabilitas Emosi (SD = {{ $stdDev }}):</span>
        <span class="interp-val">
          @if($stdDev <= 2) Sangat Stabil — emosi terkendali dengan baik.
          @elseif($stdDev <= 4) Stabil — fluktuasi masih dalam batas normal.
          @elseif($stdDev <= 6) Cukup — ada fluktuasi yang perlu diperhatikan.
          @else Kurang Stabil — performa sangat fluktuatif, emosi kurang stabil.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">5. Semangat Kerja ({{ $motivation }}):</span>
        <span class="interp-val">
          @if($motivation === 'Positif') Baik — peserta menunjukkan peningkatan performa seiring waktu.
          @elseif($motivation === 'Stabil') Netral — performa awal dan akhir relatif sama.
          @else Menurun — motivasi atau konsentrasi cenderung berkurang di akhir tes.
          @endif
        </span>
      </div>
    </div>

    {{-- Per-column detail table --}}
    @if($colCount > 0)
    <div class="page-break"></div>
    <div class="section-title">DETAIL PER KOLOM</div>
    <table class="detail-table">
      <thead>
        <tr>
          <th style="width:40px;">Kolom</th>
          <th style="width:50px;">Durasi (dtk)</th>
          <th style="width:50px;">Dijawab</th>
          <th style="width:50px;">Benar</th>
          <th style="width:50px;">Salah</th>
          <th style="width:50px;">Akurasi</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cols as $ci => $colData)
        @php
          $att = $colData['attempted'] ?? 0;
          $cor = $colData['correct_count'] ?? 0;
          $wrong = $att - $cor;
          $dur = $colData['duration'] ?? '-';
          $colAcc = $att > 0 ? round(($cor / $att) * 100, 0) : 0;
          $note = '';
          if ($att === 0) $note = 'Tidak dijawab';
          elseif ($colAcc >= 90) $note = 'Sangat Baik';
          elseif ($colAcc >= 70) $note = 'Baik';
          elseif ($colAcc >= 50) $note = 'Cukup';
          else $note = 'Kurang';
        @endphp
        <tr>
          <td>{{ $ci + 1 }}</td>
          <td>{{ $dur }}</td>
          <td>{{ $att }}</td>
          <td class="{{ $cor > 0 ? 'val-good' : '' }}">{{ $cor }}</td>
          <td class="{{ $wrong > 0 ? 'val-bad' : '' }}">{{ $wrong }}</td>
          <td>{{ $colAcc }}%</td>
          <td>{{ $note }}</td>
        </tr>
        @endforeach
        <tr style="background:#f5f3ff;font-weight:700;">
          <td colspan="2">TOTAL</td>
          <td>{{ $totalAttempted }}</td>
          <td class="val-good">{{ $totalCorrect }}</td>
          <td class="val-bad">{{ $totalAttempted - $totalCorrect }}</td>
          <td>{{ $accuracy }}%</td>
          <td></td>
        </tr>
      </tbody>
    </table>
    @endif

    <div class="footer">
      Dokumen ini digenerate secara otomatis oleh Sistem Psikotest Online<br>
      Tanggal cetak: {{ now()->format('d/m/Y H:i:s') }} | copyright &copy;2026 Shindengen HR Internal Team
    </div>
  </div>

</body>
</html>
