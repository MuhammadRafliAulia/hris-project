<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title>Edit Bank Soal - {{ $bank->title }}</title>
 <style>
 body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; padding:20px; }
 .container { max-width:900px; margin:0 auto; }
 .card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:20px; margin-bottom:20px; }
 h1 { font-size:20px; color:#0f172a; margin:0 0 12px 0; }
 h2 { font-size:16px; color:#334155; margin:18px 0 12px 0; }
 input, textarea, select { width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; color:#0f172a; box-sizing:border-box; font-family:inherit; }
 textarea { resize:vertical; }
 .btn { background:#003e6f; color:#fff; border:none; padding:8px 12px; border-radius:6px; font-size:14px; cursor:pointer; text-decoration:none; display:inline-block; }
 .btn:hover { background:#002a4f; }
 .btn-danger { background:#dc2626; }
 .btn-danger:hover { background:#b91c1c; }
 .btn-link { background:#10b981; }
 .btn-link:hover { background:#059669; }
 .error { color:#dc2626; font-size:12px; margin-top:4px; }
 .success { background:#d1fae5; color:#065f46; padding:12px; border-radius:6px; margin-bottom:16px; font-size:13px; }
 label { display:block; font-size:13px; color:#334155; margin-bottom:6px; margin-top:12px; }
 .form-group { margin-bottom:16px; }
 .back-link { color:#0f172a; text-decoration:none; font-size:14px; }
 .back-link:hover { text-decoration:underline; }
 .subtest-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-bottom:12px; transition:box-shadow 0.15s; }
 .subtest-card:hover { box-shadow:0 2px 8px rgba(0,0,0,0.06); }
 .subtest-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
 .subtest-title { font-size:16px; font-weight:600; color:#0f172a; }
 .subtest-meta { font-size:12px; color:#64748b; margin-top:4px; }
 .subtest-actions { display:flex; gap:8px; flex-wrap:wrap; }
 .badge { display:inline-block; font-size:11px; padding:3px 8px; border-radius:12px; font-weight:600; }
 .badge-blue { background:#dbeafe; color:#1e40af; }
 .badge-green { background:#d1fae5; color:#065f46; }
 .badge-amber { background:#fef3c7; color:#92400e; }
 .empty { text-align:center; color:#94a3b8; padding:30px; font-size:14px; }
 </style>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
 <div class="container">
 <a href="{{ route('banks.index') }}" class="back-link">&larr; Kembali</a>

 <div class="card">
 <h1>{{ $bank->title }}</h1>
 @if(session('success'))
 <div class="success">{{ session('success') }}</div>
 @endif

 <form method="POST" action="{{ route('banks.update', $bank) }}">
 @csrf @method('PUT')
 <div class="form-group">
 <label for="title">Judul</label>
 <input id="title" type="text" name="title" value="{{ $bank->title }}" required>
 @error('title')<div class="error">{{ $message }}</div>@enderror
 </div>
 <div class="form-group">
 <label for="description">Deskripsi</label>
 <textarea id="description" name="description" rows="3">{{ $bank->description }}</textarea>
 @error('description')<div class="error">{{ $message }}</div>@enderror
 </div>
 <div class="form-group">
 <label for="duration_minutes">Waktu Pengerjaan Keseluruhan (menit)</label>
 <div style="display:flex;align-items:center;gap:8px;">
 <input id="duration_minutes" type="number" name="duration_minutes" value="{{ $bank->duration_minutes }}" min="1" max="600" placeholder="Contoh: 60" style="width:180px;">
 <span style="font-size:13px;color:#64748b;">menit (kosongkan jika tanpa batas waktu global)</span>
 </div>
 @error('duration_minutes')<div class="error">{{ $message }}</div>@enderror
 </div>
 <div class="form-group">
 <label for="target">Tipe Peserta</label>
 <select id="target" name="target">
 <option value="karyawan" {{ old('target', $bank->target ?? 'karyawan') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
 <option value="calon_karyawan" {{ old('target', $bank->target ?? '') === 'calon_karyawan' ? 'selected' : '' }}>Calon Karyawan</option>
 </select>
 @error('target')<div class="error">{{ $message }}</div>@enderror
 </div>

 <button type="submit" class="btn">Simpan</button>
 </form>
 </div>

 <div class="card">
 <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
 <h2 style="margin:0;">Sub-Test ({{ $subTests->count() }})</h2>
 </div>

 @if($subTests->count() > 0)
 @foreach($subTests as $index => $subTest)
 <div class="subtest-card">
 <div class="subtest-header">
 <div>
 <div class="subtest-title">{{ $index + 1 }}. {{ $subTest->title }}</div>
 <div class="subtest-meta">
 @if($subTest->description) {{ Str::limit($subTest->description, 80) }} &middot; @endif
 <span class="badge badge-blue">{{ $subTest->questions_count }} Soal</span>
 <span class="badge badge-green">{{ $subTest->example_questions_count }} Contoh</span>
 @if($subTest->duration_minutes) <span class="badge badge-amber">{{ $subTest->duration_minutes }} menit</span> @endif
 </div>
 </div>
 <div class="subtest-actions">
 <a href="{{ route('sub-tests.edit', $subTest) }}" class="btn" style="background:#3b82f6;padding:6px 12px;font-size:12px;">Edit & Kelola Soal</a>
 <form method="POST" action="{{ route('sub-tests.delete', $subTest) }}" style="display:inline;" onsubmit="return confirm('Hapus sub-test ini beserta semua soalnya?');">
 @csrf @method('DELETE')
 <button type="submit" class="btn btn-danger" style="padding:6px 12px;font-size:12px;">Hapus</button>
 </form>
 </div>
 </div>
 </div>
 @endforeach
 @else
 <div class="empty">Belum ada sub-test. Tambahkan sub-test di bawah untuk memulai.</div>
 @endif
 </div>

 <div class="card">
 <h2>Tambah Sub-Test Baru</h2>
 <form method="POST" action="{{ route('banks.store') }}">
 @csrf
 <input type="hidden" name="bank_id" value="{{ $bank->id }}">
 <div class="form-group">
 <label for="sub_test_title">Judul Sub-Test *</label>
 <input id="sub_test_title" type="text" name="sub_test_title" placeholder="Contoh: Tes Logika, Tes Verbal, dll..." required>
 @error('sub_test_title')<div class="error">{{ $message }}</div>@enderror
 </div>
 <div class="form-group">
 <label for="sub_test_description">Deskripsi (opsional)</label>
 <textarea id="sub_test_description" name="sub_test_description" rows="2" placeholder="Instruksi atau penjelasan sub-test..."></textarea>
 </div>
 <div class="form-group">
 <label for="sub_test_duration">Durasi Sub-Test (menit)</label>
 <div style="display:flex;align-items:center;gap:8px;">
 <input id="sub_test_duration" type="number" name="sub_test_duration" min="1" max="600" placeholder="Contoh: 15" style="width:180px;">
 <span style="font-size:13px;color:#64748b;">menit (kosongkan jika tanpa batas waktu)</span>
 </div>
 </div>
 <button type="submit" class="btn">+ Tambah Sub-Test</button>
 </form>
 </div>

 <div class="card">
 <h2>Link Akses Tes</h2>
 <p style="font-size:13px;color:#64748b;margin-bottom:16px;">Bagikan link ini ke semua peserta. Satu link untuk semua sub-test.</p>
 <div style="display:flex;align-items:center;gap:8px;">
 <input type="text" id="sharedLink" value="{{ route('test.register', $bank->slug) }}" readonly style="flex:1;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;font-size:13px;background:#f8fafc;color:#0f172a;font-family:monospace;">
 <button onclick="copyLink()" type="button" class="btn btn-link" id="copyBtn" style="white-space:nowrap;">Copy Link</button>
 </div>
@if($bank->target === 'calon_karyawan')
<div style="margin-top:12px;padding:12px;border-radius:8px;background:#f1f5f9;">
		<h3 style="margin:0 0 8px 0;">Generate contoh kredensial untuk calon karyawan</h3>
		<form method="POST" action="{{ route('banks.credentials.generate', $bank) }}">
			@csrf
			<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
				<input id="gen_email" name="email" type="email" placeholder="Masukkan email calon..." style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;width:320px;" required>
				<button type="submit" class="btn">Generate & Simpan</button>
			</div>
		</form>

		<div style="margin-top:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
			<a href="{{ route('banks.credentials.template', $bank) }}" class="btn" style="padding:6px 10px;font-size:13px;">Download Template (Excel)</a>
			<a href="{{ route('banks.credentials.export', $bank) }}" class="btn" style="padding:6px 10px;font-size:13px;">Export Kredensial</a>
			<form method="POST" action="{{ route('banks.credentials.import', $bank) }}" enctype="multipart/form-data" style="display:inline-flex;gap:8px;align-items:center;">
				@csrf
				<input type="file" name="file" accept=".xlsx,.xls,.csv" style="padding:6px;border:1px solid #cbd5e1;border-radius:6px;background:#fff;">
				<button type="submit" class="btn" style="padding:6px 10px;font-size:13px;">Import</button>
			</form>
		</div>

		@if(session('generated_bulk'))
		<div style="margin-top:12px;padding:10px;border-radius:6px;background:#ecfccb;color:#134e4a;font-size:13px;">
			<strong>Hasil Import:</strong>
			<ul style="margin:8px 0 0 18px;">
			@foreach(session('generated_bulk') as $gb)
				<li>{{ $gb['username'] }} — {{ $gb['password'] }}</li>
			@endforeach
			</ul>
			<div style="font-size:12px;color:#475569;margin-top:6px;">Password hanya ditampilkan sekali setelah import. Simpan atau kirim ke kandidat.</div>
		</div>
		@endif

		@if(session('generated'))
			@php $g = session('generated'); @endphp
			<div style="margin-top:10px;padding:10px;border-radius:6px;background:#ecfccb;color:#134e4a;">
				<div>Berhasil dibuat — <strong>{{ $g['username'] }}</strong></div>
				<div style="margin-top:6px;">Password: <strong>{{ $g['password'] }}</strong></div>
				<div style="font-size:12px;color:#475569;margin-top:6px;">Catat password ini dan berikan ke kandidat — ini hanya ditampilkan sekali.</div>
			</div>
		@endif

		<div style="font-size:12px;color:#64748b;margin-top:8px;">Username menggunakan email kandidat. Password acak 6 karakter disimpan terenkripsi. Anda dapat menghapus akses kapan saja.</div>

		@if(isset($applicantCredentials) && $applicantCredentials->count() > 0)
			<form method="POST" action="{{ route('banks.credentials.delete_multiple', $bank) }}" onsubmit="return confirm('Hapus semua kredensial yang terpilih?');">
				@csrf
				<div style="margin-top:12px;overflow:auto;">
					<table style="width:100%;border-collapse:collapse;font-size:13px;">
						<thead>
							<tr style="text-align:left;border-bottom:1px solid #e2e8f0;">
								<th style="padding:8px; width:36px;"><input type="checkbox" id="select_all_creds" onclick="toggleAllCreds(this)"></th>
								<th style="padding:8px;">
									<a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'dir' => (request()->query('sort')=='username' && request()->query('dir')=='asc') ? 'desc' : 'asc']) }}" data-sort="username" style="color:inherit;text-decoration:none;">Username
										<span class="sort-indicator" aria-hidden="true" style="margin-left:6px;font-size:12px;">↕</span>
									</a>
								</th>
								<th style="padding:8px;">Password (lihat admin)</th>
								<th style="padding:8px;">
									<a href="{{ request()->fullUrlWithQuery(['sort' => 'used', 'dir' => (request()->query('sort')=='used' && request()->query('dir')=='asc') ? 'desc' : 'asc']) }}" data-sort="used" style="color:inherit;text-decoration:none;">Status
										<span class="sort-indicator" aria-hidden="true" style="margin-left:6px;font-size:12px;">↕</span>
									</a>
								</th>
								<th style="padding:8px;">
									<a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'dir' => (request()->query('sort')=='created_at' && request()->query('dir')=='asc') ? 'desc' : 'asc']) }}" data-sort="created_at" style="color:inherit;text-decoration:none;">Dibuat
										<span class="sort-indicator" aria-hidden="true" style="margin-left:6px;font-size:12px;">↕</span>
									</a>
								</th>
								<th style="padding:8px;">Aksi</th>
							</tr>
						</thead>
						<tbody>
						@foreach($applicantCredentials as $cred)
							<tr style="border-bottom:1px solid #f1f5f9;">
								<td style="padding:8px;vertical-align:middle;"><input type="checkbox" name="credentials[]" value="{{ $cred->id }}"></td>
								<td style="padding:8px;vertical-align:middle;">{{ $cred->username }}</td>
								<td style="padding:8px;vertical-align:middle;">{{ $cred->plain_password ?? '-' }}</td>
								<td style="padding:8px;vertical-align:middle;">@if($cred->used)<span style="color:#dc2626;font-weight:600;">Dipakai</span>@else<span style="color:#065f46;font-weight:600;">Tersedia</span>@endif</td>
								<td style="padding:8px;vertical-align:middle;">{{ $cred->created_at->format('Y-m-d H:i') }}</td>
								<td style="padding:8px;vertical-align:middle;">
									<button type="button" class="btn btn-danger" style="padding:6px 10px;font-size:12px;" onclick="singleDelete('{{ route('banks.credentials.delete', [$bank, $cred]) }}')">Hapus</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				<div style="margin-top:10px;display:flex;gap:8px;align-items:center;">
					<button type="submit" class="btn btn-danger" style="padding:8px 12px;">Hapus Terpilih</button>
				</div>
			</form>
		@endif

		<script>
		// AJAX sort: intercept header link clicks and fetch sorted data without full page reload
		(function(){
			var table = document.querySelector('table');
			if(!table) return;
			var tbody = table.querySelector('tbody');
			var bankId = '{{ $bank->id }}';
			var baseUrl = '{{ route('banks.credentials.list', $bank) }}'; // GET endpoint (uses route helper to include prefix)
			var deleteBase = baseUrl; // DELETE endpoint will be deleteBase + '/' + id

			function renderRows(items){
				var html = '';
				items.forEach(function(c){
					html += '<tr style="border-bottom:1px solid #f1f5f9;">';
					html += '<td style="padding:8px;vertical-align:middle;"><input type="checkbox" name="credentials[]" value="'+c.id+'"></td>';
					html += '<td style="padding:8px;vertical-align:middle;">'+escapeHtml(c.username)+'</td>';
					html += '<td style="padding:8px;vertical-align:middle;">'+(c.plain_password?escapeHtml(c.plain_password):'-')+'</td>';
					html += '<td style="padding:8px;vertical-align:middle;">'+(c.used?'<span style="color:#dc2626;font-weight:600;">Dipakai</span>':'<span style="color:#065f46;font-weight:600;">Tersedia</span>')+'</td>';
					html += '<td style="padding:8px;vertical-align:middle;">'+escapeHtml(c.created_at)+'</td>';
					html += '<td style="padding:8px;vertical-align:middle;">';
					html += '<button type="button" class="btn btn-danger" style="padding:6px 10px;font-size:12px;" onclick="singleDelete(\''+ deleteBase + '/' + c.id + '\')">Hapus</button>';
					html += '</td>';
					html += '</tr>';
				});
				tbody.innerHTML = html;
			}

			function escapeHtml(str){ if(!str) return ''; return String(str).replace(/[&<>"]+/g, function(s){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s];}); }

			// attach listeners to header links
			var headerLinks = table.querySelectorAll('th a');
			function updateIndicators(activeSort, activeDir){
				headerLinks.forEach(function(link){
					var s = link.getAttribute('data-sort');
					var ind = link.querySelector('.sort-indicator');
					if(!ind) return;
					if(s === activeSort){
						ind.textContent = (activeDir === 'asc') ? '↑' : '↓';
						ind.style.opacity = '1';
					} else {
						ind.textContent = '↕';
						ind.style.opacity = '0.5';
					}
				});
			}

			// set initial indicator based on server query params
			var initialSort = '{{ request()->query('sort', 'created_at') }}';
			var initialDir = '{{ request()->query('dir', 'desc') }}';
			var headerLinks = table.querySelectorAll('th a');
			updateIndicators(initialSort, initialDir);

			headerLinks.forEach(function(a){
				a.addEventListener('click', function(e){
					e.preventDefault();
					var href = a.href;
					try{
						var url = new URL(href);
						var sort = url.searchParams.get('sort') || 'created_at';
						var dir = url.searchParams.get('dir') || 'desc';
						var fetchUrl = baseUrl + '?sort=' + encodeURIComponent(sort) + '&dir=' + encodeURIComponent(dir);
						console.log('Fetching credentials:', fetchUrl);
						fetch(fetchUrl, {credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json'}})
						.then(function(resp){
							if(!resp.ok){ console.error('Failed to fetch credentials', resp.status); alert('Gagal memuat data kredensial (status '+resp.status+'). Cek console.'); return null; }
							return resp.json();
						})
						.then(function(json){ if(json && json.data){ renderRows(json.data); console.log('Credentials loaded:', json.data.length); } else { console.warn('No data in response', json); } })
						.catch(function(err){ console.error('Fetch error', err); alert('Terjadi kesalahan saat memuat data. Cek console.'); });

						// update indicators immediately to provide feedback
						updateIndicators(sort, dir);
					}catch(ex){ console.error(ex); }
				});
			});
		})();
		</script>

</div>
@endif
 <div style="margin-top:12px;background:#dbeafe;color:#0c4a6e;padding:10px 14px;border-radius:6px;font-size:12px;">
 <strong>Satu link untuk semua sub-test.</strong> Peserta akan mengerjakan tiap sub-test secara berurutan.
 </div>
 <script>
 function copyLink() {
 var input = document.getElementById('sharedLink');
 input.select(); document.execCommand('copy');
 var btn = document.getElementById('copyBtn');
 btn.textContent = 'Copied!';
 setTimeout(function() { btn.textContent = 'Copy Link'; }, 2000);
 }
function toggleAllCreds(master){
	var checkboxes = document.querySelectorAll('input[name="credentials[]"]');
	checkboxes.forEach(function(cb){ cb.checked = master.checked; });
}
function singleDelete(url){
	if(!confirm('Hapus kredensial ini?')) return;
	var form = document.createElement('form');
	form.method = 'POST';
	form.action = url;
	var token = document.createElement('input'); token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}'; form.appendChild(token);
	var method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE'; form.appendChild(method);
	document.body.appendChild(form);
	form.submit();
}
 </script>
 </div>
 </div>
</body>
</html>
