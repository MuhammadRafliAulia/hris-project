<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
 <title>Tes Psikotest</title>
 <style>
 body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; padding:20px; }
 .container { max-width:800px; margin:0 auto; }
 .card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:24px; }
 h1 { font-size:18px; color:#0f172a; margin:0 0 20px 0; }
 .info { background:#dbeafe; color:#0c4a6e; padding:12px; border-radius:6px; margin-bottom:20px; font-size:13px; }
 .error-alert { background:#fee2e2; color:#991b1b; padding:12px; border-radius:6px; margin-bottom:20px; font-size:13px; border-left:3px solid #dc2626; }
 form { display:flex; flex-direction:column; }
 .question-section { margin-top:24px; padding-top:24px; border-top:1px solid #e2e8f0; }
 .question-text { background:#f8fafc; border-left:3px solid #003e6f; padding:16px; margin-bottom:16px; color:#0f172a; font-size:14px; }
 .options { margin-bottom:24px; }
 .option { margin-bottom:12px; }
 .option label { display:flex; align-items:flex-start; margin:0; cursor:pointer; }
 .option input[type=radio] { margin-right:8px; cursor:pointer; margin-top:3px; flex-shrink:0; }
 .option label span { cursor:pointer; }
 .option-img { max-width:200px; max-height:140px; border-radius:6px; border:1px solid #e2e8f0; margin-top:6px; display:block; }
 .text-input { width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; box-sizing:border-box; }
 .text-input:focus { outline:none; border-color:#003e6f; box-shadow:0 0 0 3px rgba(0,62,111,0.1); }
 .btn-submit { background:#003e6f; color:#fff; border:none; padding:14px 20px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer; margin-top:20px; transition:background 0.2s; }
 .btn-submit:hover { background:#002a4f; }
 .btn-submit:disabled { background:#94a3b8; cursor:not-allowed; }
 .question-counter { font-size:12px; color:#64748b; margin-bottom:6px; }
 .progress-bar { background:#e2e8f0; border-radius:4px; height:6px; margin-bottom:20px; overflow:hidden; }
 .progress-fill { background:#003e6f; height:100%; border-radius:4px; transition:width 0.3s; }
 .timer-bar { background:#003e6f; color:#fff; padding:12px 20px; border-radius:8px; display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; position:sticky; top:10px; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .timer-bar .timer-label { font-size:13px; opacity:0.9; }
 .timer-bar .timer-clock { font-size:22px; font-weight:700; font-variant-numeric:tabular-nums; letter-spacing:1px; }
 .timer-bar.warning { background:#dc2626; animation: pulse 1s infinite; }
 @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.85;} }
 .copyright { text-align:center; margin-top:24px; font-size:12px; color:#64748b; }

 /* Sub-test cards */
 .subtest-cards { display:grid; grid-template-columns:1fr; gap:16px; margin:20px 0; }
 .subtest-card { background:#fff; border:2px solid #e2e8f0; border-radius:12px; padding:20px; cursor:pointer; transition:all 0.2s; position:relative; overflow:hidden; }
 .subtest-card:hover { border-color:#003e6f; box-shadow:0 4px 12px rgba(0,62,111,0.1); transform:translateY(-2px); }
 .subtest-card.completed { border-color:#10b981; background:#f0fdf4; }
 .subtest-card.active { border-color:#003e6f; background:#eff6ff; }
 .subtest-card .st-order { font-size:28px; font-weight:800; color:#cbd5e1; position:absolute; top:12px; right:16px; }
 .subtest-card.completed .st-order { color:#10b981; }
 .subtest-card .st-title { font-size:16px; font-weight:600; color:#0f172a; margin-bottom:6px; }
 .subtest-card .st-desc { font-size:12px; color:#64748b; margin-bottom:10px; line-height:1.5; }
 .subtest-card .st-meta { display:flex; gap:12px; font-size:11px; color:#94a3b8; }
 .subtest-card .st-meta span { display:flex; align-items:center; gap:4px; }
 .subtest-card .st-status { display:inline-block; font-size:10px; padding:3px 10px; border-radius:12px; font-weight:600; margin-top:8px; }
 .st-status.pending { background:#f1f5f9; color:#64748b; }
 .st-status.done { background:#d1fae5; color:#065f46; }
 .st-status.in-progress { background:#dbeafe; color:#1e40af; }

 /* Example questions screen */
 .example-screen { display:none; }
 .example-screen.show { display:block; }
 .example-header { background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff; padding:20px; border-radius:12px; margin-bottom:20px; }
 .example-header h2 { margin:0; font-size:18px; }
 .example-header p { margin:6px 0 0; opacity:0.9; font-size:13px; }
 .example-question { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:16px; margin-bottom:14px; }
 .example-question .eq-label { font-size:10px; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
 .example-answer { background:#d1fae5; border:1px solid #6ee7b7; padding:10px 14px; border-radius:6px; margin-top:10px; font-size:13px; color:#065f46; }
 .btn-start-test { background:#003e6f; color:#fff; border:none; padding:14px 24px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; }
 .btn-start-test:hover { background:#002a4f; }
 .btn-back-overview { background:#64748b; color:#fff; border:none; padding:10px 16px; border-radius:6px; font-size:13px; cursor:pointer; margin-bottom:16px; }
 .btn-back-overview:hover { background:#475569; }

 /* Test screen per subtest */
 .subtest-test-screen { display:none; }
 .subtest-test-screen.show { display:block; }
 .subtest-test-header { background:#003e6f; color:#fff; padding:14px 20px; border-radius:8px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:10px; z-index:90; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .subtest-test-header h3 { margin:0; font-size:15px; }
 .st-timer { font-size:18px; font-weight:700; font-variant-numeric:tabular-nums; letter-spacing:1px; }
 .subtest-test-header.st-warning { background:#dc2626; animation: pulse 1s infinite; }
 .btn-finish-subtest { background:#10b981; color:#fff; border:none; padding:12px 20px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; }
 .btn-finish-subtest:hover { background:#059669; }

 /* Overview screen */
 .overview-screen { display:block; }
 .overview-screen.hidden { display:none; }

 /* Anti-cheat */
 body { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; -webkit-touch-callout:none; touch-action:pan-y; }
 .text-input, textarea { -webkit-user-select:text; -moz-user-select:text; user-select:text; -webkit-touch-callout:default; }
 * { -webkit-tap-highlight-color: transparent; }
 #anti-cheat-warning {
 display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999;
 background:rgba(0,0,0,0.85); justify-content:center; align-items:center;
 }
 #anti-cheat-warning.show { display:flex; }
 #anti-cheat-warning .acw-box {
 background:#fff; border-radius:12px; padding:32px; max-width:420px; width:90%; text-align:center;
 box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:acw-pop 0.3s ease;
 }
 @keyframes acw-pop { from{transform:scale(0.8);opacity:0} to{transform:scale(1);opacity:1} }
 #anti-cheat-warning .acw-icon { font-size:48px; margin-bottom:12px; }
 #anti-cheat-warning .acw-title { font-size:18px; font-weight:700; color:#dc2626; margin-bottom:8px; }
 #anti-cheat-warning .acw-msg { font-size:13px; color:#475569; line-height:1.6; margin-bottom:16px; }
 #anti-cheat-warning .acw-count { font-size:12px; color:#991b1b; font-weight:600; margin-bottom:16px; background:#fee2e2; padding:8px 12px; border-radius:6px; }
 #anti-cheat-warning .acw-btn { background:#003e6f; color:#fff; border:none; padding:10px 24px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; }
 #anti-cheat-warning .acw-btn:hover { background:#002a4f; }
 .violation-badge { background:#dc2626; color:#fff; font-size:11px; padding:4px 10px; border-radius:20px; margin-left:8px; font-weight:600; display:none; }
 .violation-badge.show { display:inline-block; }

 /* Screen-capture content protection overlay */
 .screen-protect {
 position:fixed; top:0; left:0; width:100%; height:100%; z-index:9998;
 background:#fff; pointer-events:none; display:none;
 }
 .screen-protect.active {
 display:block; pointer-events:all;
 }
 /* Screenshot blocked notification */
 .ss-blocked-toast {
 position:fixed; top:20px; left:50%; transform:translateX(-50%);
 background:#dc2626; color:#fff; padding:10px 24px; border-radius:8px;
 font-size:13px; font-weight:600; z-index:10000; display:none;
 box-shadow:0 4px 12px rgba(0,0,0,0.3); animation:acw-pop 0.3s ease;
 }
 .ss-blocked-toast.show { display:block; }
 /* Confirmation modal styles */
 .confirm-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:none; align-items:center; justify-content:center; z-index:11000; }
 .confirm-modal-backdrop.show { display:flex; }
 .confirm-modal { background:#fff; border-radius:10px; padding:18px; width:92%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,0.25); }
 .confirm-modal h3 { margin:0 0 8px 0; font-size:18px; color:#0f172a; }
 .confirm-modal p { margin:0 0 16px 0; color:#475569; font-size:14px; }
 .confirm-actions { display:flex; gap:10px; justify-content:flex-end; }
 .confirm-btn { padding:10px 14px; border-radius:8px; font-weight:600; cursor:pointer; border:none; }
 .confirm-cancel { background:#e5e7eb; color:#0f172a; }
 .confirm-yes { background:#003e6f; color:#fff; }
 </style>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
 <!-- Screen capture protection overlay -->
 <div class="screen-protect" id="screenProtect"></div>
 <!-- Screenshot blocked toast -->
 <div class="ss-blocked-toast" id="ssBlockedToast">&#x26D4; Screenshot terdeteksi! Tindakan ini dicatat sebagai pelanggaran.</div>

<!-- Confirmation modal -->
<div class="confirm-modal-backdrop" id="confirmModal">
	<div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
		<h3 id="confirmModalTitle">Konfirmasi Pengiriman</h3>
		<p id="confirmModalMessage">Anda yakin ingin mengirim jawaban?</p>
		<div class="confirm-actions">
			<button type="button" class="confirm-btn confirm-cancel" id="confirmCancelBtn">Batal</button>
			<button type="button" class="confirm-btn confirm-yes" id="confirmYesBtn">Kirim</button>
		</div>
	</div>
</div>

 <div class="container">
 <div class="card">
 <h1>{{ $bank->title }}</h1>
 <div class="info" style="line-height:1.8;">
 Peserta: <strong>{{ $response->participant_name }}</strong><br class="mobile-br">
 NIK: {{ $response->nik }}<br class="mobile-br">
 Dept: {{ $response->department }} &middot; Jabatan: {{ $response->position }}<br class="mobile-br">
 Email: {{ $response->participant_email ?? '-' }} &middot; Telp: {{ $response->phone ?? '-' }}
 @if($bank->duration_minutes)
 <br class="mobile-br"> Waktu: {{ $bank->duration_minutes }} menit
 @endif
 </div>

 @if($remainingSeconds !== null)
 <div class="timer-bar" id="timerBar">
 <div>
 <div class="timer-label"> Sisa Waktu Pengerjaan</div>
 </div>
 <div style="display:flex;align-items:center;gap:8px;">
 <div class="timer-clock" id="timerClock">--:--:--</div>
 <span class="violation-badge" id="violationBadge"></span>
 </div>
 </div>
 @endif

 {{-- Anti-cheat notice --}}
 <div style="background:#fef3c7;color:#92400e;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:12px;border-left:3px solid #f59e0b;">
 <strong>Mode Ujian Aktif</strong> — Dilarang berpindah tab, menekan tombol home, berpindah aplikasi, copy/paste, atau meninggalkan halaman. Pelanggaran akan dicatat dan ujian dihentikan otomatis setelah 3x pelanggaran.
 </div>

 @if(session('error'))
 <div class="error-alert"> {{ session('error') }}</div>
 @endif

 @if($errors->any())
 <div class="error-alert">
 <strong>Perhatian:</strong>
 <ul style="margin:6px 0 0 0; padding-left:20px;">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 {{-- ============================================ --}}
 {{-- SUB-TEST MODE --}}
 {{-- ============================================ --}}
 @if($hasSubTests)

 <form method="POST" action="{{ route('test.submit', $response->token) }}" id="testForm">
 @csrf

 {{-- OVERVIEW SCREEN: Sub-test cards --}}
 <div id="overviewScreen" class="overview-screen">
 <div style="text-align:center;margin-bottom:16px;">
 <p style="font-size:13px;color:#64748b;margin:0;">Kerjakan seluruh sub-test di bawah ini. Klik kartu untuk memulai.</p>
 </div>

 <div class="progress-bar">
 <div class="progress-fill" id="progressFill" style="width:0%"></div>
 </div>

 <div class="subtest-cards">
 @foreach($subTests as $stIdx => $subTest)
 <div class="subtest-card" id="stCard{{ $subTest->id }}" onclick="openSubTest({{ $subTest->id }})">
 <div class="st-order">{{ $stIdx + 1 }}</div>
 <div class="st-title">{{ $subTest->title }}</div>
 @if($subTest->description)
 <div class="st-desc">{{ Str::limit($subTest->description, 100) }}</div>
 @endif
 <div class="st-meta">
 <span> {{ $subTest->questions->count() }} soal</span>
 @if($subTest->duration_minutes)
 <span> {{ $subTest->duration_minutes }} menit</span>
 @endif
 @if($subTest->exampleQuestions->count() > 0)
 <span> {{ $subTest->exampleQuestions->count() }} contoh</span>
 @endif
 </div>
 <div class="st-status pending" id="stStatus{{ $subTest->id }}">Belum Dikerjakan</div>
 </div>
 @endforeach
 </div>

 <button type="submit" class="btn-submit" id="submitBtn" style="margin-top:16px;">Selesaikan Semua Tes</button>
 </div>

 {{-- PER-SUBTEST SCREENS --}}
 @foreach($subTests as $subTest)
 {{-- Example Questions Screen --}}
 @if($subTest->exampleQuestions->count() > 0)
 <div class="example-screen" id="exampleScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali</button>
 <div class="example-header">
 <h2> Contoh Soal — {{ $subTest->title }}</h2>
 <p>Pelajari contoh soal berikut sebelum mengerjakan tes. Contoh soal ini <strong>tidak dinilai</strong>.</p>
 @if($subTest->description)
 <p style="margin-top:8px;font-style:italic;">{{ $subTest->description }}</p>
 @endif
 </div>

 @foreach($subTest->exampleQuestions as $eIdx => $eq)
 <div class="example-question">
 <div class="eq-label">Contoh {{ $eIdx + 1 }}</div>
 <div class="question-text" style="border-left-color:#f59e0b;">{{ $eq->question }}</div>

@if($eq->image_data)
<div style="margin:12px 0; text-align:center;">
<img src="{{ route('questions.image', $eq) }}" alt="Gambar contoh" style="max-width:100%; max-height:300px; border-radius:6px;">
</div>
@elseif($eq->image)
<div style="margin:12px 0; text-align:center;">
<img src="/hrissdi/storage/{{ $eq->image }}" alt="Gambar contoh" style="max-width:100%; max-height:300px; border-radius:6px;">
</div>
@endif

 @if($eq->audio)
 <div style="margin:12px 0;">
 <audio controls style="width:100%; max-width:400px;">
 <source src="/hrissdi/storage/{{ $eq->audio }}" type="audio/mpeg">
 </audio>
 </div>
 @endif

 @if($eq->type === 'multiple_choice')
 <div style="font-size:13px;">
 @foreach(['A' => $eq->option_a, 'B' => $eq->option_b, 'C' => $eq->option_c, 'D' => $eq->option_d] as $k => $opt)
 @if($opt)
 <div class="option" style="padding:6px 0;">
 <strong>{{ $k }}.</strong> {{ $opt }}
 @if($eq->correct_answer === $k) <span style="color:#10b981;font-weight:600;"> ✓ Benar</span> @endif
 </div>
 @endif
 @endforeach
 </div>
 <div class="example-answer"> Jawaban yang benar: <strong>{{ $eq->correct_answer }}</strong></div>
 @elseif($eq->type === 'text')
 <div class="example-answer"> Jawaban yang benar: <strong>{{ $eq->correct_answer_text }}</strong></div>
 @elseif($eq->type === 'survey')
 <div style="font-size:13px;">
 @php $sLabels=['A','B','C','D','E']; $sFields=['option_a','option_b','option_c','option_d','option_e']; @endphp
 @for($si=0; $si<($eq->option_count??2); $si++)
 @if($eq->{$sFields[$si]})
 <div class="option" style="padding:6px 0;"><strong>{{ $sLabels[$si] }}.</strong> {{ $eq->{$sFields[$si]} }}</div>
 @endif
 @endfor
 </div>
 @endif
 </div>
 @endforeach

 <button type="button" class="btn-start-test" onclick="startSubTestQuestions({{ $subTest->id }})">
 Mulai Tes {{ $subTest->title }} →
 </button>
 </div>
 @endif

 {{-- Real Test Questions Screen --}}
 <div class="subtest-test-screen" id="testScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali ke Daftar Sub-Test</button>
 <div class="subtest-test-header" id="stHeader{{ $subTest->id }}">
 <div>
 <h3 style="margin:0;">{{ $subTest->title }}</h3>
 <span style="font-size:11px;opacity:0.8;">{{ $subTest->questions->count() }} soal</span>
 </div>
 @if($subTest->duration_minutes)
 <div class="st-timer" id="stTimer{{ $subTest->id }}" data-duration="{{ $subTest->duration_minutes }}">{{ sprintf('%02d', $subTest->duration_minutes) }}:00</div>
 @endif
 </div>

 @if($subTest->description)
 <div style="background:#f1f5f9;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:12px;color:#475569;">
 {{ $subTest->description }}
 </div>
 @endif

 <div class="question-section" style="border-top:none;margin-top:0;padding-top:0;">
 @foreach($subTest->questions as $qIdx => $question)
 <div class="question-block" data-question="{{ $question->id }}" data-subtest="{{ $subTest->id }}">
 <div class="question-counter">Pertanyaan {{ $qIdx + 1 }} dari {{ $subTest->questions->count() }}</div>
 <div class="question-text">{{ $question->question }}</div>

@if($question->image_data)
<div style="margin:12px 0; text-align:center;">
<img src="{{ route('questions.image', $question) }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@elseif($question->image)
<div style="margin:12px 0; text-align:center;">
<img src="/hrissdi/storage/{{ $question->image }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@endif

 @if($question->audio)
 <div style="margin:12px 0;">
 <audio controls style="width:100%; max-width:400px;">
 <source src="/hrissdi/storage/{{ $question->audio }}" type="audio/mpeg">
 Browser Anda tidak mendukung audio player.
 </audio>
 </div>
 @endif

 @if($question->type === 'text')
 <div style="margin-bottom:24px;">
 <input type="text" name="answers[{{ $question->id }}]" class="text-input answer-input" placeholder="Masukkan jawaban Anda...">
 </div>
 @elseif($question->type === 'narrative')
 <div style="margin-bottom:24px;">
 <textarea name="answers[{{ $question->id }}]" class="text-input answer-input" rows="5" placeholder="Tulis jawaban narasi Anda..." style="resize:vertical; min-height:100px;"></textarea>
 </div>
 @elseif($question->type === 'survey')
 <div class="options">
 @php
 $surveyLabels = ['A','B','C','D','E'];
 $surveyFields = ['option_a','option_b','option_c','option_d','option_e'];
 $optCount = $question->option_count ?? 2;
 @endphp
 @for($si = 0; $si < $optCount; $si++)
 @if($question->{$surveyFields[$si]})
 <div class="option">
 <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $surveyLabels[$si] }}" class="answer-input" style="width:16px; height:16px; accent-color:#003e6f;">
 <span>{{ $question->{$surveyFields[$si]} }}</span>
 </label>
 </div>
 @endif
 @endfor
 </div>
 @else
 <div class="options">
 @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d] as $key => $option)
 @php $optImgField = 'option_' . strtolower($key) . '_image'; @endphp
 <div class="option">
 <label>
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="answer-input">
 <span>
 <strong>{{ $key }}.</strong> {{ $option }}
 @if($question->$optImgField)
 <img src="/hrissdi/storage/{{ $question->$optImgField }}" alt="Opsi {{ $key }}" class="option-img">
 @endif
 </span>
 </label>
 </div>
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>

 <button type="button" class="btn-finish-subtest" onclick="finishSubTest({{ $subTest->id }})">
 Selesai — Kembali ke Daftar Sub-Test
 </button>
 </div>
 @endforeach
 </form>

 {{-- ============================================ --}}
 {{-- LEGACY MODE (no sub-tests) --}}
 {{-- ============================================ --}}
 @else
 @if($questions->count() > 0)
 <div class="progress-bar">
 <div class="progress-fill" id="progressFill" style="width:0%"></div>
 </div>

 <form method="POST" action="{{ route('test.submit', $response->token) }}" id="testForm">
 @csrf

 <div class="question-section">
 @foreach($questions as $index => $question)
 <div class="question-block" data-question="{{ $question->id }}">
 <div class="question-counter">Pertanyaan {{ $index + 1 }} dari {{ $questions->count() }}</div>
 <div class="question-text">{{ $question->question }}</div>

@if($question->image_data)
<div style="margin:12px 0; text-align:center;">
<img src="{{ route('questions.image', $question) }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@elseif($question->image)
<div style="margin:12px 0; text-align:center;">
<img src="/hrissdi/storage/{{ $question->image }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@endif

 @if($question->audio)
 <div style="margin:12px 0;">
 <audio controls style="width:100%; max-width:400px;">
 <source src="/hrissdi/storage/{{ $question->audio }}" type="audio/mpeg">
 Browser Anda tidak mendukung audio player.
 </audio>
 </div>
 @endif

 @if($question->type === 'text')
 <div style="margin-bottom:24px;">
 <input type="text" name="answers[{{ $question->id }}]" class="text-input answer-input" placeholder="Masukkan jawaban Anda..." required>
 </div>
 @elseif($question->type === 'narrative')
 <div style="margin-bottom:24px;">
 <textarea name="answers[{{ $question->id }}]" class="text-input answer-input" rows="5" placeholder="Tulis jawaban narasi Anda..." style="resize:vertical; min-height:100px;"></textarea>
 </div>
 @elseif($question->type === 'survey')
 <div class="options">
 @php
 $surveyLabels = ['A','B','C','D','E'];
 $surveyFields = ['option_a','option_b','option_c','option_d','option_e'];
 $optCount = $question->option_count ?? 2;
 @endphp
 @for($si = 0; $si < $optCount; $si++)
 @if($question->{$surveyFields[$si]})
 <div class="option">
 <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $surveyLabels[$si] }}" class="answer-input" style="width:16px; height:16px; accent-color:#003e6f;">
 <span>{{ $question->{$surveyFields[$si]} }}</span>
 </label>
 </div>
 @endif
 @endfor
 </div>
 @else
 <div class="options">
 @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d] as $key => $option)
 @php $optImgField = 'option_' . strtolower($key) . '_image'; @endphp
 <div class="option">
 <label>
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="answer-input" required>
 <span>
 <strong>{{ $key }}.</strong> {{ $option }}
 @if($question->$optImgField)
 <img src="/hrissdi/storage/{{ $question->$optImgField }}" alt="Opsi {{ $key }}" class="option-img">
 @endif
 </span>
 </label>
 </div>
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>

 <button type="submit" class="btn-submit" id="submitBtn">Selesaikan Tes</button>
 </form>
 @else
 <p style="color:#94a3b8; text-align:center; padding:40px;">Bank soal masih kosong. Hubungi administrator.</p>
 @endif
 @endif

 <div class="copyright">copyright &copy;2026 Shindengen HR Internal Team</div>
 </div>
 </div>

 {{-- Anti-Cheat Warning Overlay --}}
 <div id="anti-cheat-warning">
 <div class="acw-box">
 <div class="acw-icon"></div>
 <div class="acw-title">Peringatan Anti-Cheat!</div>
 <div class="acw-msg" id="acw-msg">Anda terdeteksi meninggalkan halaman ujian. Tindakan ini tercatat oleh sistem.</div>
 <div class="acw-count" id="acw-count"></div>
 <button class="acw-btn" id="acw-btn" onclick="dismissWarning()">Kembali ke Ujian</button>
 </div>
 </div>

 <script>
 // === PROGRESS TRACKING ===
 @if($hasSubTests)
 var subTestIds = @json($subTests->pluck('id'));
 var subTestQuestionCounts = @json($subTests->mapWithKeys(function($st) { return [$st->id => $st->questions->count()]; }));
 var totalQuestions = Object.values(subTestQuestionCounts).reduce(function(a,b){return a+b;},0);
 var completedSubTests = new Set();
 var answeredSet = new Set();
 @else
 var totalQuestions = {{ $questions->count() }};
 var answeredSet = new Set();
 @endif

 var progressFill = document.getElementById('progressFill');

 function updateProgress() {
 if (totalQuestions === 0) return;
 var pct = Math.round((answeredSet.size / totalQuestions) * 100);
 if (progressFill) progressFill.style.width = pct + '%';
 }

 document.querySelectorAll('.answer-input').forEach(function(input) {
 var event = input.type === 'radio' ? 'change' : 'input';
 input.addEventListener(event, function() {
 var questionBlock = this.closest('.question-block');
 var questionId = questionBlock.dataset.question;
 if (this.value.trim()) {
 answeredSet.add(questionId);
 } else {
 answeredSet.delete(questionId);
 }
 updateProgress();
 });
 });

 @if($hasSubTests)
 // === SUB-TEST NAVIGATION ===
 function hideAllScreens() {
 document.getElementById('overviewScreen').classList.add('hidden');
 document.querySelectorAll('.example-screen').forEach(function(el) { el.classList.remove('show'); });
 document.querySelectorAll('.subtest-test-screen').forEach(function(el) { el.classList.remove('show'); });
 }

 function backToOverview() {
 pauseAllSubTestTimers();
 hideAllScreens();
 document.getElementById('overviewScreen').classList.remove('hidden');
 window.scrollTo(0, 0);
 }

 function openSubTest(stId) {
 if (completedSubTests.has(stId)) return; // Don't reopen completed sub-tests
 pauseAllSubTestTimers();
 hideAllScreens();
 // If has example questions screen, show that first; otherwise go straight to test
 var exampleScreen = document.getElementById('exampleScreen' + stId);
 if (exampleScreen) {
 exampleScreen.classList.add('show');
 } else {
 var testScreen = document.getElementById('testScreen' + stId);
 if (testScreen) testScreen.classList.add('show');
 startSubTestTimer(stId);
 }
 window.scrollTo(0, 0);

 // Mark card as active
 document.querySelectorAll('.subtest-card').forEach(function(c) { c.classList.remove('active'); });
 var card = document.getElementById('stCard' + stId);
 if (card && !completedSubTests.has(stId)) {
 card.classList.add('active');
 var status = document.getElementById('stStatus' + stId);
 if (status) { status.className = 'st-status in-progress'; status.textContent = 'Sedang Dikerjakan'; }
 }
 }

 function startSubTestQuestions(stId) {
 hideAllScreens();
 var testScreen = document.getElementById('testScreen' + stId);
 if (testScreen) testScreen.classList.add('show');
 window.scrollTo(0, 0);
 startSubTestTimer(stId);
 }

 // === SUB-TEST TIMERS ===
 var subTestTimers = {};
 var subTestRemaining = {};

 function startSubTestTimer(stId) {
 var timerEl = document.getElementById('stTimer' + stId);
 if (!timerEl) return;
 // Only initialize remaining on first open
 if (typeof subTestRemaining[stId] === 'undefined') {
 var durationMin = parseInt(timerEl.getAttribute('data-duration')) || 0;
 if (durationMin <= 0) return;
 subTestRemaining[stId] = durationMin * 60;
 }
 // Clear any existing interval for this sub-test
 if (subTestTimers[stId]) clearInterval(subTestTimers[stId]);

 var headerEl = document.getElementById('stHeader' + stId);

 function tickSt() {
 subTestRemaining[stId]--;
 var s = subTestRemaining[stId];
 if (s < 0) s = 0;
 var m = Math.floor(s / 60);
 var sec = s % 60;
 timerEl.textContent = String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
 if (s <= 60 && headerEl) headerEl.classList.add('st-warning');
 if (s <= 0) {
 clearInterval(subTestTimers[stId]);
 subTestTimers[stId] = null;
 timerEl.textContent = 'WAKTU HABIS';
 finishSubTest(stId);
 }
 }

 subTestTimers[stId] = setInterval(tickSt, 1000);
 }

 function pauseSubTestTimer(stId) {
 if (subTestTimers[stId]) {
 clearInterval(subTestTimers[stId]);
 subTestTimers[stId] = null;
 }
 }

 function pauseAllSubTestTimers() {
 for (var id in subTestTimers) {
 if (subTestTimers[id]) {
 clearInterval(subTestTimers[id]);
 subTestTimers[id] = null;
 }
 }
 }

 function finishSubTest(stId) {
 pauseSubTestTimer(stId);
	// mark completed locally
	completedSubTests.add(stId);

	// collect answers for this subtest (include empty answers) and attach as hidden inputs
	try {
		var form = document.getElementById('testForm');
		if (form) {
			// remove any previous hidden markers for this subtest
			var prev = form.querySelectorAll('input[data-subtest-id="' + stId + '"]');
			prev.forEach(function(n){ n.remove(); });

			var qblocks = document.querySelectorAll('.question-block[data-subtest="' + stId + '"]');
			qblocks.forEach(function(qb){
				var qid = qb.dataset.question;
				var input = qb.querySelector('.answer-input');
				var val = '';
				if (input) {
					if (input.type === 'radio') {
						var sel = qb.querySelector('.answer-input:checked');
						val = sel ? sel.value : '';
					} else {
						val = input.value || '';
					}
				} else {
					// multiple inputs (e.g., radio group) - try to find checked
					var sel2 = qb.querySelector('.answer-input:checked');
					val = sel2 ? sel2.value : '';
				}
				var h = document.createElement('input');
				h.type = 'hidden';
				h.name = 'answers[' + qid + ']';
				h.value = val;
				h.setAttribute('data-subtest-id', stId);
				form.appendChild(h);
			});

			// add marker that this subtest was finished
			var mh = document.createElement('input');
			mh.type = 'hidden'; mh.name = 'subtest_completed[' + stId + ']'; mh.value = '1'; mh.setAttribute('data-subtest-id', stId);
			form.appendChild(mh);
		}
	} catch (e) {
		console.error('finishSubTest attach error', e);
	}

	// Update card UI
	var card = document.getElementById('stCard' + stId);
	if (card) { card.classList.remove('active'); card.classList.add('completed'); }
	var status = document.getElementById('stStatus' + stId);
	if (status) { status.className = 'st-status done'; status.textContent = ' Selesai'; }
 // Update card meta with remaining time
 var timerEl = document.getElementById('stTimer' + stId);
 if (timerEl && typeof subTestRemaining[stId] !== 'undefined' && subTestRemaining[stId] <= 0) {
 // Mark as timed out on card
 var metaEl = card ? card.querySelector('.st-meta') : null;
 if (metaEl) {
 var timeoutSpan = document.createElement('span');
 timeoutSpan.style.color = '#dc2626';
 timeoutSpan.style.fontWeight = '600';
 timeoutSpan.textContent = 'Waktu habis';
 metaEl.appendChild(timeoutSpan);
 }
 }

 backToOverview();
 }
 @endif

 // === ANTI-CHEAT SYSTEM ===
 var MAX_VIOLATIONS = 3;
 var violationCount = 0;
 var violationLog = [];
 var acWarning = document.getElementById('anti-cheat-warning');
 var acMsg = document.getElementById('acw-msg');
 var acCount = document.getElementById('acw-count');
 var acBtn = document.getElementById('acw-btn');

 function showViolation(reason) {
 violationCount++;
 violationLog.push({
 type: reason,
 time: new Date().toISOString(),
 count: violationCount
 });
 var remaining = MAX_VIOLATIONS - violationCount;

 if (violationCount >= MAX_VIOLATIONS) {
 acMsg.textContent = reason + ' Batas pelanggaran tercapai. Ujian akan dikirim otomatis.';
 acCount.textContent = ' Pelanggaran: ' + violationCount + '/' + MAX_VIOLATIONS + ' — UJIAN DIHENTIKAN';
 acBtn.style.display = 'none';
 acWarning.classList.add('show');
 setTimeout(function() { forceSubmit('Anti-cheat: batas pelanggaran tercapai (' + violationCount + 'x)'); }, 2000);
 return;
 }

 acMsg.textContent = reason + ' Jika Anda melakukan ini ' + remaining + ' kali lagi, ujian akan otomatis dikirim.';
 acCount.textContent = ' Pelanggaran: ' + violationCount + '/' + MAX_VIOLATIONS;
 acWarning.classList.add('show');

 var badge = document.getElementById('violationBadge');
 if (badge) { badge.textContent = ' ' + violationCount + '/' + MAX_VIOLATIONS; badge.classList.add('show'); }
 }

 function dismissWarning() {
 acWarning.classList.remove('show');
 }

 function forceSubmit(reason) {
 isSubmitting = true;
 isAutoSubmit = true;
 var btn = document.getElementById('submitBtn');
 if (btn) { btn.disabled = true; btn.textContent = ' Ujian dihentikan...'; }
 appendViolationData(reason);
 setTimeout(function() { document.getElementById('testForm').submit(); }, 500);
 }

 function appendViolationData(note) {
 var form = document.getElementById('testForm');
 var fields = {
 'violation_count': violationCount,
 'violation_log': JSON.stringify(violationLog),
 'anti_cheat_note': note || ''
 };
 for (var key in fields) {
 var existing = form.querySelector('input[name="' + key + '"]');
 if (existing) existing.remove();
 var input = document.createElement('input');
 input.type = 'hidden'; input.name = key; input.value = fields[key];
 form.appendChild(input);
 }
 }

 var lastViolationTime = 0;
 var isAutoSubmit = false;
 var isSubmitting = false;
// timestamp of last detected screenshot (used to avoid double-counting blur/visibility events)
window.__lastScreenshotAt = 0;
 function triggerViolation(reason) {
 if (isSubmitting) return;
 var now = Date.now();
 if (now - lastViolationTime < 1000) return;
 lastViolationTime = now;
 showViolation(reason);
 }
 document.addEventListener('visibilitychange', function() {
 if (document.hidden) {
	 // ignore visibility changes that immediately follow a detected screenshot
	 if (window.__lastScreenshotAt && (Date.now() - window.__lastScreenshotAt) < 2500) return;
	 triggerViolation('Anda terdeteksi berpindah tab atau meninggalkan halaman ujian.');
 }
 });
 window.addEventListener('blur', function() {
 // ignore blur events that immediately follow a detected screenshot (snipping tool may cause blur)
 if (window.__lastScreenshotAt && (Date.now() - window.__lastScreenshotAt) < 2500) return;
 triggerViolation('Anda terdeteksi meninggalkan jendela ujian.');
 });

 document.addEventListener('copy', function(e) { e.preventDefault(); showViolation('Copy tidak diizinkan selama ujian berlangsung.'); });
 document.addEventListener('cut', function(e) { e.preventDefault(); showViolation('Cut tidak diizinkan selama ujian berlangsung.'); });
 document.addEventListener('paste', function(e) { e.preventDefault(); showViolation('Paste tidak diizinkan selama ujian berlangsung.'); });
 document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
 document.addEventListener('keydown', function(e) {
 if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || e.key === 'v' || e.key === 'V' || e.key === 'x' || e.key === 'X' || e.key === 'u' || e.key === 'U' || e.key === 'a' || e.key === 'A')) {
 if ((e.key === 'a' || e.key === 'A') && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
 e.preventDefault();
 }
 if (e.key === 'F12') e.preventDefault();
 if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) e.preventDefault();
 });
 document.addEventListener('dragstart', function(e) { e.preventDefault(); });

 // === MOBILE-SPECIFIC ANTI-CHEAT ===
 window.addEventListener('pagehide', function() {
 triggerViolation('Anda terdeteksi meninggalkan halaman ujian (home/app switch).');
 });
 window.addEventListener('pageshow', function(e) {
 if (e.persisted) {
 triggerViolation('Anda terdeteksi kembali dari aplikasi lain.');
 }
 });
 var longPressTimer = null;
 document.addEventListener('touchstart', function(e) {
 longPressTimer = setTimeout(function() {
 if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
 e.preventDefault();
 }
 }, 500);
 }, { passive: false });
 document.addEventListener('touchend', function() { clearTimeout(longPressTimer); });
 document.addEventListener('touchmove', function() { clearTimeout(longPressTimer); });
 document.addEventListener('touchstart', function(e) {
 if (e.touches.length > 1) { e.preventDefault(); }
 }, { passive: false });
 var lastWidth = window.innerWidth;
 var lastHeight = window.innerHeight;
 window.addEventListener('resize', function() {
 var w = window.innerWidth, h = window.innerHeight;
 if (w === lastWidth && Math.abs(h - lastHeight) > 100) {
 lastHeight = h;
 return;
 }
 lastWidth = w; lastHeight = h;
 });

// Manual submit with confirmation (custom modal)
document.getElementById('testForm').addEventListener('submit', function(e) {
	if (isAutoSubmit) return;
	// prevent immediate submit to control flow
	e.preventDefault();
	if (isSubmitting) return;
	isSubmitting = true;

	var unanswered = totalQuestions - answeredSet.size;

	// If there are unanswered questions, allow immediate submit (user requested behavior)
	if (unanswered > 0) {
		appendViolationData(violationCount > 0 ? 'Peserta submit manual dengan ' + violationCount + ' pelanggaran' : '');
		var btn = document.getElementById('submitBtn');
		if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
		// submit form (hidden inputs for finished subtests are attached by finishSubTest)
		document.getElementById('testForm').submit();
		return;
	}

	// No unanswered questions -> show confirmation modal as safeguard
	var modal = document.getElementById('confirmModal');
	var msgEl = document.getElementById('confirmModalMessage');
	msgEl.textContent = 'Yakin ingin mengirimkan jawaban? Anda tidak dapat mengubah jawaban setelah ini.';
	modal.classList.add('show');

	var onCancel = function() {
		modal.classList.remove('show');
		isSubmitting = false;
		cleanup();
	};

	var onYes = function() {
		modal.classList.remove('show');
		// Attach violation data and submit
		appendViolationData(violationCount > 0 ? 'Peserta submit manual dengan ' + violationCount + ' pelanggaran' : '');
		var btn = document.getElementById('submitBtn');
		if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
		cleanup();
		document.getElementById('testForm').submit();
	};

	function cleanup() {
		document.getElementById('confirmCancelBtn').removeEventListener('click', onCancel);
		document.getElementById('confirmYesBtn').removeEventListener('click', onYes);
	}

	document.getElementById('confirmCancelBtn').addEventListener('click', onCancel);
	document.getElementById('confirmYesBtn').addEventListener('click', onYes);
});

 // === TIMER ===
 @if($remainingSeconds !== null)
 (function() {
 var remaining = {{ (int) $remainingSeconds }};
 var timerClock = document.getElementById('timerClock');
 var timerBar = document.getElementById('timerBar');

 function formatTime(s) {
 if (s < 0) s = 0;
 var h = Math.floor(s / 3600);
 var m = Math.floor((s % 3600) / 60);
 var sec = s % 60;
 return (h > 0 ? String(h).padStart(2,'0') + ':' : '') +
 String(m).padStart(2,'0') + ':' +
 String(sec).padStart(2,'0');
 }

 function tick() {
 remaining--;
 timerClock.textContent = formatTime(remaining);
 if (remaining <= 300 && !timerBar.classList.contains('warning')) {
 timerBar.classList.add('warning');
 }
 if (remaining <= 0) {
 clearInterval(timerInterval);
 timerClock.textContent = '00:00';
 autoSubmitForm();
 return;
 }
 }

 function autoSubmitForm() {
 isAutoSubmit = true;
 var btn = document.getElementById('submitBtn');
 btn.disabled = true;
 btn.textContent = ' Waktu Habis! Mengirim otomatis...';
 timerClock.textContent = 'WAKTU HABIS';
 @if($hasSubTests)
 // Go back to overview before submitting so form is visible
 backToOverview();
 @endif
 setTimeout(function() {
 document.getElementById('testForm').submit();
 }, 1000);
 }

 timerClock.textContent = formatTime(remaining);
 if (remaining <= 300) timerBar.classList.add('warning');
 if (remaining <= 0) {
 autoSubmitForm();
 } else {
 var timerInterval = setInterval(tick, 1000);
 }

 window.addEventListener('beforeunload', function(e) {
 if (remaining > 0) {
 e.preventDefault();
 e.returnValue = 'Tes sedang berlangsung. Jika Anda keluar, timer akan tetap berjalan!';
 }
 });
 })();
 @endif

 // === ANTI-SCREENSHOT SYSTEM ===
 (function() {
 var screenProtect = document.getElementById('screenProtect');
 var ssToast = document.getElementById('ssBlockedToast');
 var ssToastTimer = null;

 // Show toast notification
 function showSSToast() {
 if (ssToastTimer) clearTimeout(ssToastTimer);
 ssToast.classList.add('show');
 ssToastTimer = setTimeout(function() {
 ssToast.classList.remove('show');
 }, 3000);
 }

 // Blank the screen to ruin screenshot — stays white for 2 seconds
 function flashProtect() {
 screenProtect.classList.add('active');
 // Also try to overwrite clipboard with blank
 if (navigator.clipboard && navigator.clipboard.writeText) {
 navigator.clipboard.writeText(' ').catch(function() {});
 }
 setTimeout(function() {
 screenProtect.classList.remove('active');
 }, 2000);
 }

 // Handle screenshot attempt
 function onScreenshotAttempt(method) {
 flashProtect();
 showSSToast();
 // mark time so blur/visibility handlers ignore the immediate follow-up
 window.__lastScreenshotAt = Date.now();
 triggerViolation('Screenshot terdeteksi (' + method + '). Screenshot tidak diizinkan selama ujian.');
 }

 // 1. Block PrintScreen key (keyup catches it more reliably)
 document.addEventListener('keyup', function(e) {
 if (e.key === 'PrintScreen') {
 e.preventDefault();
 onScreenshotAttempt('PrintScreen');
 // Try to clear clipboard
 if (navigator.clipboard && navigator.clipboard.writeText) {
 navigator.clipboard.writeText('').catch(function() {});
 }
 }
 });

 // 2. Block PrintScreen on keydown too
 document.addEventListener('keydown', function(e) {
 if (e.key === 'PrintScreen') {
 e.preventDefault();
 return false;
 }
 // Windows Snipping Tool: Win+Shift+S
 if ((e.metaKey || e.key === 'Meta') && e.shiftKey && (e.key === 's' || e.key === 'S')) {
 e.preventDefault();
 onScreenshotAttempt('Snipping Tool');
 return false;
 }
 // Mac screenshots: Cmd+Shift+3, Cmd+Shift+4, Cmd+Shift+5
 if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4' || e.key === '5')) {
 e.preventDefault();
 onScreenshotAttempt('Mac Screenshot');
 return false;
 }
 // Alt+PrintScreen (Windows active window screenshot)
 if (e.altKey && e.key === 'PrintScreen') {
 e.preventDefault();
 onScreenshotAttempt('Alt+PrintScreen');
 return false;
 }
 // Ctrl+PrintScreen
 if (e.ctrlKey && e.key === 'PrintScreen') {
 e.preventDefault();
 onScreenshotAttempt('Ctrl+PrintScreen');
 return false;
 }
 });

 // 3. Monitor clipboard for image content (detects screenshots that bypass key events)
 if (navigator.clipboard && navigator.clipboard.read) {
 var clipboardCheckInterval = setInterval(function() {
 try {
 navigator.permissions.query({ name: 'clipboard-read' }).then(function(result) {
 if (result.state === 'granted') {
 navigator.clipboard.read().then(function(items) {
 for (var i = 0; i < items.length; i++) {
 var types = items[i].types;
 for (var j = 0; j < types.length; j++) {
 if (types[j].indexOf('image') !== -1) {
 navigator.clipboard.writeText('').catch(function() {});
 onScreenshotAttempt('Clipboard Image');
 }
 }
 }
 }).catch(function() {});
 }
 }).catch(function() {});
 } catch(ex) {}
 }, 3000);
 }

 // 4. Detect screen sharing / screen capture API
 if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
 var origGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);
 navigator.mediaDevices.getDisplayMedia = function() {
 onScreenshotAttempt('Screen Sharing');
 return Promise.reject(new Error('Screen capture blocked during exam'));
 };
 }

 // 5. CSS-based protection: hide content on print
 var printStyle = document.createElement('style');
 printStyle.textContent = '@media print { body { display:none !important; } body::after { content:"Screenshot/Print tidak diizinkan selama ujian"; display:block; font-size:24px; text-align:center; padding:100px; color:#dc2626; } }';
 document.head.appendChild(printStyle);

 // 6. Block Ctrl+P (print)
 document.addEventListener('keydown', function(e) {
 if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
 e.preventDefault();
 onScreenshotAttempt('Print');
 return false;
 }
 });
 })();

 </script>
</body>
</html>
