<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Data Diri - {{ $bank->title }}</title>
 <style>
 * { margin:0; padding:0; box-sizing:border-box; }
 body { 
 font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
 background: #ffffff;
 min-height: 100vh;
 display: flex;
 align-items: center;
 justify-content: center;
 padding: 20px;
 }
 .container { max-width: 500px; width: 100%; }
 .card {
 background: #fff;
 border-radius: 12px;
 box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
 padding: 40px;
 }
 .header {
 text-align: center;
 margin-bottom: 30px;
 }
 .header h1 {
 font-size: 24px;
 color: #0f172a;
 margin-bottom: 8px;
 }
 .header p {
 color: #64748b;
 font-size: 14px;
 }
 .form-group {
 margin-bottom: 20px;
 }
 label {
 display: block;
 font-size: 14px;
 font-weight: 500;
 color: #334155;
 margin-bottom: 8px;
 }
 input[type="text"],
 input[type="email"],
 input[type="tel"],
 select {
 width: 100%;
 padding: 10px 12px;
 border: 1px solid #e2e8f0;
 border-radius: 6px;
 font-size: 14px;
 transition: all 0.2s;
 font-family: inherit;
 }
 input[type="text"]:focus,
 input[type="email"]:focus,
 input[type="tel"]:focus,
 select:focus {
 outline: none;
 border-color: #003e6f;
 box-shadow: 0 0 0 3px rgba(0, 62, 111, 0.1);
 }
 .btn {
 width: 100%;
 padding: 11px;
 background: #003e6f;
 color: #fff;
 border: none;
 border-radius: 6px;
 font-size: 15px;
 font-weight: 600;
 cursor: pointer;
 transition: all 0.2s;
 }
 .btn:hover {
 transform: translateY(-2px);
 box-shadow: 0 10px 20px rgba(0, 62, 111, 0.3);
 background: #002a4f;
 }
 .error-alert {
 background: #fee;
 color: #c41e3a;
 padding: 12px;
 border-radius: 6px;
 margin-bottom: 20px;
 font-size: 14px;
 border-left: 4px solid #c41e3a;
 }
 .info-text {
 font-size: 13px;
 color: #64748b;
 margin-top: 6px;
 }
 </style>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
 <div class="container">
 <div class="card">
 <div class="header">
 <h1>Formulir Peserta Tes</h1>
 <p>{{ $bank->title }}</p>
 </div>

 @if($bank->duration_minutes)
 <div style="background:#dbeafe;color:#0c4a6e;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;text-align:center;">
 <strong>Waktu Pengerjaan: {{ $bank->duration_minutes }} menit</strong>
 <div style="font-size:12px;color:#475569;margin-top:4px;">Timer akan dimulai setelah Anda menekan "Lanjutkan ke Tes". Jawaban terkirim otomatis jika waktu habis.</div>
 </div>
 @endif

 @if($errors->any())
 <div class="error-alert">
 <strong> Pemberitahuan:</strong><br>
 {{ $errors->first() }}
 </div>
 @endif

 <form method="POST" action="{{ route('test.start', $slug) }}">
 @csrf

 
 <div class="form-group">
 <label for="nik">NIK (Nomor Induk Karyawan) *</label>
 <input 
 type="text" 
 id="nik" 
 name="nik" 
 placeholder="Masukkan NIK Anda"
 value="{{ old('nik') }}"
 required
 autofocus
 >
 <div class="info-text">Nomor Induk Karyawan sesuai data perusahaan</div>
 </div>

<div class="form-group">
<label for="participant_name">Nama Lengkap *</label>
<input
 type="text"
 id="participant_name"
 name="participant_name"
 placeholder="Masukkan nama lengkap Anda"
 value="{{ old('participant_name') }}"
 required
 >
<div class="info-text">Nama sesuai KTP atau data perusahaan</div>
</div>

 <div class="form-group">
 <label for="department">Departemen *</label>
 <select id="department" name="department" required>
 <option value="">-- Pilih Departemen --</option>
 @foreach($departments as $dept)
 <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
 @endforeach
 </select>
 <div class="info-text">Departemen tempat Anda bekerja</div>
 </div>

 <div class="form-group">
 <label for="position">Jabatan *</label>
 <input 
 type="text" 
 id="position" 
 name="position" 
 placeholder="Contoh: Operator, Staff, Supervisor, dll"
 value="{{ old('position') }}"
 required
 >
 <div class="info-text">Jabatan Anda saat ini</div>
 </div>

 <div class="form-group">
 <label for="participant_email">Email *</label>
 <input 
 type="email" 
 id="participant_email" 
 name="participant_email" 
 placeholder="Masukkan alamat email Anda"
 value="{{ old('participant_email') }}"
 required
 >
 <div class="info-text">Alamat email aktif untuk keperluan komunikasi</div>
 </div>

 <div class="form-group">
 <label for="phone">Nomor Telepon *</label>
 <input 
 type="tel" 
 id="phone" 
 name="phone" 
 placeholder="Contoh: 08123456789"
 value="{{ old('phone') }}"
 required
 >
 <div class="info-text">Nomor telepon/HP yang aktif</div>
 </div>

 <button type="submit" class="btn">Lanjutkan ke Tes</button>
 </form>

 
 </div>
 </div>

<!-- Modal Tata Tertib -->
<div id="rulesModal" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,0.6);backdrop-filter:blur(2px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
	<div style="max-width:720px;width:100%;background:#fff;border-radius:12px;padding:22px;box-shadow:0 20px 60px rgba(2,6,23,0.4);">
		<h2 style="margin:0 0 12px 0">Tata Tertib Mengikuti Assessment</h2>
		<ul style="color:#0f172a;margin:12px 0 18px 18px;line-height:1.6">
			<li>Peserta wajib hadir tepat waktu dan mengikuti instruksi pengawas.</li>
			<li>Peserta dilarang berganti tab/browser selama assessment berlangsung.</li>
			<li>Peserta dilarang melakukan screenshot, screen recording, atau tindakan serupa.</li>
			<li>Peserta dilarang melakukan copy-paste dari soal maupun jawaban.</li>
			<li>Peserta wajib mengerjakan assessment secara mandiri tanpa bantuan pihak lain.</li>
			<li>Pelanggaran terhadap tata tertib ini dapat berakibat pada pembatalan hasil assessment.</li>
		</ul>
		<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px">
			<button id="rulesCancel" style="background:#f1f5f9;border:none;padding:10px 14px;border-radius:8px;cursor:pointer">Batal</button>
			<button id="rulesAccept" style="background:#003e6f;color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer">Saya Setuju, Lanjutkan ke Tes</button>
		</div>
	</div>
</div>

@if(session('duplicate_error'))
 <script>
 setTimeout(() => {
 alert('anda sudah tidak bisa mengerjakan test ini lagi');
 window.location.href = '{{ url("/") }}';
 }, 100);
 </script>
 @endif
<script>
	(function(){
		const form = document.querySelector('form[method="POST"][action]');
		if (!form) return;
		const modal = document.getElementById('rulesModal');
		const btnAccept = document.getElementById('rulesAccept');
		const btnCancel = document.getElementById('rulesCancel');

		form.addEventListener('submit', function(e){
			// show modal instead of submitting directly
			e.preventDefault();
			if (modal) modal.style.display = 'flex';
		});

		if (btnAccept) btnAccept.addEventListener('click', function(){
			if (modal) modal.style.display = 'none';
			// submit the form after acceptance
			form.submit();
		});
		if (btnCancel) btnCancel.addEventListener('click', function(){
			if (modal) modal.style.display = 'none';
		});
	})();
</script>
</body>
</html>
