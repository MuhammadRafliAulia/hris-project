<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Formulir Biodata - {{ $bank->title }}</title>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
 <style>
 body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial;background:#fff;padding:20px}
 .container{max-width:600px;margin:40px auto}
 .card{background:#fff;border:1px solid #e6eef6;padding:28px;border-radius:10px}
 label{display:block;margin-bottom:8px;color:#334155;font-weight:600}
 input[type=text],input[type=email],input[type=tel],textarea,select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;margin-bottom:12px}
 textarea{min-height:100px}
 .btn{background:#003e6f;color:#fff;padding:10px 14px;border-radius:8px;border:none;cursor:pointer;width:100%}
 .info{font-size:13px;color:#64748b;margin-top:8px}
 </style>
</head>
<body>
 <div class="container">
  <div class="card">
    <h2 style="margin:0 0 12px 0">Formulir Biodata</h2>
    <p style="color:#64748b;margin:0 0 12px 0">Lengkapi biodata untuk melanjutkan ke tes.</p>

    @if($errors->any())
      <div style="color:#dc2626;margin-bottom:12px">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('test.start', $slug) }}">
      @csrf

      <label for="participant_name">Nama Lengkap *</label>
      <input id="participant_name" name="participant_name" type="text" value="{{ old('participant_name') }}" required>

      <label for="participant_email">Email *</label>
      <input id="participant_email" name="participant_email" type="email" value="{{ old('participant_email') }}" required>

      <label for="phone">Nomor Telepon *</label>
      <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required>

      <label for="address">Alamat *</label>
      <textarea id="address" name="address" required>{{ old('address') }}</textarea>

      <!-- For calon_karyawan we don't collect NIK / Departemen / Jabatan -->

      <button class="btn" type="submit">Lanjutkan ke Tes</button>
    </form>

    <div class="info">Jika ada masalah, hubungi tim rekrutmen.</div>
  </div>
 </div>
</body>
</html>