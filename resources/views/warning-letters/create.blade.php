<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Surat Peringatan</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; display:flex; height:100vh; }
    .sidebar { width:280px; min-width:280px; flex-shrink:0; background:#fff; border-right:1px solid #e2e8f0; padding:20px; box-sizing:border-box; overflow-y:auto; }
    .sidebar h2 { font-size:16px; color:#0f172a; margin:0 0 16px 0; font-weight:600; }
    .sidebar-menu { list-style:none; margin:0; padding:0; }
    .sidebar-menu li { margin-bottom:8px; }
    .sidebar-menu a { display:block; padding:10px 12px; color:#334155; text-decoration:none; border-radius:6px; font-size:14px; }
    .sidebar-menu a:hover { background:#f1f5f9; }
    .main { flex:1; display:flex; flex-direction:column; }
    .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 24px; }
    .topbar h1 { margin:0; font-size:20px; color:#0f172a; }
    .content { flex:1; padding:24px; overflow-y:auto; }
    .card { background:#fff; border:1px solid #e2e8f0; padding:24px; border-radius:8px; max-width:600px; }
    h2 { font-size:18px; color:#0f172a; margin:0 0 20px 0; }
    label { display:block; font-size:13px; color:#334155; margin-bottom:6px; margin-top:14px; font-weight:600; }
    input, select, textarea { width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; color:#0f172a; box-sizing:border-box; font-family:inherit; }
    textarea { resize:vertical; min-height:80px; }
    .radio-group { display:flex; gap:16px; margin-top:6px; }
    .radio-group label { display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:400; margin-top:0; }
    .radio-group input[type=radio] { width:auto; }
    .sp-option { padding:8px 16px; border:2px solid #cbd5e1; border-radius:6px; cursor:pointer; transition:all 0.2s; text-align:center; }
    .sp-option:hover { border-color:#003e6f; }
    .sp-option.selected { border-color:#003e6f; background:#dbeafe; }
    .btn { background:#003e6f; color:#fff; border:none; padding:10px 16px; border-radius:6px; font-size:14px; cursor:pointer; margin-top:20px; }
    .btn-cancel { background:#64748b; margin-left:8px; text-decoration:none; display:inline-block; }
    .error { color:#dc2626; font-size:12px; margin-top:4px; }

    .info-box { background:#dbeafe; color:#0c4a6e; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  @include('layouts.sidebar')

  <div class="main">
    <div class="topbar">
      <h1>Tambah Surat Peringatan</h1>
    </div>
    
    <div class="content">
      <div class="card">
        <h2>Data Surat Peringatan Baru</h2>

        @if(session('success'))
          <div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:6px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
        @endif

        <div class="info-box">
          @if(isset($isAdminProd) && $isAdminProd)
            📋 Isi form di bawah ini. Data akan dikirim ke Super Admin.
          @else
            📋 Isi form di bawah ini. Setelah disimpan, PDF surat peringatan akan langsung ditampilkan.
          @endif
        </div>

        <form method="POST" action="{{ route('warning-letters.store') }}">
          @csrf

          <label for="nama">Nama Karyawan</label>
          <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama karyawan">
          @error('nama')<div class="error">{{ $message }}</div>@enderror

          <label for="nik">NIK</label>
          <input id="nik" type="text" name="nik" value="{{ old('nik') }}" placeholder="Masukkan NIK karyawan">
          @error('nik')<div class="error">{{ $message }}</div>@enderror

          <label for="jabatan">Jabatan</label>
          <input id="jabatan" type="text" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Masukkan jabatan">
          @error('jabatan')<div class="error">{{ $message }}</div>@enderror

          <label for="departemen">Departemen</label>
          <select id="departemen" name="departemen" required>
            <option value="">-- Pilih Departemen --</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->name }}" {{ old('departemen') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
          </select>
          @error('departemen')<div class="error">{{ $message }}</div>@enderror

          <label>SP Level (Surat Peringatan)</label>
          <div class="radio-group">
            <label style="padding:8px 16px;border:2px solid {{ old('sp_level') == '1' ? '#003e6f' : '#cbd5e1' }};border-radius:6px;background:{{ old('sp_level') == '1' ? '#fef08a' : '#fff' }};">
              <input type="radio" name="sp_level" value="1" {{ old('sp_level', '1') == '1' ? 'checked' : '' }} onchange="updateSpStyle(this)"> SP-1
            </label>
            <label style="padding:8px 16px;border:2px solid {{ old('sp_level') == '2' ? '#003e6f' : '#cbd5e1' }};border-radius:6px;background:{{ old('sp_level') == '2' ? '#fed7aa' : '#fff' }};">
              <input type="radio" name="sp_level" value="2" {{ old('sp_level') == '2' ? 'checked' : '' }} onchange="updateSpStyle(this)"> SP-2
            </label>
            <label style="padding:8px 16px;border:2px solid {{ old('sp_level') == '3' ? '#003e6f' : '#cbd5e1' }};border-radius:6px;background:{{ old('sp_level') == '3' ? '#fecaca' : '#fff' }};">
              <input type="radio" name="sp_level" value="3" {{ old('sp_level') == '3' ? 'checked' : '' }} onchange="updateSpStyle(this)"> SP-3
            </label>
          </div>
          @error('sp_level')<div class="error">{{ $message }}</div>@enderror

          <label for="alasan">Alasan / Pelanggaran</label>
          <textarea id="alasan" name="alasan" required placeholder="Jelaskan alasan pemberian surat peringatan">{{ old('alasan') }}</textarea>
          @error('alasan')<div class="error">{{ $message }}</div>@enderror

          <label for="paragraf_kedua">Paragraf Kedua (opsional)</label>
          <textarea id="paragraf_kedua" name="paragraf_kedua" placeholder="Isi paragraf tambahan setelah alasan (opsional)">{{ old('paragraf_kedua') }}</textarea>
          @error('paragraf_kedua')<div class="error">{{ $message }}</div>@enderror

          <label for="nomor_surat">Nomor Surat (opsional)</label>
          <input id="nomor_surat" type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" placeholder="Contoh: SP/001/II/2026">
          @error('nomor_surat')<div class="error">{{ $message }}</div>@enderror

          <label for="tanggal_surat">Tanggal Surat</label>
          <input id="tanggal_surat" type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}">
          @error('tanggal_surat')<div class="error">{{ $message }}</div>@enderror

          <label for="tempat">Tempat</label>
          <input id="tempat" type="text" name="tempat" value="{{ old('tempat') }}" placeholder="Contoh:Produksi Assy Regulator">
          @error('tempat')<div class="error">{{ $message }}</div>@enderror

          <label for="proses">Proses</label>
          <input id="proses" type="text" name="proses" value="{{ old('proses') }}" placeholder="Contoh: Produksi / Pengemasan">
          @error('proses')<div class="error">{{ $message }}</div>@enderror

          <div style="margin-top:24px;">
            @if(isset($isAdminProd) && $isAdminProd)
              <button type="submit" class="btn">📤 Kirim Surat Peringatan</button>
            @else
              <button type="submit" class="btn">💾 Simpan & Lihat PDF</button>
              <a href="{{ route('warning-letters.index') }}" class="btn btn-cancel">Batal</a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function updateSpStyle(radio) {
      var labels = document.querySelectorAll('.radio-group label');
      var colors = {'1': '#fef08a', '2': '#fed7aa', '3': '#fecaca'};
      labels.forEach(function(lbl) {
        var input = lbl.querySelector('input[type=radio]');
        if (input.checked) {
          lbl.style.borderColor = '#003e6f';
          lbl.style.background = colors[input.value] || '#fff';
        } else {
          lbl.style.borderColor = '#cbd5e1';
          lbl.style.background = '#fff';
        }
      });
    }
  </script>
</body>
</html>
