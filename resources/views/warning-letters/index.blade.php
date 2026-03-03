<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Surat Peringatan</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; display:flex; height:100vh; }
    .sidebar { width:280px; min-width:280px; flex-shrink:0; background:#fff; border-right:1px solid #e2e8f0; padding:20px; box-sizing:border-box; overflow-y:auto; }
    .sidebar h2 { font-size:16px; color:#0f172a; margin:0 0 16px 0; font-weight:600; }
    .sidebar-menu { list-style:none; margin:0; padding:0; }
    .sidebar-menu li { margin-bottom:8px; }
    .sidebar-menu a { display:block; padding:10px 12px; color:#334155; text-decoration:none; border-radius:6px; font-size:14px; transition:all 0.2s; }
    .sidebar-menu a:hover { background:#f1f5f9; color:#0f172a; }
    .sidebar-menu a.active { background:#dbeafe; color:#0c4a6e; font-weight:500; }
    .main { flex:1; display:flex; flex-direction:column; }
    .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; }
    .topbar h1 { margin:0; font-size:20px; color:#0f172a; }
    .content { flex:1; padding:24px; overflow-y:auto; }
    .card { background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:8px; margin-bottom:20px; }
    .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .header h2 { margin:0; font-size:18px; color:#0f172a; }
    .btn { background:#003e6f; color:#fff; border:none; padding:8px 14px; border-radius:6px; font-size:14px; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-small { padding:6px 10px; font-size:12px; }
    .btn-danger { background:#dc2626; }
    .btn-success { background:#10b981; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px; text-align:left; border-bottom:1px solid #e2e8f0; font-size:14px; }
    th { background:#f1f5f9; color:#334155; font-weight:600; }
    .sp-badge { padding:4px 10px; border-radius:4px; font-size:12px; font-weight:600; display:inline-block; }
    .sp-1 { background:#fef08a; color:#713f12; }
    .sp-2 { background:#fed7aa; color:#9a3412; }
    .sp-3 { background:#fecaca; color:#991b1b; }
    .status-badge { padding:4px 10px; border-radius:4px; font-size:11px; font-weight:600; display:inline-block; }
    .status-pending { background:#fef9c3; color:#854d0e; }
    .status-approved { background:#d1fae5; color:#065f46; }
    .approved-by { font-size:11px; color:#475569; margin-top:3px; }
    .empty { text-align:center; color:#64748b; padding:40px; }
    .success { background:#d1fae5; color:#065f46; padding:12px; border-radius:6px; margin-bottom:16px; font-size:13px; }

  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  @include('layouts.sidebar')
  <div class="main">
    <div class="topbar">
      <h1>Surat Peringatan</h1>
    </div>
    <div class="content">
      <div style="display:flex;gap:10px;align-items:center;margin-bottom:18px;flex-wrap:wrap;">
        <a href="{{ route('warning-letters.create') }}" class="btn" style="background:#003e6f;">+ Tambah Surat Peringatan</a>
        <a href="{{ route('warning-letters.export') }}" class="btn" style="background:#10b981;">📥 Export Excel</a>
        <a href="{{ route('warning-letters.import-form') }}" class="btn" style="background:#3b82f6;">📤 Import Excel</a>
        <a href="{{ route('warning-letters.template') }}" class="btn" style="background:#f59e0b;color:#fff;">📄 Download Template</a>
      </div>

      @if(optional(auth()->user())->isInternalHR() || optional(auth()->user())->isSuperAdmin())
        <div style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">
          @php $baseParams = request()->except(['page']); @endphp
          <a href="{{ route('warning-letters.index', array_merge($baseParams, ['view' => 'ready'])) }}" class="btn" style="background:{{ request('view')=='ready' ? '#065f46' : '#003e6f' }};">Ready for HR</a>
          <a href="{{ route('warning-letters.index', array_merge($baseParams, ['view' => 'archive'])) }}" class="btn" style="background:{{ request('view')=='archive' ? '#065f46' : '#003e6f' }};">Archive</a>
          <a href="{{ route('warning-letters.index', array_merge($baseParams, ['view' => 'all'])) }}" class="btn" style="background:{{ request('view')=='all' || !request('view') ? '#065f46' : '#003e6f' }};">All</a>
        </div>
      @endif

      @if(session('success'))
        <div class="success">{{ session('success') }}</div>
      @endif

      <form method="GET" action="" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama / Jabatan / Dept" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
        <select name="sp_level" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
          <option value="">Semua SP Level</option>
          <option value="1" {{ request('sp_level') == '1' ? 'selected' : '' }}>SP-1</option>
          <option value="2" {{ request('sp_level') == '2' ? 'selected' : '' }}>SP-2</option>
          <option value="3" {{ request('sp_level') == '3' ? 'selected' : '' }}>SP-3</option>
        </select>
        <button type="submit" class="btn" style="background:#003e6f;">Cari</button>
        @if(request('q') || request('sp_level'))
          <a href="{{ route('warning-letters.index') }}" class="btn" style="background:#64748b;">Reset</a>
        @endif
      </form>

      <div class="card">
        <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>No. Surat</th>
              <th>Nama</th>
              <th>Jabatan</th>
              <th>Departemen</th>
              <th>SP Level</th>
              <th>Alasan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($letters as $idx => $letter)
            <tr>
              <td>{{ $idx + 1 }}</td>
              <td>{{ $letter->tanggal_surat ? $letter->tanggal_surat->format('d/m/Y') : '-' }}</td>
              <td>{{ $letter->nomor_surat ?: '-' }}</td>
              <td>{{ $letter->nama }}</td>
              <td>{{ $letter->jabatan }}</td>
              <td>{{ $letter->departemen }}</td>
              <td><span class="sp-badge sp-{{ $letter->sp_level }}">{{ $letter->sp_label }}</span></td>
              <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $letter->alasan }}</td>
              <td>
                @if($letter->status === 'approved')
                  <span class="status-badge status-approved">✅ Approved</span>
                  @if($letter->approver)
                    <div class="approved-by">oleh: {{ $letter->approver->name }}</div>
                  @endif
                @elseif($letter->status === 'pending_hr')
                  <span class="status-badge" style="background:#dbeafe;color:#1e40af;">⏳ Menunggu HR</span>
                @else
                  <span class="status-badge status-pending">⏳ Pending</span>
                @endif
              </td>
              <td style="white-space:nowrap;">
                @if($letter->isPendingHr())
                  <a href="{{ route('warning-letters.sign-form', $letter) }}" class="btn btn-small" style="background:#065f46;">✍️ Sign HR</a>
                @elseif($letter->isPending())
                  <a href="{{ route('warning-letters.sign-form', $letter) }}" class="btn btn-small" style="background:#7c3aed;">✍️ Sign</a>
                @endif
                <a href="{{ route('warning-letters.show-pdf', $letter) }}" class="btn btn-small" style="background:#003e6f;" target="_blank">📄 PDF</a>
                <a href="{{ route('warning-letters.download-pdf', $letter) }}" class="btn btn-small" style="background:#10b981;">⬇ Download</a>
                <a href="{{ route('warning-letters.edit', $letter) }}" class="btn btn-small" style="background:#3b82f6;">Edit</a>
                <form method="POST" action="{{ route('warning-letters.destroy', $letter) }}" style="display:inline;">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Yakin hapus surat peringatan ini?')">Hapus</button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="10" class="empty">Belum ada data surat peringatan. Tambahkan data baru atau import dari file Excel.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        </div>
      </div>

      <div style="text-align:center; color:#64748b; font-size:12px; margin-top:20px;">
        copyright @2026 Shindengen HR Internal Team
      </div>
    </div>
  </div>
</body>
</html>
