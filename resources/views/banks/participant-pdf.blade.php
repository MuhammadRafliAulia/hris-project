<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Hasil Tes - {{ $response->participant_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }

    .header { background: #003e6f; color: #fff; padding: 20px 30px; }
    .header h1 { font-size: 18px; margin-bottom: 4px; }
    .header p { font-size: 11px; opacity: 0.85; }

    .content { padding: 24px 30px; }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .info-table td { padding: 6px 10px; border: 1px solid #d1d5db; font-size: 11px; }
    .info-table .label { background: #f1f5f9; font-weight: 600; width: 180px; color: #334155; }
    .info-table .value { color: #1e293b; }

    .score-box { background: #f0f9ff; border: 2px solid #003e6f; border-radius: 6px; padding: 16px; text-align: center; margin-bottom: 24px; }
    .score-box .score-number { font-size: 28px; font-weight: 700; color: #003e6f; }
    .score-box .score-label { font-size: 11px; color: #64748b; margin-top: 4px; }
    .score-box .percentage { font-size: 14px; font-weight: 600; margin-top: 6px; }
    .score-good { color: #065f46; }
    .score-ok { color: #92400e; }
    .score-poor { color: #991b1b; }

    .section-title { font-size: 13px; font-weight: 700; color: #003e6f; margin: 20px 0 10px 0; padding-bottom: 6px; border-bottom: 2px solid #003e6f; }

    .answers-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .answers-table th { background: #003e6f; color: #fff; padding: 8px 10px; font-size: 11px; text-align: left; font-weight: 600; }
    .answers-table td { padding: 7px 10px; border: 1px solid #d1d5db; font-size: 11px; vertical-align: top; }
    .answers-table tr:nth-child(even) td { background: #f8fafc; }
    .correct { color: #065f46; font-weight: 600; }
    .incorrect { color: #991b1b; font-weight: 600; }

    .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 30px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
    .watermark { position: fixed; top: 10px; right: 10px; font-size: 14px; font-weight: 700; color: #dc2626; opacity: 1; z-index: 999; pointer-events: none; }
  </style>
</head>
<body>
  <div class="watermark">CONFIDENTIAL</div>

  <div class="header">
    <h1>LAPORAN HASIL TES PSIKOTEST</h1>
    <p>{{ $bank->title }}</p>
  </div>

  <div class="content">

    {{-- Informasi Peserta --}}
    <div class="section-title">INFORMASI PESERTA</div>
    <table class="info-table">
      <tr>
        <td class="label">Nama Peserta</td>
        <td class="value">{{ $response->participant_name }}</td>
      </tr>
      <tr>
        <td class="label">NIK</td>
        <td class="value">{{ $response->nik ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Departemen</td>
        <td class="value">{{ $response->department ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Jabatan</td>
        <td class="value">{{ $response->position ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Email</td>
        <td class="value">{{ $response->participant_email ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">No. Telepon</td>
        <td class="value">{{ $response->phone ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Waktu Mulai</td>
        <td class="value">{{ $response->started_at ? $response->started_at->format('d/m/Y H:i:s') : '-' }}</td>
      </tr>
      <tr>
        <td class="label">Waktu Selesai</td>
        <td class="value">{{ $response->completed_at ? $response->completed_at->format('d/m/Y H:i:s') : '-' }}</td>
      </tr>
      @if($response->started_at && $response->completed_at)
      <tr>
        <td class="label">Durasi Pengerjaan</td>
        <td class="value">{{ $response->started_at->diff($response->completed_at)->format('%H jam %I menit %S detik') }}</td>
      </tr>
      @endif
    </table>

    {{-- Skor --}}
    @php
      $scoreableCount = $questions->whereNotIn('type', ['narrative', 'survey'])->count();
      $scorePct = $scoreableCount > 0 ? round(($response->score / $scoreableCount) * 100, 2) : 0;
    @endphp
    <div class="score-box">
      <div class="score-number">{{ $response->score }} / {{ $scoreableCount }}</div>
      <div class="score-label">Jawaban Benar dari {{ $scoreableCount }} Soal yang Dinilai{{ $totalQuestions > $scoreableCount ? ' (+ ' . ($totalQuestions - $scoreableCount) . ' soal narasi/survei)' : '' }}</div>
      <div class="percentage {{ $scorePct >= 70 ? 'score-good' : ($scorePct >= 50 ? 'score-ok' : 'score-poor') }}">
        {{ $scorePct }}%
        @if($scorePct >= 70) - BAIK @elseif($scorePct >= 50) - CUKUP @else - KURANG @endif
      </div>
    </div>

    {{-- Detail Jawaban --}}
    <div class="section-title">DETAIL JAWABAN</div>
    <table class="answers-table">
      <thead>
        <tr>
          <th style="width:35px;">No</th>
          <th>Pertanyaan</th>
          <th style="width:80px;">Tipe</th>
          <th style="width:100px;">Jawaban Peserta</th>
          <th style="width:100px;">Jawaban Benar</th>
          <th style="width:60px;">Hasil</th>
        </tr>
      </thead>
      <tbody>
        @foreach($questions as $index => $question)
          @php
            $userAnswer = $answers[$question->id] ?? '-';
            if ($question->type === 'narrative' || $question->type === 'survey') {
                $correctAnswer = '-';
                $isCorrect = null;
            } elseif ($question->type === 'text') {
                $correctAnswer = $question->correct_answer_text;
                $isCorrect = strtolower(trim((string)$userAnswer)) === strtolower(trim($correctAnswer));
            } else {
                $correctAnswer = $question->correct_answer;
                $isCorrect = $userAnswer === $correctAnswer;
            }
          @endphp
          <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>{{ \Illuminate\Support\Str::limit($question->question, 80) }}</td>
            <td>{{ $question->type === 'narrative' ? 'Narasi' : ($question->type === 'survey' ? 'Survei' : ($question->type === 'text' ? 'Isian' : 'Pilihan Ganda')) }}</td>
            <td>{{ in_array($question->type, ['narrative', 'survey']) ? \Illuminate\Support\Str::limit((string)$userAnswer, 60) : ($userAnswer ?? '-') }}</td>
            <td>{{ $correctAnswer }}</td>
            <td style="text-align:center;" class="{{ $isCorrect === null ? '' : ($isCorrect ? 'correct' : 'incorrect') }}">
              @if($isCorrect === null)
                <span style="color:#64748b;">—</span>
              @else
                {{ $isCorrect ? '✓ Benar' : '✗ Salah' }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="footer">
      Dokumen ini digenerate secara otomatis oleh Sistem Psikotest Online<br>
      Tanggal cetak: {{ now()->format('d/m/Y H:i:s') }} | copyright &copy;2026 Shindengen HR Internal Team
    </div>
  </div>

</body>
</html>
