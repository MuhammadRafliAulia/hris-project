<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>PAPIKOSTIK Report - {{ $response->participant_name }}</title>
<style>
 body { font-family: 'Helvetica', 'Arial', sans-serif; margin:0; padding:0; color:#1e293b; font-size:11px; }
 .page { padding:30px 40px; }
 .header { text-align:center; border-bottom:3px solid #7c3aed; padding-bottom:16px; margin-bottom:20px; }
 .header h1 { font-size:20px; color:#7c3aed; margin:0 0 4px 0; letter-spacing:1px; }
 .header h2 { font-size:13px; color:#64748b; margin:0; font-weight:400; }
 .info-table { width:100%; border-collapse:collapse; margin-bottom:20px; }
 .info-table td { padding:5px 10px; font-size:11px; }
 .info-table .label { color:#64748b; width:130px; }
 .info-table .value { color:#1e293b; font-weight:600; }
 .section-title { font-size:14px; font-weight:700; color:#7c3aed; margin:20px 0 12px 0; border-bottom:2px solid #ede9fe; padding-bottom:6px; }
 .cat-legend { width:100%; border-collapse:collapse; margin-bottom:16px; }
 .cat-legend td { padding:3px 6px; font-size:10px; }
 .cat-dot { width:12px; height:12px; border-radius:2px; display:inline-block; }
 .dim-table { width:100%; border-collapse:collapse; margin-bottom:16px; }
 .dim-table th { background:#7c3aed; color:#fff; padding:6px 8px; font-size:9px; text-align:left; }
 .dim-table td { padding:5px 8px; font-size:10px; border-bottom:1px solid #e2e8f0; }
 .dim-table tr:nth-child(even) { background:#faf5ff; }
 .conclusion-box { background:#ede9fe; border-left:4px solid #7c3aed; padding:14px 16px; border-radius:0 8px 8px 0; margin:16px 0; font-size:11px; line-height:1.6; color:#1e293b; }
 .job-match-box { background:#f0fdf4; border-left:4px solid #059669; padding:14px 16px; border-radius:0 8px 8px 0; margin:16px 0; }
 .job-match-box h4 { color:#059669; margin:0 0 8px 0; font-size:12px; }
 .job-match-box ul { margin:0; padding-left:18px; font-size:11px; color:#1e293b; line-height:1.6; }
 .footer { text-align:center; font-size:9px; color:#94a3b8; margin-top:20px; border-top:1px solid #e2e8f0; padding-top:10px; }
 .watermark { position: fixed; top: 10px; right: 10px; font-size: 14px; font-weight: 700; color: #dc2626; opacity: 1; z-index: 999; pointer-events: none; }
 .score-grid { display:table; width:100%; margin-bottom:16px; }
 .score-grid-row { display:table-row; }
 .score-grid-cell { display:table-cell; text-align:center; padding:6px 2px; }
 .score-dim { font-size:10px; font-weight:700; color:#7c3aed; }
 .score-val { font-size:16px; font-weight:800; color:#1e293b; }
</style>
</head>
<body>
<div class="watermark">CONFIDENTIAL</div>
<div class="page">
 <div class="header">
  <h1>LAPORAN TES PAPIKOSTIK</h1>
  <h2>PA Preference Inventory by Kostick</h2>
 </div>

 <table class="info-table">
  <tr><td class="label">Nama Peserta</td><td class="value">{{ $response->participant_name }}</td></tr>
  <tr><td class="label">NIK</td><td class="value">{{ $response->nik ?? '-' }}</td></tr>
  <tr><td class="label">Departemen</td><td class="value">{{ $response->department ?? '-' }}</td></tr>
  <tr><td class="label">Jabatan</td><td class="value">{{ $response->position ?? '-' }}</td></tr>
  <tr><td class="label">Email</td><td class="value">{{ $response->participant_email ?? '-' }}</td></tr>
  <tr><td class="label">Tanggal Tes</td><td class="value">{{ $response->completed_at ? \Carbon\Carbon::parse($response->completed_at)->format('d F Y, H:i') : '-' }}</td></tr>
  <tr><td class="label">Sub-Test</td><td class="value">{{ $subTest->title }}</td></tr>
 </table>

 @php
  $papiData = $response->responses['papikostik_' . $subTest->id] ?? null;
  $scores = $papiData['scores'] ?? [];
  $dims = ['N','G','A','L','P','I','T','V','S','O','B','R','D','C','X','Z','E','K','F','W'];
  $wheelDims = $dims;
  $dimNames = [
   'N' => 'Need to Finish Task', 'G' => 'Hard Intense Worker', 'A' => 'Need to Achieve',
   'L' => 'Leadership Role', 'P' => 'Need to Control Others', 'I' => 'Ease in Decision Making',
   'T' => 'Pace', 'V' => 'Vigorous Type', 'S' => 'Social Extension',
   'R' => 'Theoretical Type', 'D' => 'Interest in Working with Details', 'C' => 'Organized Type',
   'X' => 'Need for Change', 'B' => 'Need to Belong to Groups', 'O' => 'Need for Closeness & Affection',
   'Z' => 'Need for Achievement', 'E' => 'Role of the Educator', 'K' => 'Need for Forceful Action',
   'F' => 'Need to Support Authority', 'W' => 'Need for Rules & Supervision'
  ];
  $dimDescriptions = [
   'N' => 'Kebutuhan menyelesaikan tugas secara mandiri dan tuntas',
   'G' => 'Pekerja keras dan gigih dalam bekerja',
   'A' => 'Kecepatan dan semangat dalam bekerja',
   'L' => 'Kecenderungan mengambil peran kepemimpinan',
   'P' => 'Kebutuhan untuk mengontrol dan mempengaruhi orang lain',
   'I' => 'Kemampuan berpikir independen dalam mengambil keputusan',
   'T' => 'Kebutuhan akan kedekatan personal dalam hubungan kerja',
   'V' => 'Semangat dan energi yang aktif dan bersemangat',
   'S' => 'Kepercayaan diri dan keluasan dalam pergaulan sosial',
   'R' => 'Minat terhadap pemikiran teoritis dan analitis',
   'D' => 'Ketelitian dan perhatian dalam mengerjakan detail',
   'C' => 'Kemampuan mengorganisir dan merencanakan pekerjaan',
   'X' => 'Kebutuhan akan perubahan dan variasi dalam pekerjaan',
   'B' => 'Kebutuhan mengikuti aturan dan arahan',
   'O' => 'Empati dan sensitivitas terhadap perasaan orang lain',
   'Z' => 'Ambisi dan motivasi berprestasi tinggi',
   'E' => 'Kemampuan mempengaruhi dan membujuk orang lain',
   'K' => 'Ketekunan dan ketahanan dalam menghadapi tekanan',
   'F' => 'Kebutuhan mendukung dan menolong orang lain',
   'W' => 'Kebutuhan akan rasa aman dan lingkungan yang stabil'
  ];

  // Determine top 5 and bottom 5
  $sortedScores = [];
  foreach ($dims as $d) {
   $sortedScores[$d] = $scores[$d] ?? 0;
  }
  arsort($sortedScores);
  $topDims = array_slice(array_keys($sortedScores), 0, 5);
  asort($sortedScores);
  $bottomDims = array_slice(array_keys($sortedScores), 0, 5);

  // Job match based on top dimensions
  $jobMap = [
   'G' => 'Posisi yang membutuhkan pekerja keras dan dedikasi tinggi',
   'N' => 'Posisi yang memerlukan ketelitian dan penyelesaian tugas mandiri',
   'A' => 'Posisi dengan target dan tenggat waktu ketat',
   'L' => 'Posisi kepemimpinan, manajer, supervisor',
   'P' => 'Posisi manajerial, koordinator proyek',
   'I' => 'Posisi analis, konsultan, peneliti',
   'T' => 'Posisi customer service, HR, konselor',
   'V' => 'Posisi lapangan, sales, event organizer',
   'S' => 'Posisi public relations, marketing, komunikasi',
   'R' => 'Posisi riset, pengembangan, akademisi',
   'D' => 'Posisi quality control, auditor, akuntan',
   'C' => 'Posisi project manager, administrator, planner',
   'X' => 'Posisi kreatif, inovasi, startup',
   'B' => 'Posisi administrasi, compliance, birokrasi',
   'O' => 'Posisi konseling, kerja sosial, perawatan',
   'Z' => 'Posisi kompetitif, sales high-target, entrepreneur',
   'E' => 'Posisi trainer, pembicara, negosiator',
   'K' => 'Posisi yang membutuhkan ketahanan dan kegigihan',
   'F' => 'Posisi mentoring, coaching, support role',
   'W' => 'Posisi administrasi terstruktur, back office'
  ];

  // ===== GENERATE PAPIKOSTIK WHEEL CHART AS PNG (GD) =====
  $catDefs = [
   ['name' => 'ARAH KERJA', 'start' => 0, 'count' => 3, 'fill' => '#bbf7d0', 'stroke' => '#16a34a'],
   ['name' => 'KEPEMIMPINAN', 'start' => 3, 'count' => 2, 'fill' => '#fed7aa', 'stroke' => '#ea580c'],
   ['name' => 'AKTIVITAS', 'start' => 5, 'count' => 3, 'fill' => '#fecaca', 'stroke' => '#dc2626'],
   ['name' => 'PERGAULAN', 'start' => 8, 'count' => 3, 'fill' => '#fef9c3', 'stroke' => '#ca8a04'],
   ['name' => 'GAYA KERJA', 'start' => 11, 'count' => 4, 'fill' => '#d9f99d', 'stroke' => '#65a30d'],
   ['name' => 'SIFAT', 'start' => 15, 'count' => 3, 'fill' => '#a5f3fc', 'stroke' => '#0891b2'],
   ['name' => 'KETAATAN', 'start' => 18, 'count' => 2, 'fill' => '#bfdbfe', 'stroke' => '#2563eb'],
  ];

  $hexRgb = function($h) { $h = ltrim($h,'#'); return [hexdec(substr($h,0,2)), hexdec(substr($h,2,2)), hexdec(substr($h,4,2))]; };

  $imgSz = 500; $cx = 250; $cy = 250;
  $maxR = 160; $ringIn = 167; $ringOut = 195; $lblR = 215;
  $nDim = 20; $aStep = 360 / $nDim;

  $img = imagecreatetruecolor($imgSz, $imgSz);
  imagesavealpha($img, true);
  imagealphablending($img, true);
  imageantialias($img, true);

  $white   = imagecolorallocate($img, 255, 255, 255);
  $gLight  = imagecolorallocate($img, 221, 227, 234);
  $gBold   = imagecolorallocate($img, 165, 180, 196);
  $purple  = imagecolorallocate($img, 124, 58, 237);
  $pFill   = imagecolorallocatealpha($img, 196, 181, 253, 65);
  $dkText  = imagecolorallocate($img, 30, 41, 59);
  $mutText = imagecolorallocate($img, 148, 163, 184);
  $wh      = imagecolorallocate($img, 255, 255, 255);
  imagefill($img, 0, 0, $white);

  // Grid circles
  for ($lv = 1; $lv <= 9; $lv++) {
   $r = (int)round($lv * $maxR / 9);
   $c = ($lv % 3 === 0) ? $gBold : $gLight;
   imagesetthickness($img, ($lv % 3 === 0) ? 2 : 1);
   imagearc($img, $cx, $cy, $r * 2, $r * 2, 0, 360, $c);
  }
  imagesetthickness($img, 1);

  // Spokes
  foreach ($wheelDims as $i => $dm) {
   $ar = deg2rad(-90 + $i * $aStep);
   imageline($img, $cx, $cy, (int)round($cx + $maxR * cos($ar)), (int)round($cy + $maxR * sin($ar)), $gLight);
  }

  // Category ring arcs (polygon approximation)
  foreach ($catDefs as $cat) {
   $sd = -90 + $cat['start'] * $aStep - $aStep / 2;
   $ed = -90 + ($cat['start'] + $cat['count']) * $aStep - $aStep / 2;
   $steps = 50;
   $pts = [];
   for ($j = 0; $j <= $steps; $j++) {
    $a = deg2rad($sd + ($ed - $sd) * $j / $steps);
    $pts[] = (int)round($cx + $ringOut * cos($a));
    $pts[] = (int)round($cy + $ringOut * sin($a));
   }
   for ($j = $steps; $j >= 0; $j--) {
    $a = deg2rad($sd + ($ed - $sd) * $j / $steps);
    $pts[] = (int)round($cx + $ringIn * cos($a));
    $pts[] = (int)round($cy + $ringIn * sin($a));
   }
   $fRgb = $hexRgb($cat['fill']); $sRgb = $hexRgb($cat['stroke']);
   $fc = imagecolorallocate($img, $fRgb[0], $fRgb[1], $fRgb[2]);
   $sc = imagecolorallocate($img, $sRgb[0], $sRgb[1], $sRgb[2]);
   imagefilledpolygon($img, $pts, $fc);
   imagesetthickness($img, 2);
   imagepolygon($img, $pts, $sc);
   imagesetthickness($img, 1);
  }

  // Score polygon
  $polyPts = []; $dotXY = [];
  foreach ($wheelDims as $i => $dm) {
   $ar = deg2rad(-90 + $i * $aStep);
   $v = $scores[$dm] ?? 0;
   $sr = ($v / 9) * $maxR;
   $polyPts[] = (int)round($cx + $sr * cos($ar));
   $polyPts[] = (int)round($cy + $sr * sin($ar));
   $dotXY[] = [(int)round($cx + $sr * cos($ar)), (int)round($cy + $sr * sin($ar))];
  }
  imagefilledpolygon($img, $polyPts, $pFill);
  imagesetthickness($img, 2);
  imagepolygon($img, $polyPts, $purple);
  imagesetthickness($img, 1);

  // Score dots
  foreach ($dotXY as $d) {
   imagefilledellipse($img, $d[0], $d[1], 10, 10, $purple);
   imagefilledellipse($img, $d[0], $d[1], 5, 5, $wh);
  }

  // Dimension labels
  foreach ($wheelDims as $i => $dm) {
   $ar = deg2rad(-90 + $i * $aStep);
   $lx = (int)round($cx + $lblR * cos($ar));
   $ly = (int)round($cy + $lblR * sin($ar));
   $fw = strlen($dm) * imagefontwidth(5);
   $fh = imagefontheight(5);
   imagestring($img, 5, $lx - (int)($fw / 2), $ly - (int)($fh / 2), $dm, $dkText);
  }

  // Grid level numbers
  for ($lv = 3; $lv <= 9; $lv += 3) {
   $lvR = (int)round($lv * $maxR / 9);
   imagestring($img, 2, $cx + 5, $cy - $lvR - 7, (string)$lv, $mutText);
  }

  ob_start();
  imagepng($img);
  $pngData = ob_get_clean();
  imagedestroy($img);
  $chartBase64 = 'data:image/png;base64,' . base64_encode($pngData);
 @endphp

 <div class="section-title">Diagram PAPIKOSTIK (Psychogram)</div>

 <div style="text-align:center;margin-bottom:10px;">
  <img src="{{ $chartBase64 }}" width="380" height="380" style="display:inline-block;">
 </div>

 <!-- Category legend -->
 <table class="cat-legend">
  @foreach($catDefs as $cat)
  <tr>
   <td style="width:16px;"><div class="cat-dot" style="background:{{ $cat['fill'] }};border:1.5px solid {{ $cat['stroke'] }};"></div></td>
   <td style="font-weight:700;color:{{ $cat['stroke'] }};width:110px;">{{ $cat['name'] }}</td>
   <td style="color:#64748b;">@php echo implode(', ', array_slice($wheelDims, $cat['start'], $cat['count'])); @endphp</td>
  </tr>
  @endforeach
 </table>

 <div class="section-title">Interpretasi Dimensi</div>
 <table class="dim-table">
  <thead>
   <tr>
    <th style="width:30px;">Dim</th>
    <th style="width:180px;">Nama Dimensi</th>
    <th>Deskripsi</th>
    <th style="width:40px;">Skor</th>
    <th style="width:60px;">Level</th>
   </tr>
  </thead>
  <tbody>
  @foreach($dims as $dim)
   @php
    $val = $scores[$dim] ?? 0;
    if ($val >= 7) $level = 'Tinggi';
    elseif ($val >= 4) $level = 'Sedang';
    else $level = 'Rendah';
    $levelColor = $val >= 7 ? '#059669' : ($val >= 4 ? '#d97706' : '#dc2626');
   @endphp
   <tr>
    <td style="font-weight:700;color:#7c3aed;">{{ $dim }}</td>
    <td>{{ $dimNames[$dim] ?? $dim }}</td>
    <td>{{ $dimDescriptions[$dim] ?? '-' }}</td>
    <td style="text-align:center;font-weight:700;">{{ $val }}</td>
    <td style="color:{{ $levelColor }};font-weight:600;text-align:center;">{{ $level }}</td>
   </tr>
  @endforeach
  </tbody>
 </table>

 <div class="section-title">Kesimpulan</div>
 <div class="conclusion-box">
  @php
   $topNames = array_map(fn($d) => $d . ' (' . ($dimNames[$d] ?? '') . ')', $topDims);
   $bottomNames = array_map(fn($d) => $d . ' (' . ($dimNames[$d] ?? '') . ')', $bottomDims);
  @endphp
  <strong>{{ $response->participant_name }}</strong> menunjukkan dimensi kepribadian yang dominan pada
  <strong>{{ implode(', ', $topNames) }}</strong>.
  Hal ini mengindikasikan kecenderungan yang kuat dalam
  @foreach($topDims as $idx => $td)
   {{ strtolower($dimDescriptions[$td] ?? '') }}{{ $idx < count($topDims) - 1 ? ', ' : '.' }}
  @endforeach
  <br><br>
  Dimensi yang lebih rendah meliputi <strong>{{ implode(', ', $bottomNames) }}</strong>,
  yang dapat menjadi area pengembangan untuk meningkatkan efektivitas kerja secara menyeluruh.
 </div>

 <div class="section-title">Rekomendasi Posisi</div>
 <div class="job-match-box">
  <h4>Posisi yang Cocok Berdasarkan Profil PAPIKOSTIK:</h4>
  <ul>
   @foreach($topDims as $td)
    @if(isset($jobMap[$td]))
    <li><strong>{{ $td }}</strong>: {{ $jobMap[$td] }}</li>
    @endif
   @endforeach
  </ul>
 </div>

 <div class="footer">
  Laporan ini dihasilkan secara otomatis oleh Sistem HRIS &mdash; {{ date('d F Y, H:i') }}<br>
  PA Preference Inventory by Kostick &copy; Interpretasi berdasarkan teori kepribadian Kostick
 </div>
</div>
</body>
</html>
