<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Approval Surat Peringatan - Layer {{ $layer }}</title>
  <style>
    body{font-family:Inter,system-ui, -apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:#f3f6f9;padding:28px;color:#0f172a}
    .card{max-width:1200px;margin:28px auto;background:#fff;padding:24px;border-radius:10px;border:1px solid #e6eef6;box-shadow:0 6px 20px rgba(11,35,57,0.06)}
    label{display:block;margin-top:10px;font-weight:600;color:#0b2540}
    input{width:100%;padding:12px 14px;margin-top:8px;border:1px solid #d6e3ef;border-radius:8px;box-sizing:border-box;max-width:720px;background:#fbfdff}
    .field-note{font-size:12px;color:#3b5873;margin-top:6px}
    .btn{display:inline-block;padding:10px 16px;border-radius:8px;border:none;background:#0b5fa5;color:#fff;cursor:pointer;margin-top:14px;box-shadow:0 6px 12px rgba(11,95,165,0.12)}
    .btn:hover{filter:brightness(.95)}
    .btn-reject{background:#c42f2f;margin-left:8px;box-shadow:0 6px 12px rgba(196,47,47,0.12)}
    .btn-ghost{background:transparent;border:1px solid #cbd5e1;color:#0b2540}
    .preview-iframe { width:100%; height:640px; border:0; min-height:520px; display:block }
    .preview-wrap { width:100%; height:640px; min-height:520px; border-radius:10px; overflow:hidden; background:#fff; margin-top:8px; box-shadow:inset 0 1px 0 rgba(255,255,255,0.4)}
    .section-title{font-size:15px;color:#08263b;margin-bottom:6px}
     /* layout for signature/form area: keep preview (main iframe) full width;
       make the form box small so it doesn't shrink the main preview */
    .sig-row { display:flex; gap:16px; align-items:flex-start; }
      .sig-left { width:360px; flex-shrink:0; }
      .sig-right { flex:1; }
      .sig-panel{border:1px solid #e1eef8;padding:12px;border-radius:8px;background:#ffffff}
      .sig-canvas-wrap{border:2px dashed #e1ecf8;border-radius:8px;padding:8px;background:#fbfdff}
      .sig-preview{border:1px solid #e6eef6;padding:10px;border-radius:8px;background:#fbfdff;min-height:120px}
    .modal-center { display:flex; align-items:center; justify-content:center; }
    .form-fields{display:flex;flex-direction:column;gap:10px;padding:12px;box-sizing:border-box}
    .form-fields > div{max-width:720px;margin:0 auto;width:100%}
    .form-fields input{width:100%;display:block}

    @media (max-width: 1024px) {
      .card{max-width:980px}
    }
    @media (max-width: 768px) {
      body{padding:10px}
      .card{margin:8px;padding:14px}
      h2{font-size:16px}
      p{font-size:13px}
      label{font-size:13px}
      input{padding:10px 12px;font-size:14px;max-width:100%}
      .preview-iframe { height:320px; min-height:260px; }
      .preview-wrap { height:320px; min-height:260px; }
      .sig-row { flex-direction:column; }
      .sig-left { width:100%; }
      .sig-right { width:100%; }
      .sig-panel{padding:10px}
      .sig-canvas-wrap{padding:6px}
      .btn{width:100%;text-align:center;box-sizing:border-box}
      .btn-reject{margin-left:0;margin-top:8px}
      .btn-ghost{width:100%;text-align:center}
      .section-title{font-size:14px}
      .form-fields{padding:8px 0}
      .form-fields input{max-width:100%}
      .modal-center > div { width:95%; max-width:100%; padding:16px; }
      .modal-center h3{font-size:15px}
      .modal-center textarea{font-size:13px}
    }
    @media (max-width: 400px) {
      body{padding:6px}
      .card{margin:4px;padding:10px}
      h2{font-size:15px}
      input{padding:8px 10px;font-size:13px}
      .preview-iframe { height:260px; min-height:220px; }
      .preview-wrap { height:260px; min-height:220px; }
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Approval Surat Peringatan (Layer {{ $layer }})</h2>
    <p><strong>Nama:</strong> {{ $warningLetter->nama }}</p>
    @if($warningLetter->nik)<p><strong>NIK:</strong> {{ $warningLetter->nik }}</p>@endif
    <p><strong>Jabatan:</strong> {{ $warningLetter->jabatan }}</p>
    <p><strong>Departemen:</strong> {{ $warningLetter->departemen }}</p>

    <div style="margin-bottom:18px;">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div class="section-title">Preview SP</div>
        <div style="margin-left:auto;">
          <a href="{{ $previewLink }}" target="_blank" class="btn btn-ghost" style="margin-top:0">Buka di Tab Baru</a>
        </div>
      </div>
      <div class="preview-wrap">
        <iframe src="{{ $previewLink }}" class="preview-iframe" title="Preview SP"></iframe>
      </div>
    </div>

    <form method="POST" action="{{ route('warning-letters.approval.post', ['warning_letter' => $warningLetter->id, 'layer' => $layer]) }}" onsubmit="return prepareSubmit();">
      @csrf
      <div class="form-fields">
        <div>
          <label for="nik">NIK</label>
          <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required>
        </div>
        <div>
          <label for="nama">Nama</label>
          <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required>
        </div>
        <div>
          <label for="jabatan">Jabatan</label>
          <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required>
        </div>
      </div>
      <div id="formError" style="display:none;margin-top:8px;color:#9a1f1f;background:#fff1f1;padding:8px;border-radius:6px;font-size:13px;border:1px solid #f5c6c6;max-width:720px">NIK, nama, dan jabatan harus diisi</div>

      <label style="margin-top:12px;">Tanda Tangan (Gambar atau Ketik)</label>
      <div class="sig-row">
        <div class="sig-left">
          <div class="sig-panel">
            <div style="display:flex;gap:8px;margin-bottom:10px;">
              <button type="button" class="btn btn-ghost" onclick="switchTab('draw')" id="tabDraw">✏️ Gambar</button>
              <button type="button" class="btn btn-ghost" onclick="switchTab('upload')" id="tabUploadBtn">📁 Upload</button>
            </div>
            <div id="panelDraw">
              <div class="sig-canvas-wrap">
                <canvas id="sigCanvas" width="520" height="160" style="width:100%;height:120px;cursor:crosshair;"></canvas>
                <div id="placeholder" style="position:relative;margin-top:-120px;text-align:center;color:#7b8fa3;pointer-events:none;">Tanda tangan di sini</div>
              </div>
              <button type="button" class="btn" style="margin-top:8px;background:#e04b4b;" onclick="clearSig()">Hapus</button>
            </div>
            <div id="panelUpload" style="display:none;">
              <div style="text-align:center;padding:8px;">
                <input type="file" id="fileInput" accept="image/png,image/jpeg,image/jpg,image/webp" style="display:block;margin:0 auto;" onchange="handleUpload(this)">
                <div id="uploadPreviewWrap" style="display:none;margin-top:10px;"><img id="uploadPreview" style="max-width:280px;max-height:120px;border:1px solid #e6eef6;border-radius:6px;" alt="Preview"></div>
              </div>
              <button type="button" class="btn" style="margin-top:8px;background:#e04b4b;" onclick="clearUpload()">Hapus</button>
            </div>
          </div>
        </div>
        <div class="sig-right">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <label style="margin:0">Preview / Keterangan</label>
          </div>
          <div id="sigPreviewBox" class="sig-preview" style="margin-top:8px;">
            <div id="sigPreviewText">Belum ada tanda tangan</div>
            <img id="sigPreviewImg" src="" style="display:none;max-width:100%;border-radius:6px;margin-top:6px;" alt="TTD">
          </div>
        </div>
      </div>

      <input type="hidden" name="signature" id="sigData" value="">
      <input type="hidden" name="reason" id="reasonField" value="">
      <input type="hidden" name="action" id="actionField" value="approve">
      <div style="margin-top:12px;">
        <button type="submit" class="btn" onclick="document.getElementById('actionField').value='approve'">Approve</button>
        <button type="button" class="btn btn-reject" onclick="showRejectModal()">Reject</button>
      </div>
    </form>

    <script>
      var canvas = document.getElementById('sigCanvas');
      var ctx = canvas.getContext('2d');
      var drawing = false; var hasDrawn = false;
      ctx.strokeStyle = '#1a1a1a'; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.lineJoin = 'round';

      function getPos(e){var rect=canvas.getBoundingClientRect();var scaleX=canvas.width/rect.width;var scaleY=canvas.height/rect.height; if(e.touches){return {x:(e.touches[0].clientX-rect.left)*scaleX,y:(e.touches[0].clientY-rect.top)*scaleY};} return {x:(e.clientX-rect.left)*scaleX,y:(e.clientY-rect.top)*scaleY};}
      function start(e){e.preventDefault();drawing=true;hasDrawn=true;document.getElementById('placeholder').style.display='none';var p=getPos(e);ctx.beginPath();ctx.moveTo(p.x,p.y);} 
      function move(e){ if(!drawing) return; e.preventDefault(); var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); }
      function stop(e){ drawing=false; }
      canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); canvas.addEventListener('mouseup', stop); canvas.addEventListener('mouseleave', stop);
      canvas.addEventListener('touchstart', start); canvas.addEventListener('touchmove', move); canvas.addEventListener('touchend', stop);

      function clearSig(){ ctx.clearRect(0,0,canvas.width,canvas.height); hasDrawn=false; document.getElementById('placeholder').style.display='block'; document.getElementById('sigPreviewText').innerText='Belum ada tanda tangan'; document.getElementById('sigPreviewImg').style.display='none'; document.getElementById('sigPreviewImg').src=''; document.getElementById('sigData').value=''; }

      var uploadData = null;
      function handleUpload(input){ var file = input.files[0]; if(!file) return; if(!file.type.match(/^image\/(png|jpe?g|webp)$/)){ alert('Format file harus PNG, JPG, atau WEBP.'); input.value=''; return; } if(file.size > 2*1024*1024){ alert('Ukuran file maksimal 2MB.'); input.value=''; return; } var reader=new FileReader(); reader.onload=function(e){ uploadData = e.target.result; document.getElementById('uploadPreview').src = uploadData; document.getElementById('uploadPreviewWrap').style.display='block'; document.getElementById('sigPreviewImg').src = uploadData; document.getElementById('sigPreviewImg').style.display='block'; document.getElementById('sigPreviewText').innerText = ''; }; reader.readAsDataURL(file); }
      function clearUpload(){ uploadData = null; document.getElementById('fileInput').value=''; document.getElementById('uploadPreview').src=''; document.getElementById('uploadPreviewWrap').style.display='none'; document.getElementById('sigPreviewImg').style.display='none'; document.getElementById('sigPreviewImg').src=''; }

      function switchTab(mode){ document.getElementById('panelDraw').style.display = (mode === 'draw') ? '' : 'none'; document.getElementById('panelUpload').style.display = (mode === 'upload') ? '' : 'none'; }

      function prepareSubmit(){ var mode = (document.getElementById('panelUpload').style.display === '') ? 'upload' : 'draw'; if(document.getElementById('actionField').value === 'approve'){
          // client-side check: ensure NIK, nama and jabatan are filled
          var nikVal = (document.getElementById('nik').value || '').trim();
          var namaVal = (document.getElementById('nama').value || '').trim();
          var jabVal = (document.getElementById('jabatan').value || '').trim();
          if (!nikVal || !namaVal || !jabVal) {
            document.getElementById('formError').style.display = 'block';
            return false;
          } else {
            document.getElementById('formError').style.display = 'none';
          }
          if(mode === 'upload'){
            if(!uploadData){ alert('Silakan upload tanda tangan terlebih dahulu.'); return false; }
            document.getElementById('sigData').value = uploadData;
          } else {
            if(!hasDrawn){ alert('Silakan tanda tangan di area gambar terlebih dahulu.'); return false; }
            var data = canvas.toDataURL('image/png'); document.getElementById('sigData').value = data; document.getElementById('sigPreviewImg').src = data; document.getElementById('sigPreviewImg').style.display='block'; document.getElementById('sigPreviewText').innerText='';
          }
      }
        return true; }
    </script>
    <!-- Reject Reason Modal -->
    <div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(10,20,30,0.45);align-items:center;justify-content:center;padding:20px;z-index:9999;">
      <div class="modal-center">
        <div style="max-width:640px;width:100%;background:#fff;padding:20px;border-radius:10px;border:1px solid #e6eef6;box-shadow:0 12px 36px rgba(11,35,57,0.12);">
          <h3 style="margin:0 0 8px 0;color:#0b2540;font-size:17px;">Alasan Penolakan</h3>
          <p style="margin:0 0 12px 0;color:#3b5873;font-size:13px;">Masukkan alasan mengapa pengajuan ini ditolak. Alasan akan terlihat pada tooltip di progress.</p>
          <textarea id="rejectReasonInput" rows="4" style="width:100%;padding:12px;border:1px solid #e1ecf8;border-radius:8px;font-size:13px;box-sizing:border-box;" placeholder="Ketikan alasan penolakan..."></textarea>
          <div style="margin-top:12px;text-align:right;">
            <button type="button" class="btn btn-ghost" onclick="hideRejectModal()" style="margin-right:8px;">Batal</button>
            <button type="button" class="btn" onclick="submitReject()">Kirim & Tolak</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      function showRejectModal() {
        document.getElementById('rejectReasonInput').value = '';
        document.getElementById('rejectModal').style.display = 'flex';
        setTimeout(function(){ document.getElementById('rejectReasonInput').focus(); }, 50);
      }
      function hideRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
      }
      function submitReject() {
        // ensure NIK, nama and jabatan are present
        var nikVal = (document.getElementById('nik').value || '').trim();
        var namaVal = (document.getElementById('nama').value || '').trim();
        var jabVal = (document.getElementById('jabatan').value || '').trim();
        if (!nikVal || !namaVal || !jabVal) {
          document.getElementById('formError').style.display = 'block';
          hideRejectModal();
          return;
        }
        var reason = document.getElementById('rejectReasonInput').value || '';
        reason = reason.trim();
        if (!reason) {
          alert('Alasan penolakan wajib diisi.');
          document.getElementById('rejectReasonInput').focus();
          return;
        }
        document.getElementById('formError').style.display = 'none';
        document.getElementById('reasonField').value = reason;
        document.getElementById('actionField').value = 'reject';
        document.querySelector('form').submit();
      }
    </script>
  </div>
</body>
</html>
