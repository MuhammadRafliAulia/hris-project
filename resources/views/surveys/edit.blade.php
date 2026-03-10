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

.section-group{margin-top:24px;padding-top:24px;border-top:2px solid var(--border);}
.section-title{font-size:16px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.section-card{background:#f0f7ff;border:2px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:16px;position:relative;}
.section-header{display:flex;gap:12px;align-items:flex-start;margin-bottom:12px;}
.section-num{width:28px;height:28px;background:var(--primary);color:#fff;border-radius:6px;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.section-inputs{flex:1;}
.section-inputs .form-group{margin-bottom:8px;}
.remove-section{position:absolute;top:10px;right:10px;width:26px;height:26px;border:none;background:#fecaca;color:#dc2626;border-radius:6px;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.remove-section:hover{background:#fca5a5;}
.add-section-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;border:2px dashed var(--border);border-radius:10px;background:transparent;color:var(--text-secondary);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;margin-bottom:16px;}
.add-section-btn:hover{border-color:var(--primary);color:var(--primary);background:rgba(0,62,111,.02);}

.question-section{margin-top:12px;padding:12px;background:#fff;border:1px solid var(--border);border-radius:8px;}
.question-section-title{font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid var(--border);}
.question-card{background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:8px;position:relative;transition:all .15s;}
.question-card:hover{border-color:var(--primary-light);box-shadow:0 2px 6px rgba(0,62,111,.05);}
.question-number{position:absolute;top:10px;left:10px;width:20px;height:20px;background:var(--primary);color:#fff;border-radius:4px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;}
.question-header{margin-left:28px;display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap;}
.q-text{flex:1;min-width:180px;}
.q-type{width:150px;flex-shrink:0;}
.q-required{width:auto;display:flex;align-items:center;padding-top:20px;}
.remove-question{position:absolute;top:8px;right:8px;width:22px;height:22px;border:none;background:#fecaca;color:#dc2626;border-radius:4px;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.remove-question:hover{background:#fca5a5;}
.options-area{margin-left:28px;margin-top:6px;}
.option-row{display:flex;align-items:center;gap:6px;margin-bottom:4px;}
.option-row input{flex:1;padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:12px;font-family:inherit;}
.remove-option{width:20px;height:20px;border:none;background:#fecaca;color:#dc2626;border-radius:4px;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;}
.add-option{display:inline-flex;align-items:center;gap:3px;border:none;background:transparent;color:var(--primary);font-size:11px;cursor:pointer;padding:2px 0;font-weight:600;font-family:inherit;}
.add-option:hover{text-decoration:underline;}
.add-question-btn{display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px;border:1px dashed var(--border);border-radius:6px;background:transparent;color:var(--text-secondary);font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;margin-top:6px;}
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

      <form method="POST" action="{{ route('surveys.update', $survey) }}" id="surveyForm" onsubmit="validateForm(event)">
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

          {{-- SECTIONS --}}
          <div class="section-group">
            <div class="section-title">📑 Bagian (Section)</div>
            <button type="button" class="add-section-btn" onclick="addSection()">＋ Tambah Section</button>
            <div id="sectionsContainer"></div>
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
let sectionIndex = 0;

function addSection(data) {
  const container = document.getElementById('sectionsContainer');
  const secIdx = sectionIndex++;
  const s = data || { title: '', description: '', questions: [] };
  const secId = 'section-' + secIdx;

  const card = document.createElement('div');
  card.className = 'section-card';
  card.id = secId;

  let questionsHtml = '';
  (s.questions || []).forEach((q, qIdx) => {
    const qId = secIdx + '_' + qIdx;
    let optionsHtml = '';
    if (q.type === 'multiple_choice' && q.options && q.options.length > 0) {
      q.options.forEach((opt, oi) => {
        optionsHtml += `<div class="option-row">
          <input type="text" name="sections[${secIdx}][questions][${qIdx}][options][]" value="${escHtml(opt)}" placeholder="Opsi ${oi+1}">
          <button type="button" class="remove-option" onclick="this.parentElement.remove()">✕</button>
        </div>`;
      });
    }

    questionsHtml += `
      <div class="question-card" id="q-${qId}">
        <div class="question-number">${qIdx + 1}</div>
        <button type="button" class="remove-question" onclick="removeQuestion('${secIdx}', ${qIdx})">✕</button>
        <div class="question-header">
          <div class="form-group q-text">
            <label class="form-label" style="font-size:11px;">Pertanyaan</label>
            <input type="text" name="sections[${secIdx}][questions][${qIdx}][question]" class="form-input" value="${escHtml(q.question)}" placeholder="Tulis pertanyaan..." required style="font-size:12px;padding:6px 10px;">
          </div>
          <div class="form-group q-type">
            <label class="form-label" style="font-size:11px;">Tipe</label>
            <select name="sections[${secIdx}][questions][${qIdx}][type]" class="form-select" onchange="toggleOptions('${secIdx}', ${qIdx}, this.value)" style="font-size:12px;padding:6px 10px;">
              <option value="scale" ${q.type==='scale'?'selected':''}>Skala 1-5</option>
              <option value="multiple_choice" ${q.type==='multiple_choice'?'selected':''}>Pilihan Ganda</option>
              <option value="text" ${q.type==='text'?'selected':''}>Isian Teks</option>
            </select>
          </div>
          <div class="q-required">
            <label class="form-check" style="font-size:11px;">
              <input type="hidden" name="sections[${secIdx}][questions][${qIdx}][is_required]" value="0">
              <input type="checkbox" name="sections[${secIdx}][questions][${qIdx}][is_required]" value="1" ${q.is_required?'checked':''}>
              <span>Wajib</span>
            </label>
          </div>
        </div>
        <div class="options-area" id="opts-${qId}" style="display:${q.type==='multiple_choice'?'block':'none'}">
          <div class="form-label" style="margin-bottom:6px;font-size:11px;">Opsi Jawaban</div>
          <div id="optsList-${qId}">${optionsHtml}</div>
          <button type="button" class="add-option" onclick="addOption('${secIdx}', ${qIdx})">＋ Tambah</button>
        </div>
      </div>
    `;
  });

  card.innerHTML = `
    <div class="section-header">
      <div class="section-num">${secIdx + 1}</div>
      <div class="section-inputs" style="flex:1;">
        <div class="form-group" style="margin-bottom:8px;">
          <label class="form-label">Judul Section</label>
          <input type="text" name="sections[${secIdx}][title]" class="form-input" value="${escHtml(s.title)}" placeholder="cth: Kepuasan Kerja" required>
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi Section</label>
          <textarea name="sections[${secIdx}][description]" class="form-textarea" placeholder="Jelaskan section ini..." style="min-height:60px;font-size:12px;padding:8px 12px;">${escHtml(s.description || '')}</textarea>
        </div>
      </div>
      <button type="button" class="remove-section" onclick="removeSection(${secIdx})">✕</button>
    </div>
    <div class="question-section">
      <div class="question-section-title">Pertanyaan dalam Section ini</div>
      <div id="questions-${secIdx}">${questionsHtml}</div>
      <button type="button" class="add-question-btn" onclick="addQuestion(${secIdx})">＋ Tambah Pertanyaan</button>
    </div>
  `;
  
  container.appendChild(card);
  renumberSections();
}

function removeSection(idx) {
  var el = document.getElementById('section-' + idx);
  if (el) { el.remove(); renumberSections(); }
}

function renumberSections() {
  var cards = document.querySelectorAll('#sectionsContainer .section-card');
  cards.forEach((card, i) => {
    var num = card.querySelector('.section-num');
    if (num) num.textContent = i + 1;
  });
}

function addQuestion(secIdx) {
  const container = document.getElementById('questions-' + secIdx);
  if (!container) return;
  
  const idx = container.children.length;
  const qId = secIdx + '_' + idx;
  
  const card = document.createElement('div');
  card.className = 'question-card';
  card.id = 'q-' + qId;
  
  card.innerHTML = `
    <div class="question-number">${idx + 1}</div>
    <button type="button" class="remove-question" onclick="removeQuestion(${secIdx}, ${idx})">✕</button>
    <div class="question-header">
      <div class="form-group q-text">
        <label class="form-label" style="font-size:11px;">Pertanyaan</label>
        <input type="text" name="sections[${secIdx}][questions][${idx}][question]" class="form-input" placeholder="Tulis pertanyaan..." required style="font-size:12px;padding:6px 10px;">
      </div>
      <div class="form-group q-type">
        <label class="form-label" style="font-size:11px;">Tipe</label>
        <select name="sections[${secIdx}][questions][${idx}][type]" class="form-select" onchange="toggleOptions(${secIdx}, ${idx}, this.value)" style="font-size:12px;padding:6px 10px;">
          <option value="scale">Skala 1-5</option>
          <option value="multiple_choice">Pilihan Ganda</option>
          <option value="text">Isian Teks</option>
        </select>
      </div>
      <div class="q-required">
        <label class="form-check" style="font-size:11px;">
          <input type="hidden" name="sections[${secIdx}][questions][${idx}][is_required]" value="0">
          <input type="checkbox" name="sections[${secIdx}][questions][${idx}][is_required]" value="1" checked>
          <span>Wajib</span>
        </label>
      </div>
    </div>
    <div class="options-area" id="opts-${qId}" style="display:none;">
      <div class="form-label" style="margin-bottom:6px;font-size:11px;">Opsi Jawaban</div>
      <div id="optsList-${qId}"></div>
      <button type="button" class="add-option" onclick="addOption(${secIdx}, ${idx})">＋ Tambah</button>
    </div>
  `;
  
  container.appendChild(card);
  addOption(secIdx, idx);
  addOption(secIdx, idx);
}

function removeQuestion(secIdx, qIdx) {
  const el = document.getElementById('q-' + secIdx + '_' + qIdx);
  if (el) {
    el.remove();
    renumberQuestions(secIdx);
  }
}

function renumberQuestions(secIdx) {
  const container = document.getElementById('questions-' + secIdx);
  if (!container) return;
  const cards = container.querySelectorAll('.question-card');
  cards.forEach((card, i) => {
    const num = card.querySelector('.question-number');
    if (num) num.textContent = i + 1;
  });
}

function toggleOptions(secIdx, qIdx, type) {
  const area = document.getElementById('opts-' + secIdx + '_' + qIdx);
  if (area) {
    area.style.display = type === 'multiple_choice' ? 'block' : 'none';
    if (type === 'multiple_choice') {
      const list = document.getElementById('optsList-' + secIdx + '_' + qIdx);
      if (list && list.children.length === 0) {
        addOption(secIdx, qIdx);
        addOption(secIdx, qIdx);
      }
    }
  }
}

function addOption(secIdx, qIdx) {
  const list = document.getElementById('optsList-' + secIdx + '_' + qIdx);
  if (!list) return;
  const oi = list.children.length + 1;
  const row = document.createElement('div');
  row.className = 'option-row';
  row.innerHTML = `<input type="text" name="sections[${secIdx}][questions][${qIdx}][options][]" placeholder="Opsi ${oi}" style="font-size:12px;padding:6px 10px;">
    <button type="button" class="remove-option" onclick="this.parentElement.remove()">✕</button>`;
  list.appendChild(row);
}

function escHtml(str) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(str || ''));
  return div.innerHTML;
}

function validateForm(event) {
  event.preventDefault();
  console.log('=== Form Validation Start ===');
  
  const title = document.querySelector('input[name="title"]').value.trim();
  console.log('Title:', title);
  if (!title) {
    alert('⚠️ Judul survey tidak boleh kosong!');
    return false;
  }

  const sections = document.querySelectorAll('#sectionsContainer .section-card');
  console.log('Sections found:', sections.length);
  if (sections.length === 0) {
    alert('⚠️ Tambahkan minimal 1 section!');
    return false;
  }

  let isValid = true;
  sections.forEach((section, sIdx) => {
    const secTitle = section.querySelector('input[name*="[title]"]');
    if (!secTitle) {
      console.warn(`Section ${sIdx}: Title input not found`);
      isValid = false;
      return;
    }
    const secTitleVal = secTitle.value.trim();
    console.log(`Section ${sIdx} title:`, secTitleVal);
    if (!secTitleVal) {
      alert(`⚠️ Section ${sIdx + 1}: Judul section wajib diisi!`);
      isValid = false;
      return;
    }

    const questions = section.querySelectorAll('.question-card');
    console.log(`Section ${sIdx} questions found:`, questions.length);
    if (questions.length === 0) {
      alert(`⚠️ Section ${sIdx + 1}: Tambahkan minimal 1 pertanyaan!`);
      isValid = false;
      return;
    }

    questions.forEach((q, qIdx) => {
      const questionInput = q.querySelector('input[name*="[question]"]');
      if (!questionInput) {
        console.warn(`Section ${sIdx}, Question ${qIdx}: Question input not found`);
        isValid = false;
        return;
      }
      const questionVal = questionInput.value.trim();
      console.log(`Section ${sIdx}, Question ${qIdx}:`, questionVal);
      if (!questionVal) {
        alert(`⚠️ Section ${sIdx + 1}, Pertanyaan ${qIdx + 1}: Teks pertanyaan wajib diisi!`);
        isValid = false;
        return;
      }
    });
  });

  if (!isValid) {
    console.log('Validation failed');
    return false;
  }

  console.log('=== Form Validation Success - Submitting ===');
  document.getElementById('surveyForm').submit();
}

// Load existing sections
const existingSections = @json($survey->sections()->orderBy('order')->with('questions')->get());
if (existingSections.length > 0) {
  existingSections.forEach(s => {
    addSection({
      title: s.title,
      description: s.description,
      questions: s.questions.map(q => ({
        question: q.question,
        type: q.type,
        is_required: q.is_required,
        options: q.options || []
      }))
    });
  });
} else {
  addSection();
}
</script>
</body>
</html>
