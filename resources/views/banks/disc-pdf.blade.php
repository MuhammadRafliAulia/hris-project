<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan DISC - {{ $response->participant_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
    .header { background: #0d9488; color: #fff; padding: 18px 30px; }
    .header h1 { font-size: 16px; margin-bottom: 2px; }
    .header p { font-size: 10px; opacity: 0.85; }
    .content { padding: 20px 30px; }
    .section-title { font-size: 12px; font-weight: 700; color: #0d9488; margin: 16px 0 8px 0; padding-bottom: 4px; border-bottom: 2px solid #14b8a6; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 5px 10px; border: 1px solid #d1d5db; font-size: 10px; }
    .info-table .label { background: #f0fdfa; font-weight: 600; width: 160px; color: #0d9488; }
    .info-table .value { color: #1e293b; }

    .metrics-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .metrics-grid td { padding: 12px; text-align: center; border: 1px solid #e2e8f0; width: 25%; }
    .metrics-grid .metric-val { font-size: 22px; font-weight: 800; }
    .metrics-grid .metric-label { font-size: 9px; color: #64748b; margin-top: 2px; }
    .metric-d { color: #dc2626; background: #fef2f2; }
    .metric-i { color: #d97706; background: #fffbeb; }
    .metric-s { color: #059669; background: #ecfdf5; }
    .metric-c { color: #2563eb; background: #eff6ff; }

    .profile-box { background: #f0fdfa; border: 2px solid #14b8a6; border-radius: 8px; padding: 14px; margin-bottom: 16px; text-align: center; }
    .profile-box .profile-type { font-size: 28px; font-weight: 800; color: #0d9488; }
    .profile-box .profile-desc { font-size: 10px; color: #374151; margin-top: 4px; }

    .graph-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 16px; background: #fafafe; }
    .graph-title { font-size: 11px; font-weight: 700; color: #0d9488; margin-bottom: 8px; }

    .interpretation-box { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 6px; padding: 12px 14px; margin-bottom: 16px; }
    .interpretation-box h3 { font-size: 11px; color: #0d9488; margin-bottom: 6px; }
    .interpretation-box p { font-size: 10px; color: #374151; margin-bottom: 4px; }
    .interp-item { padding: 4px 0; }
    .interp-label { font-weight: 700; }

    .conclusion-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 14px 16px; margin-bottom: 16px; }
    .conclusion-box h3 { font-size: 12px; color: #0d9488; margin-bottom: 8px; }
    .conclusion-box p { font-size: 10px; color: #1e293b; line-height: 1.7; text-align: justify; }

    .job-match-box { background: #eff6ff; border: 1px solid #93c5fd; border-radius: 6px; padding: 14px 16px; margin-bottom: 16px; }
    .job-match-box h3 { font-size: 12px; color: #1d4ed8; margin-bottom: 8px; }
    .job-match-box .job-category { margin-bottom: 8px; }
    .job-match-box .job-category-title { font-size: 10px; font-weight: 700; color: #1e40af; margin-bottom: 3px; }
    .job-match-box .job-list { font-size: 10px; color: #374151; line-height: 1.6; }
    .job-match-box .job-tag { display: inline-block; background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: 600; margin: 2px 2px; }
    .job-match-box .match-note { font-size: 9px; color: #6b7280; margin-top: 8px; font-style: italic; }
    .interp-d { color: #dc2626; }
    .interp-i { color: #d97706; }
    .interp-s { color: #059669; }
    .interp-c { color: #2563eb; }

    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 9px; }
    .detail-table th { background: #0d9488; color: #fff; padding: 5px 4px; text-align: center; font-weight: 600; }
    .detail-table td { padding: 4px; border: 1px solid #d1d5db; text-align: center; }
    .detail-table tr:nth-child(even) td { background: #f8fafc; }
    .badge-m { background: #059669; color: #fff; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: 600; }
    .badge-l { background: #dc2626; color: #fff; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: 600; }

    .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
    .page-break { page-break-before: always; }
  .watermark { position: fixed; top: 10px; right: 10px; font-size: 14px; font-weight: 700; color: #dc2626; opacity: 1; z-index: 999; pointer-events: none; }
 </style>
</head>
<body>
  <div class="watermark">CONFIDENTIAL</div>
    <h1>LAPORAN HASIL TES DISC</h1>
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

    {{-- Scores --}}
    @php
      $dData = ($response->responses ?? [])['disc_' . $subTest->id] ?? null;
      $scores = $dData['scores'] ?? ['D' => ['most' => 0, 'least' => 0], 'I' => ['most' => 0, 'least' => 0], 'S' => ['most' => 0, 'least' => 0], 'C' => ['most' => 0, 'least' => 0]];
      $profileType = $dData['profile_type'] ?? '-';
      $answers = $dData['answers'] ?? [];
      $dM = $scores['D']['most'] ?? 0;
      $iM = $scores['I']['most'] ?? 0;
      $sM = $scores['S']['most'] ?? 0;
      $cM = $scores['C']['most'] ?? 0;
      $dL = $scores['D']['least'] ?? 0;
      $iL = $scores['I']['least'] ?? 0;
      $sL = $scores['S']['least'] ?? 0;
      $cL = $scores['C']['least'] ?? 0;
      $maxScore = max($dM, $iM, $sM, $cM, 1);

      // Profile names
      $profileNames = [
        'D' => 'Dominance',
        'I' => 'Influence',
        'S' => 'Steadiness',
        'C' => 'Compliance',
      ];
      $profileDescriptions = [
        'D' => 'Tipe kepribadian yang tegas, berorientasi hasil, kompetitif, dan suka memimpin. Fokus pada pencapaian tujuan dan pengendalian situasi.',
        'I' => 'Tipe kepribadian yang antusias, optimis, komunikatif, dan suka bersosialisasi. Fokus pada hubungan interpersonal dan mempengaruhi orang lain.',
        'S' => 'Tipe kepribadian yang sabar, stabil, loyal, dan kooperatif. Fokus pada kestabilan, harmoni, dan mendukung tim.',
        'C' => 'Tipe kepribadian yang teliti, analitis, sistematis, dan berorientasi kualitas. Fokus pada akurasi, prosedur, dan standar tinggi.',
      ];
      $primaryType = strlen($profileType) > 0 ? substr($profileType, 0, 1) : 'D';
      $primaryName = $profileNames[$primaryType] ?? 'Unknown';
      $primaryDesc = $profileDescriptions[$primaryType] ?? '';
    @endphp

    {{-- Profile Type --}}
    <div class="section-title">PROFIL KEPRIBADIAN</div>
    <div class="profile-box">
      <div class="profile-type">{{ $profileType }}</div>
      <div style="font-size:12px;font-weight:600;color:#0f766e;margin-top:2px;">{{ $primaryName }}</div>
      <div class="profile-desc">{{ $primaryDesc }}</div>
    </div>

    {{-- Score Metrics --}}
    <div class="section-title">SKOR DISC (MOST)</div>
    <table class="metrics-grid">
      <tr>
        <td class="metric-d">
          <div class="metric-val">{{ $dM }}</div>
          <div class="metric-label">Dominance (D)<br>Tegas & Berorientasi Hasil</div>
        </td>
        <td class="metric-i">
          <div class="metric-val">{{ $iM }}</div>
          <div class="metric-label">Influence (I)<br>Antusias & Komunikatif</div>
        </td>
        <td class="metric-s">
          <div class="metric-val">{{ $sM }}</div>
          <div class="metric-label">Steadiness (S)<br>Sabar & Stabil</div>
        </td>
        <td class="metric-c">
          <div class="metric-val">{{ $cM }}</div>
          <div class="metric-label">Compliance (C)<br>Teliti & Sistematis</div>
        </td>
      </tr>
    </table>

    {{-- SVG Radar Chart --}}
    <div class="graph-box">
      <div class="graph-title">Grafik Profil DISC</div>
      @php
        $gW = 400; $gH = 320;
        $cx = $gW / 2; $cy = $gH / 2;
        $maxR = 120;

        // 4 axes: top=D, right=I, bottom=S, left=C
        $axes = [
          ['label' => 'D', 'angle' => -90],
          ['label' => 'I', 'angle' => 0],
          ['label' => 'S', 'angle' => 90],
          ['label' => 'C', 'angle' => 180],
        ];
        $vals = [$dM, $iM, $sM, $cM];
        $colors = ['#dc2626', '#f59e0b', '#059669', '#2563eb'];

        // Calculate polygon points
        $points = [];
        foreach ($axes as $ai => $axis) {
          $rad = deg2rad($axis['angle']);
          $r = $maxR * ($vals[$ai] / max($maxScore, 1));
          $points[] = [
            'x' => round($cx + $r * cos($rad), 1),
            'y' => round($cy + $r * sin($rad), 1),
          ];
        }
        $polyPoints = implode(' ', array_map(fn($p) => $p['x'] . ',' . $p['y'], $points));
      @endphp
      <svg width="{{ $gW }}" height="{{ $gH }}" viewBox="0 0 {{ $gW }} {{ $gH }}" style="width:100%;height:auto;">
        {{-- Grid circles --}}
        @for($gl = 1; $gl <= 4; $gl++)
          @php $gr = $maxR * ($gl / 4); @endphp
          <polygon points="{{ round($cx, 1) }},{{ round($cy - $gr, 1) }} {{ round($cx + $gr, 1) }},{{ round($cy, 1) }} {{ round($cx, 1) }},{{ round($cy + $gr, 1) }} {{ round($cx - $gr, 1) }},{{ round($cy, 1) }}"
            fill="none" stroke="#e2e8f0" stroke-width="0.5"/>
          <text x="{{ round($cx + 4, 1) }}" y="{{ round($cy - $gr + 10, 1) }}" font-size="7" fill="#94a3b8">{{ round(($maxScore / 4) * $gl) }}</text>
        @endfor

        {{-- Axis lines --}}
        @foreach($axes as $ai => $axis)
          @php
            $rad = deg2rad($axis['angle']);
            $ex = round($cx + $maxR * cos($rad), 1);
            $ey = round($cy + $maxR * sin($rad), 1);
          @endphp
          <line x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $ex }}" y2="{{ $ey }}" stroke="#cbd5e1" stroke-width="0.5"/>
          @php
            $lx = round($cx + ($maxR + 20) * cos($rad), 1);
            $ly = round($cy + ($maxR + 20) * sin($rad), 1);
          @endphp
          <text x="{{ $lx }}" y="{{ round($ly + 4, 1) }}" text-anchor="middle" font-size="10" font-weight="700" fill="{{ $colors[$ai] }}">{{ $axis['label'] }}</text>
        @endforeach

        {{-- Data polygon --}}
        <polygon points="{{ $polyPoints }}" fill="rgba(13,148,136,0.15)" stroke="#0d9488" stroke-width="2"/>

        {{-- Data points --}}
        @foreach($points as $pi => $pt)
          <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4" fill="{{ $colors[$pi] }}" stroke="#fff" stroke-width="1"/>
          <text x="{{ $pt['x'] }}" y="{{ round($pt['y'] - 8, 1) }}" text-anchor="middle" font-size="9" font-weight="700" fill="{{ $colors[$pi] }}">{{ $vals[$pi] }}</text>
        @endforeach
      </svg>
    </div>

    {{-- Least Scores --}}
    <div class="section-title">SKOR LEAST (Paling Tidak Sesuai)</div>
    <table class="metrics-grid">
      <tr>
        <td class="metric-d">
          <div class="metric-val">{{ $dL }}</div>
          <div class="metric-label">D (Least)</div>
        </td>
        <td class="metric-i">
          <div class="metric-val">{{ $iL }}</div>
          <div class="metric-label">I (Least)</div>
        </td>
        <td class="metric-s">
          <div class="metric-val">{{ $sL }}</div>
          <div class="metric-label">S (Least)</div>
        </td>
        <td class="metric-c">
          <div class="metric-val">{{ $cL }}</div>
          <div class="metric-label">C (Least)</div>
        </td>
      </tr>
    </table>

    {{-- Interpretation --}}
    <div class="section-title">INTERPRETASI HASIL</div>
    <div class="interpretation-box">
      <h3>Analisis Profil Kepribadian DISC</h3>

      <div class="interp-item">
        <span class="interp-label interp-d">1. Dominance / D (Skor: {{ $dM }}/24):</span>
        <span>
          @if($dM >= 16) Sangat Tinggi — Anda sangat tegas, kompetitif, dan suka mengambil kendali. Anda cenderung berorientasi pada hasil dan tidak takut mengambil keputusan sulit.
          @elseif($dM >= 10) Sedang — Anda memiliki ketegasan yang cukup baik. Anda bisa memimpin saat dibutuhkan namun juga tahu kapan harus berkolaborasi.
          @elseif($dM >= 5) Rendah — Anda cenderung lebih kooperatif dan menghindari konfrontasi langsung. Anda lebih suka bekerja dalam tim daripada memimpin sendiri.
          @else Sangat Rendah — Anda sangat menghindari konfrontasi dan lebih memilih mengikuti arahan orang lain.
          @endif
        </span>
      </div>

      <div class="interp-item">
        <span class="interp-label interp-i">2. Influence / I (Skor: {{ $iM }}/24):</span>
        <span>
          @if($iM >= 16) Sangat Tinggi — Anda sangat komunikatif, antusias, dan pandai mempengaruhi orang lain. Anda menikmati interaksi sosial dan membangun hubungan.
          @elseif($iM >= 10) Sedang — Anda cukup komunikatif dan bisa membangun hubungan baik. Anda nyaman berinteraksi namun juga menghargai waktu sendiri.
          @elseif($iM >= 5) Rendah — Anda cenderung lebih pendiam dan selektif dalam berinteraksi. Anda lebih fokus pada tugas daripada hubungan sosial.
          @else Sangat Rendah — Anda sangat introvert dan lebih memilih bekerja secara mandiri dengan sedikit interaksi sosial.
          @endif
        </span>
      </div>

      <div class="interp-item">
        <span class="interp-label interp-s">3. Steadiness / S (Skor: {{ $sM }}/24):</span>
        <span>
          @if($sM >= 16) Sangat Tinggi — Anda sangat sabar, stabil, dan dapat diandalkan. Anda menghargai konsistensi dan harmoni dalam lingkungan kerja.
          @elseif($sM >= 10) Sedang — Anda memiliki keseimbangan yang baik antara stabilitas dan fleksibilitas. Anda bisa beradaptasi namun juga menghargai rutinitas.
          @elseif($sM >= 5) Rendah — Anda cenderung menyukai perubahan dan variasi. Anda mudah beradaptasi tetapi mungkin kurang sabar dengan rutinitas.
          @else Sangat Rendah — Anda sangat dinamis dan cepat bosan dengan rutinitas. Anda selalu mencari tantangan dan perubahan baru.
          @endif
        </span>
      </div>

      <div class="interp-item">
        <span class="interp-label interp-c">4. Compliance / C (Skor: {{ $cM }}/24):</span>
        <span>
          @if($cM >= 16) Sangat Tinggi — Anda sangat teliti, analitis, dan berorientasi pada kualitas. Anda mematuhi standar tinggi dan prosedur yang terstruktur.
          @elseif($cM >= 10) Sedang — Anda memiliki ketelitian yang baik dan menghargai kualitas. Anda bisa bekerja secara terstruktur namun juga fleksibel saat dibutuhkan.
          @elseif($cM >= 5) Rendah — Anda cenderung lebih fleksibel dan tidak terlalu terikat pada aturan. Anda lebih suka kebebasan daripada struktur yang kaku.
          @else Sangat Rendah — Anda sangat fleksibel dan spontan. Anda tidak terlalu mementingkan detail dan lebih fokus pada gambaran besar.
          @endif
        </span>
      </div>
    </div>

    {{-- Kesimpulan --}}
    @php
      $secondaryType = strlen($profileType) > 1 ? substr($profileType, 1, 1) : null;
      $secondaryName = $secondaryType ? ($profileNames[$secondaryType] ?? '') : '';

      // Conclusions per primary type
      $conclusions = [
        'D' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil kepribadian dominan bertipe <strong>Dominance (D)</strong> dengan skor ' . $dM . '/24. Individu dengan tipe ini memiliki dorongan kuat untuk mencapai hasil, mengambil keputusan secara cepat, dan mampu memimpin dalam situasi yang menantang. Mereka cenderung langsung, tegas, dan berorientasi pada pencapaian target. Dalam lingkungan kerja, individu tipe D unggul saat diberikan otonomi dan tantangan, namun perlu mengembangkan kesabaran serta kepekaan terhadap perasaan rekan kerja agar tercipta kolaborasi yang lebih efektif.',
        'I' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil kepribadian dominan bertipe <strong>Influence (I)</strong> dengan skor ' . $iM . '/24. Individu dengan tipe ini memiliki kemampuan komunikasi yang sangat baik, antusiasme tinggi, dan secara alami mampu membangun hubungan interpersonal yang positif. Mereka cenderung optimis, persuasif, dan senang bekerja dalam tim yang dinamis. Dalam lingkungan kerja, individu tipe I sangat efektif dalam peran yang melibatkan interaksi sosial dan memotivasi orang lain, namun perlu mengembangkan fokus pada detail dan konsistensi dalam menyelesaikan tugas-tugas administratif.',
        'S' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil kepribadian dominan bertipe <strong>Steadiness (S)</strong> dengan skor ' . $sM . '/24. Individu dengan tipe ini memiliki loyalitas tinggi, kesabaran yang luar biasa, dan kemampuan untuk menciptakan stabilitas dalam tim. Mereka cenderung kooperatif, dapat diandalkan, dan berorientasi pada harmoni lingkungan kerja. Dalam organisasi, individu tipe S menjadi penopang tim yang sangat berharga karena konsistensi dan kemampuan mendengarkan mereka, namun perlu didorong untuk lebih terbuka terhadap perubahan dan menyuarakan pendapat secara asertif.',
        'C' => 'Berdasarkan hasil tes DISC, ' . $response->participant_name . ' menunjukkan profil kepribadian dominan bertipe <strong>Compliance (C)</strong> dengan skor ' . $cM . '/24. Individu dengan tipe ini memiliki standar kualitas yang tinggi, pola pikir analitis, dan ketelitian dalam setiap pekerjaan. Mereka cenderung sistematis, objektif, dan sangat memperhatikan akurasi data serta kepatuhan terhadap prosedur. Dalam lingkungan kerja, individu tipe C sangat handal dalam peran yang membutuhkan presisi dan pemecahan masalah berbasis data, namun perlu mengembangkan fleksibilitas dan kemampuan mengambil keputusan cepat dalam situasi yang ambigu.',
      ];

      $secondaryNote = '';
      if ($secondaryType && $secondaryType !== $primaryType) {
        $secTraits = [
          'D' => 'dengan sentuhan ketegasan dan orientasi hasil dari dimensi Dominance',
          'I' => 'dengan sentuhan kemampuan komunikasi dan persuasi dari dimensi Influence',
          'S' => 'dengan sentuhan stabilitas dan loyalitas dari dimensi Steadiness',
          'C' => 'dengan sentuhan ketelitian dan pendekatan analitis dari dimensi Compliance',
        ];
        $secondaryNote = ' Profil ini diperkuat ' . ($secTraits[$secondaryType] ?? '') . ', yang membentuk kombinasi unik <strong>' . $profileType . '</strong> dalam pendekatan kerja sehari-hari.';
      }

      $conclusionText = ($conclusions[$primaryType] ?? $conclusions['D']) . $secondaryNote;

      // Job matches per primary type (based on DISC occupational psychology)
      $jobMatches = [
        'D' => [
          'Sangat Sesuai' => ['CEO / Direktur', 'Manajer Proyek', 'Entrepreneur', 'Sales Manager', 'Operations Director', 'Business Development Manager', 'Konsultan Strategi'],
          'Sesuai' => ['Team Leader', 'Manajer Produksi', 'Pengacara / Litigator', 'Manajer Logistik', 'Supervisor Lapangan', 'Risk Manager'],
        ],
        'I' => [
          'Sangat Sesuai' => ['Public Relations Manager', 'Marketing Manager', 'Account Executive', 'Event Organizer', 'Trainer / Fasilitator', 'Brand Manager', 'Media Specialist'],
          'Sesuai' => ['HR Recruitment', 'Customer Relationship Manager', 'Sales Representative', 'Content Creator', 'Tour Leader', 'MC / Presenter'],
        ],
        'S' => [
          'Sangat Sesuai' => ['HR & People Development', 'Customer Service Manager', 'Admin Manager', 'Konselor / Psikolog', 'Perawat / Healthcare', 'Social Worker', 'Office Manager'],
          'Sesuai' => ['Staff Administrasi', 'Guru / Pengajar', 'Librarian', 'Lab Technician', 'Quality Assurance', 'Technical Support'],
        ],
        'C' => [
          'Sangat Sesuai' => ['Data Analyst', 'Akuntan / Auditor', 'Software Engineer', 'Quality Control Manager', 'Research Analyst', 'Compliance Officer', 'Apoteker'],
          'Sesuai' => ['Programmer / Developer', 'Drafter / Engineering', 'Financial Planner', 'Arsiparis / Dokumentasi', 'Lab Analyst', 'Statistician'],
        ],
      ];

      $matchData = $jobMatches[$primaryType] ?? $jobMatches['D'];

      // Job environment recommendation
      $envRecommendations = [
        'D' => 'Lingkungan kerja ideal: kompetitif, cepat berubah, memberikan otonomi dalam pengambilan keputusan, dan berorientasi pada pencapaian target yang terukur.',
        'I' => 'Lingkungan kerja ideal: kolaboratif, dinamis, banyak interaksi sosial, dan memberikan ruang untuk kreativitas serta pengakuan atas kontribusi.',
        'S' => 'Lingkungan kerja ideal: stabil, terstruktur, harmonis, memiliki prosedur yang jelas, dan memberikan apresiasi atas kesetiaan serta konsistensi kerja.',
        'C' => 'Lingkungan kerja ideal: terorganisir, berbasis data, memiliki standar kualitas tinggi, dan memberikan waktu yang cukup untuk analisis mendalam.',
      ];
      $envNote = $envRecommendations[$primaryType] ?? '';
    @endphp

    <div class="section-title">KESIMPULAN</div>
    <div class="conclusion-box">
      <h3>Ringkasan Profil {{ $response->participant_name }}</h3>
      <p>{!! $conclusionText !!}</p>
    </div>

    {{-- Job Match --}}
    <div class="section-title">REKOMENDASI KESESUAIAN JABATAN (JOB MATCH)</div>
    <div class="job-match-box">
      <h3>Posisi yang Direkomendasikan untuk Profil {{ $profileType }} ({{ $primaryName }})</h3>

      @foreach($matchData as $category => $jobs)
      <div class="job-category">
        <div class="job-category-title">{{ $category }}:</div>
        <div class="job-list">
          @foreach($jobs as $job)
            <span class="job-tag">{{ $job }}</span>
          @endforeach
        </div>
      </div>
      @endforeach

      <div style="margin-top:10px;padding:8px 10px;background:#f0f9ff;border-radius:4px;">
        <div style="font-size:10px;font-weight:600;color:#0369a1;margin-bottom:3px;">Lingkungan Kerja Ideal:</div>
        <div style="font-size:9px;color:#374151;">{{ $envNote }}</div>
      </div>

      <div class="match-note">
        * Rekomendasi ini disusun berdasarkan teori DISC oleh William Moulton Marston dan praktik psikologi industri-organisasi. Hasil bersifat indikatif dan sebaiknya dikombinasikan dengan asesmen kompetensi, wawancara, serta pengalaman kerja untuk keputusan penempatan yang optimal.
      </div>
    </div>

    {{-- Detail per group --}}
    @if(count($answers) > 0)
    <div class="page-break"></div>
    <div class="section-title">DETAIL JAWABAN PER GRUP</div>
    <table class="detail-table">
      <thead>
        <tr>
          <th style="width:35px;">Grup</th>
          <th>Pernyataan D</th>
          <th>Pernyataan I</th>
          <th>Pernyataan S</th>
          <th>Pernyataan C</th>
          <th style="width:50px;">Most</th>
          <th style="width:50px;">Least</th>
        </tr>
      </thead>
      <tbody>
        @php $discConfig = $subTest->disc_config ?? []; $questions = $discConfig['questions'] ?? []; @endphp
        @foreach($answers as $ai => $ans)
        @php
          $q = $questions[$ai] ?? null;
          $stmts = ['D' => '', 'I' => '', 'S' => '', 'C' => ''];
          if ($q) {
            foreach ($q['statements'] as $s) {
              $stmts[$s['trait']] = $s['text'];
            }
          }
        @endphp
        <tr>
          <td>{{ $ai + 1 }}</td>
          <td style="text-align:left;font-size:8px;">{{ $stmts['D'] }}</td>
          <td style="text-align:left;font-size:8px;">{{ $stmts['I'] }}</td>
          <td style="text-align:left;font-size:8px;">{{ $stmts['S'] }}</td>
          <td style="text-align:left;font-size:8px;">{{ $stmts['C'] }}</td>
          <td>@if($ans['most'] ?? null)<span class="badge-m">{{ $ans['most'] }}</span>@else - @endif</td>
          <td>@if($ans['least'] ?? null)<span class="badge-l">{{ $ans['least'] }}</span>@else - @endif</td>
        </tr>
        @endforeach
        <tr style="background:#f0fdfa;font-weight:700;">
          <td colspan="5" style="text-align:right;">TOTAL MOST / LEAST</td>
          <td>{{ $dM + $iM + $sM + $cM }}</td>
          <td>{{ $dL + $iL + $sL + $cL }}</td>
        </tr>
      </tbody>
    </table>
    @endif

    <div class="footer">
      Digenerate otomatis oleh HRIS — {{ now()->format('d/m/Y H:i') }}
    </div>

  </div>
</body>
</html>
