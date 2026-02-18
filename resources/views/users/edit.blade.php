<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User - HRIS</title>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body style="margin:0;">
<div style="display:flex;min-height:100vh;background:#f7fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial;">
    @include('layouts.sidebar')
    <div class="main" style="flex:1;display:flex;flex-direction:column;">
        <div class="topbar" style="background:#fff;border-bottom:1px solid #e2e8f0;padding:16px 24px;display:flex;align-items:center;gap:16px;">
            <button onclick="window.history.back()" style="background:#e2e8f0;border:none;color:#334155;padding:6px 16px;border-radius:6px;font-size:14px;cursor:pointer;display:inline-block;">← Back</button>
            <h1 style="margin:0;font-size:20px;color:#0f172a;">Edit User</h1>
        </div>
        <div style="flex:1;padding:24px;overflow-y:auto;">
            <div style="background:#fff;border:1px solid #e2e8f0;padding:24px;border-radius:8px;max-width:600px;">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <label for="name" style="display:block;font-size:13px;color:#334155;margin-bottom:6px;margin-top:14px;">Nama / Username</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;color:#0f172a;box-sizing:border-box;font-family:inherit;">
                    @error('name')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                    <label for="email" style="display:block;font-size:13px;color:#334155;margin-bottom:6px;margin-top:14px;">Email</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;color:#0f172a;box-sizing:border-box;font-family:inherit;">
                    @error('email')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                    <label for="role" style="display:block;font-size:13px;color:#334155;margin-bottom:6px;margin-top:14px;">Role</label>
                    <select name="role" id="role" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;color:#0f172a;box-sizing:border-box;font-family:inherit;background:#fff;">
                        <option value="">-- Pilih Role --</option>
                        <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="recruitmentteam" {{ old('role', $user->role) === 'recruitmentteam' ? 'selected' : '' }}>Recruitment Team</option>
                        <option value="admin_prod" {{ old('role', $user->role) === 'admin_prod' ? 'selected' : '' }}>Admin Department</option>
                        <option value="top_level_management" {{ old('role', $user->role) === 'top_level_management' ? 'selected' : '' }}>Top Level Management</option>
                        <option value="internal_hr" {{ old('role', $user->role) === 'internal_hr' ? 'selected' : '' }}>Internal HR</option>
                    </select>
                    @error('role')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                    <label for="password" style="display:block;font-size:13px;color:#334155;margin-bottom:6px;margin-top:14px;">Password <span style="color:#94a3b8;font-size:11px;">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" id="password" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;color:#0f172a;box-sizing:border-box;font-family:inherit;">
                    @error('password')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                    <label for="password_confirmation" style="display:block;font-size:13px;color:#334155;margin-bottom:6px;margin-top:14px;">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;color:#0f172a;box-sizing:border-box;font-family:inherit;">

                    <div style="margin-top:20px;">
                        <button type="submit" style="background:#003e6f;color:#fff;border:none;padding:10px 12px;border-radius:6px;font-size:14px;cursor:pointer;">Update</button>
                        <a href="{{ route('users.index') }}" style="background:#64748b;color:#fff;border:none;padding:10px 12px;border-radius:6px;font-size:14px;cursor:pointer;margin-left:8px;text-decoration:none;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
