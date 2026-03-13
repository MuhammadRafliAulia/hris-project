<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $survey->title }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{--primary:#003e6f;--primary-light:#0a5a9e;--bg:#f0f4f8;--card:#fff;--border:#e2e8f0;--text:#0f172a;--text-secondary:#64748b;--text-muted:#94a3b8;--danger:#ef4444;--radius:12px;}
body{margin:0;font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;background:var(--bg);min-height:100vh;display:flex;justify-content:center;padding:30px 16px;}
.container{max-width:680px;width:100%;}
.survey-header{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);padding:28px 32px;color:#fff;border-radius:var(--radius) var(--radius) 0 0;position:relative;overflow:hidden;}
.survey-header::before{content:'';position:absolute;top:-60px;right:-60px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.06);}
.survey-title{font-size:22px;font-weight:800;z-index:1;position:relative;}
.survey-desc{font-size:13px;color:rgba(255,255,255,.75);margin-top:6px;z-index:1;position:relative;line-height:1.5;}
.survey-body{background:var(--card);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius) var(--radius);padding:24px 28px;}

.identity-section{background:#f0f7ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:24px;}
.identity-title{font-size:13px;font-weight:700;color:var(--primary);margin-bottom:12px;}

.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:6px;}
.form-label .required{color:var(--danger);}
.form-input,.form-select,.form-textarea{width:100%;padding:9px 14px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;color:var(--text);background:var(--card);transition:border-color .15s;}
.form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,62,111,.08);}
.form-textarea{resize:vertical;min-height:80px;}

