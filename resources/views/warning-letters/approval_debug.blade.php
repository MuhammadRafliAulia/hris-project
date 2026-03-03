<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Debug Signature</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;background:#f8fafc;padding:20px} .card{max-width:900px;margin:24px auto;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:8px} pre{background:#0f172a;color:#fff;padding:12px;border-radius:6px;overflow:auto}</style>
</head>
<body>
  <div class="card">
    <h2>Debug: Invalid Signature for Approval Link</h2>
    <p>Informasi ini membantu mengetahui mengapa signature dinyatakan tidak valid.</p>
    <h3>Request URL</h3>
    <pre>{{ $info['request_full_url'] }}</pre>

    <h3>Generated Link (what system creates)</h3>
    <pre>{{ $info['generated_link'] }}</pre>

    <h3>App URL</h3>
    <pre>{{ $info['app_url'] }}</pre>

    <h3>Request Host & Forwarded Headers</h3>
    <pre>{{ json_encode($info['headers'], JSON_PRETTY_PRINT) }}</pre>

    <h3>Request Host for Debug</h3>
    <pre>{{ $info['request_scheme_host'] }}</pre>

    <p>Jika host/scheme berbeda antara generated link dan request, perbaiki <strong>APP_URL</strong> di file <strong>.env</strong> atau konfigurasikan proxy agar meneruskan header <code>X-Forwarded-Proto</code> dan <code>X-Forwarded-Host</code>.</p>
  </div>
</body>
</html>
