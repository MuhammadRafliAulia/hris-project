<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Engagement Survey - HRIS</title>
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
.btn-sm{padding:5px 12px;font-size:11px;}

.content{background:var(--card);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius) var(--radius);padding:0;}
.stats-bar{display:flex;gap:10px;padding:16px 24px;border-bottom:1px solid var(--border);background:#f8fafc;flex-wrap:wrap;}
.stat-card{flex:1;min-width:120px;padding:12px 16px;background:var(--card);border:1px solid var(--border);border-radius:10px;display:flex;align-items:center;gap:10px;}
.stat-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.stat-icon.total{background:linear-gradient(135deg,#dbeafe,#bfdbfe);}
.stat-icon.active{background:linear-gradient(135deg,#d1fae5,#a7f3d0);}
.stat-icon.draft{background:linear-gradient(135deg,#fef3c7,#fde68a);}
.stat-icon.closed{background:linear-gradient(135deg,#fecaca,#fca5a5);}
.stat-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:500;}
.stat-value{font-size:18px;font-weight:700;color:var(--text);line-height:1.2;}

.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th{padding:12px 16px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;border-bottom:2px solid var(--border);background:#f8fafc;}
td{padding:12px 16px;font-size:13px;color:var(--text);border-bottom:1px solid #f1f5f9;vertical-align:middle;}
tr:hover td{background:rgba(0,62,111,.01);}
.badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:600;display:inline-block;}
.badge-active{background:#d1fae5;color:#059669;}
.badge-draft{background:#fef3c7;color:#d97706;}
.badge-closed{background:#fecaca;color:#dc2626;}
.actions{display:flex;gap:4px;flex-wrap:wrap;}
.link-input{display:flex;align-items:center;gap:6px;}
.link-input input{padding:5px 8px;border:1px solid var(--border);border-radius:6px;font-size:11px;width:220px;color:var(--text-secondary);background:#f8fafc;}
.copy-btn{padding:4px 10px;background:var(--primary);color:#fff;border:none;border-radius:6px;font-size:10px;cursor:pointer;font-weight:600;transition:all .15s;}
.copy-btn:hover{background:var(--primary-light);}
.empty{padding:60px 20px;text-align:center;}
.empty-icon{font-size:48px;margin-bottom:12px;}
.empty-text{font-size:14px;color:var(--text-muted);}
</style>
</head>
<body>
<div style="display:flex;min-height:100vh;background:var(--bg);">
@include('layouts.sidebar')
<div style="flex:1;display:flex;flex-direction:column;min-width:0;">
  <div style="display:flex;justify-content:center;padding:24px 20px 0;flex:1;">
    <div style="max-width:1200px;width:100%;">

      @if(session('success'))
      <div style="background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;padding:10px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        ✅ {{ session('success') }}
      </div>
      @endif

      <div class="page-header">
        <div>
          <div class="page-title">📋 Engagement Survey</div>
          <div class="page-subtitle">Kelola survey kepuasan karyawan</div>
        </div>
        <div class="header-actions">
          <a href="{{ route('surveys.create') }}" class="btn btn-white">＋ Buat Survey</a>
        </div>
      </div>

      <div class="content">
        <div class="stats-bar">
          <div class="stat-card">
            <div class="stat-icon total">📊</div>
            <div><div class="stat-label">Total</div><div class="stat-value">{{ $surveys->count() }}</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon active">✅</div>
            <div><div class="stat-label">Active</div><div class="stat-value">{{ $surveys->where('status','active')->count() }}</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon draft">📝</div>
            <div><div class="stat-label">Draft</div><div class="stat-value">{{ $surveys->where('status','draft')->count() }}</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon closed">🔒</div>
            <div><div class="stat-label">Closed</div><div class="stat-value">{{ $surveys->where('status','closed')->count() }}</div></div>
          </div>
        </div>

        @if($surveys->isEmpty())
        <div class="empty">
          <div class="empty-icon">📋</div>
          <div class="empty-text">Belum ada survey. Klik <strong>Buat Survey</strong> untuk membuat yang pertama.</div>
        </div>
        @else
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Pertanyaan</th>
                <th>Responden</th>
                <th>Link</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($surveys as $i => $survey)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <div style="font-weight:600;">{{ $survey->title }}</div>
                  @if($survey->description)
                  <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ Str::limit($survey->description, 60) }}</div>
                  @endif
                </td>
                <td>
                  <span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span>
                </td>
                <td>{{ $survey->questions_count }} soal</td>
                <td>
                  <span style="font-weight:700;color:var(--primary);">{{ $survey->responses_count }}</span>
                </td>
                <td>
                  @if($survey->status === 'active')
                  <div class="link-input">
                    <input type="text" value="{{ $survey->public_url }}" readonly id="link-{{ $survey->id }}">
                    <button class="copy-btn" onclick="copyLink({{ $survey->id }})">📋 Copy</button>
                  </div>
                  @else
                  <span style="font-size:11px;color:var(--text-muted);">—</span>
                  @endif
                </td>
                <td>
                  <div class="actions">
                    <a href="{{ route('surveys.results', $survey) }}" class="btn btn-sm btn-ghost">📊 Hasil</a>
                    <a href="{{ route('surveys.edit', $survey) }}" class="btn btn-sm btn-ghost">✏️</a>
                    @if($survey->status === 'draft')
                    <form method="POST" action="{{ route('surveys.toggle-status', $survey) }}" style="margin:0;">
                      @csrf
                      <input type="hidden" name="status" value="active">
                      <button class="btn btn-sm" style="background:#d1fae5;color:#059669;">▶ Aktifkan</button>
                    </form>
                    @elseif($survey->status === 'active')
                    <form method="POST" action="{{ route('surveys.toggle-status', $survey) }}" style="margin:0;">
                      @csrf
                      <input type="hidden" name="status" value="closed">
                      <button class="btn btn-sm" style="background:#fef3c7;color:#d97706;">⏸ Tutup</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('surveys.toggle-status', $survey) }}" style="margin:0;">
                      @csrf
                      <input type="hidden" name="status" value="active">
                      <button class="btn btn-sm" style="background:#d1fae5;color:#059669;">▶ Aktifkan</button>
                    </form>
                    @endif
                    <a href="{{ route('surveys.export', $survey) }}" class="btn btn-sm btn-ghost">📥 Excel</a>
                    <form method="POST" action="{{ route('surveys.destroy', $survey) }}" style="margin:0;" onsubmit="return confirm('Yakin hapus survey ini?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
</div>
<script>
function copyLink(id) {
  var inp = document.getElementById('link-' + id);
  inp.select();
  document.execCommand('copy');
  var btn = inp.nextElementSibling;
  btn.textContent = '✅ Copied!';
  setTimeout(() => { btn.textContent = '📋 Copy'; }, 2000);
}
</script>
</body>
</html>
