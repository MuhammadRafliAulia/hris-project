<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Terima Kasih</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{margin:0;font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;background:#f0f4f8;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
.card{max-width:480px;width:100%;background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:48px 32px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.06);}
.icon{font-size:64px;margin-bottom:16px;}
.title{font-size:22px;font-weight:800;color:#0f172a;margin-bottom:8px;}
.desc{font-size:14px;color:#64748b;line-height:1.6;margin-bottom:24px;}
.divider{width:60px;height:3px;background:linear-gradient(90deg,#003e6f,#0a5a9e);border-radius:2px;margin:0 auto 24px;}
.survey-title{font-size:12px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
</style>
</head>
<body>
<div class="card">
  <div class="icon">🎉</div>
  <div class="title">Terima Kasih!</div>
  <div class="divider"></div>
  <div class="desc">
    Jawaban Anda telah berhasil dikirim.<br>
    Terima kasih atas partisipasi dan masukan Anda untuk membantu kami menjadi lebih baik.
  </div>
  <div class="survey-title">{{ $survey->title }}</div>
</div>
</body>
</html>
