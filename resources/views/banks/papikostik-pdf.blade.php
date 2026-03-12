<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Papikostik - {{ $response->participant_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.45; }
    .header-bar { background: #7c3aed; color: #fff; padding: 14px 50px; }
    .cat-dot { display: inline-block; width: 10px; height: 10px; border-radius: 2px; margin-right: 4px; vertical-align: middle; }
    .header-bar h1 { font-size: 14px; font-weight: 700; margin: 0; display: inline; }
    .header-bar p { font-size: 9px; opacity: .8; margin: 2px 0 0 0; }
    .header-bar .conf { float: right; font-size: 9px; font-weight: 700; color: #e9d5ff; letter-spacing: 1px; margin-top: 2px; }
    .content { padding: 14px 50px 30px 50px; }
    .section-title { font-size: 11px; font-weight: 700; color: #7c3aed; margin: 12px 0 6px 0; padding-bottom: 3px; border-bottom: 1.5px solid #7c3aed; text-transform: uppercase; letter-spacing: 0.3px; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .info-table td { padding: 4px 8px; border: 1px solid #d1d5db; font-size: 9px; }
    .info-table .label { background: #f5f3ff; font-weight: 600; width: 130px; color: #7c3aed; }
    .chart-box { border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px; margin-bottom: 10px; background: #fafafa; text-align: center; }
    .chart-box .gt { font-size: 10px; font-weight: 700; color: #7c3aed; margin-bottom: 6px; text-align: left; }
    .cat-legend { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8px; }
    .cat-legend th { background: #7c3aed; color: #fff; padding: 3px 6px; text-align: left; font-weight: 600; }
    .cat-legend td { padding: 3px 6px; border: 1px solid #d1d5db; }
    .cat-legend .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 3px; vertical-align: middle; }
    .dim-table { width: 100%; border-collapse: collapse; font-size: 8px; }
    .dim-table th { background: #7c3aed; color: #fff; padding: 4px 3px; text-align: center; font-weight: 600; }
    .dim-table td { padding: 3px; border: 1px solid #d1d5db; text-align: center; }
    .dim-table tr:nth-child(even) td { background: #f8fafc; }
    .dim-table td:nth-child(3) { text-align: left; }
    .lvl-h { color: #065f46; font-weight: 700; }
    .lvl-s { color: #92400e; font-weight: 600; }
    .lvl-r { color: #991b1b; font-weight: 600; }
    .conclusion-box { background: #f5f3ff; border: 1px solid #c4b5fd; border-radius: 5px; padding: 10px 12px; margin-bottom: 10px; }
    .conclusion-box h3 { font-size: 10px; color: #7c3aed; margin-bottom: 4px; }
    .conclusion-box p { font-size: 9px; color: #374151; line-height: 1.5; }
    .tag { display: inline-block; background: #ede9fe; color: #7c3aed; border-radius: 3px; padding: 2px 6px; font-size: 8px; font-weight: 600; margin: 1px; }
    .footer { text-align: center; font-size: 8px; color: #94a3b8; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; }
    .page-break { page-break-before: always; }
  </style>
</head>
<body>

  <div class="header-bar">
    <span class="conf">CONFIDENTIAL</span>
    <h1>LAPORAN HASIL TES PAPIKOSTIK</h1>
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
      $papiData = ($response->responses ?? [])['papikostik_' . $subTest->id] ?? null;
      $pScores = $papiData['scores'] ?? [];
      $dims = ['N','G','A','L','P','I','T','V','S','O','B','R','D','C','X','Z','E','K','F','W'];
      $dimNames = [
        'N'=>'Need to Finish Task','G'=>'Hard Intense Worker','A'=>'Need to Achieve',
        'L'=>'Leadership Role','P'=>'Need to Control Others','I'=>'Ease in Decision Making',
        'T'=>'Pace','V'=>'Vigorous Type','S'=>'Social Extension',
        'R'=>'Theoretical Type','D'=>'Interest in Working with Details','C'=>'Organized Type',
        'X'=>'Need for Change','B'=>'Need to Belong to Groups','O'=>'Need for Closeness & Affection',
        'Z'=>'Need for Achievement','E'=>'Role of the Educator','K'=>'Need for Forceful Action',
        'F'=>'Need to Support Authority','W'=>'Need for Rules & Supervision'
      ];
      $dimDescriptions = [
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

      $scores = [];
      foreach ($dims as $d) $scores[$d] = $pScores[$d] ?? 0;

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

      $sortedScores = $scores;
      arsort($sortedScores);
      $topDims = array_slice(array_keys($sortedScores), 0, 5);
      $bottomScores = $scores;
      asort($bottomScores);
      $bottomDims = array_slice(array_keys($bottomScores), 0, 5);

      $jobMap = [
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

    {{-- GD Wheel Chart --}}
    <div class="section-title">Psikogram</div>
    <div class="chart-box">
      <div class="gt">Profil 20 Dimensi Papikostik</div>
      @php
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

        // Grid circles (1-9), bolder every 3rd
        for($lv=1;$lv<=9;$lv++){
          $r=(int)round($lv*$maxRP/9);
          imagesetthickness($img,($lv%3===0)?2:1);
          imagearc($img,$cxP,$cyP,$r*2,$r*2,0,360,($lv%3===0)?$gBold:$gLight);
        }
        imagesetthickness($img,1);

        // Spokes
        foreach($dims as $i=>$dm){
          $ar=deg2rad(-90+$i*$aStep);
          imageline($img,$cxP,$cyP,(int)round($cxP+$maxRP*cos($ar)),(int)round($cyP+$maxRP*sin($ar)),$gLight);
        }

        // Category ring arcs (polygon-based band)
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

        // Score polygon (filled + outlined)
        $polyPts=[]; $dotXY=[];
        foreach($dims as $i=>$dm){
          $ar=deg2rad(-90+$i*$aStep); $v=$scores[$dm]??0; $sr=($v/9)*$maxRP;
          $polyPts[]=(int)round($cxP+$sr*cos($ar)); $polyPts[]=(int)round($cyP+$sr*sin($ar));
          $dotXY[]=[(int)round($cxP+$sr*cos($ar)),(int)round($cyP+$sr*sin($ar))];
        }
        imagefilledpolygon($img,$polyPts,$pFill); imagesetthickness($img,2); imagepolygon($img,$polyPts,$purple); imagesetthickness($img,1);

        // Dots (purple with white center)
        foreach($dotXY as $d){imagefilledellipse($img,$d[0],$d[1],10,10,$purple);imagefilledellipse($img,$d[0],$d[1],5,5,$whC);}

        // Dimension labels
        foreach($dims as $i=>$dm){
          $ar=deg2rad(-90+$i*$aStep);$lx=(int)round($cxP+$lblR*cos($ar));$ly=(int)round($cyP+$lblR*sin($ar));
          $fw=strlen($dm)*imagefontwidth(5);$fh=imagefontheight(5);
          imagestring($img,5,$lx-(int)($fw/2),$ly-(int)($fh/2),$dm,$dkText);
        }

        // Score markers (3, 6, 9)
        for($lv=3;$lv<=9;$lv+=3){$lvR2=(int)round($lv*$maxRP/9);imagestring($img,2,$cxP+5,$cyP-$lvR2-7,(string)$lv,$mutText);}

        ob_start(); imagepng($img); $pngW = ob_get_clean(); imagedestroy($img);
        $wheelChart = 'data:image/png;base64,' . base64_encode($pngW);
      @endphp
      <img src="{{ $wheelChart }}" width="340" style="display:inline-block;">
    </div>

    <div class="section-title">Legenda Kategori</div>
    <table class="cat-legend">
      <tbody>
      @foreach($catDefs as $cat)
        <tr>
          <td style="width:14px;"><div class="cat-dot" style="background:{{ $cat['fill'] }};border:1px solid {{ $cat['stroke'] }};"></div></td>
          <td style="font-weight:700;color:{{ $cat['stroke'] }};width:100px;">{{ $cat['name'] }}</td>
          <td style="color:#64748b;">{{ implode(', ', array_slice($dims, $cat['start'], $cat['count'])) }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>

    </div>
    {{-- Dimension Table --}}
    <div class="page-break"></div>
    <div class="header-bar">
      <span class="conf">CONFIDENTIAL</span>
      <h1>INTERPRETASI DIMENSI &mdash; {{ $response->participant_name }}</h1>
      <p>{{ $bank->title }} &mdash; {{ $subTest->title }}</p>
    </div>
    <div class="content">

    <div class="section-title">Detail 20 Dimensi</div>
    <table class="dim-table">
      <thead>
        <tr>
          <th style="width:25px;">Dim</th>
          <th style="width:85px;">Nama</th>
          <th>Deskripsi</th>
          <th style="width:35px;">Skor</th>
          <th style="width:45px;">Level</th>
        </tr>
      </thead>
      <tbody>
        @foreach($dims as $d)
        @php
          $sc = $scores[$d];
          if ($sc >= 7) { $lvl = 'Tinggi'; $lc = 'lvl-h'; }
          elseif ($sc >= 4) { $lvl = 'Sedang'; $lc = 'lvl-s'; }
          else { $lvl = 'Rendah'; $lc = 'lvl-r'; }
        @endphp
        <tr>
          <td style="font-weight:700;color:#7c3aed;">{{ $d }}</td>
          <td>{{ $dimNames[$d] }}</td>
          <td>{{ $dimDescriptions[$d] }}</td>
          <td style="font-weight:700;">{{ $sc }}</td>
          <td class="{{ $lc }}">{{ $lvl }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="section-title" style="margin-top:14px;">Kesimpulan</div>
    <div class="conclusion-box">
      <h3>Dimensi Dominan (Top 5)</h3>
      <p>
        @foreach($topDims as $td)
          <strong>{{ $td }}</strong> ({{ $dimNames[$td] }}, skor {{ $scores[$td] }}){{ !$loop->last ? ', ' : '.' }}
        @endforeach
      </p>
      <h3 style="margin-top:6px;">Dimensi Terendah (Bottom 5)</h3>
      <p>
        @foreach($bottomDims as $bd)
          <strong>{{ $bd }}</strong> ({{ $dimNames[$bd] }}, skor {{ $scores[$bd] }}){{ !$loop->last ? ', ' : '.' }}
        @endforeach
      </p>
    </div>

    <div class="section-title">Rekomendasi Jabatan</div>
    <div class="conclusion-box">
      <h3>Bidang Pekerjaan yang Sesuai</h3>
      <p style="margin-top:4px;">
        @php
          $recs = [];
          foreach ($topDims as $td) { if(isset($jobMap[$td])) $recs[] = ['dim'=>$td, 'desc'=>$jobMap[$td]]; }
        @endphp
        @foreach($recs as $rec)
          <span class="tag">{{ $rec['dim'] }}: {{ $rec['desc'] }}</span>
        @endforeach
      </p>
    </div>

    <div class="footer">
      Dokumen digenerate otomatis oleh Sistem HRIS &mdash; Tanggal cetak: {{ now()->format('d/m/Y H:i') }}
      | &copy; {{ date('Y') }} Shindengen HR Internal Team
    </div>
    <div style="font-size:7px; color:#9ca3af; line-height:1.4; padding:4px 0 0 0; text-align:center;">
      Hasil tes ini digenerate berdasarkan teori PAPI-Kostick oleh Max M. Kostick (1966) yang mengukur 20 dimensi kepribadian kerja,
      berlandaskan <em>Need Theory</em> oleh Henry A. Murray (1938) dalam <em>Explorations in Personality</em>.
      Hasil bersifat indikatif dan dimaksudkan sebagai alat bantu asesmen, bukan sebagai diagnosis psikologis.
      Interpretasi lebih lanjut disarankan dilakukan oleh psikolog profesional berlisensi.
    </div>

  </div>
</body>
</html>
