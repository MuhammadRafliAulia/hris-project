<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan DISC - {{ $response->participant_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.45; }
    .header-bar { background: #0d9488; color: #fff; padding: 14px 50px; }
    .header-bar h1 { font-size: 14px; font-weight: 700; margin: 0; display: inline; }
    .header-bar p { font-size: 9px; opacity: .8; margin: 2px 0 0 0; }
    .header-bar .conf { float: right; font-size: 9px; font-weight: 700; color: #fecaca; letter-spacing: 1px; margin-top: 2px; }
    .content { padding: 14px 50px 30px 50px; }
    .section-title { font-size: 11px; font-weight: 700; color: #0d9488; margin: 12px 0 6px 0; padding-bottom: 3px; border-bottom: 1.5px solid #14b8a6; text-transform: uppercase; letter-spacing: 0.3px; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .info-table td { padding: 4px 8px; border: 1px solid #d1d5db; font-size: 9px; }
    .info-table .label { background: #f0fdfa; font-weight: 600; width: 130px; color: #0d9488; }
    .metrics-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .metrics-grid td { padding: 8px 4px; text-align: center; border: 1px solid #e2e8f0; width: 25%; }
    .metrics-grid .mv { font-size: 20px; font-weight: 800; }
    .metrics-grid .ml { font-size: 8px; color: #64748b; margin-top: 1px; }
    .md { color: #dc2626; background: #fef2f2; }
    .mi { color: #d97706; background: #fffbeb; }
    .ms { color: #059669; background: #ecfdf5; }
    .mc { color: #2563eb; background: #eff6ff; }
    .profile-box { background: #f0fdfa; border: 1.5px solid #14b8a6; border-radius: 6px; padding: 10px; margin-bottom: 10px; text-align: center; }
    .profile-box .pt { font-size: 24px; font-weight: 800; color: #0d9488; }
    .profile-box .pn { font-size: 11px; font-weight: 600; color: #0f766e; margin-top: 1px; }
    .profile-box .pd { font-size: 9px; color: #374151; margin-top: 3px; line-height: 1.4; }
    .graph-box { border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px; margin-bottom: 10px; background: #fafafa; text-align: center; }
    .graph-box .gt { font-size: 10px; font-weight: 700; color: #0d9488; margin-bottom: 6px; text-align: left; }
    .interp-box { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 5px; padding: 10px 12px; margin-bottom: 10px; }
    .interp-box h3 { font-size: 10px; color: #0d9488; margin-bottom: 4px; }
    .interp-item { padding: 2px 0; font-size: 9px; color: #374151; line-height: 1.5; }
    .interp-label { font-weight: 700; }
    .id { color: #dc2626; } .ii { color: #d97706; } .is { color: #059669; } .ic { color: #2563eb; }
    .concl-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 5px; padding: 10px 12px; margin-bottom: 10px; }
    .concl-box h3 { font-size: 10px; color: #0d9488; margin-bottom: 4px; }
    .concl-box p { font-size: 9px; color: #1e293b; line-height: 1.6; text-align: justify; }
    .job-box { background: #eff6ff; border: 1px solid #93c5fd; border-radius: 5px; padding: 10px 12px; margin-bottom: 10px; }
    .job-box h3 { font-size: 10px; color: #1d4ed8; margin-bottom: 6px; }
    .job-cat { margin-bottom: 5px; }
    .job-cat-t { font-size: 9px; font-weight: 700; color: #1e40af; margin-bottom: 2px; }
    .job-tag { display: inline-block; background: #dbeafe; color: #1e40af; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: 600; margin: 1px; }
    .job-env { margin-top: 6px; padding: 6px 8px; background: #f0f9ff; border-radius: 3px; font-size: 9px; }
    .job-env strong { color: #0369a1; }
    .match-note { font-size: 8px; color: #6b7280; margin-top: 6px; font-style: italic; }
    .detail-table { width: 100%; border-collapse: collapse; font-size: 8px; }
    .detail-table th { background: #0d9488; color: #fff; padding: 4px 3px; text-align: center; font-weight: 600; }
    .detail-table td { padding: 3px; border: 1px solid #d1d5db; text-align: center; }
    .detail-table tr:nth-child(even) td { background: #f8fafc; }
    .bm { background: #059669; color: #fff; padding: 1px 4px; border-radius: 2px; font-size: 7px; font-weight: 600; }
    .bl { background: #dc2626; color: #fff; padding: 1px 4px; border-radius: 2px; font-size: 7px; font-weight: 600; }
    .footer { text-align: center; font-size: 8px; color: #94a3b8; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; }
    .page-break { page-break-before: always; }
  </style>
</head>
<body>

  <div class="header-bar">
    <span class="conf">CONFIDENTIAL</span>
    <h1>LAPORAN HASIL TES DISC</h1>
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
      $dData = ($response->responses ?? [])['disc_' . $subTest->id] ?? null;
      $scores = $dData['scores'] ?? ['D'=>['most'=>0,'least'=>0],'I'=>['most'=>0,'least'=>0],'S'=>['most'=>0,'least'=>0],'C'=>['most'=>0,'least'=>0]];
      $profileType = $dData['profile_type'] ?? '-';
      $answers = $dData['answers'] ?? [];
      $dM = $scores['D']['most'] ?? 0; $iM = $scores['I']['most'] ?? 0;
      $sM = $scores['S']['most'] ?? 0; $cM = $scores['C']['most'] ?? 0;
      $dL = $scores['D']['least'] ?? 0; $iL = $scores['I']['least'] ?? 0;
      $sL = $scores['S']['least'] ?? 0; $cL = $scores['C']['least'] ?? 0;
      $maxScore = max($dM, $iM, $sM, $cM, 1);
      $profileNames = ['D'=>'Dominance','I'=>'Influence','S'=>'Steadiness','C'=>'Compliance'];
      $profileDescriptions = [
        'D'=>'Tegas, berorientasi hasil, kompetitif, dan suka memimpin.',
        'I'=>'Antusias, optimis, komunikatif, dan suka bersosialisasi.',
        'S'=>'Sabar, stabil, loyal, dan kooperatif.',
        'C'=>'Teliti, analitis, sistematis, dan berorientasi kualitas.',
      ];
      $primaryType = strlen($profileType) > 0 ? substr($profileType, 0, 1) : 'D';
      $primaryName = $profileNames[$primaryType] ?? 'Unknown';
      $primaryDesc = $profileDescriptions[$primaryType] ?? '';
    @endphp

    <div class="section-title">Profil Kepribadian</div>
    <div class="profile-box">
      <div class="pt">{{ $profileType }}</div>
      <div class="pn">{{ $primaryName }}</div>
      <div class="pd">{{ $primaryDesc }}</div>
    </div>

    <div class="section-title">Skor DISC (Most)</div>
    <table class="metrics-grid">
      <tr>
        <td class="md"><div class="mv">{{ $dM }}</div><div class="ml">Dominance (D)</div></td>
        <td class="mi"><div class="mv">{{ $iM }}</div><div class="ml">Influence (I)</div></td>
        <td class="ms"><div class="mv">{{ $sM }}</div><div class="ml">Steadiness (S)</div></td>
        <td class="mc"><div class="mv">{{ $cM }}</div><div class="ml">Compliance (C)</div></td>
      </tr>
    </table>

    {{-- GD Radar Chart --}}
    <div class="graph-box">
      <div class="gt">Grafik Profil DISC</div>
      @php
        $imgW = 400; $imgH = 300;
        $cxR = (int)($imgW / 2); $cyR = (int)($imgH / 2);
        $maxR = 110;
        $img = imagecreatetruecolor($imgW, $imgH);
        imagesavealpha($img, true); imagealphablending($img, true); imageantialias($img, true);
        $bg = imagecolorallocate($img, 250, 250, 254);
        $gc = imagecolorallocate($img, 226, 232, 240);
        $gb = imagecolorallocate($img, 203, 213, 225);
        $tl = imagecolorallocate($img, 13, 148, 136);
        $tf = imagecolorallocatealpha($img, 13, 148, 136, 90);
        $mt = imagecolorallocate($img, 148, 163, 184);
        $cD = imagecolorallocate($img, 220, 38, 38);
        $cI = imagecolorallocate($img, 217, 119, 6);
        $cS = imagecolorallocate($img, 5, 150, 105);
        $cC = imagecolorallocate($img, 37, 99, 235);
        $wt = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        $ax = [-90, 0, 90, 180]; $lb = ['D','I','S','C']; $ac = [$cD,$cI,$cS,$cC]; $av = [$dM,$iM,$sM,$cM];
        for ($g = 1; $g <= 4; $g++) {
          $gr = (int)round($maxR * $g / 4); $gp = [];
          foreach ($ax as $a) { $r = deg2rad($a); $gp[] = (int)round($cxR+$gr*cos($r)); $gp[] = (int)round($cyR+$gr*sin($r)); }
          imagepolygon($img, $gp, $g===4?$gb:$gc);
          imagestring($img, 1, $cxR+3, $cyR-$gr-8, (string)round(($maxScore/4)*$g), $mt);
        }
        foreach ($ax as $a) { $r = deg2rad($a); imageline($img, $cxR, $cyR, (int)round($cxR+$maxR*cos($r)), (int)round($cyR+$maxR*sin($r)), $gc); }
        $pp = []; $dd = [];
        foreach ($ax as $i => $a) {
          $r = deg2rad($a); $sr = $maxR*($av[$i]/max($maxScore,1));
          $px = (int)round($cxR+$sr*cos($r)); $py = (int)round($cyR+$sr*sin($r));
          $pp[] = $px; $pp[] = $py; $dd[] = [$px, $py];
        }
        imagefilledpolygon($img, $pp, $tf);
        imagesetthickness($img, 2); imagepolygon($img, $pp, $tl); imagesetthickness($img, 1);
        foreach ($dd as $i => $d) {
          imagefilledellipse($img, $d[0], $d[1], 10, 10, $ac[$i]);
          imagefilledellipse($img, $d[0], $d[1], 5, 5, $wt);
          $vs = (string)$av[$i]; imagestring($img, 3, $d[0]-(int)(strlen($vs)*imagefontwidth(3)/2), $d[1]-16, $vs, $ac[$i]);
        }
        foreach ($ax as $i => $a) {
          $r = deg2rad($a); $lx = (int)round($cxR+($maxR+18)*cos($r)); $ly = (int)round($cyR+($maxR+18)*sin($r));
          $fw = strlen($lb[$i])*imagefontwidth(4);
          imagestring($img, 4, $lx-(int)($fw/2), $ly-(int)(imagefontheight(4)/2), $lb[$i], $ac[$i]);
        }
        ob_start(); imagepng($img); $pngD = ob_get_clean(); imagedestroy($img);
        $discChart = 'data:image/png;base64,' . base64_encode($pngD);
      @endphp
      <img src="{{ $discChart }}" width="340" style="display:inline-block;">
    </div>

    <div class="section-title">Skor Least</div>
    <table class="metrics-grid">
      <tr>
        <td class="md"><div class="mv">{{ $dL }}</div><div class="ml">D (Least)</div></td>
        <td class="mi"><div class="mv">{{ $iL }}</div><div class="ml">I (Least)</div></td>
        <td class="ms"><div class="mv">{{ $sL }}</div><div class="ml">S (Least)</div></td>
        <td class="mc"><div class="mv">{{ $cL }}</div><div class="ml">C (Least)</div></td>
      </tr>
    </table>

    <div class="section-title">Interpretasi Hasil</div>
    <div class="interp-box">
      <h3>Analisis Profil Kepribadian DISC</h3>
      <div class="interp-item">
        <span class="interp-label id">1. Dominance ({{ $dM }}/24):</span>
        @if($dM >= 16) Sangat Tinggi — sangat tegas, kompetitif, dan berorientasi pada hasil.
        @elseif($dM >= 10) Sedang — memiliki ketegasan yang cukup, bisa memimpin saat dibutuhkan.
        @elseif($dM >= 5) Rendah — lebih kooperatif dan menghindari konfrontasi langsung.
        @else Sangat Rendah — cenderung mengikuti arahan orang lain.
        @endif
      </div>
      <div class="interp-item">
        <span class="interp-label ii">2. Influence ({{ $iM }}/24):</span>
        @if($iM >= 16) Sangat Tinggi — sangat komunikatif dan pandai mempengaruhi orang lain.
        @elseif($iM >= 10) Sedang — cukup komunikatif dan bisa membangun hubungan baik.
        @elseif($iM >= 5) Rendah — lebih pendiam dan selektif dalam berinteraksi.
        @else Sangat Rendah — sangat introvert dan memilih bekerja mandiri.
        @endif
      </div>
      <div class="interp-item">
        <span class="interp-label is">3. Steadiness ({{ $sM }}/24):</span>
        @if($sM >= 16) Sangat Tinggi — sangat sabar, stabil, dan dapat diandalkan.
        @elseif($sM >= 10) Sedang — keseimbangan baik antara stabilitas dan fleksibilitas.
        @elseif($sM >= 5) Rendah — menyukai perubahan dan variasi, mudah beradaptasi.
        @else Sangat Rendah — sangat dinamis dan cepat bosan dengan rutinitas.
        @endif
      </div>
      <div class="interp-item">
        <span class="interp-label ic">4. Compliance ({{ $cM }}/24):</span>
        @if($cM >= 16) Sangat Tinggi — sangat teliti, analitis, dan berorientasi pada kualitas.
        @elseif($cM >= 10) Sedang — ketelitian baik, bisa terstruktur namun juga fleksibel.
        @elseif($cM >= 5) Rendah — lebih fleksibel dan tidak terlalu terikat aturan.
        @else Sangat Rendah — sangat fleksibel, fokus pada gambaran besar.
        @endif
      </div>
    </div>

    @php
      $secondaryType = strlen($profileType) > 1 ? substr($profileType, 1, 1) : null;
      $conclusions = [
        'D' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil dominan bertipe <strong>Dominance (D)</strong> dengan skor ' . $dM . '/24. Individu tipe ini memiliki dorongan kuat untuk mencapai hasil, mengambil keputusan cepat, dan mampu memimpin dalam situasi menantang. Dalam lingkungan kerja, tipe D unggul saat diberikan otonomi dan tantangan, namun perlu mengembangkan kesabaran serta kepekaan terhadap rekan kerja.',
        'I' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil dominan bertipe <strong>Influence (I)</strong> dengan skor ' . $iM . '/24. Individu tipe ini memiliki kemampuan komunikasi sangat baik, antusiasme tinggi, dan mampu membangun hubungan interpersonal positif. Dalam lingkungan kerja, tipe I sangat efektif dalam peran interaksi sosial, namun perlu mengembangkan fokus pada detail dan konsistensi.',
        'S' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil dominan bertipe <strong>Steadiness (S)</strong> dengan skor ' . $sM . '/24. Individu tipe ini memiliki loyalitas tinggi, kesabaran luar biasa, dan kemampuan menciptakan stabilitas tim. Dalam organisasi, tipe S menjadi penopang tim yang berharga, namun perlu didorong untuk lebih terbuka terhadap perubahan.',
        'C' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil dominan bertipe <strong>Compliance (C)</strong> dengan skor ' . $cM . '/24. Individu tipe ini memiliki standar kualitas tinggi, pola pikir analitis, dan ketelitian dalam setiap pekerjaan. Dalam lingkungan kerja, tipe C sangat handal dalam peran presisi, namun perlu mengembangkan fleksibilitas.',
      ];
      $secTraits = ['D'=>'ketegasan dari Dominance','I'=>'komunikasi dari Influence','S'=>'stabilitas dari Steadiness','C'=>'ketelitian dari Compliance'];
      $secNote = ($secondaryType && $secondaryType !== $primaryType) ? ' Diperkuat sentuhan ' . ($secTraits[$secondaryType] ?? '') . ', membentuk kombinasi <strong>'.$profileType.'</strong>.' : '';
      $conclusionText = ($conclusions[$primaryType] ?? $conclusions['D']) . $secNote;
      $jobMatches = [
        'D'=>['Sangat Sesuai'=>['CEO/Direktur','Manajer Proyek','Entrepreneur','Sales Manager','Operations Director','Business Development'],'Sesuai'=>['Team Leader','Manajer Produksi','Pengacara','Manajer Logistik','Supervisor','Risk Manager']],
        'I'=>['Sangat Sesuai'=>['PR Manager','Marketing Manager','Account Executive','Event Organizer','Trainer','Brand Manager'],'Sesuai'=>['HR Recruitment','CRM','Sales Representative','Content Creator','MC/Presenter']],
        'S'=>['Sangat Sesuai'=>['HR & People Dev','CS Manager','Admin Manager','Konselor/Psikolog','Healthcare','Social Worker'],'Sesuai'=>['Staff Admin','Guru/Pengajar','Lab Technician','Quality Assurance','Technical Support']],
        'C'=>['Sangat Sesuai'=>['Data Analyst','Akuntan/Auditor','Software Engineer','QC Manager','Research Analyst','Compliance Officer'],'Sesuai'=>['Programmer','Financial Planner','Arsiparis','Lab Analyst','Statistician']],
      ];
      $matchData = $jobMatches[$primaryType] ?? $jobMatches['D'];
      $envRec = ['D'=>'Kompetitif, cepat berubah, otonomi tinggi, target terukur.','I'=>'Kolaboratif, dinamis, banyak interaksi sosial, ruang kreativitas.','S'=>'Stabil, terstruktur, harmonis, prosedur jelas.','C'=>'Terorganisir, berbasis data, standar kualitas tinggi.'];
      $envNote = $envRec[$primaryType] ?? '';
    @endphp

    <div class="section-title">Kesimpulan</div>
    <div class="concl-box">
      <h3>Ringkasan Profil {{ $response->participant_name }}</h3>
      <p>{!! $conclusionText !!}</p>
    </div>

    <div class="section-title">Rekomendasi Kesesuaian Jabatan</div>
    <div class="job-box">
      <h3>Posisi Direkomendasikan &mdash; Profil {{ $profileType }} ({{ $primaryName }})</h3>
      @foreach($matchData as $category => $jobs)
      <div class="job-cat">
        <div class="job-cat-t">{{ $category }}:</div>
        <div>@foreach($jobs as $j)<span class="job-tag">{{ $j }}</span>@endforeach</div>
      </div>
      @endforeach
      <div class="job-env"><strong>Lingkungan Kerja Ideal:</strong> {{ $envNote }}</div>
      <div class="match-note">* Rekomendasi berdasarkan teori DISC oleh William Moulton Marston. Bersifat indikatif.</div>
    </div>

    @if(count($answers) > 0)
    </div>
    <div class="page-break"></div>
    <div class="header-bar">
      <span class="conf">CONFIDENTIAL</span>
      <h1>DETAIL JAWABAN &mdash; {{ $response->participant_name }}</h1>
      <p>{{ $bank->title }} &mdash; {{ $subTest->title }}</p>
    </div>
    <div class="content">

    <div class="section-title">Detail Jawaban Per Grup</div>
    <table class="detail-table">
      <thead>
        <tr>
          <th style="width:28px;">Grup</th>
          <th>Pernyataan D</th>
          <th>Pernyataan I</th>
          <th>Pernyataan S</th>
          <th>Pernyataan C</th>
          <th style="width:36px;">Most</th>
          <th style="width:36px;">Least</th>
        </tr>
      </thead>
      <tbody>
        @php $discConfig = $subTest->disc_config ?? []; $questions = $discConfig['questions'] ?? []; @endphp
        @foreach($answers as $ai => $ans)
        @php
          $q = $questions[$ai] ?? null;
          $stmts = ['D'=>'','I'=>'','S'=>'','C'=>''];
          if ($q) { foreach ($q['statements'] as $s) { $stmts[$s['trait']] = $s['text']; } }
        @endphp
        <tr>
          <td>{{ $ai + 1 }}</td>
          <td style="text-align:left;font-size:7px;">{{ $stmts['D'] }}</td>
          <td style="text-align:left;font-size:7px;">{{ $stmts['I'] }}</td>
          <td style="text-align:left;font-size:7px;">{{ $stmts['S'] }}</td>
          <td style="text-align:left;font-size:7px;">{{ $stmts['C'] }}</td>
          <td>@if($ans['most'] ?? null)<span class="bm">{{ $ans['most'] }}</span>@else -@endif</td>
          <td>@if($ans['least'] ?? null)<span class="bl">{{ $ans['least'] }}</span>@else -@endif</td>
        </tr>
        @endforeach
        <tr style="background:#f0fdfa;font-weight:700;">
          <td colspan="5" style="text-align:right;">TOTAL MOST / LEAST</td>
          <td>{{ $dM+$iM+$sM+$cM }}</td>
          <td>{{ $dL+$iL+$sL+$cL }}</td>
        </tr>
      </tbody>
    </table>
    @endif

    <div class="footer">
      Dokumen digenerate otomatis oleh Sistem HRIS &mdash; Tanggal cetak: {{ now()->format('d/m/Y H:i') }}
      | &copy; {{ date('Y') }} Shindengen HR Internal Team
    </div>
    <div style="font-size:7px; color:#9ca3af; line-height:1.4; padding:4px 0 0 0; text-align:center;">
      Hasil tes ini digenerate berdasarkan teori DISC oleh William Moulton Marston (1928) dalam <em>Emotions of Normal People</em>.
      Hasil bersifat indikatif dan dimaksudkan sebagai alat bantu asesmen, bukan sebagai diagnosis psikologis.
      Interpretasi lebih lanjut disarankan dilakukan oleh psikolog profesional berlisensi.
    </div>

  </div>
</body>
</html>