.question-card{background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:18px 20px;margin-bottom:14px;}
.q-header{display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;}
.q-number{width:26px;height:26px;background:var(--primary);color:#fff;border-radius:7px;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.q-text{font-size:13px;font-weight:600;color:var(--text);line-height:1.4;}
.q-text .required{color:var(--danger);margin-left:2px;}

/* Scale input */
.scale-group{display:flex;gap:6px;flex-wrap:wrap;}
.scale-option{flex:1;min-width:50px;}
.scale-option input{display:none;}
.scale-option label{display:flex;flex-direction:column;align-items:center;padding:10px 6px;border:2px solid var(--border);border-radius:8px;cursor:pointer;transition:all .15s;text-align:center;}
.scale-option label:hover{border-color:var(--primary-light);background:rgba(0,62,111,.02);}
.scale-option input:checked + label{border-color:var(--primary);background:rgba(0,62,111,.06);box-shadow:0 0 0 3px rgba(0,62,111,.1);}
.scale-value{font-size:18px;font-weight:800;color:var(--primary);}
.scale-label{font-size:9px;color:var(--text-muted);margin-top:2px;}

.section-progress{padding:0 28px 16px 28px;}
.progress-bar{height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;margin-bottom:8px;}
.progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--primary-light));transition:width 0.3s;}
.progress-text{font-size:12px;color:var(--text-secondary);text-align:center;}
.section-nav{display:flex;gap:12px;margin-top:24px;justify-content:space-between;}
.btn-prev{background:var(--text-secondary);color:#fff;padding:12px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-family:inherit;}
.btn-prev:hover{background:#334155;}
.btn-next{background:var(--primary);color:#fff;padding:12px 20px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-family:inherit;}
.btn-next:hover{background:var(--primary-light);}
.btn-submit{background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;width:100%;padding:14px;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s;margin-top:8px;}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,62,111,.25);}
.section-title-form{font-size:16px;font-weight:700;color:var(--primary);margin-bottom:8px;}
.section-desc-form{font-size:13px;color:var(--text-secondary);margin-bottom:16px;line-height:1.5;}
.hidden-section{display:none;}

/* Multiple choice */
.choice-group{display:flex;flex-direction:column;gap:6px;}
.choice-option input{display:none;}
.choice-option label{display:flex;align-items:center;gap:10px;padding:10px 14px;border:2px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;color:var(--text);transition:all .15s;}
.choice-option label:hover{border-color:var(--primary-light);background:rgba(0,62,111,.02);}
.choice-option input:checked + label{border-color:var(--primary);background:rgba(0,62,111,.06);}
.choice-dot{width:18px;height:18px;border:2px solid var(--border);border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.choice-option input:checked + label .choice-dot{border-color:var(--primary);background:var(--primary);}
.choice-option input:checked + label .choice-dot::after{content:'';width:6px;height:6px;background:#fff;border-radius:50%;}

.submit-btn{width:100%;padding:14px;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s;margin-top:8px;}
.submit-btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,62,111,.25);}

.error-box{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;}
.error-box ul{margin:4px 0 0 16px;}

/* === MOBILE ≤768px === */
@media (max-width: 768px) {
  body { padding: 12px 6px; }
  .container { max-width: 100%; }
  .survey-header { padding: 20px 16px; border-radius: 10px 10px 0 0; }
  .survey-title { font-size: 18px; }
  .survey-desc { font-size: 12px; }
  .survey-body { padding: 16px 12px; }
  .identity-section { padding: 12px; }
  .identity-title { font-size: 12px; }
  .form-input, .form-select, .form-textarea { font-size: 14px; padding: 10px 12px; }
  .question-card { padding: 14px; margin-bottom: 10px; }
  .q-number { width: 24px; height: 24px; font-size: 10px; border-radius: 6px; }
  .q-text { font-size: 13px; }
  .scale-group { gap: 4px; }
  .scale-option { min-width: 44px; }
  .scale-option label { padding: 8px 4px; border-radius: 6px; }
  .scale-value { font-size: 15px; }
  .scale-label { font-size: 8px; }
  .choice-option label { padding: 10px 12px; font-size: 13px; }
  .submit-btn { font-size: 14px; padding: 13px; }
}

/* === SMALL MOBILE ≤400px === */
@media (max-width: 400px) {
  body { padding: 8px 4px; }
  .survey-header { padding: 16px 12px; }
  .survey-title { font-size: 16px; }
  .survey-body { padding: 12px 8px; }
  .question-card { padding: 12px 10px; }
  .scale-group { gap: 3px; }
  .scale-option { min-width: 38px; }
  .scale-option label { padding: 6px 2px; }
  .scale-value { font-size: 14px; }
  .scale-label { font-size: 7px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 50px; }
  .form-input, .form-select, .form-textarea { font-size: 13px; padding: 8px 10px; }
}
</style>
</head>
<body>
<div class="container">
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

  <form method="POST" action="{{ url('survey/' . $survey->token . '/submit') }}">
    @csrf
    <div class="survey-header">
      <div class="survey-title">{{ $survey->title }}</div>
      @if($survey->description)
      <div class="survey-desc">{{ $survey->description }}</div>
      @endif
    </div>

    <div class="survey-body">
      @if(!$survey->is_anonymous)
      <div class="identity-section">
        <div class="identity-title">📋 Identitas Responden</div>
        <div class="form-group">
          <label class="form-label">Nama <span class="required">*</span></label>
          <input type="text" name="respondent_name" class="form-input" value="{{ old('respondent_name') }}" required placeholder="Nama lengkap">
        </div>
        <div class="form-group">
          <label class="form-label">Departemen</label>
          <select name="respondent_department" class="form-select">
            <option value="">— Pilih Departemen —</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->name }}" {{ old('respondent_department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">NIK</label>
          <input type="text" name="respondent_nik" class="form-input" value="{{ old('respondent_nik') }}" placeholder="Nomor Induk Karyawan">
        </div>
      </div>
      @endif

      @php
        $allSections = $survey->sections->sortBy('order')->values();
        $totalSections = $allSections->count();
      @endphp

      @if($totalSections > 0)
      <div class="section-progress">
        <div class="progress-bar">
          <div class="progress-fill" id="progressFill" style="width: {{ $totalSections > 0 ? (1 / $totalSections) * 100 : 100 }}%"></div>
        </div>
        <div class="progress-text" id="progressText">
          Section 1 dari {{ $totalSections }}
        </div>
      </div>

      @foreach($allSections as $sIdx => $section)
      <div class="section-panel" data-section="{{ $sIdx }}" style="{{ $sIdx > 0 ? 'display:none;' : '' }}">
        <div class="section-title-form">{{ $section->title }}</div>
        @if($section->description)
        <div class="section-desc-form">{{ $section->description }}</div>
        @endif

        @foreach($section->questions->sortBy('order') as $qIdx => $question)
        <div class="question-card">
          <div class="q-header">
            <div class="q-number">{{ $qIdx + 1 }}</div>
            <div class="q-text">
              {{ $question->question }}
              @if($question->is_required)<span class="required">*</span>@endif
            </div>
          </div>

          @if($question->type === 'scale')
          <div class="scale-group">
            @php $scaleLabels = ['','Sangat Tidak Setuju','Tidak Setuju','Netral','Setuju','Sangat Setuju']; @endphp
            @for($s = 1; $s <= 5; $s++)
            <div class="scale-option">
              <input type="radio" name="answers[{{ $question->id }}]" value="{{ $s }}" id="q{{ $question->id }}_s{{ $s }}" {{ old('answers.'.$question->id) == $s ? 'checked' : '' }}>
              <label for="q{{ $question->id }}_s{{ $s }}">
                <span class="scale-value">{{ $s }}</span>
                <span class="scale-label">{{ $scaleLabels[$s] }}</span>
              </label>
            </div>
            @endfor
          </div>

          @elseif($question->type === 'multiple_choice')
          <div class="choice-group">
            @foreach($question->options as $opt)
            <div class="choice-option">
              <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" id="q{{ $question->id }}_{{ Str::slug($opt) }}" {{ old('answers.'.$question->id) == $opt ? 'checked' : '' }}>
              <label for="q{{ $question->id }}_{{ Str::slug($opt) }}">
                <div class="choice-dot"></div>
                {{ $opt }}
              </label>
            </div>
            @endforeach
          </div>

          @else
          <textarea name="answers[{{ $question->id }}]" class="form-textarea" placeholder="Tulis jawaban Anda...">{{ old('answers.'.$question->id) }}</textarea>
          @endif
        </div>
        @endforeach
      </div>
      @endforeach

      {{-- SECTION NAVIGATION --}}
      <div class="section-nav">
        <button type="button" class="btn-prev" id="btnPrev" style="display:none;" onclick="changeSection(-1)">← Sebelumnya</button>
        <div id="navSpacer"></div>
        <button type="button" class="btn-next" id="btnNext" onclick="changeSection(1)">Selanjutnya →</button>
        <button type="submit" class="btn-submit" id="btnSubmit" style="display:none;">📤 Kirim Jawaban</button>
      </div>

      @else
      {{-- NO SECTIONS (fallback) --}}
      @foreach($survey->questions->sortBy('order') as $qIdx => $question)
      <div class="question-card">
        <div class="q-header">
          <div class="q-number">{{ $qIdx + 1 }}</div>
          <div class="q-text">
            {{ $question->question }}
            @if($question->is_required)<span class="required">*</span>@endif
          </div>
        </div>

        @if($question->type === 'scale')
        <div class="scale-group">
          @php $scaleLabels = ['','Sangat Tidak Setuju','Tidak Setuju','Netral','Setuju','Sangat Setuju']; @endphp
          @for($s = 1; $s <= 5; $s++)
          <div class="scale-option">
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $s }}" id="q{{ $question->id }}_s{{ $s }}" {{ old('answers.'.$question->id) == $s ? 'checked' : '' }}>
            <label for="q{{ $question->id }}_s{{ $s }}">
              <span class="scale-value">{{ $s }}</span>
              <span class="scale-label">{{ $scaleLabels[$s] }}</span>
            </label>
          </div>
          @endfor
        </div>

        @elseif($question->type === 'multiple_choice')
        <div class="choice-group">
          @foreach($question->options as $opt)
          <div class="choice-option">
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" id="q{{ $question->id }}_{{ Str::slug($opt) }}" {{ old('answers.'.$question->id) == $opt ? 'checked' : '' }}>
            <label for="q{{ $question->id }}_{{ Str::slug($opt) }}">
              <div class="choice-dot"></div>
              {{ $opt }}
            </label>
          </div>
          @endforeach
        </div>

        @else
        <textarea name="answers[{{ $question->id }}]" class="form-textarea" placeholder="Tulis jawaban Anda...">{{ old('answers.'.$question->id) }}</textarea>
        @endif
      </div>
      @endforeach

      <button type="submit" class="btn-submit">📤 Kirim Jawaban</button>
      @endif
    </div>
  </form>
