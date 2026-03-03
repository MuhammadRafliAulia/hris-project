<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Peringatan {{ $letter->sp_label }} - {{ $letter->nama }}</title>
  <style>
    /* Page margins: top right bottom left (requested) */
    @page { margin: 3cm 3cm 3cm 4cm; }
    :root { --content-margin-left: 80px; --content-margin-right: 40px; --text-indent: 40px; }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Times, serif; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #1a1a1a; line-height: 1.6; }
    /* .page padding provides an inner content area; combined with @page gives formal margins */
    .page { padding: 32px 48px; max-width: 800px; margin: 0 auto; }

    .header { text-align: center; border-bottom: 3px double #003e6f; padding-bottom: 14px; margin-bottom: 24px; }
    .header .company { font-size: 18pt; font-weight: bold; color: #003e6f; letter-spacing: 1px; }
    .header .address { font-size: 9pt; color: #64748b; margin-top: 4px; }

    .title { text-align: center; margin: 20px 0 6px 0; }
    .title h1 { font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
    .nomor-surat { text-align: center; font-size: 11pt; margin-bottom: 20px; }

    /* Main body paragraphs: use uniform left/right content margins and consistent indentation */
    .body-text { font-size: 11pt; text-align: justify; margin: 0 var(--content-margin-right) 12px var(--content-margin-left); }
    .body-text p { margin: 0 0 12px 0; text-indent: var(--text-indent); }

     /* Optional second paragraph block should have clear left/right margins to look formal
       and be constrained to the printable area so it won't overflow the page. */
    /* Optional second paragraph block should have the same margins and indentation as other paragraphs */
    .paragraf-kedua { margin: 12px var(--content-margin-right) 16px var(--content-margin-left); text-align: justify; font-size:11pt; overflow-wrap: break-word; word-wrap: break-word; hyphens: auto; page-break-inside: avoid; }
    .paragraf-kedua p { text-indent: var(--text-indent); margin-bottom: 12px; }

    /* Recipient and detail tables align with the content margin */
    .recipient { margin: 0 var(--content-margin-right) 16px var(--content-margin-left); }
    .recipient table { border-collapse: collapse; }
    .recipient td { padding: 2px 8px 2px 0; font-size: 11pt; vertical-align: top; }
    /* Labels for recipient and detail rows should NOT be bold per request */
    .recipient .label { font-weight: normal; width: 120px; }

    .sig-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
    .sig-table td { text-align: center; vertical-align: top; padding: 0 8px; width: 50%; }
    .sig-block { margin-bottom: 20px; }
    .sig-block .sig-title { font-size: 10pt; font-weight: bold; margin-bottom: 4px; }
    .sig-block .sig-jabatan { font-size: 9pt; color: #475569; margin-bottom: 6px; }
    .sig-block .sig-img { height: 60px; display: flex; align-items: center; justify-content: center; margin: 4px auto; }
    .sig-block .sig-img img { max-width: 150px; max-height: 55px; }
    .sig-block .sig-name { font-size: 10pt; font-weight: bold; border-top: 1px solid #1a1a1a; display: inline-block; padding-top: 3px; min-width: 150px; font-family: 'Times New Roman', Times, serif; }

    .sig-full { text-align: center; margin-top: 10px; }

    .closing { margin-top: 20px; font-size: 11pt; text-align: justify; margin-left:var(--content-margin-left); margin-right:var(--content-margin-right); }
    .closing p { margin-bottom: 10px; text-indent: var(--text-indent); }

    .footer { text-align: center; font-size: 8pt; color: #94a3b8; margin-top: 30px; padding-top: 10px; border-top: 1px solid #e2e8f0; }

    @media print { .page { padding: 20px 40px; } }
  </style>
</head>
<body>
  <div class="page">

    {{-- Header removed per request (company kop omitted) --}}

    {{-- Judul Surat --}}
    <div class="title">
      <h1>Surat Peringatan</h1>
    </div>
    <div class="nomor-surat">
      @if($letter->nomor_surat)
        Nomor: {{ $letter->nomor_surat }}
      @else
        Nomor: ...................
      @endif
    </div>

    {{-- Paragraf Pembuka --}}
    <div class="body-text">
      <p>Surat peringatan ini dibuat oleh pimpinan kerja dan ditujukan kepada:</p>
    </div>

    {{-- Data Penerima --}}
    <div class="recipient">
      <table>
        <tr>
          <td class="label">Nama</td>
          <td>: {{ $letter->nama }}</td>
        </tr>
        @if($letter->nik)
        <tr>
          <td class="label">NIK</td>
          <td>: {{ $letter->nik }}</td>
        </tr>
        @endif
        <tr>
          <td class="label">Jabatan</td>
          <td>: {{ $letter->jabatan }}</td>
        </tr>
        <tr>
          <td class="label">Departemen</td>
          <td>: {{ $letter->departemen }}</td>
        </tr>
      </table>
    </div>

    {{-- Paragraf Alasan --}}
    <div class="body-text">
      <p>Surat peringatan ini diterbitkan oleh pimpinan kerja berdasarkan atas terjadinya kelalaian Pekerja dalam melaksanakan proses produksi dengan detail berikut:</p>
    </div>

    {{-- Detail block: Tempat, Proses, Tanggal, Reason --}}
    <div class="recipient" style="margin-top:8px;">
      <table>
        <tr>
          <td class="label">Tempat</td>
          <td>: {{ $letter->tempat ?? '-' }}</td>
        </tr>
        <tr>
          <td class="label">Proses</td>
          <td>: {{ $letter->proses ?? '-' }}</td>
        </tr>
        <tr>
          <td class="label">Tanggal</td>
          <td>: {{ $letter->tanggal_surat ? $letter->tanggal_surat->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
          <td class="label">Reason</td>
          <td>: {{ $letter->alasan }}</td>
        </tr>
      </table>
    </div>

    {{-- Paragraf Kedua (opsional) --}}
    @if($letter->paragraf_kedua)
    <div class="paragraf-kedua">
      <p>{{ $letter->paragraf_kedua }}</p>
    </div>
    @endif

    {{-- Paragraf Penutup --}}
    <div class="closing">
      <p>Oleh karena itu, selaku pimpinan kerja memberikan <strong>{{ $letter->sp_label }}</strong>. Hal ini bertujuan untuk mengingatkan kepada operator didalam bekerja agar tidak terjadi kelalaian dikemudian hari. Surat peringatan ini berlaku selama 6 (enam) bulan terhitung dari tanggal dikeluarkannya surat peringatan ini.</p>
    </div>

    {{-- Tempat & Tanggal (di atas kolom tanda tangan, kanan bawah) --}}
    <div style="text-align: right; margin-top: 18px; font-size: 11pt;">
      Bekasi, {{ $letter->tanggal_surat ? $letter->tanggal_surat->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
    </div>

    {{-- ===== TANDA TANGAN 5 LAYER (sebar di kanan bawah) ===== --}}
    <div style="clear:both; height:18px;"></div>
    <div style="text-align: right; margin-top:6px;">
      <table style="display:inline-table;border-collapse:collapse;">
        <tr>
          @for($i=1;$i<=5;$i++)
            <td style="width:110px;padding:6px 8px;vertical-align:top;text-align:center;">
              <div style="font-size:10pt;font-weight:600;margin-bottom:4px;font-family:'Times New Roman', Times, serif;">
                @if($i==1) PT. Shindengen Indonesia @elseif($i==2) Pekerja @elseif($i==3) Saksi 1 @elseif($i==4) Saksi 2 @else HR @endif
              </div>
              @php
                $sig = $letter->{'signature_' . $i} ?? null;
                $name = $letter->{'signer_name_' . $i} ?? null;
                $jab = $letter->{'signer_jabatan_' . $i} ?? null;
              @endphp
              <div style="height:42px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                @if($sig)
                  <img src="{{ $sig }}" alt="TTD {{ $i }}" style="max-width:100px;max-height:42px;">
                @else
                  <div style="width:100px;height:42px;"></div>
                @endif
              </div>
              <div style="font-size:10pt;font-weight:600;border-top:1px solid #1a1a1a;padding-top:4px;margin-top:6px;min-height:20px;font-family:'Times New Roman', Times, serif;">
                {{ $name ?: '(........................)' }}
              </div>
              <div style="font-size:9pt;color:#475569;margin-top:4px;font-family:'Times New Roman', Times, serif;">
                {{ $jab ?: '' }}
              </div>
            </td>
          @endfor
        </tr>
      </table>
    </div>

    {{-- Approved notice removed from template per request --}}

    {{-- Footer removed per design (no company footer) --}}

  </div>
</body>
</html>
