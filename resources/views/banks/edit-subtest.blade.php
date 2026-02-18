<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Edit Sub-Test - {{ $subTest->title }}</title>
 <style>
 body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; padding:20px; }
 .container { max-width:900px; margin:0 auto; }
 .card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:20px; margin-bottom:20px; }
 h1 { font-size:20px; color:#0f172a; margin:0 0 6px 0; }
 h2 { font-size:16px; color:#334155; margin:18px 0 12px 0; }
 input, textarea, select { width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; color:#0f172a; box-sizing:border-box; font-family:inherit; }
 textarea { resize:vertical; }
 .btn { background:#003e6f; color:#fff; border:none; padding:8px 12px; border-radius:6px; font-size:14px; cursor:pointer; text-decoration:none; display:inline-block; }
 .btn:hover { background:#002a4f; }
 .btn-danger { background:#dc2626; }
 .btn-danger:hover { background:#b91c1c; }
 .error { color:#dc2626; font-size:12px; margin-top:4px; }
 .success { background:#d1fae5; color:#065f46; padding:12px; border-radius:6px; margin-bottom:16px; font-size:13px; }
 label { display:block; font-size:13px; color:#334155; margin-bottom:6px; margin-top:12px; }
 .form-group { margin-bottom:16px; }
 .back-link { color:#0f172a; text-decoration:none; font-size:14px; }
 .back-link:hover { text-decoration:underline; }
 .question { background:#f8fafc; border:1px solid #e2e8f0; padding:16px; border-radius:6px; margin-bottom:12px; }
 .question.example { border-left:3px solid #f59e0b; background:#fffbeb; }
 .question-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
 .question-text { color:#0f172a; font-weight:500; margin-bottom:10px; }
 .option { margin-bottom:8px; font-size:13px; }
 .badge { display:inline-block; font-size:10px; padding:2px 8px; border-radius:10px; font-weight:600; }
 .badge-example { background:#fef3c7; color:#92400e; }
 .badge-soal { background:#dbeafe; color:#1e40af; }
 .section-title { font-size:14px; font-weight:600; color:#334155; margin:16px 0 10px 0; padding:8px 12px; background:#f1f5f9; border-radius:6px; }
 </style>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
 <div class="container">
 <a href="{{ route('banks.edit', $bank) }}" class="back-link">&larr; Kembali ke {{ $bank->title }}</a>

 <div class="card">
 <h1>{{ $subTest->title }}</h1>
 <p style="font-size:13px;color:#64748b;margin:0;">Sub-test dari: {{ $bank->title }}</p>

 @if(session('success'))
 <div class="success" style="margin-top:12px;">{{ session('success') }}</div>
 @endif

 <form method="POST" action="{{ route('sub-tests.update', $subTest) }}" style="margin-top:16px;">
 @csrf @method('PUT')
 <div class="form-group">
 <label for="title">Judul Sub-Test</label>
 <input id="title" type="text" name="title" value="{{ $subTest->title }}" required>
 @error('title')<div class="error">{{ $message }}</div>@enderror
 </div>
 <div class="form-group">
 <label for="description">Deskripsi / Instruksi</label>
 <textarea id="description" name="description" rows="3">{{ $subTest->description }}</textarea>
 </div>
 <div class="form-group">
 <label for="duration_minutes"> Durasi (menit)</label>
 <div style="display:flex;align-items:center;gap:8px;">
 <input id="duration_minutes" type="number" name="duration_minutes" value="{{ $subTest->duration_minutes }}" min="1" max="600" placeholder="15" style="width:180px;">
 <span style="font-size:13px;color:#64748b;">menit (kosongkan = tanpa batas waktu)</span>
 </div>
 </div>
 <button type="submit" class="btn">Simpan Perubahan</button>
 </form>
 </div>

 {{-- Example Questions --}}
 <div class="card">
 <div class="section-title"> Contoh Soal ({{ $exampleQuestions->count() }})</div>
 <p style="font-size:12px;color:#64748b;margin:0 0 12px 0;">Contoh soal ditampilkan sebelum peserta mengerjakan sub-test ini. Tidak dinilai.</p>
 @if($exampleQuestions->count() > 0)
 @foreach($exampleQuestions as $eq)
 <div class="question example">
 <div class="question-header">
 <div>
 <span class="badge badge-example">Contoh</span>
 <span class="question-text" style="margin-left:8px;">{{ Str::limit($eq->question, 80) }}</span>
 </div>
 <div style="display:flex;gap:8px;">
 <a href="{{ route('questions.edit', $eq) }}" class="btn" style="background:#0ea5ad;padding:6px 10px;font-size:12px;"></a>
 <form method="POST" action="{{ route('questions.delete', $eq) }}" style="display:inline;" onsubmit="return confirm('Hapus contoh soal ini?');">
 @csrf @method('DELETE')
 <button type="submit" class="btn btn-danger" style="padding:6px 10px;font-size:12px;"></button>
 </form>
 </div>
 </div>
 @if($eq->type === 'multiple_choice')
 <div style="font-size:12px;margin-top:8px;">
 <div class="option"><strong>A.</strong> {{ Str::limit($eq->option_a, 50) }}</div>
 <div class="option"><strong>B.</strong> {{ Str::limit($eq->option_b, 50) }}</div>
 <div class="option"><strong>C.</strong> {{ Str::limit($eq->option_c, 50) }}</div>
 <div class="option"><strong>D.</strong> {{ Str::limit($eq->option_d, 50) }}</div>
 <div style="background:#d1fae5;padding:6px 8px;border-radius:4px;margin-top:4px;color:#065f46;font-size:11px;">
 Jawaban Benar: <strong>{{ $eq->correct_answer }}</strong>
 </div>
 </div>
 @elseif($eq->type === 'text')
 <div style="font-size:12px;background:#f1f5f9;padding:6px 8px;border-radius:4px;margin-top:8px;">Jawaban: {{ $eq->correct_answer_text }}</div>
 @else
 <div style="font-size:12px;color:#64748b;margin-top:8px;"> Tipe: {{ ucfirst($eq->type) }}</div>
 @endif
 </div>
 @endforeach
 @else
 <p style="color:#94a3b8;text-align:center;padding:16px;font-size:13px;">Belum ada contoh soal.</p>
 @endif
 </div>

 {{-- Real Questions --}}
 <div class="card">
 <div class="section-title"> Soal Tes ({{ $questions->count() }})</div>
 @if($questions->count() > 0)
 @foreach($questions as $question)
 <div class="question">
 <div class="question-header">
 <div>
 <div class="question-text">{{ $question->order + 1 }}. {{ Str::limit($question->question, 60) }}</div>
 <div style="font-size:12px;color:#64748b;margin-top:4px;">
 @if($question->type === 'narrative') Narasi
 @elseif($question->type === 'text') Isian
 @elseif($question->type === 'survey') Survei ({{ $question->option_count }} Pilihan)
 @else Pilihan Ganda
 @endif
 @if($question->image) · @endif
 @if($question->audio) · @endif
 </div>
 </div>
 <div style="display:flex;gap:8px;">
 <a href="{{ route('questions.edit', $question) }}" class="btn" style="background:#0ea5ad;padding:6px 10px;font-size:12px;"> Edit</a>
 <form method="POST" action="{{ route('questions.delete', $question) }}" style="display:inline;" onsubmit="return confirm('Hapus soal ini?');">
 @csrf @method('DELETE')
 <button type="submit" class="btn btn-danger" style="padding:6px 10px;font-size:12px;"></button>
 </form>
 </div>
 </div>
 @if($question->type === 'multiple_choice')
 <div style="font-size:12px;margin-top:8px;">
 <div class="option"><strong>A.</strong> {{ Str::limit($question->option_a, 50) }}</div>
 <div class="option"><strong>B.</strong> {{ Str::limit($question->option_b, 50) }}</div>
 <div class="option"><strong>C.</strong> {{ Str::limit($question->option_c, 50) }}</div>
 <div class="option"><strong>D.</strong> {{ Str::limit($question->option_d, 50) }}</div>
 <div style="background:#f1f5f9;padding:6px 8px;border-radius:4px;margin-top:4px;font-size:11px;">
 Jawaban Benar: <strong style="color:#003e6f;">{{ $question->correct_answer }}</strong>
 </div>
 </div>
 @elseif($question->type === 'text')
 <div style="font-size:12px;background:#f1f5f9;padding:6px 8px;border-radius:4px;margin-top:8px;">Jawaban: {{ $question->correct_answer_text }}</div>
 @elseif($question->type === 'survey')
 <div style="font-size:12px;margin-top:8px;">
 @php $sLabels=['A','B','C','D','E']; $sFields=['option_a','option_b','option_c','option_d','option_e']; @endphp
 @for($si=0; $si<($question->option_count??2); $si++)
 @if($question->{$sFields[$si]}) <div class="option"><strong>{{ $sLabels[$si] }}.</strong> {{ Str::limit($question->{$sFields[$si]}, 50) }}</div> @endif
 @endfor
 </div>
 @endif
 </div>
 @endforeach
 @else
 <p style="color:#94a3b8;text-align:center;padding:16px;font-size:13px;">Belum ada soal tes.</p>
 @endif
 </div>

 {{-- Add Question Form --}}
 <div class="card">
 <h2> Tambah Soal</h2>
 <div style="margin-bottom:14px;">
 <label style="display:flex;align-items:center;gap:8px;margin-top:0;cursor:pointer;">
 <input type="checkbox" id="isExampleToggle" style="width:auto;accent-color:#f59e0b;">
 <span style="font-size:13px;font-weight:500;color:#92400e;">Tandai sebagai Contoh Soal (ditampilkan sebelum tes, tidak dinilai)</span>
 </label>
 </div>

 <form method="POST" action="{{ route('banks.store') }}" id="addQuestionForm" enctype="multipart/form-data">
 @csrf
 <input type="hidden" name="bank_id" value="{{ $bank->id }}">
 <input type="hidden" name="sub_test_id" value="{{ $subTest->id }}">
 <input type="hidden" name="is_example" id="isExampleInput" value="0">

 @if($errors->any())
 <div style="background:#fee2e2;color:#9b1c1c;padding:10px;border-radius:6px;margin-bottom:12px;">
	 <strong>Terjadi kesalahan:</strong>
	 <ul style="margin:8px 0 0 18px;padding:0;">
		 @foreach($errors->all() as $err)
			 <li>{{ $err }}</li>
		 @endforeach
	 </ul>
 </div>
 @endif

 <div class="form-group">
 <label for="type">Tipe Soal</label>
 <select id="type" name="type" required>
 <option value="multiple_choice">Pilihan Ganda (A/B/C/D)</option>
 <option value="survey">Pilihan Ganda Survei (Tanpa Jawaban Benar)</option>
 <option value="text">Isian Jawaban (Teks)</option>
 <option value="narrative">Narasi (Tanpa Jawaban Benar)</option>
 </select>
 </div>

 <div class="form-group">
 <label for="question">Soal</label>
 <textarea id="question" name="question" rows="3" placeholder="Masukkan soal..." required></textarea>
 </div>

 <div class="form-group">
 <label for="image">Gambar (opsional)</label>
 <input id="image" type="file" name="image" accept="image/*">
 <small style="color:#64748b;">Maksimal 5MB. Format: JPEG, PNG, JPG, GIF</small>
 @error('image')<div class="error">{{ $message }}</div>@enderror
 </div>

 <div class="form-group">
 <label for="audio">Audio (opsional)</label>
 <input id="audio" type="file" name="audio" accept="audio/*">
 @error('audio')<div class="error">{{ $message }}</div>@enderror
 </div>

 <div id="multipleChoiceOptions">
 <div class="form-group"><label>Opsi A</label><input type="text" name="option_a" placeholder="Opsi A...">
 <div style="margin-top:6px;"><label style="font-size:12px;color:#64748b;margin-bottom:4px;">Gambar Opsi A (opsional)</label><input type="file" name="option_a_image" accept="image/*" style="font-size:12px;"></div></div>
 <div class="form-group"><label>Opsi B</label><input type="text" name="option_b" placeholder="Opsi B...">
 <div style="margin-top:6px;"><label style="font-size:12px;color:#64748b;margin-bottom:4px;">Gambar Opsi B (opsional)</label><input type="file" name="option_b_image" accept="image/*" style="font-size:12px;"></div></div>
 <div class="form-group"><label>Opsi C</label><input type="text" name="option_c" placeholder="Opsi C...">
 <div style="margin-top:6px;"><label style="font-size:12px;color:#64748b;margin-bottom:4px;">Gambar Opsi C (opsional)</label><input type="file" name="option_c_image" accept="image/*" style="font-size:12px;"></div></div>
 <div class="form-group"><label>Opsi D</label><input type="text" name="option_d" placeholder="Opsi D...">
 <div style="margin-top:6px;"><label style="font-size:12px;color:#64748b;margin-bottom:4px;">Gambar Opsi D (opsional)</label><input type="file" name="option_d_image" accept="image/*" style="font-size:12px;"></div></div>
 <div class="form-group">
 <label>Jawaban Benar</label>
 <select name="correct_answer" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;">
 <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
 </select>
 </div>
 </div>

 <div id="surveyOptions" style="display:none;">
 <div style="background:#e0f2fe;color:#0c4a6e;padding:10px 14px;border-radius:6px;margin-bottom:14px;font-size:13px;">
 Tipe Survei — tidak ada jawaban benar/salah.
 </div>
 <div class="form-group">
 <label>Jumlah Pilihan</label>
 <select id="option_count" name="option_count">
 <option value="2">2</option><option value="3">3</option><option value="4" selected>4</option><option value="5">5</option>
 </select>
 </div>
 @foreach(['a','b','c','d','e'] as $si => $letter)
 <div id="survey_opt_{{ $letter }}" class="form-group" style="{{ $si >= 4 ? 'display:none;' : '' }}">
 <label>Opsi {{ strtoupper($letter) }}</label>
 <input type="text" name="option_{{ $letter }}" placeholder="Opsi {{ strtoupper($letter) }}..." class="survey-option-input">
 </div>
 @endforeach
 </div>

 <div id="textAnswerOption" style="display:none;">
 <div class="form-group">
 <label>Jawaban Benar</label>
 <input id="correct_answer_text" type="text" name="correct_answer_text" placeholder="Masukkan jawaban yang benar...">
 </div>
 </div>

 <button type="submit" class="btn">+ Tambah Soal</button>
 </form>

 <script>
 var typeSelect = document.getElementById('type');
 var mcOpts = document.getElementById('multipleChoiceOptions');
 var surveyOpts = document.getElementById('surveyOptions');
 var textOpt = document.getElementById('textAnswerOption');
 // Only target text inputs and selects for required validation. Keep file inputs (images/audio) optional.
 var mcInputs = mcOpts.querySelectorAll('input[type="text"], select');
 var textInput = document.getElementById('correct_answer_text');
 var optCountSelect = document.getElementById('option_count');
 var isExampleToggle = document.getElementById('isExampleToggle');
 var isExampleInput = document.getElementById('isExampleInput');

 isExampleToggle.addEventListener('change', function() {
 isExampleInput.value = this.checked ? '1' : '0';
 });

 // Robust toggle: show/hide sections and set `required` only on visible text/select inputs.
 function isVisible(el) {
	 return el && el.offsetParent !== null;
 }

 function setRequiredIfVisible(el, required) {
	 if (!el) return;
	 if (required && isVisible(el)) el.setAttribute('required', 'required');
	 else el.removeAttribute('required');
 }

 function toggleType() {
	 var t = typeSelect.value;
	 mcOpts.style.display = 'none';
	 surveyOpts.style.display = 'none';
	 textOpt.style.display = 'none';

	 // Show the selected section
	 if (t === 'multiple_choice') {
		 mcOpts.style.display = 'block';
	 } else if (t === 'survey') {
		 surveyOpts.style.display = 'block';
	 } else if (t === 'text') {
		 textOpt.style.display = 'block';
	 }

	 // For all text/select inputs inside the MC block, require only if visible
	 mcOpts.querySelectorAll('input[type="text"], select').forEach(function(i) {
		 setRequiredIfVisible(i, true);
	 });

	 // Survey options: require only visible option inputs
	 document.querySelectorAll('.survey-option-input').forEach(function(i) {
		 setRequiredIfVisible(i, true);
	 });

	 // Correct answer text field
	 setRequiredIfVisible(textInput, true);
 }

 function updateSurveyOpts() {
	 var c = parseInt(optCountSelect.value);
	 ['a','b','c','d','e'].forEach(function(l, i) {
		 var el = document.getElementById('survey_opt_' + l);
		 var inp = el.querySelector('input');
		 if (i < c) {
			 el.style.display = 'block';
		 } else {
			 el.style.display = 'none';
			 inp.value = '';
		 }
	 });
	 // Re-apply required rules after changing visibility
	 document.querySelectorAll('.survey-option-input').forEach(function(i) { setRequiredIfVisible(i, true); });
 }

 typeSelect.addEventListener('change', toggleType);
 optCountSelect.addEventListener('change', updateSurveyOpts);
 // Initialize UI
 toggleType();
 </script>
 </div>
 </div>
</body>
</html>
