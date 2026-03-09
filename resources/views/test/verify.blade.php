<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Verifikasi Kredensial - {{ $bank->title }}</title>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
 <style>
 *{box-sizing:border-box;margin:0;padding:0}
 body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial;background:#f8fafc;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
 .container{max-width:460px;width:100%}
 .card{background:#fff;border:1px solid #e2e8f0;padding:32px 28px;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.06)}
 .card h2{font-size:20px;font-weight:700;color:#0f172a;margin-bottom:6px}
 .card p{color:#64748b;font-size:14px;margin-bottom:20px;line-height:1.5}
 .form-group{margin-bottom:16px}
 .form-group label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}
 .form-group input{width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;font-family:inherit;color:#0f172a;background:#fff;transition:border-color .15s,box-shadow .15s}
 .form-group input:focus{outline:none;border-color:#003e6f;box-shadow:0 0 0 3px rgba(0,62,111,0.08)}
 .btn{background:#003e6f;color:#fff;padding:12px 14px;border-radius:8px;border:none;cursor:pointer;width:100%;font-size:15px;font-weight:600;font-family:inherit;margin-top:4px;transition:background .15s}
 .btn:hover{background:#002a4f}
 .error{color:#dc2626;background:#fef2f2;border:1px solid #fecaca;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
 .info{font-size:13px;color:#94a3b8;margin-top:16px;text-align:center}
 @media (max-width: 768px) {
   body{padding:14px}
   .card{padding:24px 18px}
   .card h2{font-size:18px}
   .form-group input{font-size:16px;padding:11px 14px}
   .btn{font-size:16px;padding:13px}
 }
 @media (max-width: 400px) {
   body{padding:8px}
   .card{padding:20px 14px}
   .card h2{font-size:17px}
 }
 </style>
</head>
<body>
 <div class="container">
  <div class="card">
    <h2>Masukkan Kredensial</h2>
    <p>Masukkan username dan password yang diberikan untuk mengakses formulir pendaftaran.</p>

    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('test.verify', $slug) }}">
      @csrf
      <div class="form-group">
        <label for="applicant_username">Username</label>
        <input id="applicant_username" name="applicant_username" type="text" value="{{ old('applicant_username') }}" required placeholder="Masukkan username">
      </div>

      <div class="form-group">
        <label for="applicant_password">Password</label>
        <input id="applicant_password" name="applicant_password" type="password" required placeholder="Masukkan password">
      </div>

      <button class="btn" type="submit">Verifikasi</button>
    </form>

    <div class="info">Jika tidak punya kredensial, hubungi tim rekrutmen.</div>
  </div>
 </div>
</body>
</html>