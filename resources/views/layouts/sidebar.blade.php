{{-- Mobile Hamburger --}}
<button id="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('sidebar-open')">
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>
<div id="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('sidebar-open')"></div>

<div class="sidebar" style="width:280px;min-width:280px;flex-shrink:0;background:#ffffff;color:#6b7280;border-right:1px solid rgba(15,23,42,0.06);padding:18px;box-sizing:border-box;overflow-y:auto;display:flex;flex-direction:column;min-height:100vh;position:relative;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;">
  {{-- Mobile close --}}
  <button id="sidebar-close" onclick="document.querySelector('.sidebar').classList.remove('sidebar-open')" style="display:none;position:absolute;top:12px;right:12px;background:none;border:none;cursor:pointer;padding:4px;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>

  <div style="flex:1;display:flex;flex-direction:column;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid rgba(15,23,42,0.04);">
      <img src="{{ asset('logo.png') }}" alt="Logo" style="width:100px;height:auto;object-fit:contain;flex-shrink:0;display:block;margin:0;padding:0;">
      <div style="min-width:0;">
        <div style="font-size:11px;color:#003e6f;font-weight:700;line-height:1.2;letter-spacing:-0.2px;">Human Resource Information System</div>
        <div style="font-size:8px;color:#6b7280;letter-spacing:0.4px;margin-top:2px;">Shindengen Indonesia</div>
      </div>
    </div>
    <h2 style="font-size:11px;color:#6b7280;margin:0 0 12px 0;font-weight:400;text-transform:uppercase;letter-spacing:0.6px;">Menu</h2>
    <ul class="sidebar-menu" style="list-style:none;margin:0;padding:0;">
      @if(optional(auth()->user())->isSuperAdmin())
      <li style="margin-bottom:8px;"><a href="{{ route('dashboard') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Dashboard</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('banks.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Psikotest Online</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('banks.cheat-log') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Log Kecurangan</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('employees.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Database Karyawan</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('warning-letters.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Surat Peringatan</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('activity-logs.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Log Aktivitas</a></li>
      @endif
      @if(optional(auth()->user())->isRecruitmentTeam())
      <li style="margin-bottom:8px;"><a href="{{ route('recruitment.dashboard') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Dashboard</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('banks.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Psikotest Online</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('banks.cheat-log') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Log Kecurangan</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('tasks.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Task Management</a></li>
      @endif
      @if(optional(auth()->user())->isInternalHR())
      <li style="margin-bottom:8px;"><a href="{{ route('dashboard') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Dashboard</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('tasks.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Task Management</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('employees.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Database Karyawan</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('warning-letters.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">Surat Peringatan</a></li>
      @endif
      @if(optional(auth()->user())->isAdminProd())
      <li style="margin-bottom:8px;"><a href="{{ route('warning-letters.create') }}" style="display:block;padding:10px 12px;color:#334155;text-decoration:none;border-radius:6px;font-size:14px;">⚠️ Input Surat Peringatan</a></li>
      @endif
      @if(optional(auth()->user())->isTopLevelManagement())
      <li style="margin-bottom:8px;"><a href="{{ route('dashboard') }}" style="display:block;padding:10px 12px;color:#334155;text-decoration:none;border-radius:6px;font-size:14px;">Dashboard</a></li>
      <li style="margin-bottom:8px;"><a href="{{ route('tasks.index') }}" style="display:block;padding:10px 12px;color:#334155;text-decoration:none;border-radius:6px;font-size:14px;">📋 Task Management</a></li>
      @endif
    </ul>
    @if(optional(auth()->user())->isSuperAdmin())
    <div style="border-top:1px solid rgba(15,23,42,0.04);padding-top:16px;margin-top:16px;">
      <div style="font-size:13px;color:#6b7280;font-weight:400;margin-bottom:8px;cursor:pointer;" onclick="document.getElementById('setting-dropdown').classList.toggle('show');">
        ⚙️ Setting <span style="float:right;">&#9660;</span>
      </div>
      <ul id="setting-dropdown" class="setting-dropdown" style="display:none;list-style:none;padding:0;margin:0;">
        <li><a href="{{ route('departments.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">🏢 Master Departemen</a></li>
        <li><a href="{{ route('users.index') }}" style="display:block;padding:10px 12px;color:#6b7280;text-decoration:none;border-radius:6px;font-size:13px;font-weight:400;">👤 Master User</a></li>
      </ul>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var settingTitle = document.querySelector('.sidebar div[onclick]');
        var dropdown = document.getElementById('setting-dropdown');
        if(settingTitle && dropdown) {
          settingTitle.addEventListener('click', function() {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
          });
        }
      });
    </script>
    @endif
  </div>

  {{-- Portal Access --}}
  <div style="border-top:1px solid rgba(15,23,42,0.04);padding-top:14px;margin-bottom:14px;">
    <div style="font-size:12px;color:#6b7280;font-weight:400;margin-bottom:10px;">🌐 Portal Access</div>
    <div style="display:flex;flex-direction:column;gap:8px;">
      <a href="https://hrmsystemapp.com/login" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:#f8fafc;border:1px solid #e6eef7;border-radius:6px;color:#6b7280;text-decoration:none;font-size:10px;font-weight:400;transition:all 0.15s;" onmouseover="this.style.background='#eef8ff';this.style.borderColor='#cfefff';" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e6eef7';">
        <span style="font-size:16px;">📊</span> Human Resource Management System SDI
      </a>
      <a href="https://portal2.example.com" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:#f8fafc;border:1px solid #e6f0ea;border-radius:6px;color:#6b7280;text-decoration:none;font-size:10px;font-weight:400;transition:all 0.15s;" onmouseover="this.style.background='#f0fbf1';this.style.borderColor='#cfeed4';" onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e6f0ea';">
        <span style="font-size:16px;">📧</span> IDFileShare
      </a>
      <!-- Assesment Test portal link removed per design update -->
    </div>
  </div>

  <div style="border-top:1px solid rgba(15,23,42,0.04); padding-top:14px; margin-top:auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:8px;min-width:0;">
        <div style="width:30px;height:30px;border-radius:50%;background:rgba(15,23,42,0.04);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#6b7280;flex-shrink:0;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div style="min-width:0;"><div style="font-size:11px;font-weight:400;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div><div style="font-size:9px;color:#6b7280;text-transform:capitalize;">{{ auth()->user()->role }}</div></div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" style="background:none;border:1px solid rgba(15,23,42,0.06);border-radius:6px;padding:5px 10px;font-size:11px;color:#6b7280;cursor:pointer;font-weight:400;transition:all 0.15s;display:flex;align-items:center;gap:4px;" onmouseover="this.style.background='rgba(15,23,42,0.04)';this.style.color='#374151';this.style.borderColor='rgba(15,23,42,0.12)';" onmouseout="this.style.background='none';this.style.color='#6b7280';this.style.borderColor='rgba(15,23,42,0.06)';">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Logout
        </button>
      </form>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var sidebar = document.querySelector('.sidebar');
  var overlay = document.getElementById('sidebar-overlay');
  if (!sidebar || !overlay) return;

  // Show/hide overlay when sidebar opens/closes
  var observer = new MutationObserver(function() {
    overlay.style.display = sidebar.classList.contains('sidebar-open') ? 'block' : 'none';
  });
  observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

  // Close sidebar on menu click (mobile)
  sidebar.querySelectorAll('a').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) sidebar.classList.remove('sidebar-open');
    });
  });
});
</script>
