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
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $s }}" id="q{{ $question->id }}_s{{ $s }}" {{ old('answers.'.$question->id) == $s ? 'checked' : '' }} {{ $question->is_required ? 'required' : '' }}>
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
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" id="q{{ $question->id }}_{{ Str::slug($opt) }}" {{ old('answers.'.$question->id) == $opt ? 'checked' : '' }} {{ $question->is_required ? 'required' : '' }}>
            <label for="q{{ $question->id }}_{{ Str::slug($opt) }}">
              <div class="choice-dot"></div>
              {{ $opt }}
            </label>
          </div>
          @endforeach
        </div>

        @else
        <textarea name="answers[{{ $question->id }}]" class="form-textarea" placeholder="Tulis jawaban Anda..." {{ $question->is_required ? 'required' : '' }}>{{ old('answers.'.$question->id) }}</textarea>
        @endif
      </div>
      @endforeach

      <button type="submit" class="submit-btn">📤 Kirim Jawaban</button>
    </div>
  </form>
</div>
</body>
</html>
