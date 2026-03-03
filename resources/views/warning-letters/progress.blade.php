<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Progress Pengajuan SP</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; display:flex; height:100vh; }
    .main { flex:1; display:flex; flex-direction:column; }
    .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; }
    .topbar h1 { margin:0; font-size:20px; color:#0f172a; }
    .content { flex:1; padding:24px; overflow-y:auto; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px; text-align:left; border-bottom:1px solid #e2e8f0; font-size:14px; }
    th { background:#f1f5f9; color:#334155; font-weight:600; }
  </style>
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
  @include('layouts.sidebar')

  <div class="main">
    <div class="topbar">
      <h1>Progress Pengajuan SP</h1>
    </div>

    <div class="content">
      <div style="max-width:1200px;margin:0 auto;">
        <p style="color:#475569">Daftar pengajuan SP beserta status tanda tangan pada layer 1-4.</p>

        <div style="background:#fff;padding:16px;border-radius:8px;border:1px solid #e2e8f0;">
          <form id="bulkDeleteForm" method="POST" action="{{ route('warning-letters.bulk-delete') }}">
            @csrf
            <table style="width:100%;border-collapse:collapse;font-family:Inter,system-ui,Arial;font-size:13px;">
            <thead>
              <tr style="text-align:left;border-bottom:1px solid #e6eef7;">
                <th style="padding:8px"><input id="select_all" type="checkbox"></th>
                <th style="padding:8px">No</th>
                <th style="padding:8px">Nama</th>
                <th style="padding:8px">Departemen</th>
                <th style="padding:8px">SP Level</th>
                <th style="padding:8px">Pimpinan Kerja</th>
                <th style="padding:8px">Pekerja</th>
                <th style="padding:8px">Saksi 1</th>
                <th style="padding:8px">Saksi 2</th>
                <th style="padding:8px">Tanggal</th>
                <th style="padding:8px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- rows start --}}
              @foreach($letters as $idx => $letter)
                @php
                  $statusFor = function($i) use ($letter) {
                    $sig = $letter->{'signature_'.$i};
                    if (empty($sig)) return 'pending';
                    if (is_string($sig) && strpos($sig, 'rejected') !== false) return 'rejected';
                    return 'approved';
                  };
                @endphp
                @php
                  // check if all 4 layers are approved (cannot be deleted)
                  $all4 = true;
                  for ($j = 1; $j <= 4; $j++) {
                      $s = $letter->{'signature_'.$j};
                      if (empty($s) || (is_string($s) && strpos($s, 'rejected') !== false)) { $all4 = false; break; }
                  }
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9;">
                  <td style="padding:8px;vertical-align:middle;text-align:center;">
                    <input type="checkbox" name="ids[]" value="{{ $letter->id }}" class="row-checkbox" {{ $all4 ? 'disabled' : '' }}>
                  </td>
                  <td style="padding:8px;vertical-align:middle">{{ $idx + 1 }}</td>
                  <td style="padding:8px;vertical-align:middle">{{ $letter->nama }}</td>
                  <td style="padding:8px;vertical-align:middle">{{ $letter->departemen }}</td>
                  <td style="padding:8px;vertical-align:middle">{{ $letter->sp_label }}</td>
                  @for($i=1;$i<=4;$i++)
                    @php
                      $st = $statusFor($i);
                      $signer = $letter->{'signer_name_'.$i} ?? null;
                      $signer_jab = $letter->{'signer_jabatan_'.$i} ?? null;
                      $sigRaw = $letter->{'signature_'.$i};
                      if ($st === 'approved' && $signer) {
                        $tip = "Approved by {$signer}" . ($signer_jab ? " ({$signer_jab})" : "");
                      } elseif ($st === 'rejected' && $signer) {
                        // try to extract reason from signature stored as 'rejected_via_link:NAME|REASON @ TIMESTAMP'
                        $reasonText = '';
                        if (is_string($sigRaw) && strpos($sigRaw, 'rejected_via_link:') !== false) {
                          $meta = substr($sigRaw, strlen('rejected_via_link:'));
                          // if contains '|', split to get reason
                          if (strpos($meta, '|') !== false) {
                            list($namePart, $rest) = explode('|', $meta, 2);
                            // rest may contain 'REASON @ TIMESTAMP'
                            if (strpos($rest, ' @ ') !== false) {
                              list($reasonPart, $tsPart) = explode(' @ ', $rest, 2);
                              $reasonText = trim($reasonPart);
                            } else {
                              $reasonText = trim($rest);
                            }
                          }
                        }
                        $tip = "Rejected by {$signer}" . ($signer_jab ? " ({$signer_jab})" : "");
                        if ($reasonText) {
                          $tip .= " — Reason: {$reasonText}";
                        }
                      } else {
                        $tip = "Pending";
                      }
                    @endphp
                    <td style="padding:8px;vertical-align:middle;text-align:center;" title="{{ $tip }}">
                      @if($st === 'approved')
                        <span style="color:#10b981;font-size:18px;">✔️</span>
                      @elseif($st === 'rejected')
                        <span style="color:#ef4444;font-size:18px;">✖️</span>
                      @else
                        <span style="color:#94a3b8;font-size:16px;">◻️</span>
                      @endif
                    </td>
                  @endfor
                  <td style="padding:8px;vertical-align:middle">{{ $letter->created_at->format('d/m/Y') }}</td>
                  <td style="padding:8px;vertical-align:middle;white-space:nowrap;">
                    <a href="{{ route('warning-letters.sign-form', $letter->id) }}" class="btn" style="background:#7c3aed;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;font-size:13px;margin-right:6px;">✍️ Sign</a>
                    <a href="{{ route('warning-letters.show-pdf', $letter->id) }}" class="btn" style="background:#003e6f;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;font-size:13px;margin-right:6px;">Lihat PDF</a>
                    @if(!$all4)
                      <button type="button" onclick="confirmDelete({{ $letter->id }})" class="btn" style="background:#ef4444;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;font-size:13px;">Hapus</button>
                    @else
                      <button type="button" disabled class="btn" style="background:#94a3b8;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;font-size:13px;">Hapus</button>
                    @endif
                  </td>
                </tr>
              @endforeach
              {{-- rows end --}}
            </tbody>
          </table>
          <div style="margin-top:12px;">
            <button id="bulkDeleteBtn" type="button" class="btn" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:6px;border:none;" onclick="submitBulkDelete()">Hapus Terpilih</button>
            <span style="margin-left:12px;color:#64748b;font-size:13px;">(Baris yang sudah terisi semua 4 tanda tangan tidak bisa dihapus)</span>
          </div>
          </form>
    </div>
    <script>
      (function(){
        var selectAll = document.getElementById('select_all');
        if (selectAll) {
          selectAll.addEventListener('change', function(){
            var rows = document.querySelectorAll('.row-checkbox');
            rows.forEach(function(cb){ if (!cb.disabled) cb.checked = selectAll.checked; });
          });
        }

        window.submitBulkDelete = function() {
          var form = document.getElementById('bulkDeleteForm');
          if (!form) return;
          var checked = form.querySelectorAll('input[name="ids[]"]:checked');
          if (!checked.length) {
            alert('Pilih minimal satu baris untuk dihapus.');
            return;
          }
          if (!confirm('Hapus data terpilih? Tindakan ini tidak dapat dibatalkan.')) return;
          form.submit();
        }

        window.confirmDelete = function(id) {
          if (!confirm('Hapus baris ini? Tindakan ini tidak dapat dibatalkan.')) return;
          var form = document.getElementById('bulkDeleteForm');
          var token = form.querySelector('input[name="_token"]').value;
          var temp = document.createElement('form');
          temp.method = 'POST';
          temp.action = '{{ route('warning-letters.bulk-delete') }}';
          var inpToken = document.createElement('input'); inpToken.type='hidden'; inpToken.name='_token'; inpToken.value = token; temp.appendChild(inpToken);
          var inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value = id; temp.appendChild(inp);
          document.body.appendChild(temp);
          temp.submit();
        }
      })();
    </script>
      </div>
    </div>
  </div>
</body>
</html>
  </div>
</div>
