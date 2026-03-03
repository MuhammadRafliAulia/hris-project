<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Hasil Approval</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;background:#f7fafc;padding:24px} .card{max-width:680px;margin:40px auto;background:#fff;padding:20px;border-radius:8px;border:1px solid #e2e8f0} .ok{color:#065f46} .bad{color:#9a1f1f}</style>
</head>
<body>
  <div class="card">
    @if($action === 'already_approved')
      <h2 class="ok">Surat sudah disetujui sepenuhnya</h2>
      <p>Surat ini sudah memiliki tanda tangan final.</p>
    @elseif($action === 'approved')
      <h2 class="ok">Terima kasih — Approval berhasil</h2>
      <p>Anda telah menyetujui layer {{ $layer }} untuk {{ $warningLetter->nama }}.</p>
    @elseif($action === 'rejected')
      <h2 class="bad">Pengajuan ditolak</h2>
      <p>Anda menolak layer {{ $layer }} untuk {{ $warningLetter->nama }}. Admin akan diberitahu.</p>
    @else
      <h2>Hasil</h2>
      <p>Status: {{ $action }}</p>
    @endif
  </div>
  <script>
    (function(){
      // If this page was opened from the list (window.opener), refresh it so the list updates immediately
      if (window.opener && !window.opener.closed) {
        try { window.opener.location.reload(); } catch (e) { /* ignore */ }
        // attempt to close this tab after a short delay
        setTimeout(function(){ try { window.close(); } catch(e) {} }, 700);
      }
    })();
  </script>
  <div style="max-width:680px;margin:0 auto;text-align:center;margin-top:12px;color:#64748b;">
    <button onclick="try{window.close();}catch(e){}" style="padding:8px 12px;border-radius:6px;border:1px solid #e2e8f0;background:#fff;cursor:pointer;">Tutup</button>
  </div>
</body>
</html>
