<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Verifikasi Kredensial - {{ $bank->title }}</title>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
 <style>
 body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial;background:#fff;padding:20px}
 .container{max-width:480px;margin:40px auto}
 .card{background:#fff;border:1px solid #e6eef6;padding:28px;border-radius:10px}
 label{display:block;margin-bottom:8px;color:#334155;font-weight:600}
 input[type=text],input[type=email],input[type=password],textarea,select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;margin-bottom:12px}
 .btn{background:#003e6f;color:#fff;padding:10px 14px;border-radius:8px;border:none;cursor:pointer;width:100%}
 .error{color:#dc2626;margin-bottom:12px}
 .info{font-size:13px;color:#64748b;margin-top:8px}
 </style>
</head>
<body>
 <div class="container">
  <div class="card">
    <h2 style="margin:0 0 12px 0">Masukkan Kredensial</h2>
    <p style="color:#64748b;margin:0 0 12px 0">Masukkan username dan password yang diberikan untuk mengakses formulir pendaftaran.</p>

    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('test.verify', $slug) }}">
      @csrf
      <label for="applicant_username">Username</label>
      <input id="applicant_username" name="applicant_username" type="text" value="{{ old('applicant_username') }}" required>

      <label for="applicant_password">Password</label>
      <input id="applicant_password" name="applicant_password" type="password" required>

      <button class="btn" type="submit">Verifikasi</button>
    </form>

    <div class="info">Jika tidak punya kredensial, hubungi tim rekrutmen.</div>
  </div>
 </div>
</body>
</html>