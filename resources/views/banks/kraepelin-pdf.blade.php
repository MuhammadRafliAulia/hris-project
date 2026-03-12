<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Kraepelin - {{ $response->participant_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.45; }
    .header-bar { background: #5b21b6; color: #fff; padding: 14px 50px; }
    .header-bar h1 { font-size: 14px; font-weight: 700; margin: 0; display: inline; }
    .header-bar p { font-size: 9px; opacity: .8; margin: 2px 0 0 0; }
    .header-bar .conf { float: right; font-size: 9px; font-weight: 700; color: #e9d5ff; letter-spacing: 1px; margin-top: 2px; }
    .content { padding: 14px 50px 30px 50px; }
    .section-title { font-size: 11px; font-weight: 700; color: #5b21b6; margin: 12px 0 6px 0; padding-bottom: 3px; border-bottom: 1.5px solid #7c3aed; text-transform: uppercase; letter-spacing: 0.3px; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .info-table td { padding: 4px 8px; border: 1px solid #d1d5db; font-size: 9px; }
    .info-table .label { background: #f5f3ff; font-weight: 600; width: 130px; color: #5b21b6; }
    .metrics-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .metrics-grid td { padding: 8px 4px; text-align: center; border: 1px solid #e2e8f0; width: 20%; }
    .metrics-grid .mv { font-size: 16px; font-weight: 800; }
    .metrics-grid .ml { font-size: 8px; color: #64748b; margin-top: 1px; }
    .m-good { color: #065f46; background: #d1fae5; }
    .m-warn { color: #92400e; background: #fef3c7; }
    .m-bad  { color: #991b1b; background: #fecaca; }
    .m-purp { color: #5b21b6; background: #ede9fe; }
    .m-blue { color: #003e6f; background: #e0f2fe; }
    .graph-box { border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px; margin-bottom: 10px; background: #fafafa; text-align: center; }
    .graph-box .gt { font-size: 10px; font-weight: 700; color: #5b21b6; margin-bottom: 6px; text-align: left; }
    .interp-box { background: #f5f3ff; border: 1px solid #c4b5fd; border-radius: 5px; padding: 10px 12px; margin-bottom: 10px; }
    .interp-box h3 { font-size: 10px; color: #5b21b6; margin-bottom: 4px; }
    .interp-item { padding: 2px 0; font-size: 9px; color: #374151; line-height: 1.5; }
    .interp-label { font-weight: 700; color: #5b21b6; }
    .interp-val { font-weight: 600; }
    .detail-table { width: 100%; border-collapse: collapse; font-size: 8px; }
    .detail-table th { background: #5b21b6; color: #fff; padding: 4px 3px; text-align: center; font-weight: 600; }
    .detail-table td { padding: 3px; border: 1px solid #d1d5db; text-align: center; }
    .detail-table tr:nth-child(even) td { background: #f8fafc; }
    .vg { color: #065f46; font-weight: 700; }
    .vb { color: #991b1b; }
    .footer { text-align: center; font-size: 8px; color: #94a3b8; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; }
    .page-break { page-break-before: always; }
  </style>
</head>
<body>

  <div class="header-bar">
    <span class="conf">CONFIDENTIAL</span>
    <h1>LAPORAN HASIL TES KRAEPELIN</h1>
    <p>{{ $bank->title }} &mdash; {{ $subTest->title }}</p>
  </div>

  <div class="content">

    <div class="section-title">Informasi Peserta</div>
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
        <td class="value">{{ $response->started_at ? $response->started_at->format('d/m/Y H:i') : '-' }}</td>
        <td class="label">Waktu Selesai</td>
        <td class="value">{{ $response->completed_at ? $response->completed_at->format('d/m/Y H:i') : '-' }}</td>
      </tr>
    </table>

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

    <div class="section-title">Ringkasan Metrik</div>
    <table class="metrics-grid">
      <tr>
        <td class="m-purp"><div class="mv">{{ $speed }}</div><div class="ml">Kecepatan Kerja<br>(rata-rata benar/kolom)</div></td>
        <td class="{{ $accuracy >= 80 ? 'm-good' : ($accuracy >= 60 ? 'm-warn' : 'm-bad') }}"><div class="mv">{{ $accuracy }}%</div><div class="ml">Ketelitian<br>(akurasi)</div></td>
        <td class="{{ $endurance >= 85 ? 'm-good' : ($endurance >= 65 ? 'm-warn' : 'm-bad') }}"><div class="mv">{{ $endurance }}%</div><div class="ml">Ketahanan Kerja<br>(akhir vs awal)</div></td>
        <td class="m-blue"><div class="mv">{{ $stdDev }}</div><div class="ml">Stabilitas Emosi<br>(std deviasi)</div></td>
        <td class="{{ $motivation === 'Positif' ? 'm-good' : ($motivation === 'Stabil' ? 'm-warn' : 'm-bad') }}"><div class="mv">{{ $motivation }}</div><div class="ml">Semangat Kerja<br>(tren performa)</div></td>
      </tr>
    </table>

    {{-- GD Line Chart --}}
    @if($colCount > 0)
    <div class="section-title">Grafik Performa Per Kolom</div>
    <div class="graph-box">
      <div class="gt">Jawaban Benar per Kolom ({{ $colCount }} kolom)</div>
      @php
        $gW = 680; $gH = 180;
        $pL = 35; $pR = 12; $pT = 12; $pB = 22;
        $pW = $gW-$pL-$pR; $pH = $gH-$pT-$pB;
        $yMax = max($maxCorrect+2, 10);
        $stX = $colCount > 1 ? $pW/($colCount-1) : $pW;

        $img = imagecreatetruecolor($gW, $gH);
        imagesavealpha($img, true); imagealphablending($img, true); imageantialias($img, true);
        $w = imagecolorallocate($img, 250, 250, 254);
        $gc = imagecolorallocate($img, 226, 232, 240);
        $pc = imagecolorallocate($img, 124, 58, 237);
        $pf = imagecolorallocatealpha($img, 124, 58, 237, 100);
        $mt = imagecolorallocate($img, 148, 163, 184);
        imagefill($img, 0, 0, $w);

        $ySt = max(1, intval($yMax/5));
        for ($gy = 0; $gy <= $yMax; $gy += $ySt) {
          $yy = (int)round($pT+$pH-($gy/$yMax)*$pH);
          imageline($img, $pL, $yy, $gW-$pR, $yy, $gc);
          imagestring($img, 1, 2, $yy-5, (string)$gy, $mt);
        }

        // area
        $ap = [$pL, $pT+$pH];
        for ($i = 0; $i < $colCount; $i++) { $ap[] = (int)round($pL+$i*$stX); $ap[] = (int)round($pT+$pH-($correctPerCol[$i]/$yMax)*$pH); }
        $ap[] = (int)round($pL+($colCount-1)*$stX); $ap[] = $pT+$pH;
        imagefilledpolygon($img, $ap, $pf);

        // line
        imagesetthickness($img, 2);
        for ($i = 1; $i < $colCount; $i++) {
          $x1 = (int)round($pL+($i-1)*$stX); $y1 = (int)round($pT+$pH-($correctPerCol[$i-1]/$yMax)*$pH);
          $x2 = (int)round($pL+$i*$stX);     $y2 = (int)round($pT+$pH-($correctPerCol[$i]/$yMax)*$pH);
          imageline($img, $x1, $y1, $x2, $y2, $pc);
        }
        imagesetthickness($img, 1);

        // dots
        for ($i = 0; $i < $colCount; $i++) {
          $dx = (int)round($pL+$i*$stX); $dy = (int)round($pT+$pH-($correctPerCol[$i]/$yMax)*$pH);
          imagefilledellipse($img, $dx, $dy, 4, 4, $pc);
        }

        // mean dashed
        $mY = (int)round($pT+$pH-($speed/$yMax)*$pH);
        for ($x = $pL; $x < $gW-$pR; $x += 7) imageline($img, $x, $mY, min($x+4, $gW-$pR), $mY, $pc);
        imagestring($img, 1, $gW-$pR-18, $mY-8, 'avg', $pc);

        // x labels
        $le = max(1, intval($colCount/20));
        for ($i = 0; $i < $colCount; $i += $le) {
          $lx = (int)round($pL+$i*$stX); $s = (string)($i+1);
          imagestring($img, 1, $lx-(int)(strlen($s)*imagefontwidth(1)/2), $gH-12, $s, $mt);
        }

        ob_start(); imagepng($img); $png1 = ob_get_clean(); imagedestroy($img);
        $lineChart = 'data:image/png;base64,' . base64_encode($png1);
      @endphp
      <img src="{{ $lineChart }}" width="660" style="display:inline-block;">
    </div>

    {{-- GD Bar Chart --}}
    <div class="graph-box">
      <div class="gt">Perbandingan Dijawab vs Benar</div>
      @php
        $bW = max(4, $pW/$colCount - 1);
        $mxA = $colCount > 0 ? max(max($attemptedPerCol), $maxCorrect+1) : 10;

        $img2 = imagecreatetruecolor($gW, $gH);
        imagesavealpha($img2, true); imagealphablending($img2, true); imageantialias($img2, true);
        $w2 = imagecolorallocate($img2, 250, 250, 254);
        $gc2 = imagecolorallocate($img2, 226, 232, 240);
        $gb = imagecolorallocatealpha($img2, 148, 163, 184, 60);
        $pb = imagecolorallocatealpha($img2, 124, 58, 237, 30);
        $mt2 = imagecolorallocate($img2, 148, 163, 184);
        $tp = imagecolorallocate($img2, 91, 33, 182);
        $tg = imagecolorallocate($img2, 100, 116, 139);
        imagefill($img2, 0, 0, $w2);

        $ySt2 = max(1, intval($mxA/5));
        for ($gy = 0; $gy <= $mxA; $gy += $ySt2) {
          $yy = (int)round($pT+$pH-($gy/$mxA)*$pH);
          imageline($img2, $pL, $yy, $gW-$pR, $yy, $gc2);
          imagestring($img2, 1, 2, $yy-5, (string)$gy, $mt2);
        }

        for ($i = 0; $i < $colCount; $i++) {
          $bx = (int)round($pL+$i*($pW/$colCount));
          $aH = $mxA > 0 ? ($attemptedPerCol[$i]/$mxA)*$pH : 0;
          $cH = $mxA > 0 ? ($correctPerCol[$i]/$mxA)*$pH : 0;
          $hw = (int)round($bW*0.45);
          imagefilledrectangle($img2, $bx, (int)round($pT+$pH-$aH), $bx+$hw, $pT+$pH, $gb);
          imagefilledrectangle($img2, (int)round($bx+$bW*0.5), (int)round($pT+$pH-$cH), (int)round($bx+$bW*0.5)+$hw, $pT+$pH, $pb);
        }

        // legend
        $lx = $gW-140;
        imagefilledrectangle($img2, $lx, 4, $lx+8, 12, $gb);
        imagestring($img2, 2, $lx+12, 2, 'Dijawab', $tg);
        imagefilledrectangle($img2, $lx+65, 4, $lx+73, 12, $pb);
        imagestring($img2, 2, $lx+77, 2, 'Benar', $tp);

        ob_start(); imagepng($img2); $png2 = ob_get_clean(); imagedestroy($img2);
        $barChart = 'data:image/png;base64,' . base64_encode($png2);
      @endphp
      <img src="{{ $barChart }}" width="660" style="display:inline-block;">
    </div>
    @endif

    <div class="section-title">Interpretasi Hasil</div>
    <div class="interp-box">
      <h3>Analisis Psikologis Tes Kraepelin</h3>
      <div class="interp-item">
        <span class="interp-label">1. Kecepatan Kerja ({{ $speed }}):</span>
        <span class="interp-val">
          @if($speed >= 15) Tinggi — mampu bekerja dengan cepat.
          @elseif($speed >= 8) Sedang — kecepatan dalam batas normal.
          @else Rendah — bekerja relatif lambat.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">2. Ketelitian ({{ $accuracy }}%):</span>
        <span class="interp-val">
          @if($accuracy >= 85) Sangat Baik — sangat teliti dan akurat.
          @elseif($accuracy >= 70) Baik — ketelitian memadai.
          @elseif($accuracy >= 50) Cukup — perlu peningkatan.
          @else Kurang — banyak kesalahan.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">3. Ketahanan Kerja ({{ $endurance }}%):</span>
        <span class="interp-val">
          @if($endurance >= 90) Sangat Baik — konsisten hingga akhir.
          @elseif($endurance >= 75) Baik — sedikit penurunan, masih wajar.
          @elseif($endurance >= 60) Cukup — ada penurunan perlu diperhatikan.
          @else Kurang — penurunan signifikan, stamina rendah.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">4. Stabilitas Emosi (SD={{ $stdDev }}):</span>
        <span class="interp-val">
          @if($stdDev <= 2) Sangat Stabil — emosi terkendali baik.
          @elseif($stdDev <= 4) Stabil — fluktuasi normal.
          @elseif($stdDev <= 6) Cukup — ada fluktuasi perlu diperhatikan.
          @else Kurang Stabil — performa fluktuatif.
          @endif
        </span>
      </div>
      <div class="interp-item">
        <span class="interp-label">5. Semangat Kerja ({{ $motivation }}):</span>
        <span class="interp-val">
          @if($motivation === 'Positif') Baik — peningkatan performa seiring waktu.
          @elseif($motivation === 'Stabil') Netral — performa awal dan akhir sama.
          @else Menurun — konsentrasi berkurang di akhir tes.
          @endif
        </span>
      </div>
    </div>

    </div>
    @if($colCount > 0)
    <div class="page-break"></div>
    <div class="header-bar">
      <span class="conf">CONFIDENTIAL</span>
      <h1>DETAIL PER KOLOM &mdash; {{ $response->participant_name }}</h1>
      <p>{{ $bank->title }} &mdash; {{ $subTest->title }}</p>
    </div>
    <div class="content">

    <div class="section-title">Detail Per Kolom</div>
    <table class="detail-table">
      <thead>
        <tr>
          <th style="width:35px;">Kolom</th>
          <th style="width:45px;">Durasi</th>
          <th style="width:45px;">Dijawab</th>
          <th style="width:45px;">Benar</th>
          <th style="width:45px;">Salah</th>
          <th style="width:45px;">Akurasi</th>
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
          $colAcc = $att > 0 ? round(($cor/$att)*100, 0) : 0;
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
          <td class="{{ $cor > 0 ? 'vg' : '' }}">{{ $cor }}</td>
          <td class="{{ $wrong > 0 ? 'vb' : '' }}">{{ $wrong }}</td>
          <td>{{ $colAcc }}%</td>
          <td>{{ $note }}</td>
        </tr>
        @endforeach
        <tr style="background:#f5f3ff;font-weight:700;">
          <td colspan="2">TOTAL</td>
          <td>{{ $totalAttempted }}</td>
          <td class="vg">{{ $totalCorrect }}</td>
          <td class="vb">{{ $totalAttempted - $totalCorrect }}</td>
          <td>{{ $accuracy }}%</td>
          <td></td>
        </tr>
      </tbody>
    </table>
    @endif

    <div class="footer">
      Dokumen digenerate otomatis oleh Sistem HRIS &mdash; Tanggal cetak: {{ now()->format('d/m/Y H:i') }}
      | &copy; {{ date('Y') }} Shindengen HR Internal Team
    </div>
    <div style="font-size:7px; color:#9ca3af; line-height:1.4; padding:4px 0 0 0; text-align:center;">
      Hasil tes ini digenerate berdasarkan teori Kraepelin oleh Emil Kraepelin (1902) dalam <em>Die Arbeitskurve</em>
      dan adaptasi Pauli Test oleh Richard Pauli (1938). Metrik yang diukur meliputi kecepatan, ketelitian, ketahanan,
      stabilitas emosi, dan semangat kerja. Hasil bersifat indikatif dan dimaksudkan sebagai alat bantu asesmen,
      bukan sebagai diagnosis psikologis. Interpretasi lebih lanjut disarankan dilakukan oleh psikolog profesional berlisensi.
    </div>

  </div>
</body>
</html>
