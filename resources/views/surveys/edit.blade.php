<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Edit Survey - HRIS</title>
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{--primary:#003e6f;--primary-light:#0a5a9e;--bg:#f0f4f8;--card:#fff;--border:#e2e8f0;--text:#0f172a;--text-secondary:#64748b;--text-muted:#94a3b8;--success:#10b981;--warning:#f59e0b;--danger:#ef4444;--radius:12px;}
body{margin:0;font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;}
.page-header{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);padding:20px 28px;color:#fff;display:flex;align-items:center;flex-wrap:wrap;gap:12px;border-radius:var(--radius) var(--radius) 0 0;position:relative;overflow:hidden;}
.page-header::before{content:'';position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.06);}
.page-title{font-size:20px;font-weight:700;z-index:1;}
.page-subtitle{font-size:12px;color:rgba(255,255,255,.7);z-index:1;}
.header-actions{margin-left:auto;display:flex;gap:8px;z-index:1;}
.btn{padding:8px 18px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-white{background:#fff;color:var(--primary);}
.btn-white:hover{background:#f0f4ff;transform:translateY(-1px);}
.btn-primary{background:var(--primary);color:#fff;}
.btn-primary:hover{background:var(--primary-light);}
.btn-danger{background:var(--danger);color:#fff;}
.btn-danger:hover{background:#dc2626;}
.btn-ghost{background:transparent;color:var(--text-secondary);border:1px solid var(--border);}
.btn-ghost:hover{background:var(--bg);}

.content{background:var(--card);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius) var(--radius);padding:24px;}
.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:6px;}
.form-input,.form-select,.form-textarea{width:100%;padding:9px 14px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;color:var(--text);background:var(--card);transition:border-color .15s;}
.form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,62,111,.08);}
.form-textarea{resize:vertical;min-height:80px;}
.form-row{display:flex;gap:16px;flex-wrap:wrap;}
.form-row > .form-group{flex:1;min-width:200px;}
.form-check{display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;}
.form-check input[type="checkbox"]{width:16px;height:16px;cursor:pointer;}

.question-section{margin-top:24px;padding-top:24px;border-top:2px solid var(--border);}
.question-section-title{font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.question-card{background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:12px;position:relative;transition:all .15s;}
.question-card:hover{border-color:var(--primary-light);box-shadow:0 2px 8px rgba(0,62,111,.06);}
.question-number{position:absolute;top:12px;left:14px;width:24px;height:24px;background:var(--primary);color:#fff;border-radius:6px;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;}
.question-header{display:flex;gap:12px;margin-left:36px;align-items:flex-start;flex-wrap:wrap;}
.question-header .form-group{margin-bottom:8px;}
.q-text{flex:1;min-width:200px;}
.q-type{width:180px;flex-shrink:0;}
.q-required{width:auto;display:flex;align-items:center;padding-top:22px;}
.remove-question{position:absolute;top:12px;right:12px;width:26px;height:26px;border:none;background:#fecaca;color:#dc2626;border-radius:6px;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.remove-question:hover{background:#fca5a5;}
.options-area{margin-left:36px;margin-top:8px;}
.option-row{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
.option-row input{flex:1;padding:7px 12px;border:1px solid var(--border);border-radius:6px;font-size:12px;font-family:inherit;}
.option-row input:focus{outline:none;border-color:var(--primary);}
.remove-option{width:24px;height:24px;border:none;background:#fecaca;color:#dc2626;border-radius:6px;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;}
.add-option{display:inline-flex;align-items:center;gap:4px;border:none;background:transparent;color:var(--primary);font-size:11px;cursor:pointer;padding:4px 0;font-weight:600;font-family:inherit;}
.add-option:hover{text-decoration:underline;}
.add-question-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;border:2px dashed var(--border);border-radius:10px;background:transparent;color:var(--text-secondary);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;}
.add-question-btn:hover{border-color:var(--primary);color:var(--primary);background:rgba(0,62,111,.02);}
.form-actions{display:flex;gap:8px;margin-top:24px;justify-content:flex-end;}
.error-box{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;}
.error-box ul{margin:4px 0 0 16px;}
</style>
</head>
<body>
<div style="display:flex;min-height:100vh;background:var(--bg);">
@include('layouts.sidebar')
<div style="flex:1;display:flex;flex-direction:column;min-width:0;">
  <div style="display:flex;justify-content:center;padding:24px 20px 0;flex:1;">
    <div style="max-width:900px;width:100%;">

      @if($errors->any())
      <div class="error-box">
        <strong>Terdapat kesalahan:</strong>
        <ul>
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form method="POST" action="{{ route('surveys.update', $survey) }}" id="surveyForm">
        @csrf @method('PUT')

        <div class="page-header">
          <div>
            <div class="page-title">✏️ Edit Survey</div>
            <div class="page-subtitle">{{ $survey->title }}</div>
          </div>
          <div class="header-actions">
            <a href="{{ route('surveys.index') }}" class="btn btn-white">← Kembali</a>
          </div>
        </div>

        <div class="content">
          <div class="form-group">
            <label class="form-label">Judul Survey *</label>
            <input type="text" name="title" class="form-input" value="{{ old('title', $survey->title) }}" placeholder="cth: Survey Kepuasan Karyawan Q1 2026" required>
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-textarea" placeholder="Deskripsi singkat tentang survey ini...">{{ old('description', $survey->description) }}</textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Tanggal Mulai</label>
              <input type="date" name="start_date" class="form-input" value="{{ old('start_date', $survey->start_date ? $survey->start_date->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal Selesai</label>
              <input type="date" name="end_date" class="form-input" value="{{ old('end_date', $survey->end_date ? $survey->end_date->format('Y-m-d') : '') }}">
            </div>
          </div>

          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous', $survey->is_anonymous) ? 'checked' : '' }}>
              <span>Survey bersifat anonim (responden tidak perlu mengisi identitas)</span>
            </label>
          </div>

          {{-- QUESTIONS SECTION --}}
          <div class="question-section">
            <div class="question-section-title">📋 Daftar Pertanyaan</div>
            <div id="questionsContainer"></div>
            <button type="button" class="add-question-btn" onclick="addQuestion()">＋ Tambah Pertanyaan</button>
          </div>

          <div class="form-actions">
            <a href="{{ route('surveys.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Update Survey</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<script>
let questionIndex = 0;

function addQuestion(data) {
  const container = document.getElementById('questionsContainer');
  const idx = questionIndex++;
  const q = data || { question: '', type: 'scale', is_required: true, options: [] };

  const card = document.createElement('div');
  card.className = 'question-card';
  card.id = 'question-' + idx;

  let optionsHtml = '';
  if (q.type === 'multiple_choice' && q.options && q.options.length > 0) {
    q.options.forEach((opt, oi) => {
      optionsHtml += `<div class="option-row">
        <input type="text" name="questions[${idx}][options][]" value="${escHtml(opt)}" placeholder="Opsi ${oi+1}" required>
        <button type="button" class="remove-option" onclick="this.parentElement.remove()">✕</button>
      </div>`;
    });
  }

  card.innerHTML = `
    <div class="question-number">${idx + 1}</div>
    <button type="button" class="remove-question" onclick="removeQuestion(${idx})">✕</button>
    <div class="question-header">
      <div class="form-group q-text">
        <label class="form-label">Pertanyaan</label>
        <input type="text" name="questions[${idx}][question]" class="form-input" value="${escHtml(q.question)}" placeholder="Tulis pertanyaan..." required>
      </div>
      <div class="form-group q-type">
        <label class="form-label">Tipe</label>
        <select name="questions[${idx}][type]" class="form-select" onchange="toggleOptions(${idx}, this.value)">
          <option value="scale" ${q.type==='scale'?'selected':''}>📊 Skala 1-5</option>
          <option value="multiple_choice" ${q.type==='multiple_choice'?'selected':''}>📝 Pilihan Ganda</option>
          <option value="text" ${q.type==='text'?'selected':''}>💬 Isian Teks</option>
        </select>
      </div>
      <div class="q-required">
        <label class="form-check">
          <input type="hidden" name="questions[${idx}][is_required]" value="0">
          <input type="checkbox" name="questions[${idx}][is_required]" value="1" ${q.is_required?'checked':''}>
          <span style="font-size:11px;">Wajib</span>
        </label>
      </div>
    </div>
    <div class="options-area" id="options-${idx}" style="display:${q.type==='multiple_choice'?'block':'none'}">
      <div class="form-label" style="margin-bottom:8px;">Opsi Jawaban</div>
      <div id="optionsList-${idx}">${optionsHtml}</div>
      <button type="button" class="add-option" onclick="addOption(${idx})">＋ Tambah Opsi</button>
    </div>
  `;
  container.appendChild(card);

  if (q.type === 'multiple_choice' && (!q.options || q.options.length === 0)) {
    addOption(idx);
    addOption(idx);
  }

  renumberQuestions();
}

function removeQuestion(idx) {
  var el = document.getElementById('question-' + idx);
  if (el) { el.remove(); renumberQuestions(); }
}

function renumberQuestions() {
  var cards = document.querySelectorAll('#questionsContainer .question-card');
  cards.forEach((card, i) => {
    var num = card.querySelector('.question-number');
    if (num) num.textContent = i + 1;
  });
}

function toggleOptions(idx, type) {
  var area = document.getElementById('options-' + idx);
  if (area) {
    area.style.display = type === 'multiple_choice' ? 'block' : 'none';
    if (type === 'multiple_choice') {
      var list = document.getElementById('optionsList-' + idx);
      if (list && list.children.length === 0) { addOption(idx); addOption(idx); }
    }
  }
}

function addOption(idx) {
  var list = document.getElementById('optionsList-' + idx);
  if (!list) return;
  var oi = list.children.length + 1;
  var row = document.createElement('div');
  row.className = 'option-row';
  row.innerHTML = `<input type="text" name="questions[${idx}][options][]" placeholder="Opsi ${oi}" required>
    <button type="button" class="remove-option" onclick="this.parentElement.remove()">✕</button>`;
  list.appendChild(row);
}

function escHtml(str) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(str || ''));
  return div.innerHTML;
}

// Load existing questions
const existingQuestions = @json($survey->questions->sortBy('order')->values());
if (existingQuestions.length > 0) {
  existingQuestions.forEach(q => {
    addQuestion({
      question: q.question,
      type: q.type,
      is_required: q.is_required,
      options: q.options || []
    });
  });
} else {
  addQuestion();
}
</script>
</body>
</html>
