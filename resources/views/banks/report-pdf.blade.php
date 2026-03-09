<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Rapor - {{ $response->participant_name }}</title>
<style>
 * { margin:0; padding:0; box-sizing:border-box; }
 body { font-family:'Helvetica','Arial',sans-serif; font-size:11px; color:#1a1a1a; line-height:1.5; }
 .header { background:#003e6f; color:#fff; padding:20px 30px; text-align:center; }
 .header h1 { font-size:18px; margin-bottom:2px; letter-spacing:1px; }
 .header p { font-size:10px; opacity:0.85; }
 .content { padding:20px 30px; }
 .section-title { font-size:13px; font-weight:700; color:#003e6f; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #003e6f; }
 .section-title-purple { font-size:13px; font-weight:700; color:#5b21b6; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #7c3aed; }
 .section-title-teal { font-size:13px; font-weight:700; color:#0d9488; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #14b8a6; }
 .section-title-violet { font-size:13px; font-weight:700; color:#7c3aed; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #a78bfa; }
 .info-table { width:100%; border-collapse:collapse; margin-bottom:16px; }
 .info-table td { padding:5px 10px; border:1px solid #d1d5db; font-size:10px; }
 .info-table .label { background:#f1f5f9; font-weight:600; width:140px; color:#334155; }
 .info-table .value { color:#1e293b; }
 .metrics-grid { width:100%; border-collapse:collapse; margin-bottom:14px; }
 .metrics-grid td { padding:10px; text-align:center; border:1px solid #e2e8f0; }
 .metrics-grid .m-val { font-size:18px; font-weight:800; }
 .metrics-grid .m-lbl { font-size:8px; color:#64748b; margin-top:2px; }
 .score-box { background:#f0f9ff; border:2px solid #003e6f; border-radius:6px; padding:14px; text-align:center; margin-bottom:16px; }
 .score-box .score-number { font-size:24px; font-weight:700; color:#003e6f; }
 .score-box .score-label { font-size:10px; color:#64748b; margin-top:2px; }
 .score-good { color:#065f46; } .score-ok { color:#92400e; } .score-poor { color:#991b1b; }
 .metric-good { color:#065f46; background:#d1fae5; }
 .metric-warn { color:#92400e; background:#fef3c7; }
 .metric-bad { color:#991b1b; background:#fecaca; }
 .metric-purple { color:#5b21b6; background:#ede9fe; }
 .metric-blue { color:#003e6f; background:#e0f2fe; }
 .metric-d { color:#dc2626; background:#fef2f2; }
 .metric-i { color:#d97706; background:#fffbeb; }
 .metric-s { color:#059669; background:#ecfdf5; }
 .metric-c { color:#2563eb; background:#eff6ff; }
 .profile-box { background:#f0fdfa; border:2px solid #14b8a6; border-radius:8px; padding:12px; margin-bottom:14px; text-align:center; }
 .profile-box .profile-type { font-size:24px; font-weight:800; color:#0d9488; }
 .profile-box .profile-desc { font-size:9px; color:#374151; margin-top:2px; }
 .interp-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 12px; margin-bottom:14px; font-size:10px; color:#374151; line-height:1.6; }
 .interp-box strong { color:#1e293b; }
 .cat-legend { width:100%; border-collapse:collapse; margin-bottom:12px; }
 .cat-legend td { padding:2px 5px; font-size:9px; }
 .cat-dot { width:10px; height:10px; border-radius:2px; display:inline-block; }
 .dim-table { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9px; }
 .dim-table th { background:#7c3aed; color:#fff; padding:4px 6px; text-align:left; font-weight:600; }
 .dim-table td { padding:3px 6px; border-bottom:1px solid #e2e8f0; }
 .dim-table tr:nth-child(even) { background:#faf5ff; }
 .graph-box { border:1px solid #e2e8f0; border-radius:6px; padding:10px; margin-bottom:14px; background:#fafafe; }
 .conclusion-box { background:#ede9fe; border-left:4px solid #7c3aed; padding:10px 12px; border-radius:0 6px 6px 0; margin-bottom:14px; font-size:10px; line-height:1.6; }
 .job-box { background:#f0fdf4; border-left:4px solid #059669; padding:10px 12px; border-radius:0 6px 6px 0; margin-bottom:14px; }
 .job-box h4 { color:#059669; margin:0 0 4px 0; font-size:11px; }
 .job-box ul { margin:0; padding-left:16px; font-size:10px; line-height:1.6; }
 .footer { text-align:center; font-size:9px; color:#94a3b8; margin-top:20px; padding-top:10px; border-top:1px solid #e2e8f0; }
 .page-break { page-break-before:always; }
 .no-data { color:#94a3b8; font-style:italic; font-size:10px; padding:8px 0; }
</style>
</head>
<body>

<div class="header">
 <h1>LAPORAN HASIL TES PSIKOTEST</h1>
 <p>{{ $bank->title }}</p>
</div>

<div class="content">

 {{-- ===== INFORMASI PESERTA ===== --}}
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
   <td class="value">{{ $response->started_at ? $response->started_at->format('d/m/Y H:i') : '-' }}</td>
   <td class="label">Waktu Selesai</td>
   <td class="value">{{ $response->completed_at ? $response->completed_at->format('d/m/Y H:i') : '-' }}</td>
  </tr>
  @if($response->started_at && $response->completed_at)
  <tr>
   <td class="label">Durasi Pengerjaan</td>
   <td class="value" colspan="3">{{ $response->started_at->diff($response->completed_at)->format('%H jam %I menit %S detik') }}</td>
  </tr>
  @endif
 </table>

 {{-- ===== SECTION 1: SKOR SOAL UMUM (Default) ===== --}}
 @php
  $defaultSubTests = $subTests->whereNotIn('type', ['kraepelin', 'disc', 'papikostik']);
  $defaultQuestions = collect();
  if ($defaultSubTests->count() > 0) {
   foreach ($defaultSubTests as $dst2) {
    $defaultQuestions = $defaultQuestions->merge($dst2->questions);
   }
  }
  // Also include questions not in any subtest
  $directQuestions = $bank->questions()->whereNull('sub_test_id')->orderBy('order')->get();
  $defaultQuestions = $defaultQuestions->merge($directQuestions);
  $scoreableQ = $defaultQuestions->whereNotIn('type', ['narrative', 'survey'])->count();
  $hasDefaultScore = $scoreableQ > 0;
 @endphp

 @if($hasDefaultScore)
 <div class="section-title">HASIL TES PENGETAHUAN</div>
 @php
  $scorePct = $scoreableQ > 0 ? round(($response->score / $scoreableQ) * 100, 2) : 0;
 @endphp
 <div class="score-box">
  <div class="score-number">{{ $response->score }} / {{ $scoreableQ }}</div>
  <div class="score-label">Jawaban Benar</div>
  <div style="font-size:14px;font-weight:600;margin-top:4px;" class="{{ $scorePct >= 70 ? 'score-good' : ($scorePct >= 50 ? 'score-ok' : 'score-poor') }}">
   {{ $scorePct }}% — @if($scorePct >= 70) BAIK @elseif($scorePct >= 50) CUKUP @else KURANG @endif
  </div>
 </div>
 @endif

 {{-- ===== SECTION 2: KRAEPELIN ===== --}}
 @php $kraepelinSTs = $subTests->where('type', 'kraepelin'); @endphp
 @foreach($kraepelinSTs as $kst)
 @php
  $kData = ($response->responses ?? [])['kraepelin_' . $kst->id] ?? null;
  $cols = $kData['columns'] ?? [];
  $colCount = count($cols);
 @endphp
 @if($colCount > 0)
 <div class="section-title-purple">HASIL KRAEPELIN — {{ $kst->title }}</div>
 @php
  $correctPerCol = array_map(fn($c) => $c['correct_count'] ?? 0, $cols);
  $attemptedPerCol = array_map(fn($c) => $c['attempted'] ?? 0, $cols);
  $totalCorrect = array_sum($correctPerCol);
  $totalAttempted = array_sum($attemptedPerCol);
  $speed = $colCount > 0 ? round($totalCorrect / $colCount, 1) : 0;
  $accuracy = $totalAttempted > 0 ? round(($totalCorrect / $totalAttempted) * 100, 1) : 0;
  $third = max(1, intval($colCount / 3));
  $firstThirdAvg = array_sum(array_slice($correctPerCol, 0, $third)) / $third;
  $lastThirdAvg = array_sum(array_slice($correctPerCol, -$third)) / $third;
  $endurance = $firstThirdAvg > 0 ? round(($lastThirdAvg / $firstThirdAvg) * 100, 0) : 0;
  $mean = $speed;
  $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $correctPerCol)) / $colCount;
  $stdDev = round(sqrt($variance), 1);
  $motivation = $lastThirdAvg > $firstThirdAvg ? 'Positif' : ($lastThirdAvg < $firstThirdAvg ? 'Menurun' : 'Stabil');
  $maxCorrect = max($correctPerCol);
 @endphp

 <table class="metrics-grid">
  <tr>
   <td class="metric-purple" style="width:20%;">
    <div class="m-val">{{ $speed }}</div>
    <div class="m-lbl">Kecepatan Kerja<br>(avg benar/kolom)</div>
   </td>
   <td class="{{ $accuracy >= 80 ? 'metric-good' : ($accuracy >= 60 ? 'metric-warn' : 'metric-bad') }}" style="width:20%;">
    <div class="m-val">{{ $accuracy }}%</div>
    <div class="m-lbl">Ketelitian</div>
   </td>
   <td class="{{ $endurance >= 85 ? 'metric-good' : ($endurance >= 65 ? 'metric-warn' : 'metric-bad') }}" style="width:20%;">
    <div class="m-val">{{ $endurance }}%</div>
    <div class="m-lbl">Ketahanan Kerja</div>
   </td>
   <td class="metric-blue" style="width:20%;">
    <div class="m-val">{{ $stdDev }}</div>
    <div class="m-lbl">Stabilitas (SD)</div>
   </td>
   <td class="{{ $motivation === 'Positif' ? 'metric-good' : ($motivation === 'Stabil' ? 'metric-warn' : 'metric-bad') }}" style="width:20%;">
    <div class="m-val">{{ $motivation }}</div>
    <div class="m-lbl">Semangat Kerja</div>
   </td>
  </tr>
 </table>

 {{-- Kraepelin SVG line graph --}}
 <div class="graph-box">
  @php
   $graphW = 660; $graphH = 140;
   $padL = 28; $padR = 10; $padT = 8; $padB = 20;
   $plotW = $graphW - $padL - $padR;
   $plotH = $graphH - $padT - $padB;
   $yMax = max($maxCorrect + 2, 10);
   $stepX = $colCount > 1 ? $plotW / ($colCount - 1) : $plotW;
   $points = []; $areaPoints = [];
   for ($i = 0; $i < $colCount; $i++) {
    $x = $padL + ($i * $stepX);
    $y = $padT + $plotH - (($correctPerCol[$i] / $yMax) * $plotH);
    $points[] = round($x,1).','.round($y,1);
    $areaPoints[] = round($x,1).','.round($y,1);
   }
   $linePath = implode(' ', $points);
   $areaPath = 'M'.$padL.','.($padT+$plotH).' L'.implode(' L', $areaPoints).' L'.round($padL+($colCount-1)*$stepX,1).','.($padT+$plotH).' Z';
   $meanY = $padT + $plotH - (($speed / $yMax) * $plotH);
  @endphp
  <svg width="{{ $graphW }}" height="{{ $graphH }}" viewBox="0 0 {{ $graphW }} {{ $graphH }}" style="width:100%;height:auto;">
   @for($gy = 0; $gy <= $yMax; $gy += max(1, intval($yMax / 5)))
    @php $yy = $padT + $plotH - (($gy / $yMax) * $plotH); @endphp
    <line x1="{{ $padL }}" y1="{{ $yy }}" x2="{{ $graphW - $padR }}" y2="{{ $yy }}" stroke="#e2e8f0" stroke-width="0.5"/>
    <text x="{{ $padL - 4 }}" y="{{ $yy + 3 }}" text-anchor="end" font-size="7" fill="#94a3b8">{{ $gy }}</text>
   @endfor
   <path d="{{ $areaPath }}" fill="rgba(124,58,237,0.1)"/>
   <line x1="{{ $padL }}" y1="{{ round($meanY,1) }}" x2="{{ $graphW - $padR }}" y2="{{ round($meanY,1) }}" stroke="#7c3aed" stroke-width="0.8" stroke-dasharray="4,3"/>
   <polyline points="{{ $linePath }}" fill="none" stroke="#7c3aed" stroke-width="1.5" stroke-linejoin="round"/>
   @for($i = 0; $i < $colCount; $i++)
    @php $px = $padL+($i*$stepX); $py = $padT+$plotH-(($correctPerCol[$i]/$yMax)*$plotH); @endphp
    <circle cx="{{ round($px,1) }}" cy="{{ round($py,1) }}" r="2" fill="#7c3aed"/>
   @endfor
  </svg>
 </div>

 <div class="interp-box">
  <strong>Interpretasi:</strong>
  Kecepatan kerja {{ $speed >= 15 ? 'tinggi' : ($speed >= 8 ? 'sedang' : 'rendah') }} ({{ $speed }}/kolom).
  Ketelitian {{ $accuracy >= 85 ? 'sangat baik' : ($accuracy >= 70 ? 'baik' : ($accuracy >= 50 ? 'cukup' : 'kurang')) }} ({{ $accuracy }}%).
  Ketahanan kerja {{ $endurance >= 90 ? 'sangat baik' : ($endurance >= 75 ? 'baik' : ($endurance >= 60 ? 'cukup' : 'kurang')) }} ({{ $endurance }}%).
  Stabilitas emosi {{ $stdDev <= 2 ? 'sangat stabil' : ($stdDev <= 4 ? 'stabil' : ($stdDev <= 6 ? 'cukup' : 'kurang stabil')) }} (SD={{ $stdDev }}).
  Semangat kerja {{ strtolower($motivation) }}.
 </div>
 @endif
 @endforeach

 {{-- ===== SECTION 3: DISC ===== --}}
 @php $discSTs = $subTests->where('type', 'disc'); @endphp
 @foreach($discSTs as $dst)
 @php
  $dData = ($response->responses ?? [])['disc_' . $dst->id] ?? null;
  $dScores = $dData['scores'] ?? ['D'=>['most'=>0,'least'=>0],'I'=>['most'=>0,'least'=>0],'S'=>['most'=>0,'least'=>0],'C'=>['most'=>0,'least'=>0]];
  $profileType = $dData['profile_type'] ?? '-';
  $hasDiscData = !empty($dData['answers']);
  $dM = $dScores['D']['most'] ?? 0; $iM = $dScores['I']['most'] ?? 0;
  $sM = $dScores['S']['most'] ?? 0; $cM = $dScores['C']['most'] ?? 0;
  $maxScore = max($dM, $iM, $sM, $cM, 1);
 @endphp
 @if($hasDiscData)

 <div class="section-title-teal">HASIL DISC — {{ $dst->title }}</div>

 @php
  $pNames = ['D'=>'Dominance','I'=>'Influence','S'=>'Steadiness','C'=>'Compliance'];
  $pDescs = [
   'D'=>'Tegas, berorientasi hasil, kompetitif, dan suka memimpin.',
   'I'=>'Antusias, optimis, komunikatif, dan suka bersosialisasi.',
   'S'=>'Sabar, stabil, loyal, dan kooperatif.',
   'C'=>'Teliti, analitis, sistematis, dan berorientasi kualitas.',
  ];
  $pt = strlen($profileType) > 0 ? substr($profileType,0,1) : 'D';
 @endphp

 <div class="profile-box">
  <div class="profile-type">{{ $profileType }}</div>
  <div style="font-size:11px;font-weight:600;color:#0f766e;">{{ $pNames[$pt] ?? '' }}</div>
  <div class="profile-desc">{{ $pDescs[$pt] ?? '' }}</div>
 </div>

 <table class="metrics-grid">
  <tr>
   <td class="metric-d" style="width:25%;"><div class="m-val">{{ $dM }}</div><div class="m-lbl">Dominance (D)</div></td>
   <td class="metric-i" style="width:25%;"><div class="m-val">{{ $iM }}</div><div class="m-lbl">Influence (I)</div></td>
   <td class="metric-s" style="width:25%;"><div class="m-val">{{ $sM }}</div><div class="m-lbl">Steadiness (S)</div></td>
   <td class="metric-c" style="width:25%;"><div class="m-val">{{ $cM }}</div><div class="m-lbl">Compliance (C)</div></td>
  </tr>
 </table>

 {{-- DISC radar chart as SVG --}}
 <div class="graph-box">
  @php
   $gW2=300; $gH2=240; $cx2=$gW2/2; $cy2=$gH2/2; $maxR2=90;
   $dAxes=[['l'=>'D','a'=>-90],['l'=>'I','a'=>0],['l'=>'S','a'=>90],['l'=>'C','a'=>180]];
   $dVals=[$dM,$iM,$sM,$cM];
   $dColors=['#dc2626','#f59e0b','#059669','#2563eb'];
   $dPts=[];
   foreach($dAxes as $ai=>$ax){
    $rad=deg2rad($ax['a']); $r=$maxR2*($dVals[$ai]/max($maxScore,1));
    $dPts[]=['x'=>round($cx2+$r*cos($rad),1),'y'=>round($cy2+$r*sin($rad),1)];
   }
   $dPoly=implode(' ',array_map(fn($p)=>$p['x'].','.$p['y'], $dPts));
  @endphp
  <svg width="{{ $gW2 }}" height="{{ $gH2 }}" viewBox="0 0 {{ $gW2 }} {{ $gH2 }}" style="width:100%;height:auto;max-width:300px;display:block;margin:0 auto;">
   @for($gl=1;$gl<=4;$gl++)
    @php $gr2=$maxR2*($gl/4); @endphp
    <polygon points="{{ $cx2 }},{{ round($cy2-$gr2,1) }} {{ round($cx2+$gr2,1) }},{{ $cy2 }} {{ $cx2 }},{{ round($cy2+$gr2,1) }} {{ round($cx2-$gr2,1) }},{{ $cy2 }}" fill="none" stroke="#e2e8f0" stroke-width="0.5"/>
   @endfor
   @foreach($dAxes as $ai=>$ax)
    @php $rad=deg2rad($ax['a']); $ex2=round($cx2+$maxR2*cos($rad),1); $ey2=round($cy2+$maxR2*sin($rad),1); @endphp
    <line x1="{{ $cx2 }}" y1="{{ $cy2 }}" x2="{{ $ex2 }}" y2="{{ $ey2 }}" stroke="#cbd5e1" stroke-width="0.5"/>
    @php $lx2=round($cx2+($maxR2+16)*cos($rad),1); $ly2=round($cy2+($maxR2+16)*sin($rad),1); @endphp
    <text x="{{ $lx2 }}" y="{{ round($ly2+4,1) }}" text-anchor="middle" font-size="10" font-weight="700" fill="{{ $dColors[$ai] }}">{{ $ax['l'] }}</text>
   @endforeach
   <polygon points="{{ $dPoly }}" fill="rgba(13,148,136,0.15)" stroke="#0d9488" stroke-width="2"/>
   @foreach($dPts as $pi=>$ptp)
    <circle cx="{{ $ptp['x'] }}" cy="{{ $ptp['y'] }}" r="4" fill="{{ $dColors[$pi] }}" stroke="#fff" stroke-width="1"/>
    <text x="{{ $ptp['x'] }}" y="{{ round($ptp['y']-8,1) }}" text-anchor="middle" font-size="8" font-weight="700" fill="{{ $dColors[$pi] }}">{{ $dVals[$pi] }}</text>
   @endforeach
  </svg>
 </div>

 <div class="interp-box">
  <strong>Interpretasi:</strong>
  Profil dominan <strong>{{ $pNames[$pt] ?? '' }} ({{ $pt }})</strong> — {{ $pDescs[$pt] ?? '' }}
  @php $st2 = strlen($profileType) > 1 ? substr($profileType,1,1) : null; @endphp
  @if($st2 && $st2 !== $pt)
   Diperkuat oleh dimensi <strong>{{ $pNames[$st2] ?? $st2 }} ({{ $st2 }})</strong>, membentuk kombinasi profil <strong>{{ $profileType }}</strong>.
  @endif
 </div>
 @endif
 @endforeach

 {{-- ===== SECTION 4: PAPIKOSTIK ===== --}}
 @php $papiSTs = $subTests->where('type', 'papikostik'); @endphp
 @foreach($papiSTs as $pst)
 @php
  $papiData = ($response->responses ?? [])['papikostik_' . $pst->id] ?? null;
  $pScores = $papiData['scores'] ?? [];
  $hasPapiData = !empty($papiData['answers']);
  $wheelDims = ['N','G','A','L','P','I','T','V','S','O','B','R','D','C','X','Z','E','K','F','W'];
  $pDimNames = [
   'N'=>'Need to Finish Task','G'=>'Hard Intense Worker','A'=>'Need to Achieve',
   'L'=>'Leadership Role','P'=>'Need to Control Others','I'=>'Ease in Decision Making',
   'T'=>'Pace','V'=>'Vigorous Type','S'=>'Social Extension',
   'R'=>'Theoretical Type','D'=>'Interest in Working with Details','C'=>'Organized Type',
   'X'=>'Need for Change','B'=>'Need to Belong to Groups','O'=>'Need for Closeness & Affection',
   'Z'=>'Need for Achievement','E'=>'Role of the Educator','K'=>'Need for Forceful Action',
   'F'=>'Need to Support Authority','W'=>'Need for Rules & Supervision'
  ];
  $pDimDescs = [
   'N'=>'Kebutuhan menyelesaikan tugas secara mandiri dan tuntas',
   'G'=>'Pekerja keras dan gigih dalam bekerja',
   'A'=>'Kecepatan dan semangat dalam bekerja',
   'L'=>'Kecenderungan mengambil peran kepemimpinan',
   'P'=>'Kebutuhan untuk mengontrol dan mempengaruhi orang lain',
   'I'=>'Kemampuan berpikir independen dalam mengambil keputusan',
   'T'=>'Kebutuhan akan kedekatan personal dalam hubungan kerja',
   'V'=>'Semangat dan energi yang aktif dan bersemangat',
   'S'=>'Kepercayaan diri dan keluasan dalam pergaulan sosial',
   'R'=>'Minat terhadap pemikiran teoritis dan analitis',
   'D'=>'Ketelitian dan perhatian dalam mengerjakan detail',
   'C'=>'Kemampuan mengorganisir dan merencanakan pekerjaan',
   'X'=>'Kebutuhan akan perubahan dan variasi dalam pekerjaan',
   'B'=>'Kebutuhan mengikuti aturan dan arahan',
   'O'=>'Empati dan sensitivitas terhadap perasaan orang lain',
   'Z'=>'Ambisi dan motivasi berprestasi tinggi',
   'E'=>'Kemampuan mempengaruhi dan membujuk orang lain',
   'K'=>'Ketekunan dan ketahanan dalam menghadapi tekanan',
   'F'=>'Kebutuhan mendukung dan menolong orang lain',
   'W'=>'Kebutuhan akan rasa aman dan lingkungan yang stabil'
  ];
 @endphp
 @if($hasPapiData)
 <div class="page-break"></div>
 <div class="section-title-violet">HASIL PAPIKOSTIK — {{ $pst->title }}</div>

 {{-- GD Wheel chart --}}
 @php
  $catDefs = [
   ['name'=>'ARAH KERJA','start'=>0,'count'=>3,'fill'=>'#bbf7d0','stroke'=>'#16a34a'],
   ['name'=>'KEPEMIMPINAN','start'=>3,'count'=>2,'fill'=>'#fed7aa','stroke'=>'#ea580c'],
   ['name'=>'AKTIVITAS','start'=>5,'count'=>3,'fill'=>'#fecaca','stroke'=>'#dc2626'],
   ['name'=>'PERGAULAN','start'=>8,'count'=>3,'fill'=>'#fef9c3','stroke'=>'#ca8a04'],
   ['name'=>'GAYA KERJA','start'=>11,'count'=>4,'fill'=>'#d9f99d','stroke'=>'#65a30d'],
   ['name'=>'SIFAT','start'=>15,'count'=>3,'fill'=>'#a5f3fc','stroke'=>'#0891b2'],
   ['name'=>'KETAATAN','start'=>18,'count'=>2,'fill'=>'#bfdbfe','stroke'=>'#2563eb'],
  ];
  $hexRgb = function($h) { $h=ltrim($h,'#'); return [hexdec(substr($h,0,2)),hexdec(substr($h,2,2)),hexdec(substr($h,4,2))]; };
  $imgSz=500; $cxP=250; $cyP=250; $maxRP=160; $ringIn=167; $ringOut=195; $lblR=215;
  $nDim=20; $aStep=360/$nDim;
  $img = imagecreatetruecolor($imgSz, $imgSz);
  imagesavealpha($img, true); imagealphablending($img, true); imageantialias($img, true);
  $white=imagecolorallocate($img,255,255,255);
  $gLight=imagecolorallocate($img,221,227,234); $gBold=imagecolorallocate($img,165,180,196);
  $purple=imagecolorallocate($img,124,58,237);
  $pFill=imagecolorallocatealpha($img,196,181,253,65);
  $dkText=imagecolorallocate($img,30,41,59);
  $mutText=imagecolorallocate($img,148,163,184);
  $whC=imagecolorallocate($img,255,255,255);
  imagefill($img,0,0,$white);
  for($lv=1;$lv<=9;$lv++){
   $r=(int)round($lv*$maxRP/9);
   imagesetthickness($img,($lv%3===0)?2:1);
   imagearc($img,$cxP,$cyP,$r*2,$r*2,0,360,($lv%3===0)?$gBold:$gLight);
  }
  imagesetthickness($img,1);
  foreach($wheelDims as $i=>$dm){
   $ar=deg2rad(-90+$i*$aStep);
   imageline($img,$cxP,$cyP,(int)round($cxP+$maxRP*cos($ar)),(int)round($cyP+$maxRP*sin($ar)),$gLight);
  }
  foreach($catDefs as $cat){
   $sd=-90+$cat['start']*$aStep-$aStep/2; $ed=-90+($cat['start']+$cat['count'])*$aStep-$aStep/2;
   $steps=50; $pts=[];
   for($j=0;$j<=$steps;$j++){$a=deg2rad($sd+($ed-$sd)*$j/$steps);$pts[]=(int)round($cxP+$ringOut*cos($a));$pts[]=(int)round($cyP+$ringOut*sin($a));}
   for($j=$steps;$j>=0;$j--){$a=deg2rad($sd+($ed-$sd)*$j/$steps);$pts[]=(int)round($cxP+$ringIn*cos($a));$pts[]=(int)round($cyP+$ringIn*sin($a));}
   $fRgb=$hexRgb($cat['fill']); $sRgb=$hexRgb($cat['stroke']);
   $fc=imagecolorallocate($img,$fRgb[0],$fRgb[1],$fRgb[2]);
   $sc=imagecolorallocate($img,$sRgb[0],$sRgb[1],$sRgb[2]);
   imagefilledpolygon($img,$pts,$fc); imagesetthickness($img,2); imagepolygon($img,$pts,$sc); imagesetthickness($img,1);
  }
  $polyPts=[]; $dotXY=[];
  foreach($wheelDims as $i=>$dm){
   $ar=deg2rad(-90+$i*$aStep); $v=$pScores[$dm]??0; $sr=($v/9)*$maxRP;
   $polyPts[]=(int)round($cxP+$sr*cos($ar)); $polyPts[]=(int)round($cyP+$sr*sin($ar));
   $dotXY[]=[(int)round($cxP+$sr*cos($ar)),(int)round($cyP+$sr*sin($ar))];
  }
  imagefilledpolygon($img,$polyPts,$pFill); imagesetthickness($img,2); imagepolygon($img,$polyPts,$purple); imagesetthickness($img,1);
  foreach($dotXY as $d){imagefilledellipse($img,$d[0],$d[1],10,10,$purple);imagefilledellipse($img,$d[0],$d[1],5,5,$whC);}
  foreach($wheelDims as $i=>$dm){
   $ar=deg2rad(-90+$i*$aStep);$lx=(int)round($cxP+$lblR*cos($ar));$ly=(int)round($cyP+$lblR*sin($ar));
   $fw=strlen($dm)*imagefontwidth(5);$fh=imagefontheight(5);
   imagestring($img,5,$lx-(int)($fw/2),$ly-(int)($fh/2),$dm,$dkText);
  }
  for($lv=3;$lv<=9;$lv+=3){$lvR2=(int)round($lv*$maxRP/9);imagestring($img,2,$cxP+5,$cyP-$lvR2-7,(string)$lv,$mutText);}
  ob_start(); imagepng($img); $pngData=ob_get_clean(); imagedestroy($img);
  $chartBase64='data:image/png;base64,'.base64_encode($pngData);
 @endphp

 <div style="text-align:center;margin-bottom:8px;">
  <img src="{{ $chartBase64 }}" width="340" height="340" style="display:inline-block;">
 </div>

 <table class="cat-legend">
  @foreach($catDefs as $cat)
  <tr>
   <td style="width:14px;"><div class="cat-dot" style="background:{{ $cat['fill'] }};border:1px solid {{ $cat['stroke'] }};"></div></td>
   <td style="font-weight:700;color:{{ $cat['stroke'] }};width:100px;">{{ $cat['name'] }}</td>
   <td style="color:#64748b;">@php echo implode(', ', array_slice($wheelDims, $cat['start'], $cat['count'])); @endphp</td>
  </tr>
  @endforeach
 </table>

 {{-- Top/Bottom dimensions --}}
 @php
  $sortedPapi = [];
  foreach ($wheelDims as $d) $sortedPapi[$d] = $pScores[$d] ?? 0;
  arsort($sortedPapi); $topDims = array_slice(array_keys($sortedPapi), 0, 5);
  asort($sortedPapi); $bottomDims = array_slice(array_keys($sortedPapi), 0, 5);
 @endphp

 <table class="dim-table">
  <thead>
   <tr><th style="width:26px;">Dim</th><th style="width:150px;">Nama</th><th>Deskripsi</th><th style="width:34px;">Skor</th><th style="width:50px;">Level</th></tr>
  </thead>
  <tbody>
  @foreach($wheelDims as $dm)
   @php
    $val=$pScores[$dm]??0;
    $level=$val>=7?'Tinggi':($val>=4?'Sedang':'Rendah');
    $lc=$val>=7?'#059669':($val>=4?'#d97706':'#dc2626');
   @endphp
   <tr>
    <td style="font-weight:700;color:#7c3aed;">{{ $dm }}</td>
    <td>{{ $pDimNames[$dm] ?? $dm }}</td>
    <td>{{ $pDimDescs[$dm] ?? '-' }}</td>
    <td style="text-align:center;font-weight:700;">{{ $val }}</td>
    <td style="color:{{ $lc }};font-weight:600;text-align:center;">{{ $level }}</td>
   </tr>
  @endforeach
  </tbody>
 </table>

 <div class="conclusion-box">
  @php
   $topNames = array_map(fn($d) => $d.' ('.$pDimNames[$d].')', $topDims);
   $bottomNames = array_map(fn($d) => $d.' ('.$pDimNames[$d].')', $bottomDims);
  @endphp
  <strong>Kesimpulan:</strong> {{ $response->participant_name }} menunjukkan dimensi kepribadian dominan pada
  <strong>{{ implode(', ', $topNames) }}</strong>.
  Dimensi yang lebih rendah: <strong>{{ implode(', ', $bottomNames) }}</strong>.
 </div>

 @php
  $pJobMap = [
   'G'=>'Posisi pekerja keras dan dedikasi tinggi','N'=>'Posisi ketelitian dan penyelesaian tugas mandiri',
   'A'=>'Posisi dengan target dan tenggat waktu ketat','L'=>'Posisi kepemimpinan, manajer',
   'P'=>'Posisi manajerial, koordinator proyek','I'=>'Posisi analis, konsultan, peneliti',
   'T'=>'Posisi customer service, HR, konselor','V'=>'Posisi lapangan, sales, event organizer',
   'S'=>'Posisi public relations, marketing','R'=>'Posisi riset, pengembangan, akademisi',
   'D'=>'Posisi quality control, auditor, akuntan','C'=>'Posisi project manager, administrator',
   'X'=>'Posisi kreatif, inovasi, startup','B'=>'Posisi administrasi, compliance',
   'O'=>'Posisi konseling, kerja sosial','Z'=>'Posisi kompetitif, sales high-target',
   'E'=>'Posisi trainer, pembicara, negosiator','K'=>'Posisi ketahanan dan kegigihan',
   'F'=>'Posisi mentoring, coaching, support role','W'=>'Posisi administrasi terstruktur, back office'
  ];
 @endphp
 <div class="job-box">
  <h4>Rekomendasi Posisi (PAPIKOSTIK):</h4>
  <ul>
   @foreach($topDims as $td)
    @if(isset($pJobMap[$td]))
    <li><strong>{{ $td }}</strong>: {{ $pJobMap[$td] }}</li>
    @endif
   @endforeach
  </ul>
 </div>
 @endif
 @endforeach

 <div class="footer">
  Dokumen ini digenerate secara otomatis oleh Sistem Psikotest Online<br>
  Tanggal cetak: {{ now()->format('d/m/Y H:i:s') }} | copyright &copy;2026 Shindengen HR Internal Team
 </div>

</div>
</body>
</html>
