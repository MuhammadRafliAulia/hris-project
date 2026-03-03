{{-- Mobile Hamburger --}}
<button id="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('sidebar-open')">
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>
<div id="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('sidebar-open')"></div>

<style>
.sb{width:260px;min-width:260px;flex-shrink:0;background:#fff;color:#475569;border-right:1px solid #f1f5f9;padding:0;box-sizing:border-box;overflow-y:auto;display:flex;flex-direction:column;min-height:100vh;font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;}
.sb-brand{display:flex;align-items:center;gap:10px;padding:18px 20px 14px;border-bottom:1px solid #f1f5f9;}
.sb-brand img{width:90px;height:auto;object-fit:contain;flex-shrink:0;}
.sb-brand-text{font-size:10px;color:#003e6f;font-weight:700;line-height:1.25;letter-spacing:-.1px;}
.sb-brand-sub{font-size:8px;color:#94a3b8;letter-spacing:.3px;margin-top:2px;}
.sb-section{padding:14px 14px 0;}
.sb-label{font-size:9px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.8px;padding:0 6px;margin-bottom:6px;}
.sb-nav{list-style:none;margin:0;padding:0;}
.sb-item{margin-bottom:2px;}
.sb-link{display:flex;align-items:center;gap:10px;padding:9px 12px;color:#475569;text-decoration:none;border-radius:8px;font-size:13px;font-weight:500;transition:all .15s ease;cursor:pointer;border:none;background:none;width:100%;text-align:left;font-family:inherit;}
.sb-link:hover{background:#f1f5f9;color:#0f172a;}
.sb-link.active{background:linear-gradient(135deg,#003e6f,#0a5a9e);color:#fff !important;font-weight:600;box-shadow:0 2px 8px rgba(0,62,111,.18);}
.sb-link.active .sb-icon{color:#fff;}
.sb-link.active .sb-arrow{color:rgba(255,255,255,.6);}
.sb-icon{width:18px;text-align:center;font-size:14px;flex-shrink:0;color:#94a3b8;transition:color .15s;}
.sb-link:hover .sb-icon{color:#475569;}
.sb-text{flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-arrow{margin-left:auto;font-size:10px;color:#94a3b8;transition:transform .25s ease;flex-shrink:0;}
.sb-arrow.open{transform:rotate(180deg);}
.sb-sub{list-style:none;margin:0;padding:0;overflow:hidden;max-height:0;transition:max-height .3s ease,opacity .25s ease;opacity:0;}
.sb-sub.open{max-height:300px;opacity:1;}
.sb-sub li{margin:0;}
.sb-sub a{display:flex;align-items:center;gap:8px;padding:7px 12px 7px 40px;color:#64748b;text-decoration:none;font-size:12px;font-weight:400;border-radius:6px;transition:all .15s;}
.sb-sub a:hover{background:#f8fafc;color:#0f172a;}
.sb-sub a.active{color:#003e6f;font-weight:600;background:#eff6ff;}
.sb-sub .sb-dot{width:5px;height:5px;border-radius:50%;background:#cbd5e1;flex-shrink:0;transition:background .15s;}
.sb-sub a.active .sb-dot{background:#003e6f;}
.sb-sub a:hover .sb-dot{background:#64748b;}
.sb-divider{height:1px;background:#f1f5f9;margin:10px 14px;}
.sb-portal{padding:10px 14px;}
.sb-portal-label{font-size:9px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.8px;padding:0 6px;margin-bottom:8px;}
.sb-portal-link{display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f8fafc;border:1px solid #f1f5f9;border-radius:6px;color:#64748b;text-decoration:none;font-size:10px;font-weight:400;transition:all .15s;margin-bottom:6px;}
.sb-portal-link:hover{background:#eff6ff;border-color:#dbeafe;color:#334155;}
.sb-portal-link span{font-size:14px;}
.sb-footer{border-top:1px solid #f1f5f9;padding:12px 16px;margin-top:auto;}
.sb-user{display:flex;align-items:center;justify-content:space-between;}
.sb-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#003e6f,#0a5a9e);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;}
.sb-user-info{min-width:0;margin-left:8px;flex:1;}
.sb-user-name{font-size:12px;font-weight:500;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-user-role{font-size:9px;color:#94a3b8;text-transform:capitalize;}
.sb-logout{background:none;border:1px solid #f1f5f9;border-radius:6px;padding:5px 10px;font-size:11px;color:#64748b;cursor:pointer;font-weight:400;transition:all .15s;display:flex;align-items:center;gap:4px;}
.sb-logout:hover{background:#fef2f2;color:#dc2626;border-color:#fecaca;}

/* Close button mobile */
#sidebar-close{display:none;position:absolute;top:12px;right:12px;background:none;border:none;cursor:pointer;padding:4px;z-index:2;}
</style>

<div class="sidebar sb">
  <button id="sidebar-close" onclick="document.querySelector('.sidebar').classList.remove('sidebar-open')">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>

  {{-- Brand --}}
  <div class="sb-brand">
    <img src="{{ asset('logo.png') }}" alt="Logo">
    <div>
      <div class="sb-brand-text">Human Resource Information System</div>
      <div class="sb-brand-sub">Shindengen Indonesia</div>
    </div>
  </div>

  <div class="sb-section" style="flex:1;">
    <div class="sb-label">Menu</div>
    <ul class="sb-nav">

      {{-- ════════════════════════════════════════════
          1. DASHBOARD (single link, no dropdown)
      ════════════════════════════════════════════ --}}
      @if(optional(auth()->user())->isSuperAdmin() || optional(auth()->user())->isInternalHR() || optional(auth()->user())->isTopLevelManagement())
      <li class="sb-item">
        <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <span class="sb-icon">📊</span><span class="sb-text">Dashboard</span>
        </a>
      </li>
      @endif
      @if(optional(auth()->user())->isRecruitmentTeam())
      <li class="sb-item">
        <a href="{{ route('recruitment.dashboard') }}" class="sb-link {{ request()->routeIs('recruitment.dashboard') ? 'active' : '' }}">
          <span class="sb-icon">📊</span><span class="sb-text">Dashboard</span>
        </a>
      </li>
      @endif

      {{-- ════════════════════════════════════════════
          2. KARYAWAN (dropdown)
             - Database Karyawan (superadmin, internal_hr)
             - Surat Peringatan (superadmin, internal_hr)
             - Input SP (admin_prod)
             - Progress SP (admin_prod)
      ════════════════════════════════════════════ --}}
      @if(optional(auth()->user())->isSuperAdmin() || optional(auth()->user())->isInternalHR() || optional(auth()->user())->isAdminProd())
      <li class="sb-item">
        <button class="sb-link" onclick="toggleMenu('menu-karyawan')">
          <span class="sb-icon">👥</span><span class="sb-text">Karyawan</span>
          <span class="sb-arrow" id="arrow-menu-karyawan">▾</span>
        </button>
        <ul class="sb-sub" id="menu-karyawan">
          @if(optional(auth()->user())->isSuperAdmin() || optional(auth()->user())->isInternalHR())
          <li><a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}"><span class="sb-dot"></span>Database Karyawan</a></li>
          <li><a href="{{ route('warning-letters.index') }}" class="{{ request()->routeIs('warning-letters.index','warning-letters.create','warning-letters.edit') ? 'active' : '' }}"><span class="sb-dot"></span>Surat Peringatan</a></li>
          <li><a href="{{ route('surveys.index') }}" class="{{ request()->routeIs('surveys.*') ? 'active' : '' }}"><span class="sb-dot"></span>Engagement</a></li>
          @endif
          @if(optional(auth()->user())->isAdminProd())
          <li><a href="{{ route('warning-letters.create') }}" class="{{ request()->routeIs('warning-letters.create') ? 'active' : '' }}"><span class="sb-dot"></span>Input Surat Peringatan</a></li>
          <li><a href="{{ route('warning-letters.progress') }}" class="{{ request()->routeIs('warning-letters.progress') ? 'active' : '' }}"><span class="sb-dot"></span>Progress Pengajuan SP</a></li>
          @endif
        </ul>
      </li>
      @endif

      {{-- ════════════════════════════════════════════
          3. RECRUITMENT & TRAINING (dropdown)
             - Psikotest Online (superadmin, recruitmentteam)
      ════════════════════════════════════════════ --}}
      @if(optional(auth()->user())->isSuperAdmin() || optional(auth()->user())->isRecruitmentTeam())
      <li class="sb-item">
        <button class="sb-link" onclick="toggleMenu('menu-recruitment')">
          <span class="sb-icon">🎓</span><span class="sb-text">Recruitment & Training</span>
          <span class="sb-arrow" id="arrow-menu-recruitment">▾</span>
        </button>
        <ul class="sb-sub" id="menu-recruitment">
          <li><a href="{{ route('banks.index') }}" class="{{ request()->routeIs('banks.index','banks.show','banks.create','banks.edit') ? 'active' : '' }}"><span class="sb-dot"></span>Psikotest Online</a></li>
        </ul>
      </li>
      @endif

      {{-- ════════════════════════════════════════════
          4. LOG (dropdown)
             - Log Aktivitas (superadmin)
             - Log Kecurangan (superadmin, recruitmentteam)
      ════════════════════════════════════════════ --}}
      @if(optional(auth()->user())->isSuperAdmin() || optional(auth()->user())->isRecruitmentTeam())
      <li class="sb-item">
        <button class="sb-link" onclick="toggleMenu('menu-log')">
          <span class="sb-icon">📋</span><span class="sb-text">Log</span>
          <span class="sb-arrow" id="arrow-menu-log">▾</span>
        </button>
        <ul class="sb-sub" id="menu-log">
          @if(optional(auth()->user())->isSuperAdmin())
          <li><a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'active' : '' }}"><span class="sb-dot"></span>Log Aktivitas</a></li>
          @endif
          <li><a href="{{ route('banks.cheat-log') }}" class="{{ request()->routeIs('banks.cheat-log') ? 'active' : '' }}"><span class="sb-dot"></span>Log Kecurangan</a></li>
        </ul>
      </li>
      @endif

      {{-- ════════════════════════════════════════════
          TASK MANAGEMENT (single link)
          (superadmin, top_level_management, recruitmentteam, internal_hr)
      ════════════════════════════════════════════ --}}
      @if(in_array(optional(auth()->user())->role, ['superadmin','top_level_management','recruitmentteam','internal_hr']))
      <li class="sb-item">
        <a href="{{ route('tasks.index') }}" class="sb-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
          <span class="sb-icon">✅</span><span class="sb-text">Task Management</span>
        </a>
      </li>
      @endif

    </ul>

    {{-- ════════════════════════════════════════════
        SETTINGS (dropdown, superadmin only)
    ════════════════════════════════════════════ --}}
    @if(optional(auth()->user())->isSuperAdmin())
    <div style="margin-top:8px;">
      <div class="sb-label">Settings</div>
      <ul class="sb-nav">
        <li class="sb-item">
          <button class="sb-link" onclick="toggleMenu('menu-settings')">
            <span class="sb-icon">⚙️</span><span class="sb-text">Setting</span>
            <span class="sb-arrow" id="arrow-menu-settings">▾</span>
          </button>
          <ul class="sb-sub" id="menu-settings">
            <li><a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}"><span class="sb-dot"></span>Master Departemen</a></li>
            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><span class="sb-dot"></span>Master User</a></li>
          </ul>
        </li>
      </ul>
    </div>
    @endif
  </div>

  <div class="sb-divider"></div>

  {{-- Portal Access --}}
  <div class="sb-portal">
    <div class="sb-portal-label">Portal Access</div>
    <a href="https://hrmsystemapp.com/login" target="_blank" class="sb-portal-link">
      <span>📊</span> Human Resource Management System SDI
    </a>
    <a href="https://sdi-fileshare.com/" target="_blank" class="sb-portal-link">
      <span>📧</span> SDI-FileShare
    </a>
  </div>

  {{-- User Footer --}}
  <div class="sb-footer">
    <div class="sb-user">
      <div style="display:flex;align-items:center;min-width:0;flex:1;">
        <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="sb-user-info">
          <div class="sb-user-name">{{ auth()->user()->name }}</div>
          <div class="sb-user-role">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" class="sb-logout">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Logout
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function toggleMenu(id) {
  var sub = document.getElementById(id);
  var arrow = document.getElementById('arrow-' + id);
  if (!sub) return;
  var isOpen = sub.classList.contains('open');
  sub.classList.toggle('open');
  if (arrow) arrow.classList.toggle('open');
}

// Auto-open dropdown if it contains an active link
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.sb-sub').forEach(function(sub) {
    if (sub.querySelector('a.active')) {
      sub.classList.add('open');
      var arrow = document.getElementById('arrow-' + sub.id);
      if (arrow) arrow.classList.add('open');
    }
  });

  // Mobile sidebar
  var sidebar = document.querySelector('.sidebar');
  var overlay = document.getElementById('sidebar-overlay');
  if (!sidebar || !overlay) return;
  var observer = new MutationObserver(function() {
    overlay.style.display = sidebar.classList.contains('sidebar-open') ? 'block' : 'none';
  });
  observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
  sidebar.querySelectorAll('a').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) sidebar.classList.remove('sidebar-open');
    });
  });
});
</script>
