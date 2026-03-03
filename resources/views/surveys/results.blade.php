<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hasil Survey - HRIS</title>
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
.btn-success{background:var(--success);color:#fff;}
.btn-success:hover{background:#059669;}

.content{background:var(--card);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius) var(--radius);padding:24px;}
.stats-row{display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.overview-card{flex:1;min-width:140px;padding:16px;background:#f8fafc;border:1px solid var(--border);border-radius:10px;text-align:center;}
.overview-value{font-size:28px;font-weight:800;color:var(--primary);}
.overview-label{font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-top:2px;}

.question-result{background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:16px;}
.qr-header{display:flex;align-items:flex-start;gap:10px;margin-bottom:14px;}
.qr-number{width:28px;height:28px;background:var(--primary);color:#fff;border-radius:8px;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.qr-title{font-size:14px;font-weight:600;color:var(--text);}
.qr-type{font-size:10px;color:var(--text-muted);margin-top:2px;}
.qr-body{margin-left:38px;}

/* Scale visualization */
.scale-summary{display:flex;align-items:center;gap:20px;margin-bottom:14px;flex-wrap:wrap;}
.scale-avg{font-size:36px;font-weight:800;color:var(--primary);}
.scale-avg small{font-size:14px;font-weight:400;color:var(--text-muted);}
.scale-bars{flex:1;min-width:200px;}
.scale-bar-row{display:flex;align-items:center;gap:8px;margin-bottom:4px;}
.scale-bar-label{font-size:11px;font-weight:600;color:var(--text-secondary);width:14px;text-align:center;}
.scale-bar-track{flex:1;height:18px;background:#e2e8f0;border-radius:4px;overflow:hidden;position:relative;}
.scale-bar-fill{height:100%;border-radius:4px;transition:width .4s ease;}
.scale-bar-fill.s5{background:linear-gradient(90deg,#10b981,#34d399);}
.scale-bar-fill.s4{background:linear-gradient(90deg,#3b82f6,#60a5fa);}
.scale-bar-fill.s3{background:linear-gradient(90deg,#f59e0b,#fbbf24);}
.scale-bar-fill.s2{background:linear-gradient(90deg,#f97316,#fb923c);}
.scale-bar-fill.s1{background:linear-gradient(90deg,#ef4444,#f87171);}
.scale-bar-count{font-size:10px;color:var(--text-muted);width:40px;text-align:right;}

/* Multiple choice visualization */
.choice-row{display:flex;align-items:center;gap:10px;margin-bottom:6px;}
.choice-label{font-size:12px;color:var(--text);min-width:120px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.choice-bar-track{flex:1;height:22px;background:#e2e8f0;border-radius:6px;overflow:hidden;position:relative;}
.choice-bar-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--primary-light));border-radius:6px;transition:width .4s ease;display:flex;align-items:center;padding:0 8px;min-width:fit-content;}
.choice-bar-text{font-size:10px;color:#fff;font-weight:600;white-space:nowrap;}
.choice-count{font-size:11px;color:var(--text-muted);width:60px;text-align:right;}

/* Text responses */
.text-responses{max-height:300px;overflow-y:auto;}
.text-response-item{padding:8px 12px;background:var(--card);border:1px solid var(--border);border-radius:8px;margin-bottom:6px;font-size:12px;color:var(--text);}
.no-responses{color:var(--text-muted);font-size:12px;font-style:italic;}

.link-box{background:#f0f7ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.link-box input{flex:1;min-width:200px;padding:7px 12px;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;background:#fff;}
.copy-btn{padding:6px 14px;background:var(--primary);color:#fff;border:none;border-radius:6px;font-size:11px;cursor:pointer;font-weight:600;}
.copy-btn:hover{background:var(--primary-light);}
</style>
</head>
<body>
<div style="display:flex;min-height:100vh;background:var(--bg);">
@include('layouts.sidebar')
<div style="flex:1;display:flex;flex-direction:column;min-width:0;">
  <div style="display:flex;justify-content:center;padding:24px 20px 0;flex:1;">
    <div style="max-width:1000px;width:100%;">

      <div class="page-header">
        <div>
          <div class="page-title">📊 Hasil Survey</div>
          <div class="page-subtitle">{{ $survey->title }}</div>
        </div>
        <div class="header-actions">
          <a href="{{ route('surveys.export', $survey) }}" class="btn btn-success">📥 Export Excel</a>
          <a href="{{ route('surveys.index') }}" class="btn btn-white">← Kembali</a>
        </div>
      </div>

      <div class="content">
        @if($survey->status === 'active')
        <div class="link-box">
          <span style="font-size:12px;font-weight:600;color:var(--primary);">📎 Link Survey:</span>
          <input type="text" value="{{ $survey->public_url }}" readonly id="surveyLink">
          <button class="copy-btn" onclick="copyLink()">📋 Copy Link</button>
        </div>
        @endif

        <div class="stats-row">
          <div class="overview-card">
            <div class="overview-value">{{ $survey->responses->count() }}</div>
            <div class="overview-label">Total Responden</div>
          </div>
          <div class="overview-card">
            <div class="overview-value">{{ $survey->questions->count() }}</div>
            <div class="overview-label">Total Pertanyaan</div>
          </div>
          <div class="overview-card">
            <div class="overview-value">
              <span class="badge badge-{{ $survey->status }}" style="font-size:13px;padding:4px 14px;">{{ ucfirst($survey->status) }}</span>
            </div>
            <div class="overview-label">Status</div>
          </div>
        </div>

        @if($survey->responses->count() === 0)
        <div style="padding:40px;text-align:center;">
          <div style="font-size:40px;margin-bottom:8px;">📭</div>
          <div style="font-size:14px;color:var(--text-muted);">Belum ada responden yang mengisi survey ini.</div>
        </div>
        @else

        {{-- ===== RESPONDENT LIST TABLE ===== --}}
        <div style="margin-bottom:20px;padding-top:20px;border-top:2px solid var(--border);">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
            <div style="font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:6px;">
              📋 Daftar Responden
              <span style="font-size:11px;color:var(--text-muted);font-weight:400;">({{ $survey->responses->count() }} orang)</span>
            </div>
            <div style="display:flex;gap:6px;align-items:center;">
              <span id="selectedCount" style="font-size:11px;color:var(--text-muted);display:none;">0 dipilih</span>
              <button type="button" id="bulkDeleteBtn" onclick="submitBulkDelete()" style="display:none;padding:5px 12px;background:#ef4444;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit;">🗑️ Hapus Terpilih</button>
            </div>
          </div>

          <form id="bulkDeleteForm" method="POST" action="{{ route('surveys.bulk-delete-responses', $survey) }}">
            @csrf
            <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
              <thead>
                <tr>
                  <th style="padding:8px 10px;text-align:center;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;width:36px;">
                    <input type="checkbox" id="checkAll" onchange="toggleAll(this)" style="width:15px;height:15px;cursor:pointer;">
                  </th>
                  <th style="padding:8px 10px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;">#</th>
                  @if(!$survey->is_anonymous)
                  <th style="padding:8px 10px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;">Nama</th>
                  <th style="padding:8px 10px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;">Dept</th>
                  <th style="padding:8px 10px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;">NIK</th>
                  @endif
                  <th style="padding:8px 10px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;">Waktu Submit</th>
                  <th style="padding:8px 10px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;">Jawaban</th>
                  <th style="padding:8px 10px;text-align:center;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;width:60px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($survey->responses->sortByDesc('created_at') as $ri => $resp)
                <tr style="border-bottom:1px solid #f1f5f9;">
                  <td style="padding:7px 10px;font-size:12px;text-align:center;">
                    <input type="checkbox" name="ids[]" value="{{ $resp->id }}" class="resp-check" onchange="updateSelected()" style="width:15px;height:15px;cursor:pointer;">
                  </td>
                  <td style="padding:7px 10px;font-size:12px;color:var(--text-muted);">{{ $ri + 1 }}</td>
                  @if(!$survey->is_anonymous)
                  <td style="padding:7px 10px;font-size:12px;font-weight:500;color:var(--text);">{{ $resp->respondent_name ?: '-' }}</td>
                  <td style="padding:7px 10px;font-size:12px;color:var(--text-secondary);">{{ $resp->respondent_department ?: '-' }}</td>
                  <td style="padding:7px 10px;font-size:12px;color:var(--text-secondary);">{{ $resp->respondent_nik ?: '-' }}</td>
                  @endif
                  <td style="padding:7px 10px;font-size:11px;color:var(--text-secondary);">{{ $resp->created_at->format('d/m/Y H:i') }}</td>
                  <td style="padding:7px 10px;font-size:11px;color:var(--text-muted);">{{ $resp->answers->count() }} jawaban</td>
                  <td style="padding:7px 10px;text-align:center;">
                    <button type="button" onclick="deleteSingle('{{ route('surveys.delete-response', [$survey, $resp]) }}')" style="padding:3px 8px;background:#fecaca;color:#dc2626;border:none;border-radius:5px;font-size:10px;cursor:pointer;font-weight:600;">🗑️</button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            </div>
          </form>
        </div>

        {{-- ===== QUESTION RESULTS VISUALIZATION ===== --}}

        @foreach($results as $qIdx => $result)
        <div class="question-result">
          <div class="qr-header">
            <div class="qr-number">{{ $qIdx + 1 }}</div>
            <div>
              <div class="qr-title">{{ $result['question']->question }}</div>
              <div class="qr-type">
                @if($result['question']->type === 'scale') 📊 Skala 1-5
                @elseif($result['question']->type === 'multiple_choice') 📝 Pilihan Ganda
                @else 💬 Isian Teks
                @endif
                @if($result['question']->is_required) <span style="color:var(--danger);">• Wajib</span> @endif
              </div>
            </div>
          </div>
          <div class="qr-body">
            @if($result['question']->type === 'scale')
              @php $totalScaleResp = array_sum($result['distribution']); @endphp
              <div class="scale-summary">
                <div class="scale-avg">{{ number_format($result['average'], 1) }}<small>/5</small></div>
                <div class="scale-bars">
                  @for($s = 5; $s >= 1; $s--)
                  @php $cnt = $result['distribution'][$s] ?? 0; $pct = $totalScaleResp > 0 ? ($cnt / $totalScaleResp * 100) : 0; @endphp
                  <div class="scale-bar-row">
                    <div class="scale-bar-label">{{ $s }}</div>
                    <div class="scale-bar-track">
                      <div class="scale-bar-fill s{{ $s }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="scale-bar-count">{{ $cnt }} ({{ number_format($pct, 0) }}%)</div>
                  </div>
                  @endfor
                </div>
              </div>
            @elseif($result['question']->type === 'multiple_choice')
              @php $totalChoices = $result['choices']->sum(); @endphp
              @foreach($result['choices'] as $choice => $count)
              @php $pct = $totalChoices > 0 ? ($count / $totalChoices * 100) : 0; @endphp
              <div class="choice-row">
                <div class="choice-label" title="{{ $choice }}">{{ $choice }}</div>
                <div class="choice-bar-track">
                  <div class="choice-bar-fill" style="width:{{ max($pct, 2) }}%">
                    <span class="choice-bar-text">{{ number_format($pct, 0) }}%</span>
                  </div>
                </div>
                <div class="choice-count">{{ $count }} resp.</div>
              </div>
              @endforeach
            @else
              @if($result['texts']->isEmpty())
              <div class="no-responses">Belum ada jawaban teks.</div>
              @else
              <div class="text-responses">
                @foreach($result['texts'] as $txt)
                <div class="text-response-item">{{ $txt }}</div>
                @endforeach
              </div>
              @endif
            @endif
          </div>
        </div>
        @endforeach

        @endif
      </div>
    </div>
  </div>
</div>
</div>
<script>
function copyLink() {
  var inp = document.getElementById('surveyLink');
  if (!inp) return;
  inp.select();
  document.execCommand('copy');
  var btn = inp.nextElementSibling;
  btn.textContent = '✅ Copied!';
  setTimeout(() => { btn.textContent = '📋 Copy Link'; }, 2000);
}

function toggleAll(master) {
  var checks = document.querySelectorAll('.resp-check');
  checks.forEach(function(c) { c.checked = master.checked; });
  updateSelected();
}

function updateSelected() {
  var checks = document.querySelectorAll('.resp-check:checked');
  var countEl = document.getElementById('selectedCount');
  var btnEl = document.getElementById('bulkDeleteBtn');
  var masterEl = document.getElementById('checkAll');
  var allChecks = document.querySelectorAll('.resp-check');

  if (checks.length > 0) {
    countEl.style.display = '';
    countEl.textContent = checks.length + ' dipilih';
    btnEl.style.display = '';
  } else {
    countEl.style.display = 'none';
    btnEl.style.display = 'none';
  }
  if (masterEl) {
    masterEl.checked = allChecks.length > 0 && checks.length === allChecks.length;
  }
}

function submitBulkDelete() {
  var checks = document.querySelectorAll('.resp-check:checked');
  if (checks.length === 0) return;
  if (!confirm('Yakin hapus ' + checks.length + ' responden yang dipilih?')) return;
  document.getElementById('bulkDeleteForm').submit();
}

function deleteSingle(url) {
  if (!confirm('Yakin hapus responden ini?')) return;
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = url;
  form.style.display = 'none';
  var csrf = document.createElement('input');
  csrf.type = 'hidden'; csrf.name = '_token';
  csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  form.appendChild(csrf);
  var method = document.createElement('input');
  method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
  form.appendChild(method);
  document.body.appendChild(form);
  form.submit();
}
</script>
<style>
.badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:600;display:inline-block;}
.badge-active{background:#d1fae5;color:#059669;}
.badge-draft{background:#fef3c7;color:#d97706;}
.badge-closed{background:#fecaca;color:#dc2626;}
</style>
</body>
</html>
