<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Data Keluarga - {{ $employee->nama }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; display:flex; height:100vh; }
        .main { flex:1; display:flex; flex-direction:column; }
        .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 24px; }
        .topbar h1 { margin:0; font-size:20px; color:#0f172a; }
        .content { flex:1; padding:24px; overflow-y:auto; }
        .card { background:#fff; border:1px solid #e2e8f0; padding:24px; border-radius:8px; max-width:700px; }
        table { width:100%; border-collapse:collapse; margin-top:16px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #e2e8f0; font-size:14px; }
        th { background:#f1f5f9; color:#334155; font-weight:600; }
        .btn { background:#003e6f; color:#fff; border:none; padding:10px 12px; border-radius:6px; font-size:14px; cursor:pointer; text-decoration:none; margin-top:18px; display:inline-block; }
        .empty { text-align:center; color:#64748b; padding:40px; }
        /* Modal styles */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:60; }
        .modal-dialog { background:#fff; border-radius:10px; max-width:720px; width:720px; padding:22px; border:1px solid #e6eef6; box-shadow:0 12px 40px rgba(2,6,23,0.25); }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .modal-body label { display:block; margin-top:10px; font-size:14px; color:#334155; }
        .modal-body input[type="text"], .modal-body input[type="date"], .modal-body textarea { width:100%; padding:10px 12px; border:1px solid #d1d9e6; border-radius:8px; margin-top:6px; font-size:14px; box-sizing:border-box; }
        .modal-actions { margin-top:16px; display:flex; gap:10px; }
        @media (max-width:780px){ .modal-dialog{ width:92%; max-width:92%; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
    @include('layouts.sidebar')
    <div class="main">
        <div class="topbar">
            <h1>Data Keluarga - {{ $employee->nama }}</h1>
        </div>
        @php $user = auth()->user(); @endphp
        <div class="content">
            <div class="card">
                <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Hubungan</th>
                            <th>Tanggal Lahir</th>
                            <th>Pekerjaan</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($families as $family)
                        <tr>
                            <td>{{ $family->nama }}</td>
                            <td>{{ $family->hubungan }}</td>
                            <td>{{ $family->tanggal_lahir }}</td>
                            <td>{{ $family->pekerjaan }}</td>
                            <td>{{ $family->alamat }}</td>
                            <td>
                                @if($user && ($user->isSuperAdmin() || $user->isInternalHR()))
                                    <a href="{{ route('families.edit', [$employee, $family]) }}" class="btn" style="padding:6px 8px;font-size:13px;margin-right:6px;">Edit</a>
                                    <form method="POST" action="{{ route('families.destroy', [$employee, $family]) }}" style="display:inline;" onsubmit="return confirm('Hapus data keluarga ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn" style="background:#dc2626;padding:6px 8px;font-size:13px;">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="empty">Belum ada data keluarga.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <!-- Modal for adding family -->
                <div id="familyModal" class="modal-overlay">
                    <div class="modal-dialog">
                        <div class="modal-header">
                            <h3 style="margin:0;">Tambah Data Keluarga untuk {{ $employee->nama }}</h3>
                            <button type="button" onclick="closeFamilyModal()" style="background:transparent;border:none;font-size:22px;line-height:1;">&times;</button>
                        </div>
                        <div class="modal-body">
                        <form method="POST" action="{{ route('families.store', $employee) }}" id="familyCreateForm">
                            @csrf
                            <input type="hidden" name="from_modal" value="1">
                            <label>Nama
                                <input type="text" name="nama" value="{{ old('nama') }}" required>
                            </label>
                            @error('nama')<div style="color:#dc2626">{{ $message }}</div>@enderror

                            <label>Hubungan
                                <input type="text" name="hubungan" value="{{ old('hubungan') }}" required>
                            </label>
                            @error('hubungan')<div style="color:#dc2626">{{ $message }}</div>@enderror

                            <label>Tanggal Lahir
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                            </label>

                            <label>Pekerjaan
                                <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}">
                            </label>

                            <label>Alamat
                                <textarea name="alamat" rows="3">{{ old('alamat') }}</textarea>
                            </label>

                            <div class="modal-actions">
                                <button type="submit" class="btn">Simpan</button>
                                <button type="button" onclick="closeFamilyModal()" class="btn" style="background:#6b7280">Batal</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
                <div style="margin-top:12px;display:flex;gap:8px;align-items:center;">
                    @php $user = auth()->user(); @endphp
                    @if($user && ($user->isSuperAdmin() || $user->isInternalHR()))
                        <button type="button" onclick="openFamilyModal()" class="btn">+ Tambah Keluarga</button>
                    @endif
                    <a href="{{ route('employees.index') }}" class="btn" style="background:#6b7280">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
function openFamilyModal(){
    var m = document.getElementById('familyModal'); if(!m) return; m.style.display='flex'; document.body.style.overflow='hidden';
}
function closeFamilyModal(){
    var m = document.getElementById('familyModal'); if(!m) return; m.style.display='none'; document.body.style.overflow='auto';
}
// If validation failed from modal submission, reopen modal to show errors and old input
document.addEventListener('DOMContentLoaded', function(){
    var shouldOpen = {{ old('from_modal') ? 'true' : 'false' }};
    if(shouldOpen){ openFamilyModal(); }
});
</script>
