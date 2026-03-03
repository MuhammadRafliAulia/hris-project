<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tanda Tangan Surat Peringatan</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; display:flex; height:100vh; }
    .sidebar { width:280px; min-width:280px; flex-shrink:0; background:#fff; border-right:1px solid #e2e8f0; padding:20px; box-sizing:border-box; overflow-y:auto; }
    .main { flex:1; display:flex; flex-direction:column; }
    .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 24px; }
    .topbar h1 { margin:0; font-size:20px; color:#0f172a; }
    .content { flex:1; padding:24px; overflow-y:auto; }
    .card { background:#fff; border:1px solid #e2e8f0; padding:24px; border-radius:8px; max-width:1000px; }
    h2 { font-size:18px; color:#0f172a; margin:0 0 20px 0; }
    label { display:block; font-size:13px; color:#334155; margin-bottom:4px; margin-top:10px; font-weight:600; }
    input { width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px; color:#0f172a; box-sizing:border-box; font-family:inherit; }
    .btn { background:#003e6f; color:#fff; border:none; padding:10px 16px; border-radius:6px; font-size:14px; cursor:pointer; margin-top:20px; text-decoration:none; display:inline-block; }
    .btn-cancel { background:#64748b; margin-left:8px; }
    .btn-clear { background:#dc2626; padding:4px 10px; font-size:11px; margin-top:4px; border:none; color:#fff; border-radius:4px; cursor:pointer; }
    .error { color:#dc2626; font-size:12px; margin-top:4px; }
    .info-box { padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; }
    .info-blue { background:#dbeafe; color:#0c4a6e; }
    .info-amber { background:#fef3c7; color:#92400e; }
    .info-green { background:#d1fae5; color:#065f46; }
    .letter-summary { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:14px 18px; margin-bottom:20px; }
    .letter-summary table { border-collapse:collapse; width:100%; }
    .letter-summary td { padding:3px 10px; font-size:13px; vertical-align:top; }
    .letter-summary .lbl { font-weight:600; color:#334155; width:130px; }
    .sp-badge { padding:3px 8px; border-radius:4px; font-size:12px; font-weight:600; display:inline-block; }
    .sp-1 { background:#fef08a; color:#713f12; }
    .sp-2 { background:#fed7aa; color:#9a3412; }
    .sp-3 { background:#fecaca; color:#991b1b; }
    .sig-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px; }
    .sig-section { padding:16px; border:1px solid #e2e8f0; border-radius:8px; background:#fafbfc; }
    .sig-section h3 { font-size:13px; color:#003e6f; margin:0 0 8px 0; display:flex; align-items:center; gap:6px; }
    .sig-section.main-sig { border-color:#003e6f; border-width:2px; }
    .sig-section.main-sig h3 { font-size:14px; }
    .sig-canvas-wrap { border:2px dashed #cbd5e1; border-radius:6px; background:#fff; position:relative; margin-top:6px; }
    .sig-canvas-wrap canvas { display:block; cursor:crosshair; width:100%; }
    .sig-canvas-wrap .placeholder { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#94a3b8; font-size:13px; pointer-events:none; }
    .sig-full { grid-column:1 / -1; }

    /* Tab Switcher */
    .sig-tabs { display:flex; gap:0; margin-top:6px; margin-bottom:0; }
    .sig-tab { flex:1; padding:6px 0; text-align:center; font-size:11px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; transition:all 0.15s; user-select:none; }
    .sig-tab:first-child { border-radius:6px 0 0 0; }
    .sig-tab:last-child { border-radius:0 6px 0 0; border-left:none; }
    .sig-tab.active { background:#003e6f; color:#fff; border-color:#003e6f; }
    .sig-tab:hover:not(.active) { background:#f1f5f9; color:#334155; }
    .sig-panel { display:none; }
    .sig-panel.active { display:block; }

    /* Upload zone */
    .upload-zone { border:2px dashed #cbd5e1; border-radius:0 0 6px 6px; background:#fff; padding:16px; text-align:center; cursor:pointer; transition:all 0.15s; min-height:120px; display:flex; flex-direction:column; align-items:center; justify-content:center; position:relative; }
    .upload-zone:hover { border-color:#003e6f; background:#f0f7ff; }
    .upload-zone.has-image { border-color:#10b981; background:#f0fdf4; }
    .upload-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
    .upload-zone .upload-icon { font-size:24px; margin-bottom:4px; color:#94a3b8; }
    .upload-zone .upload-text { font-size:11px; color:#94a3b8; }
    .upload-zone .upload-hint { font-size:10px; color:#cbd5e1; margin-top:2px; }
    .upload-preview { max-width:180px; max-height:80px; border-radius:4px; border:1px solid #e2e8f0; }
    .upload-actions { display:flex; gap:6px; margin-top:6px; justify-content:center; }
    .role-tag { font-size:10px; background:#e0e7ff; color:#3730a3; padding:2px 6px; border-radius:3px; font-weight:600; }
    .role-tag-hr { background:#d1fae5; color:#065f46; }
    .signed-preview { padding:16px; border:1px solid #d1fae5; border-radius:8px; background:#f0fdf4; }
    .signed-preview h3 { font-size:13px; color:#065f46; margin:0 0 8px 0; display:flex; align-items:center; gap:6px; }
    .signed-preview .sig-info { font-size:12px; color:#475569; margin-bottom:4px; }
    .signed-preview .sig-img { max-width:150px; max-height:55px; border:1px solid #e2e8f0; border-radius:4px; background:#fff; padding:4px; }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  @include('layouts.sidebar')

  <div class="main">
    <div class="topbar">
      @if($signMode === 'admin_prod')
        <h1>Tanda Tangan E-Sign (4 Layer)</h1>
      @elseif($signMode === 'hr_only')
        <h1>Tanda Tangan HR</h1>
      @else
        <h1>Tanda Tangan E-Sign (5 Layer)</h1>
      @endif
    </div>

    <div class="content">
      <div class="card">
        <h2>Tanda Tangan Surat Peringatan</h2>

        @if($signMode === 'admin_prod')
          <div class="info-box info-amber">
            ✍️ Isi 4 kolom tanda tangan berikut: Pimpinan Kerja, Atasan, Saksi 1, dan Saksi 2. Setelah selesai, surat akan dikirim ke Super Admin (HR) untuk tanda tangan final.
          </div>
        @elseif($signMode === 'hr_only')
          <div class="info-box info-green">
            ✍️ 4 tanda tangan utama sudah terisi. Anda hanya perlu menandatangani kolom HR untuk menyelesaikan approval surat ini.
          </div>
        @else
          <div class="info-box info-blue">
            ✍️ Tanda tangani surat peringatan ini secara digital. Terdapat 5 kolom tanda tangan: 2 penandatangan utama, 2 saksi, dan 1 HR.
          </div>
        @endif

        @if($errors->any())
          <div style="background:#fecaca;color:#991b1b;padding:12px;border-radius:6px;margin-bottom:16px;font-size:13px;">
            @foreach($errors->all() as $err)
              <div>{{ $err }}</div>
            @endforeach
          </div>
        @endif

        <div class="letter-summary">
          <div style="overflow-x:auto;">
          <table>
            <tr><td class="lbl">Nama</td><td>: {{ $warningLetter->nama }}</td></tr>
            @if($warningLetter->nik)<tr><td class="lbl">NIK</td><td>: {{ $warningLetter->nik }}</td></tr>@endif
            <tr><td class="lbl">Jabatan</td><td>: {{ $warningLetter->jabatan }}</td></tr>
            <tr><td class="lbl">Departemen</td><td>: {{ $warningLetter->departemen }}</td></tr>
            <tr><td class="lbl">SP Level</td><td>: <span class="sp-badge sp-{{ $warningLetter->sp_level }}">{{ $warningLetter->sp_label }}</span></td></tr>
            <tr><td class="lbl">Tanggal Surat</td><td>: {{ $warningLetter->tanggal_surat ? $warningLetter->tanggal_surat->format('d/m/Y') : '-' }}</td></tr>
            @if(optional(auth()->user())->isInternalHR() || optional(auth()->user())->isSuperAdmin())
              <tr><td class="lbl">Alasan</td><td>: {{ $warningLetter->alasan }}</td></tr>
            @endif
          </table>
          </div>
        </div>

        <form method="POST" action="{{ route('warning-letters.sign', $warningLetter) }}" id="signForm" @if($signMode === 'hr_only') target="_blank" @endif>
          @csrf
          <input type="hidden" name="sign_mode" value="{{ $signMode }}">

          @php
            $allSigners = [
              1 => ['label' => 'Pimpinan Kerja', 'role' => 'UTAMA', 'class' => 'main-sig', 'placeholder_name' => 'Contoh: Nama Pimpinan Kerja', 'placeholder_jabatan' => 'Contoh: Supervisor Produksi'],
              2 => ['label' => 'Atasan', 'role' => 'UTAMA', 'class' => 'main-sig', 'placeholder_name' => 'Contoh: Nama Atasan', 'placeholder_jabatan' => 'Contoh: Manager Produksi'],
              3 => ['label' => 'Saksi 1', 'role' => 'SAKSI', 'class' => '', 'placeholder_name' => 'Nama Saksi 1', 'placeholder_jabatan' => 'Jabatan Saksi 1'],
              4 => ['label' => 'Saksi 2', 'role' => 'SAKSI', 'class' => '', 'placeholder_name' => 'Nama Saksi 2', 'placeholder_jabatan' => 'Jabatan Saksi 2'],
              5 => ['label' => 'HR / HRD', 'role' => 'HR', 'class' => 'sig-full', 'placeholder_name' => 'Nama HRD', 'placeholder_jabatan' => 'Contoh: HRD Manager'],
            ];

            // Determine which signers to show as editable
            if ($signMode === 'admin_prod') {
              $editableSigners = [1, 2, 3, 4];
            } elseif ($signMode === 'hr_only') {
              $editableSigners = [5];
            } else {
              $editableSigners = [1, 2, 3, 4, 5];
            }
            // Only allow inline signing for HR and SuperAdmin — others must use generated links
            $allowInline = optional(auth()->user())->isInternalHR() || optional(auth()->user())->isSuperAdmin();
            if ($allowInline) {
              // even for HR or superadmin, only allow inline on HR box
              $editableSigners = [5];
            } else {
              $editableSigners = []; // force no inline editable signers for others
            }
          @endphp

          {{-- Show signer cards: if already signed show inactive preview, otherwise show inputs --}}

          <div class="sig-grid">
            @foreach(array_keys($allSigners) as $num)
              {{-- If current user is HR or SuperAdmin, only render HR signer (5) and skip others --}}
              @if((optional(auth()->user())->isInternalHR() || optional(auth()->user())->isSuperAdmin()) && $num != 5)
                @continue
              @endif
              @if(optional(auth()->user())->isAdminProd() && $num == 5)
                @continue
              @endif
              @php $s = $allSigners[$num];
                     $isEditable = in_array($num, $editableSigners) && empty($warningLetter->{'signature_'.$num});
              @endphp
              @if(!empty($warningLetter->{'signature_'.$num}))
                <div class="sig-section signed-preview" style="opacity:0.7;pointer-events:none;">
                  <h3>
                    ✅ {{ $s['label'] }}
                    <span class="role-tag">{{ $s['role'] }}</span>
                  </h3>
                  <div class="sig-info"><strong>Nama:</strong> {{ $warningLetter->{'signer_name_'.$num} }}</div>
                  <div class="sig-info"><strong>Jabatan:</strong> {{ $warningLetter->{'signer_jabatan_'.$num} }}</div>
                  @if($warningLetter->{'signature_'.$num})
                    <img src="{{ $warningLetter->{'signature_'.$num} }}" class="sig-img" alt="TTD {{ $num }}">
                  @endif
                </div>
              @else
                <div class="sig-section {{ $s['class'] }} {{ $num <= 2 ? 'main-sig' : '' }}">
                <h3>
                  📝 {{ $s['label'] }}
                  <span class="role-tag {{ $s['role'] === 'HR' ? 'role-tag-hr' : '' }}">{{ $s['role'] }}</span>
                </h3>

                @if($num <= 4)
                  @php
                    $link = \Illuminate\Support\Facades\URL::temporarySignedRoute('warning-letters.approval', now()->addDays(14), ['warning_letter' => $warningLetter->id, 'layer' => $num]);
                  @endphp
                  <label>Generate Link (Layer {{ $num }}) — berlaku 14 hari</label>
                  <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                    <input type="text" id="linkInput{{ $num }}" value="{{ $link }}" readonly style="flex:1;padding:8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;">
                    <button type="button" class="btn" onclick="copyLink({{ $num }})">Salin</button>
                    <a href="{{ $link }}" target="_blank" rel="noopener" class="btn" style="background:#10b981;">Buka</a>
                  </div>
                @endif

                @if($allowInline)
                <label>Nama</label>
                <input type="text" name="signer_name_{{ $num }}" value="{{ old('signer_name_'.$num) }}" required placeholder="{{ $s['placeholder_name'] }}">
                @error('signer_name_'.$num)<div class="error">{{ $message }}</div>@enderror

                <label>Jabatan</label>
                <input type="text" name="signer_jabatan_{{ $num }}" value="{{ old('signer_jabatan_'.$num) }}" required placeholder="{{ $s['placeholder_jabatan'] }}">
                @error('signer_jabatan_'.$num)<div class="error">{{ $message }}</div>@enderror

                <label>Tanda Tangan</label>
                <div class="sig-tabs">
                  <div class="sig-tab active" onclick="switchTab({{ $num }}, 'draw')" id="tabDraw{{ $num }}">✏️ Gambar</div>
                  <div class="sig-tab" onclick="switchTab({{ $num }}, 'upload')" id="tabUpload{{ $num }}">📁 Upload</div>
                </div>

                {{-- Draw Panel --}}
                <div class="sig-panel active" id="panelDraw{{ $num }}">
                  <div class="sig-canvas-wrap" style="border-radius:0 0 6px 6px;">
                    <canvas id="sigCanvas{{ $num }}" width="320" height="120"></canvas>
                    <div class="placeholder" id="placeholder{{ $num }}">Tanda tangan di sini</div>
                  </div>
                  <button type="button" class="btn-clear" onclick="clearSig({{ $num }})">🗑 Hapus</button>
                </div>

                {{-- Upload Panel --}}
                <div class="sig-panel" id="panelUpload{{ $num }}">
                  <div class="upload-zone" id="uploadZone{{ $num }}">
                    <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="handleUpload({{ $num }}, this)" id="fileInput{{ $num }}">
                    <div id="uploadPlaceholder{{ $num }}">
                      <div class="upload-icon">🖼️</div>
                      <div class="upload-text">Klik atau drop gambar tanda tangan</div>
                      <div class="upload-hint">PNG, JPG, WEBP (maks 2MB)</div>
                    </div>
                    <div id="uploadPreviewWrap{{ $num }}" style="display:none;">
                      <img id="uploadPreview{{ $num }}" class="upload-preview" alt="Preview">
                    </div>
                  </div>
                  <div class="upload-actions" id="uploadActions{{ $num }}" style="display:none;">
                    <button type="button" class="btn-clear" onclick="clearUpload({{ $num }})">🗑 Hapus</button>
                  </div>
                </div>

                <input type="hidden" name="signature_{{ $num }}" id="sigData{{ $num }}">
                <input type="hidden" id="sigMode{{ $num }}" value="draw">
                @error('signature_'.$num)<div class="error">{{ $message }}</div>@enderror
                @else
                  <div style="font-size:13px;color:#64748b;margin-top:8px;">Tanda tangan hanya dapat dilakukan melalui link yang digenerate.</div>
                @endif
              </div>
              @endif
            @endforeach
          </div>

          <div style="margin-top:24px;">
            @if($allowInline)
              @if($signMode === 'admin_prod')
                <button type="submit" class="btn" onclick="return prepareSubmit()">📤 Tandatangani & Kirim ke HR</button>
              @elseif($signMode === 'hr_only')
                <button type="submit" class="btn" style="background:#065f46;" onclick="return prepareSubmit()">✅ Tandatangani HR & Approve</button>
              @else
                <button type="submit" class="btn" onclick="return prepareSubmit()">✅ Tandatangani & Approve</button>
              @endif
            @else
              <div style="font-size:13px;color:#64748b;">Tanda tangan hanya dapat dilakukan melalui link yang digenerate. Gunakan tombol 'Buka' pada link untuk membuka form tanda tangan.</div>
            @endif

            @if($signMode === 'admin_prod')
              <a href="{{ route('warning-letters.create') }}" class="btn btn-cancel">Batal</a>
            @else
              <a href="{{ route('warning-letters.index') }}" class="btn btn-cancel">Batal</a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    var pads = {};
    var uploads = {};
    var editableNums = @json($editableSigners);

    // ===== DRAW PAD =====
    function initPad(num) {
      var canvas = document.getElementById('sigCanvas' + num);
      if (!canvas) return;
      var ctx = canvas.getContext('2d');
      var ph = document.getElementById('placeholder' + num);
      var drawing = false, hasDrawn = false;

      ctx.strokeStyle = '#1a1a1a';
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';

      function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        if (e.touches) {
          return { x: (e.touches[0].clientX - rect.left) * scaleX, y: (e.touches[0].clientY - rect.top) * scaleY };
        }
        return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
      }

      function start(e) { e.preventDefault(); drawing = true; hasDrawn = true; ph.style.display = 'none'; var p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
      function move(e) { if (!drawing) return; e.preventDefault(); var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }
      function stop() { drawing = false; }

      canvas.addEventListener('mousedown', start);
      canvas.addEventListener('mousemove', move);
      canvas.addEventListener('mouseup', stop);
      canvas.addEventListener('mouseleave', stop);
      canvas.addEventListener('touchstart', start);
      canvas.addEventListener('touchmove', move);
      canvas.addEventListener('touchend', stop);

      pads[num] = {
        clear: function() { ctx.clearRect(0, 0, canvas.width, canvas.height); hasDrawn = false; ph.style.display = 'block'; },
        getData: function() { return hasDrawn ? canvas.toDataURL('image/png') : ''; },
        has: function() { return hasDrawn; }
      };
    }

    // ===== UPLOAD IMAGE =====
    function handleUpload(num, input) {
      var file = input.files[0];
      if (!file) return;

      // Validate type
      if (!file.type.match(/^image\/(png|jpe?g|webp)$/)) {
        alert('Format file harus PNG, JPG, atau WEBP.');
        input.value = '';
        return;
      }
      // Validate size (2MB)
      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB.');
        input.value = '';
        return;
      }

      var reader = new FileReader();
      reader.onload = function(e) {
        uploads[num] = e.target.result;
        document.getElementById('uploadPreview' + num).src = e.target.result;
        document.getElementById('uploadPlaceholder' + num).style.display = 'none';
        document.getElementById('uploadPreviewWrap' + num).style.display = 'block';
        document.getElementById('uploadActions' + num).style.display = 'flex';
        document.getElementById('uploadZone' + num).classList.add('has-image');
      };
      reader.readAsDataURL(file);
    }

    function clearUpload(num) {
      uploads[num] = null;
      document.getElementById('fileInput' + num).value = '';
      document.getElementById('uploadPreview' + num).src = '';
      document.getElementById('uploadPlaceholder' + num).style.display = '';
      document.getElementById('uploadPreviewWrap' + num).style.display = 'none';
      document.getElementById('uploadActions' + num).style.display = 'none';
      document.getElementById('uploadZone' + num).classList.remove('has-image');
    }

    // ===== TAB SWITCHING =====
    function switchTab(num, mode) {
      document.getElementById('sigMode' + num).value = mode;

      document.getElementById('tabDraw' + num).classList.toggle('active', mode === 'draw');
      document.getElementById('tabUpload' + num).classList.toggle('active', mode === 'upload');
      document.getElementById('panelDraw' + num).classList.toggle('active', mode === 'draw');
      document.getElementById('panelUpload' + num).classList.toggle('active', mode === 'upload');
    }

    // ===== INIT =====
    for (var i = 0; i < editableNums.length; i++) {
      initPad(editableNums[i]);
    }

    function clearSig(num) { if (pads[num]) pads[num].clear(); }

    var sigLabels = {1: 'Pimpinan Kerja', 2: 'Atasan', 3: 'Saksi 1', 4: 'Saksi 2', 5: 'HR'};

    function prepareSubmit() {
      for (var i = 0; i < editableNums.length; i++) {
        var num = editableNums[i];
        var mode = document.getElementById('sigMode' + num).value;

        if (mode === 'upload') {
          if (!uploads[num]) {
            alert('Silakan upload gambar tanda tangan ' + sigLabels[num] + ' terlebih dahulu.');
            return false;
          }
          document.getElementById('sigData' + num).value = uploads[num];
        } else {
          if (!pads[num] || !pads[num].has()) {
            alert('Silakan tanda tangan ' + sigLabels[num] + ' terlebih dahulu.');
            return false;
          }
          document.getElementById('sigData' + num).value = pads[num].getData();
        }
      }
      return true;
    }

    function copyLink(num) {
      var el = document.getElementById('linkInput' + num);
      if (!el) return;
      el.select();
      el.setSelectionRange(0, 99999);
      try {
        document.execCommand('copy');
        alert('Link layer ' + num + ' disalin ke clipboard.');
      } catch (e) {
        prompt('Salin link berikut:', el.value);
      }
    }
  </script>
</body>
</html>
