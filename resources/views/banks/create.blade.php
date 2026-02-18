<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bank Soal Baru</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; padding:20px; }
    .container { max-width:600px; margin:0 auto; background:#fff; padding:24px; border-radius:8px; border:1px solid #e2e8f0; }
    h1 { font-size:20px; color:#0f172a; margin:0 0 18px 0; }
    label { display:block; font-size:13px; color:#334155; margin-bottom:6px; margin-top:14px; }
    input, textarea { width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; color:#0f172a; box-sizing:border-box; font-family:inherit; }
    textarea { resize:vertical; }
    .btn { background:#003e6f; color:#fff; border:none; padding:10px 12px; border-radius:6px; font-size:14px; cursor:pointer; margin-top:14px; }
    .btn-cancel { background:#64748b; margin-left:8px; }
    .error { color:#dc2626; font-size:12px; margin-top:4px; }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  <div class="container">
    <h1>Bank Soal Baru</h1>
    <form method="POST" action="{{ route('banks.store') }}">
      @csrf
      <label for="title">Judul Bank Soal</label>
      <input id="title" type="text" name="title" value="{{ old('title') }}" required>
      @error('title')<div class="error">{{ $message }}</div>@enderror

      <label for="description">Deskripsi</label>
      <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
      @error('description')<div class="error">{{ $message }}</div>@enderror

      <label for="duration_minutes">Waktu Pengerjaan (menit)</label>
      <div style="display:flex;align-items:center;gap:8px;">
        <input id="duration_minutes" type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" max="600" placeholder="Contoh: 60" style="width:180px;">
        <span style="font-size:13px;color:#64748b;">menit (kosongkan jika tanpa batas waktu)</span>
      </div>
      @error('duration_minutes')<div class="error">{{ $message }}</div>@enderror

      <label for="target">Tipe Peserta</label>
      <select id="target" name="target">
        <option value="karyawan" {{ old('target') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
        <option value="calon_karyawan" {{ old('target') === 'calon_karyawan' ? 'selected' : '' }}>Calon Karyawan</option>
      </select>
      @error('target')<div class="error">{{ $message }}</div>@enderror

      <div>
        <button type="submit" class="btn">Buat Bank Soal</button>
        <a href="{{ route('banks.index') }}" class="btn btn-cancel" style="text-decoration:none;">Batal</a>
      </div>
    </form>
  </div>
</body>
</html>
