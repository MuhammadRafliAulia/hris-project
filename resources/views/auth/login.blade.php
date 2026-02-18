<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login</title>
  <style>
    :root{--blue:#003e6f;--muted:#94a3b8}
    html,body{height:100%;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial}
    .split{display:flex;height:100vh}
    .left{flex:1; background-size:cover !important; background-position:center center; background-repeat:no-repeat; position:relative;}
    .left .overlay{position:absolute;inset:0;background:rgba(0,0,0,0.70);} /* 40% overlay (reduced) */
    .left .brand{position:relative; z-index:2; color:#fff; padding:0px; display:flex; align-items:center; justify-content:center; height:100%; text-align:center}
    .left .brand-inner{max-width:640px}
    .left h2{margin:0 0 8px;font-size:26px;line-height:1.05;font-weight:500}
    .left p{margin:0;color:rgba(255,255,255,0.9)}

    .right{width:420px;min-width:320px;background:#fff;display:flex;align-items:center;justify-content:center}
    .login-box{width:100%;max-width:360px;padding:32px}
    .login-card{background:#fff;border:1px solid #e6eef6;padding:26px;border-radius:10px;box-shadow:0 6px 18px rgba(16,24,40,0.06)}
    .logo{display:flex;flex-direction:column;align-items:center;margin-top:-15px}
    .logo img{height:84px}
    h1{font-size:20px;margin:0 0 14px;color:#0f172a;text-align:center}
    label{display:block;font-size:13px;color:#334155;margin-bottom:6px}
    input[type=text], input[type=password]{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;color:#0f172a;box-sizing:border-box}
    .btn{margin-top:12px;width:100%;background:var(--blue);color:#fff;border:none;padding:10px 12px;border-radius:8px;font-size:15px;cursor:pointer}
    .btn:hover{background:#002a4f}
    .muted{font-size:8px;color:var(--muted);text-align:center;margin-top:5px}
    .small{font-size:10px;color:#64748b;text-align:center;margin-top:-30px}
    .error{color:#dc2626;font-size:13px;margin-top:8px}
    form>div{margin-bottom:12px}

    /* responsive */
    @media(max-width:820px){
      .split{flex-direction:column}
      .left{height:36vh}
      .right{width:100%;min-width:0}
      .left .brand{align-items:center;padding:18px}
    }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  <div class="split">
    <div class="left" style="background-image: url('{{ asset('sdi.jpg') }}'), linear-gradient(135deg,#0ea5a4,#3b82f6); background-size:cover; background-position:center;">
      <div class="overlay"></div>
      <div class="brand">
        <div class="brand-inner">
          <h2>ようこそ / Selamat Datang </h2>
          <p>Internal System Human Resources Shindengen Indonesia</p>
        </div>
      </div>
    </div>

    <div class="right">
      <div class="login-box">
        <div class="login-card">
          <h1>Masuk</h1>
          <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
            @csrf
            <div>
              <label for="email">Email / Username</label>
              <input id="email" type="text" name="email" value="{{ old('email') }}" placeholder="Masukkan email atau username" required autocomplete="off">
              @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="password">Password</label>
              <input id="password" type="password" name="password" placeholder="Masukkan password" required autocomplete="new-password">
              @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn">Masuk</button>
          </form>

          <div class="logo">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <div class="small">© 2026 Human Resources Internal. All rights reserved.</div>
          </div>
          <div class="muted">Belum punya akun? Hubungi Admin HR untuk pendaftaran.</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
