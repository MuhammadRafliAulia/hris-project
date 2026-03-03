<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Data Keluarga - {{ $employee->nama }}</title>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <style>body{font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial;} .card{max-width:700px;margin:40px auto;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;} input,textarea,select{width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;margin-top:6px;} .btn{background:#003e6f;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:12px}</style>
</head>
<body>
    @include('layouts.sidebar')
    <div class="card">
        <h2>Edit Data Keluarga untuk {{ $employee->nama }}</h2>
        <form method="POST" action="{{ route('families.update', [$employee, $family]) }}">
            @csrf @method('PUT')
            <label>Nama
                <input type="text" name="nama" value="{{ old('nama', $family->nama) }}" required>
            </label>
            @error('nama')<div style="color:#dc2626">{{ $message }}</div>@enderror

            <label>Hubungan
                <input type="text" name="hubungan" value="{{ old('hubungan', $family->hubungan) }}" required>
            </label>
            @error('hubungan')<div style="color:#dc2626">{{ $message }}</div>@enderror

            <label>Tanggal Lahir
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $family->tanggal_lahir) }}">
            </label>

            <label>Pekerjaan
                <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $family->pekerjaan) }}">
            </label>

            <label>Alamat
                <textarea name="alamat" rows="3">{{ old('alamat', $family->alamat) }}</textarea>
            </label>

            <button class="btn" type="submit">Simpan</button>
            <a href="{{ route('families.index', $employee) }}" class="btn" style="background:#6b7280">Batal</a>
        </form>
    </div>
</body>
</html>
