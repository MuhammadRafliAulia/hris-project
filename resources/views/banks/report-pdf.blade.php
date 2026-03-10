<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Hasil Tes - {{ $response->participant_name }}</title>
<style>
 * { margin:0; padding:0; box-sizing:border-box; }
 body { font-family:'Helvetica','Arial',sans-serif; font-size:10.5px; color:#1e293b; line-height:1.6; }
 .header { background:#003e6f; color:#fff; padding:22px 30px; text-align:center; }
 .header h1 { font-size:18px; margin-bottom:2px; letter-spacing:1px; }
 .header p { font-size:10px; opacity:0.85; }
 .content { padding:20px 30px; }

 .section-title { font-size:13px; font-weight:700; color:#003e6f; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #003e6f; text-transform:uppercase; }
 .section-title-purple { font-size:13px; font-weight:700; color:#5b21b6; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #7c3aed; text-transform:uppercase; }
 .section-title-teal { font-size:13px; font-weight:700; color:#0d9488; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #14b8a6; text-transform:uppercase; }
 .section-title-violet { font-size:13px; font-weight:700; color:#7c3aed; margin:18px 0 8px 0; padding-bottom:4px; border-bottom:2px solid #a78bfa; text-transform:uppercase; }

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

 .chart-box { text-align:center; margin-bottom:14px; padding:8px; background:#fafafe; border:1px solid #e2e8f0; border-radius:6px; }

 .cat-legend { width:100%; border-collapse:collapse; margin-bottom:12px; }
 .cat-legend td { padding:2px 5px; font-size:9px; }
 .cat-dot { width:10px; height:10px; border-radius:2px; display:inline-block; }

 .dim-table { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9px; }
 .dim-table th { background:#7c3aed; color:#fff; padding:4px 6px; text-align:left; font-weight:600; }
 .dim-table td { padding:3px 6px; border-bottom:1px solid #e2e8f0; }
 .dim-table tr:nth-child(even) { background:#faf5ff; }

 .conclusion-section { background:#f8fafc; border:2px solid #003e6f; border-radius:6px; padding:16px 18px; margin-bottom:16px; }
 .conclusion-item { margin-bottom:12px; padding-bottom:10px; border-bottom:1px dashed #cbd5e1; }
 .conclusion-item:last-child { margin-bottom:0; padding-bottom:0; border-bottom:none; }
 .conclusion-label { font-weight:700; font-size:11px; margin-bottom:4px; }
 .conclusion-text { font-size:9.5px; color:#374151; line-height:1.8; text-align:justify; }

 .job-box { background:#f0fdf4; border-left:4px solid #059669; padding:10px 12px; border-radius:0 6px 6px 0; margin-bottom:14px; }
 .job-box h4 { color:#059669; margin:0 0 4px 0; font-size:11px; }
 .job-box ul { margin:0; padding-left:16px; font-size:10px; line-height:1.6; }

 .footer { text-align:center; font-size:9px; color:#94a3b8; margin-top:20px; padding-top:10px; border-top:1px solid #e2e8f0; }
 .page-break { page-break-before:always; }
 .watermark { position: fixed; top: 10px; right: 10px; font-size: 14px; font-weight: 700; color: #dc2626; opacity: 1; z-index: 999; pointer-events: none; }
</style>
</head>
<body>
 <div class="watermark">CONFIDENTIAL</div>

@php
 $conclusionParts = [];
 $jobRecs = [];
@endphp

<div class="header">
 <h1>LAPORAN LENGKAP HASIL TES</h1>
 <p>{{ $bank->title }}</p>
</div>

<div class="content">

 {{-- ===== INFORMASI PESERTA ===== --}}
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
  @if($response->started_at && $response->completed_at)
  <tr>
   <td class="label">Durasi Pengerjaan</td>
   <td class="value" colspan="3">{{ $response->started_at->diff($response->completed_at)->format('%H jam %I menit %S detik') }}</td>
  </tr>
  @endif
 </table>

 {{-- ===== SECTION 1: SKOR SOAL UMUM ===== --}}
 @php
  $defaultSubTests = $subTests->whereNotIn('type', ['kraepelin', 'disc', 'papikostik']);
  $defaultQuestions = collect();
  if ($defaultSubTests->count() > 0) {
   foreach ($defaultSubTests as $dst2) { $defaultQuestions = $defaultQuestions->merge($dst2->questions); }
  }
  $directQuestions = $bank->questions()->whereNull('sub_test_id')->orderBy('order')->get();
  $defaultQuestions = $defaultQuestions->merge($directQuestions);
  $scoreableQ = $defaultQuestions->whereNotIn('type', ['narrative', 'survey'])->count();
  $hasDefaultScore = $scoreableQ > 0;
 @endphp

 @if($hasDefaultScore)
 <div class="section-title">Hasil Tes Pengetahuan</div>
 @php
  $scorePct = $scoreableQ > 0 ? round(($response->score / $scoreableQ) * 100, 2) : 0;
  $scoreLevel = $scorePct >= 70 ? 'BAIK' : ($scorePct >= 50 ? 'CUKUP' : 'KURANG');
 @endphp
 <div class="score-box">
  <div class="score-number">{{ $response->score }} / {{ $scoreableQ }}</div>
  <div class="score-label">Jawaban Benar</div>
  <div style="font-size:14px;font-weight:600;margin-top:4px;" class="{{ $scorePct >= 70 ? 'score-good' : ($scorePct >= 50 ? 'score-ok' : 'score-poor') }}">
   {{ $scorePct }}% &mdash; {{ $scoreLevel }}
  </div>
 </div>
 @php
  $conclusionParts['knowledge'] = [
   'score' => $response->score, 'total' => $scoreableQ, 'pct' => $scorePct, 'level' => $scoreLevel
  ];
 @endphp
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
 <div class="section-title-purple">Hasil Kraepelin &mdash; {{ $kst->title }}</div>
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

 {{-- Kraepelin GD Line Chart --}}
 @php
  $kImgW=680; $kImgH=170; $kPadL=32; $kPadR=10; $kPadT=10; $kPadB=22;
  $kPlotW=$kImgW-$kPadL-$kPadR; $kPlotH=$kImgH-$kPadT-$kPadB;
  $kImg=imagecreatetruecolor($kImgW,$kImgH);
  imagesavealpha($kImg,true); imagealphablending($kImg,true); imageantialias($kImg,true);
  $kW=imagecolorallocate($kImg,255,255,255);
  $kGL=imagecolorallocate($kImg,226,232,240);
  $kGB=imagecolorallocate($kImg,203,213,225);
  $kPurp=imagecolorallocate($kImg,124,58,237);
  $kPurpFill=imagecolorallocatealpha($kImg,124,58,237,100);
  $kRed=imagecolorallocate($kImg,220,38,38);
  $kMut=imagecolorallocate($kImg,148,163,184);
  imagefill($kImg,0,0,$kW);
  $kYMax=max($maxCorrect+2,10);
  // horizontal grid
  for($gy=0;$gy<=$kYMax;$gy+=max(1,intval($kYMax/5))){
   $yy=(int)round($kPadT+$kPlotH-(($gy/$kYMax)*$kPlotH));
   imageline($kImg,$kPadL,$yy,$kImgW-$kPadR,$yy,$kGL);
   imagestring($kImg,2,$kPadL-20,$yy-6,(string)$gy,$kMut);
  }
  // area fill
  $kArea=[$kPadL,$kPadT+$kPlotH];
  for($i=0;$i<$colCount;$i++){
   $x=(int)round($kPadL+($colCount>1?($i*$kPlotW/($colCount-1)):$kPlotW/2));
   $y=(int)round($kPadT+$kPlotH-(($correctPerCol[$i]/$kYMax)*$kPlotH));
   $kArea[]=$x; $kArea[]=$y;
  }
  $kLastX=(int)round($kPadL+($colCount>1?(($colCount-1)*$kPlotW/($colCount-1)):$kPlotW/2));
  $kArea[]=$kLastX; $kArea[]=$kPadT+$kPlotH;
  imagefilledpolygon($kImg,$kArea,$kPurpFill);
  // data line
  imagesetthickness($kImg,2);
  for($i=1;$i<$colCount;$i++){
   $x1=(int)round($kPadL+(($i-1)*$kPlotW/max(1,$colCount-1)));
   $y1=(int)round($kPadT+$kPlotH-(($correctPerCol[$i-1]/$kYMax)*$kPlotH));
   $x2=(int)round($kPadL+($i*$kPlotW/max(1,$colCount-1)));
   $y2=(int)round($kPadT+$kPlotH-(($correctPerCol[$i]/$kYMax)*$kPlotH));
   imageline($kImg,$x1,$y1,$x2,$y2,$kPurp);
  }
  // mean dashed line
  $kMeanY=(int)round($kPadT+$kPlotH-(($speed/$kYMax)*$kPlotH));
  for($dx=$kPadL;$dx<$kImgW-$kPadR;$dx+=12){
   $ex=min($dx+7,$kImgW-$kPadR);
   imageline($kImg,$dx,$kMeanY,$ex,$kMeanY,$kRed);
  }
  // dots
  imagesetthickness($kImg,1);
  for($i=0;$i<$colCount;$i++){
   $x=(int)round($kPadL+($colCount>1?($i*$kPlotW/($colCount-1)):$kPlotW/2));
   $y=(int)round($kPadT+$kPlotH-(($correctPerCol[$i]/$kYMax)*$kPlotH));
   imagefilledellipse($kImg,$x,$y,7,7,$kPurp);
  }
  // x-axis labels (every 5th)
  for($i=0;$i<$colCount;$i++){
   if($i%5===0||$i===$colCount-1){
    $x=(int)round($kPadL+($colCount>1?($i*$kPlotW/($colCount-1)):$kPlotW/2));
    $lb=(string)($i+1);
    imagestring($kImg,1,$x-(int)(strlen($lb)*imagefontwidth(1)/2),$kPadT+$kPlotH+5,$lb,$kMut);
   }
  }
  // legend: mean line indicator
  imagestring($kImg,2,$kImgW-$kPadR-80,$kMeanY-12,'avg='.$speed,$kRed);
  ob_start(); imagepng($kImg); $kPng=ob_get_clean(); imagedestroy($kImg);
  $kChartB64='data:image/png;base64,'.base64_encode($kPng);
 @endphp
 <div class="chart-box">
  <img src="{{ $kChartB64 }}" width="580" style="display:inline-block;">
 </div>

 @php
  $conclusionParts['kraepelin'] = [
   'title'=>$kst->title, 'speed'=>$speed,
   'speedLvl'=>$speed>=15?'tinggi':($speed>=8?'sedang':'rendah'),
   'accuracy'=>$accuracy,
   'accLvl'=>$accuracy>=85?'sangat baik':($accuracy>=70?'baik':($accuracy>=50?'cukup':'kurang')),
   'endurance'=>$endurance,
   'endLvl'=>$endurance>=90?'sangat baik':($endurance>=75?'baik':($endurance>=60?'cukup':'kurang')),
   'stdDev'=>$stdDev,
   'stabLvl'=>$stdDev<=2?'sangat stabil':($stdDev<=4?'stabil':($stdDev<=6?'cukup':'kurang stabil')),
   'motivation'=>strtolower($motivation),
  ];
 @endphp
 @endif
 @endforeach

 {{-- ===== SECTION 3: DISC ===== --}}
 @php
  $discSTs = $subTests->where('type', 'disc');
  $pNames = ['D'=>'Dominance','I'=>'Influence','S'=>'Steadiness','C'=>'Compliance'];
  $pDescs = [
   'D'=>'Tegas, berorientasi hasil, kompetitif, dan suka memimpin.',
   'I'=>'Antusias, optimis, komunikatif, dan suka bersosialisasi.',
   'S'=>'Sabar, stabil, loyal, dan kooperatif.',
   'C'=>'Teliti, analitis, sistematis, dan berorientasi kualitas.',
  ];
 @endphp
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
 <div class="section-title-teal">Hasil DISC &mdash; {{ $dst->title }}</div>
 @php $pt = strlen($profileType) > 0 ? substr($profileType,0,1) : 'D'; @endphp

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


 @php
  // Detailed DISC profile descriptions
  $discProfiles = [
   'D'=>[
    'kekuatan'=>'Hasil-oriented, decisive, leadership, challenging situations, autonomy',
    'kelemahan'=>'Lack of patience, insensitive to others feelings, overly direct',
    'kerja'=>'Individu dengan tipe D unggul saat diberikan otonomi dan tantangan, namun perlu mengembangkan kesabaran serta kepekaan terhadap perasaan rekan kerja agar tercipta kolaborasi yang lebih efektif.',
    'longForm'=>'Individu dengan tipe ini memiliki dorongan kuat untuk mencapai hasil, mengambil keputusan secara cepat, dan mampu memimpin dalam situasi yang menantang. Mereka cenderung langsung, tegas, dan berorientasi pada pencapaian target.',
   ],
   'I'=>[
    'kekuatan'=>'Enthusiastic, optimistic, communicative, social, persuasive, team player',
    'kelemahan'=>'Impulsive, lack of follow-through, emotional, needs social approval',
    'kerja'=>'Individu dengan tipe I berkembang dalam lingkungan yang kolaboratif dan sosial, namun perlu mengembangkan fokus dan kedisiplinan dalam menyelesaikan tugas terstruktur.',
    'longForm'=>'Individu dengan tipe ini memiliki antusiasme tinggi, optimisme, dan komunikasi yang baik. Mereka senang bersosialisasi, persuasif, dan excellent dalam teamwork. Mereka bersemangat dalam berbagi ide dan menciptakan lingkungan yang positif.',
   ],
   'S'=>[
    'kekuatan'=>'Patient, stable, loyal, cooperative, reliable, good listener, supportive',
    'kelemahan'=>'Resistant to change, lack of assertiveness, avoids conflict, slow to decide',
    'kerja'=>'Individu dengan tipe S memberikan kontribusi besar dalam lingkungan kerja yang stabil dan mendukung. Mereka perlu didorong untuk lebih assertive dan terbuka terhadap perubahan untuk mengembangkan kepemimpinan.',
    'longForm'=>'Individu dengan tipe ini sabar, stabil, loyal, dan sangat kooperatif. Mereka dapat diandalkan, pendengar yang baik, dan selalu memberikan dukungan kepada rekan kerja. Stabilitas dan konsistensi mereka menjadi aset berharga dalam organisasi.',
   ],
   'C'=>[
    'kekuatan'=>'Detail-oriented, analytical, systematic, quality-focused, accurate, organized',
    'kelemahan'=>'Perfectionist, overly critical, slow decision-making, difficulty with ambiguity',
    'kerja'=>'Individu dengan tipe C excellent dalam role yang membutuhkan akurasi dan perhatian detail. Mereka perlu belajar untuk lebih fleksibel dan beradaptasi dengan perubahan yang lebih cepat.',
    'longForm'=>'Individu dengan tipe ini teliti, analitis, sistematis, dan berorientasi pada kualitas. Mereka memiliki standar tinggi dan memastikan setiap detail dikerjakan dengan sempurna. Pendekatan mereka yang metodis sangat valuable untuk work requiring precision.',
   ],
  ];

  // Get primary and secondary types
  $st2 = strlen($profileType) > 1 ? substr($profileType,1,1) : null;
  $ptProfile = $discProfiles[$pt] ?? [];
  $st2Profile = isset($discProfiles[$st2]) ? $discProfiles[$st2] : [];

  $conclusionParts['disc'] = [
   'title'=>$dst->title, 'profileType'=>$profileType,
   'name'=>$response->participant_name,
   'pt'=>$pt, 'ptName'=>$pNames[$pt]??'', 'ptDesc'=>$pDescs[$pt]??'',
   'ptLongForm'=>$ptProfile['longForm']??'',
   'ptKerja'=>$ptProfile['kerja']??'',
   'st2'=>$st2, 'st2Name'=>isset($pNames[$st2])?$pNames[$st2]:$st2,
   'st2LongForm'=>$st2Profile['longForm']??'',
   'dM'=>$dM,'iM'=>$iM,'sM'=>$sM,'cM'=>$cM,
  ];
 @endphp
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
 <div class="section-title-violet">Hasil PAPIKOSTIK &mdash; {{ $pst->title }}</div>

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

 <div class="chart-box">
  <img src="{{ $chartBase64 }}" width="340" style="display:inline-block;">
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

 @php
  $topNames = array_map(fn($d) => $d.' ('.$pDimNames[$d].')', $topDims);
  $bottomNames = array_map(fn($d) => $d.' ('.$pDimNames[$d].')', $bottomDims);
  $conclusionParts['papikostik'] = [
   'title'=>$pst->title, 'topDims'=>$topNames, 'bottomDims'=>$bottomNames,
   'participantName'=>$response->participant_name,
  ];
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
  foreach($topDims as $td){ if(isset($pJobMap[$td])) $jobRecs[] = ['dim'=>$td, 'desc'=>$pJobMap[$td]]; }
 @endphp
 @endif
 @endforeach

 {{-- ===== KESIMPULAN AKHIR ===== --}}
 @if(count($conclusionParts) > 0)
 <div class="page-break"></div>
 <div class="section-title">Kesimpulan Akhir</div>
 <div class="conclusion-section">

  @if(isset($conclusionParts['knowledge']))
  <div class="conclusion-item">
   <div class="conclusion-label" style="color:#003e6f;">Tes Pengetahuan</div>
   <div class="conclusion-text">
    Peserta memperoleh skor <strong>{{ $conclusionParts['knowledge']['score'] }}/{{ $conclusionParts['knowledge']['total'] }} ({{ $conclusionParts['knowledge']['pct'] }}%)</strong>.
    Kategori: <strong style="color:{{ $conclusionParts['knowledge']['pct'] >= 70 ? '#059669' : ($conclusionParts['knowledge']['pct'] >= 50 ? '#d97706' : '#dc2626') }}">{{ $conclusionParts['knowledge']['level'] }}</strong>.
    @if($conclusionParts['knowledge']['pct'] >= 70)
     Peserta menunjukkan pemahaman yang baik terhadap materi tes.
    @elseif($conclusionParts['knowledge']['pct'] >= 50)
     Peserta menunjukkan pemahaman yang cukup terhadap materi tes dan masih perlu peningkatan.
    @else
     Peserta menunjukkan pemahaman yang kurang terhadap materi tes dan perlu bimbingan lebih lanjut.
    @endif
   </div>
  </div>
  @endif

  @if(isset($conclusionParts['kraepelin']))
  @php $kr = $conclusionParts['kraepelin']; @endphp
  <div class="conclusion-item">
   <div class="conclusion-label" style="color:#5b21b6;">Kraepelin &mdash; {{ $kr['title'] }}</div>
   <div class="conclusion-text">
    Kecepatan kerja <strong>{{ $kr['speedLvl'] }}</strong> ({{ $kr['speed'] }}/kolom).
    Ketelitian <strong>{{ $kr['accLvl'] }}</strong> ({{ $kr['accuracy'] }}%).
    Ketahanan kerja <strong>{{ $kr['endLvl'] }}</strong> ({{ $kr['endurance'] }}%).
    Stabilitas emosi <strong>{{ $kr['stabLvl'] }}</strong> (SD={{ $kr['stdDev'] }}).
    Semangat kerja <strong>{{ $kr['motivation'] }}</strong>.
   </div>
  </div>
  @endif

  @if(isset($conclusionParts['disc']))
  @php $dc = $conclusionParts['disc']; @endphp
  <div class="conclusion-item">
   <div class="conclusion-label" style="color:#0d9488;">DISC &mdash; {{ $dc['title'] }}</div>
   <div class="conclusion-text">
    Berdasarkan hasil tes DISC, <strong>{{ $dc['name'] }}</strong> menunjukkan profil kepribadian dominan bertipe <strong>{{ $dc['ptName'] }} ({{ $dc['pt'] }})</strong> dengan skor <strong>{{ $dc[strtolower($dc['pt']).'M'] }}/24</strong>. 
    {{ $dc['ptLongForm'] }} 
    {{ $dc['ptKerja'] }}
    @if($dc['st2'] && $dc['st2'] !== $dc['pt'])
     Profil ini diperkuat dengan sentuhan karakteristik dari dimensi <strong>{{ $dc['st2Name'] }}</strong>, yang membentuk kombinasi unik <strong>{{ $dc['profileType'] }}</strong> dalam pendekatan kerja sehari-hari.
    @endif
   </div>
  </div>
  @endif

  @if(isset($conclusionParts['papikostik']))
  @php $pp = $conclusionParts['papikostik']; @endphp
  <div class="conclusion-item">
   <div class="conclusion-label" style="color:#7c3aed;">PAPIKOSTIK &mdash; {{ $pp['title'] }}</div>
   <div class="conclusion-text">
    {{ $pp['participantName'] }} menunjukkan dimensi kepribadian dominan pada
    <strong>{{ implode(', ', $pp['topDims']) }}</strong>.
    Dimensi yang lebih rendah: <strong>{{ implode(', ', $pp['bottomDims']) }}</strong>.
   </div>
  </div>
  @endif

 </div>

 @if(count($jobRecs) > 0)
 <div class="job-box">
  <h4>Rekomendasi Posisi:</h4>
  <ul>
   @foreach($jobRecs as $rec)
    <li><strong>{{ $rec['dim'] }}</strong>: {{ $rec['desc'] }}</li>
   @endforeach
  </ul>
 </div>
 @endif
 @endif

 <div class="footer">
  Dokumen ini digenerate secara otomatis oleh Sistem Psikotest Online<br>
  Tanggal cetak: {{ now()->format('d/m/Y H:i:s') }} | copyright &copy;2026 Shindengen HR Internal Team
 </div>

</div>
</body>
</html>