</div>

<script>
const totalSections = {{ $totalSections }};
let currentSection = 0;

function changeSection(direction) {
  const panels = document.querySelectorAll('.section-panel');
  const current = panels[currentSection];

  // Validate current section before moving forward
  if (direction > 0) {
    const radios = {};
    const textareas = [];
    current.querySelectorAll('input[type="radio"]').forEach(r => {
      if (!radios[r.name]) radios[r.name] = false;
      if (r.checked) radios[r.name] = true;
    });
    current.querySelectorAll('textarea').forEach(t => textareas.push(t));

    // Check identity section if on first section
    if (currentSection === 0) {
      const nameInput = document.querySelector('input[name="respondent_name"]');
      if (nameInput && nameInput.hasAttribute('required') && !nameInput.value.trim()) {
        alert('Silakan isi nama responden terlebih dahulu.');
        nameInput.focus();
        return;
      }
    }

    // We don't block navigation for unanswered optional questions,
    // but we keep all answers in the DOM regardless
  }

  // Hide current, show target
  current.style.display = 'none';
  currentSection += direction;
  panels[currentSection].style.display = '';

  // Update progress
  const pct = ((currentSection + 1) / totalSections) * 100;
  document.getElementById('progressFill').style.width = pct + '%';
  document.getElementById('progressText').textContent = 'Section ' + (currentSection + 1) + ' dari ' + totalSections;

  // Update buttons
  document.getElementById('btnPrev').style.display = currentSection > 0 ? '' : 'none';
  document.getElementById('navSpacer').style.display = currentSection > 0 ? 'none' : '';
  document.getElementById('btnNext').style.display = currentSection < totalSections - 1 ? '' : 'none';
  document.getElementById('btnSubmit').style.display = currentSection === totalSections - 1 ? '' : 'none';

  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Handle initial state for single-section surveys
if (totalSections <= 1) {
  const btnNext = document.getElementById('btnNext');
  const btnSubmit = document.getElementById('btnSubmit');
  if (btnNext) btnNext.style.display = 'none';
  if (btnSubmit) btnSubmit.style.display = '';
}
</script>
</body>
</html>
