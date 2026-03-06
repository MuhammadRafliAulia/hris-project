<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Weekly Planner - HRIS</title>
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{
  --primary:#003e6f;--primary-light:#0a5a9e;--primary-dark:#002a4f;
  --accent:#6366f1;--accent-light:#818cf8;
  --bg:#f0f4f8;--card:#fff;--border:#e2e8f0;--border-light:#f1f5f9;
  --text:#0f172a;--text-secondary:#64748b;--text-muted:#94a3b8;
  --success:#10b981;--warning:#f59e0b;--danger:#ef4444;--info:#3b82f6;
  --radius:12px;--shadow:0 4px 24px rgba(0,0,0,.06);
  --transition:all .2s cubic-bezier(.4,0,.2,1);
}

/* ─── Header ─── */
.wp-header{
  background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);
  padding:16px 24px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;
  border-radius:var(--radius) var(--radius) 0 0;color:#fff;position:relative;overflow:hidden;
}
.wp-header::before{content:'';position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.06);}
.wp-header::after{content:'';position:absolute;bottom:-50px;left:30%;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);}
.wp-title{font-size:18px;font-weight:700;color:#fff;letter-spacing:-.3px;display:flex;align-items:center;gap:8px;z-index:1;}

/* View Switcher */
.view-switch{display:flex;gap:2px;background:rgba(255,255,255,.15);border-radius:8px;padding:3px;margin-left:8px;z-index:1;}
.view-switch a{padding:6px 16px;border-radius:6px;font-size:12px;color:rgba(255,255,255,.7);text-decoration:none;font-weight:500;transition:var(--transition);}
.view-switch a:hover{color:#fff;background:rgba(255,255,255,.1);}
.view-switch a.active{background:#fff;color:var(--primary);font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.1);}

.wp-nav{display:flex;align-items:center;gap:4px;margin-left:auto;z-index:1;}
.wp-nav-btn{padding:6px 14px;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.1);border-radius:6px;font-size:11px;cursor:pointer;color:#fff;font-weight:500;font-family:inherit;transition:var(--transition);backdrop-filter:blur(4px);}
.wp-nav-btn:hover{background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.4);}
.wp-nav-btn.today{background:#fff;color:var(--primary);border-color:#fff;font-weight:600;}
.wp-nav-btn.today:hover{background:#f8fafc;}
.wp-week-label{font-size:13px;font-weight:600;color:#fff;min-width:140px;text-align:center;z-index:1;}
.wp-add-btn{padding:7px 16px;background:#fff;color:var(--primary);border:none;border-radius:8px;font-size:12px;cursor:pointer;font-weight:600;font-family:inherit;transition:var(--transition);z-index:1;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.wp-add-btn:hover{background:#f0f4ff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.15);}
.wp-filter{padding:6px 10px;border:1px solid rgba(255,255,255,.25);border-radius:6px;font-size:11px;background:rgba(255,255,255,.1);font-family:inherit;cursor:pointer;color:#fff;z-index:1;backdrop-filter:blur(4px);}
.wp-filter option{color:#0f172a;background:#fff;}

/* ─── Stats Row ─── */
.stats-row{display:flex;gap:10px;padding:14px 20px;background:var(--card);border-bottom:1px solid var(--border);}
.stat-card{flex:1;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);transition:var(--transition);cursor:default;}
.stat-card:hover{transform:translateY(-1px);box-shadow:0 2px 8px rgba(0,0,0,.05);}
.stat-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.stat-icon.total{background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1d4ed8;}
.stat-icon.done{background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;}
.stat-icon.overdue{background:linear-gradient(135deg,#fecaca,#fca5a5);color:#dc2626;}
.stat-icon.progress{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#d97706;}
.stat-icon.today{background:linear-gradient(135deg,#e0e7ff,#c7d2fe);color:#4f46e5;}
.stat-icon.todo{background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#475569;}
.stat-info{min-width:0;}
.stat-label{font-size:10px;color:var(--text-muted);font-weight:500;text-transform:uppercase;letter-spacing:.5px;}
.stat-value{font-size:18px;font-weight:700;color:var(--text);line-height:1.2;}

/* ─── Calendar Grid ─── */
.wp-calendar{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.wp-days-header{display:grid;grid-template-columns:48px repeat(7,1fr);background:var(--card);border-bottom:2px solid var(--border);position:sticky;top:0;z-index:10;}
.wp-day-label{padding:10px 4px;text-align:center;border-left:1px solid var(--border-light);font-size:10px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;transition:var(--transition);}
.wp-day-label .day-num{font-size:18px;font-weight:800;color:var(--text);display:block;line-height:1.2;margin:2px 0;}
.wp-day-label .day-month{font-size:9px;color:var(--text-muted);font-weight:400;}
.wp-day-label.today{background:linear-gradient(180deg,#eff6ff,#fff);}
.wp-day-label.today .day-num{color:#fff;background:var(--primary);width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 2px 8px rgba(0,62,111,.3);}
.wp-corner{padding:8px 4px;background:var(--card);display:flex;align-items:flex-end;justify-content:center;font-size:9px;color:var(--text-muted);text-transform:uppercase;font-weight:600;}

.wp-grid-scroll{flex:1;overflow-y:auto;overflow-x:hidden;position:relative;}
.wp-grid{display:grid;grid-template-columns:48px repeat(7,1fr);position:relative;}
.wp-time-label{padding:0 6px;height:40px;display:flex;align-items:flex-start;justify-content:flex-end;font-size:10px;color:var(--text-muted);font-weight:500;padding-top:2px;border-right:1px solid var(--border);background:var(--card);position:sticky;left:0;z-index:2;}
.wp-cell{height:40px;border-bottom:1px solid var(--border-light);border-left:1px solid var(--border-light);position:relative;cursor:pointer;transition:background .15s;}
.wp-cell:hover{background:#f0f4ff;}
.wp-cell.today{background:rgba(0,62,111,.02);}

/* Current time indicator */
.wp-now-line{position:absolute;left:48px;right:0;height:2px;background:var(--danger);z-index:5;pointer-events:none;box-shadow:0 0 8px rgba(239,68,68,.4);}
.wp-now-line::before{content:'';position:absolute;left:-5px;top:-4px;width:10px;height:10px;border-radius:50%;background:var(--danger);box-shadow:0 0 6px rgba(239,68,68,.5);}

/* ─── Task Block ─── */
.wp-task{position:absolute;left:3px;right:3px;border-radius:6px;padding:4px 8px;font-size:10px;cursor:pointer;z-index:3;overflow:hidden;border-left:3px solid;transition:box-shadow .2s,transform .2s;min-height:20px;backdrop-filter:blur(2px);}
.wp-task:hover{box-shadow:0 4px 12px rgba(0,0,0,.15);transform:scale(1.02);z-index:6;}
.wp-task-title{font-weight:600;color:#fff;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.wp-task-time{font-size:8px;color:rgba(255,255,255,.8);margin-top:1px;font-weight:500;}
.wp-task-assignee{position:absolute;right:3px;top:3px;width:16px;height:16px;border-radius:50%;background:rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:7px;font-weight:700;color:#fff;backdrop-filter:blur(2px);}
.wp-task.done{opacity:.45;filter:grayscale(.3);}
.wp-task.done .wp-task-title{text-decoration:line-through;}
.wp-task.overdue{animation:overdueGlow 2s ease-in-out infinite;}
@keyframes overdueGlow{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.2);}50%{box-shadow:0 0 8px 2px rgba(239,68,68,.35);}}
.wp-task-badge{position:absolute;right:3px;bottom:3px;font-size:7px;padding:1px 5px;border-radius:3px;background:rgba(0,0,0,.2);color:#fff;font-weight:600;text-transform:uppercase;letter-spacing:.3px;}

/* Priority colors */
.wp-task.priority-urgent{background:linear-gradient(135deg,#dc2626,#ef4444);border-left-color:#991b1b;}
.wp-task.priority-high{background:linear-gradient(135deg,#d97706,#f59e0b);border-left-color:#92400e;}
.wp-task.priority-medium{background:linear-gradient(135deg,#2563eb,#3b82f6);border-left-color:#1e40af;}
.wp-task.priority-low{background:linear-gradient(135deg,#475569,#64748b);border-left-color:#1e293b;}

/* ─── Detail Panel ─── */
.detail-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);z-index:1000;display:none;justify-content:flex-end;}
.detail-overlay.open{display:flex;}
.detail-panel{width:540px;max-width:100%;background:#fff;height:100%;overflow-y:auto;box-shadow:-8px 0 30px rgba(0,0,0,.12);display:flex;flex-direction:column;animation:slideIn .25s ease-out;}
@keyframes slideIn{from{transform:translateX(100%);}to{transform:translateX(0);}}
.detail-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px;background:linear-gradient(180deg,#f8fafc,#fff);}
.detail-close{background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:20px;padding:4px;border-radius:6px;transition:var(--transition);}
.detail-close:hover{color:var(--text);background:var(--border-light);}
.detail-body{flex:1;padding:20px 24px;overflow-y:auto;}
.detail-section{margin-bottom:24px;}
.detail-section-title{font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.detail-meta{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;}
.detail-meta-item{background:var(--bg);border-radius:8px;padding:10px 12px;}
.detail-meta-item label{font-size:10px;color:var(--text-muted);display:block;margin-bottom:4px;font-weight:600;text-transform:uppercase;letter-spacing:.3px;}
.detail-meta-item select,.detail-meta-item input{padding:6px 8px;border:1px solid var(--border);border-radius:6px;width:100%;font-family:inherit;font-size:12px;background:#fff;color:var(--text);transition:var(--transition);}
.detail-meta-item select:focus,.detail-meta-item input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,62,111,.1);}
.d-btn{padding:7px 14px;border:none;border-radius:8px;font-size:12px;cursor:pointer;font-weight:600;font-family:inherit;transition:var(--transition);}
.d-btn-primary{background:var(--primary);color:#fff;}
.d-btn-primary:hover{background:var(--primary-dark);transform:translateY(-1px);}
.d-btn-danger{background:var(--danger);color:#fff;}
.d-btn-danger:hover{background:#dc2626;transform:translateY(-1px);}
.d-btn-ghost{background:transparent;color:var(--text-secondary);border:1px solid var(--border);}
.d-btn-ghost:hover{background:var(--border-light);}
.d-btn-sm{padding:5px 10px;font-size:11px;}

/* Checklist */
.ck-item{display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--border-light);transition:var(--transition);}
.ck-item:hover{background:var(--bg);margin:0 -8px;padding:6px 8px;border-radius:6px;}
.ck-item input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
.ck-item span{flex:1;font-size:12px;color:var(--text);}
.ck-item span.done{text-decoration:line-through;color:var(--text-muted);}
.ck-item .del{background:none;border:none;color:var(--border);cursor:pointer;font-size:13px;transition:var(--transition);}.ck-item .del:hover{color:var(--danger);}
.ck-add{display:flex;gap:5px;margin-top:8px;}
.ck-add input{flex:1;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:12px;font-family:inherit;transition:var(--transition);}
.ck-add input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,62,111,.1);}

/* Comments */
.cm-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border-light);}
.cm-avatar{width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0;}
.cm-body{flex:1;min-width:0;}
.cm-name{font-size:11px;font-weight:700;color:var(--text);}
.cm-time{font-size:9px;color:var(--text-muted);margin-left:6px;}
.cm-text{font-size:12px;color:var(--text-secondary);margin-top:3px;line-height:1.5;}
.cm-add{display:flex;gap:6px;margin-top:10px;}
.cm-add textarea{flex:1;padding:8px 10px;border:1px solid var(--border);border-radius:8px;font-size:12px;font-family:inherit;resize:vertical;min-height:38px;transition:var(--transition);}
.cm-add textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,62,111,.1);}

/* Attachments */
.at-item{display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--bg);border:1px solid var(--border-light);border-radius:8px;margin-bottom:6px;font-size:11px;transition:var(--transition);}
.at-item:hover{border-color:var(--border);box-shadow:0 1px 4px rgba(0,0,0,.04);}
.at-name{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text);font-weight:500;}

/* Assignee Chip Selector */
.assignee-chip-container{display:flex;flex-wrap:wrap;gap:6px;max-height:200px;overflow-y:auto;padding:4px 0;}
.assignee-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 12px 5px 5px;border:1.5px solid var(--border);border-radius:999px;cursor:pointer;font-size:12px;font-weight:500;color:var(--text-secondary);background:var(--card);transition:var(--transition);user-select:none;}
.assignee-chip:hover{border-color:#94a3b8;background:#f8fafc;}
.assignee-chip.selected{border-color:var(--primary);background:rgba(0,62,111,.07);color:var(--primary);font-weight:600;}
.assignee-chip .chip-avatar{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;background:linear-gradient(135deg,#94a3b8,#64748b);flex-shrink:0;transition:var(--transition);}
.assignee-chip.selected .chip-avatar{background:linear-gradient(135deg,var(--primary),#3b82f6);}
.assignee-chip .chip-check{display:none;font-size:11px;}
.assignee-chip.selected .chip-check{display:inline;}

/* ─── Modal ─── */
.modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);z-index:1100;display:none;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-box{background:#fff;border-radius:16px;padding:28px;width:480px;max-width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease-out;}
@keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}
.modal-title{font-size:18px;font-weight:700;color:var(--text);margin-bottom:20px;display:flex;align-items:center;gap:8px;}
.fg{margin-bottom:14px;}
.fg label{display:block;font-size:11px;color:var(--text-secondary);margin-bottom:5px;font-weight:600;text-transform:uppercase;letter-spacing:.3px;}
.fg input,.fg select,.fg textarea{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;color:var(--text);background:#fff;transition:var(--transition);}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,62,111,.1);}
.fg textarea{resize:vertical;min-height:70px;}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.fg-row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;}
.form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:20px;}

/* ─── Footer ─── */
.wp-footer{background:var(--card);border-top:1px solid var(--border);padding:10px 20px;display:flex;align-items:center;gap:16px;font-size:12px;color:var(--text-secondary);border-radius:0 0 var(--radius) var(--radius);}
.wp-stat{display:flex;align-items:center;gap:5px;padding:4px 10px;border-radius:6px;background:var(--bg);transition:var(--transition);}
.wp-stat:hover{background:var(--border);}
.wp-stat b{color:var(--text);font-weight:700;}
.wp-stat.done b{color:var(--success);}
.wp-stat.overdue b{color:var(--danger);}
.wp-stat.progress b{color:var(--warning);}

/* Status badge */
.status-dot{width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:4px;}
.status-dot.todo{background:var(--text-muted);}
.status-dot.in_progress{background:var(--warning);}
.status-dot.done{background:var(--success);}

/* Overdue label inside detail */
.overdue-badge{display:inline-block;padding:2px 8px;background:#fef2f2;color:#dc2626;border-radius:4px;font-size:10px;font-weight:600;margin-left:8px;border:1px solid #fecaca;}

@media(max-width:768px){
  .wp-header{flex-direction:column;align-items:stretch;gap:10px;border-radius:0;}
  .wp-nav{margin-left:0;flex-wrap:wrap;}
  .stats-row{flex-wrap:wrap;}
  .stat-card{min-width:calc(50% - 6px);}
  .wp-calendar{overflow-x:auto;}
  .wp-days-header,.wp-grid{grid-template-columns:50px repeat(7,minmax(100px,1fr));}
  .detail-panel{width:100%;}
  .modal-box{width:95%;padding:20px;}
  .view-switch{margin-left:0;}
}
</style>
</head>
<body style="margin:0;">
<div style="display:flex;min-height:100vh;background:var(--bg);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
@include('layouts.sidebar')
<div class="main" style="flex:1;display:flex;flex-direction:column;min-width:0;">
  <div style="display:flex;justify-content:center;align-items:flex-start;padding:24px 20px 0;width:100%;min-height:0;flex:1;">
    <div style="max-width:1200px;width:100%;display:flex;flex-direction:column;height:calc(100vh - 48px);">

      {{-- HEADER --}}
      <div class="wp-header">
        <span class="wp-title">📅 Weekly Planner</span>
        <div class="view-switch">
          <a href="{{ url('tasks') }}" class="active">Weekly</a>
          <a href="{{ url('tasks?view=kanban') }}">Kanban</a>
        </div>
        <select class="wp-filter" id="filterPriority" onchange="applyFilters()">
          <option value="">All Priority</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>
        <select class="wp-filter" id="filterAssignee" onchange="applyFilters()">
          <option value="">All Assignee</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
          @endforeach
        </select>
        <div class="wp-nav">
          <button class="wp-nav-btn" onclick="navWeek(-1)">‹ Prev</button>
          <button class="wp-nav-btn today" onclick="navWeek(0)">Today</button>
          <button class="wp-nav-btn" onclick="navWeek(1)">Next ›</button>
        </div>
        <span class="wp-week-label" id="weekLabel">{{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}</span>
        <button class="wp-add-btn" onclick="openAddModal()">＋ Add Task</button>
      </div>

      {{-- STATS ROW --}}
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon total">📊</div>
          <div class="stat-info"><div class="stat-label">Total</div><div class="stat-value">{{ $totalWeek }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon todo">📝</div>
          <div class="stat-info"><div class="stat-label">To Do</div><div class="stat-value">{{ $totalTodo }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon progress">🔄</div>
          <div class="stat-info"><div class="stat-label">In Progress</div><div class="stat-value">{{ $totalInProgress }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon done">✅</div>
          <div class="stat-info"><div class="stat-label">Done</div><div class="stat-value">{{ $totalDone }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon overdue">⚠️</div>
          <div class="stat-info"><div class="stat-label">Overdue</div><div class="stat-value" style="{{ $totalOverdue > 0 ? 'color:var(--danger)' : '' }}">{{ $totalOverdue }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon today">📌</div>
          <div class="stat-info"><div class="stat-label">Today</div><div class="stat-value">{{ $totalToday }}</div></div>
        </div>
      </div>

      {{-- CALENDAR --}}
      <div class="wp-calendar" style="background:var(--card);border-left:1px solid var(--border);border-right:1px solid var(--border);">
        <div class="wp-days-header">
          <div class="wp-corner">Time</div>
          @php
            $days = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
            $todayStr = now()->format('Y-m-d');
          @endphp
          @for($i = 0; $i < 7; $i++)
            @php $d = $weekStart->copy()->addDays($i); $isToday = $d->format('Y-m-d') === $todayStr; @endphp
            <div class="wp-day-label {{ $isToday ? 'today' : '' }}" data-date="{{ $d->format('Y-m-d') }}">
              {{ $days[$i] }}
              <span class="day-num">{{ $d->format('d') }}</span>
              <span class="day-month">{{ $d->translatedFormat('M') }}</span>
            </div>
          @endfor
        </div>

        <div class="wp-grid-scroll" id="gridScroll">
          <div class="wp-grid" id="calendarGrid">
            @for($h = 6; $h <= 23; $h++)
              <div class="wp-time-label">{{ sprintf('%02d:00', $h) }}</div>
              @for($i = 0; $i < 7; $i++)
                @php $d = $weekStart->copy()->addDays($i); $isToday = $d->format('Y-m-d') === $todayStr; @endphp
                <div class="wp-cell {{ $isToday ? 'today' : '' }}" data-date="{{ $d->format('Y-m-d') }}" data-hour="{{ $h }}" onclick="clickCell('{{ $d->format('Y-m-d') }}', {{ $h }})"></div>
              @endfor
            @endfor
          </div>
          <div class="wp-now-line" id="nowLine" style="display:none;"></div>
          <div id="taskBlocksContainer" style="position:absolute;top:0;left:0;right:0;bottom:0;pointer-events:none;"></div>
        </div>
      </div>

      {{-- FOOTER --}}
      <div class="wp-footer">
        <div class="wp-stat">📊 This Week: <b>{{ $totalWeek }}</b></div>
        <div class="wp-stat done">✅ Done: <b>{{ $totalDone }}</b></div>
        <div class="wp-stat progress">🔄 In Progress: <b>{{ $totalInProgress }}</b></div>
        <div class="wp-stat overdue">⚠️ Overdue: <b>{{ $totalOverdue }}</b></div>
        <div class="wp-stat">📌 Today: <b>{{ $totalToday }}</b></div>
      </div>

    </div>
  </div>
</div>
</div>

{{-- ADD/EDIT MODAL --}}
<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-title">📝 <span id="modalTitle">Add Task</span></div>
    <form id="taskForm" onsubmit="submitTask(event)">
      <input type="hidden" id="editTaskId" value="">
      <div class="fg">
        <label>Task Title *</label>
        <input type="text" id="fTitle" required placeholder="Enter task title...">
      </div>
      <div class="fg-row">
        <div class="fg">
          <label>Date *</label>
          <input type="date" id="fDate" required>
        </div>
        <div class="fg">
          <label>Priority *</label>
          <select id="fPriority" required>
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
      </div>
      <div class="fg-row">
        <div class="fg">
          <label>Start Time *</label>
          <input type="time" id="fStartTime" required value="09:00">
        </div>
        <div class="fg">
          <label>End Time *</label>
          <input type="time" id="fEndTime" required value="10:00">
        </div>
      </div>
      <div class="fg-row">
        <div class="fg">
          <label>Assignees (multiple)</label>
          <div class="assignee-chip-container" id="fAssigneeContainer">
            @foreach($users as $u)
              <span class="assignee-chip" data-value="{{ $u->id }}" onclick="toggleAssigneeChip(this)">
                <span class="chip-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                {{ $u->name }}
                <span class="chip-check">✓</span>
              </span>
            @endforeach
          </div>
        </div>
        <div class="fg">
          <label>Status</label>
          <select id="fStatus">
            <option value="todo">To Do</option>
            <option value="in_progress">In Progress</option>
            <option value="done">Done</option>
          </select>
        </div>
      </div>
      <div class="fg">
        <label>Description</label>
        <textarea id="fDesc" placeholder="Optional description..."></textarea>
      </div>
      <div class="form-actions">
        <button type="button" class="d-btn d-btn-ghost" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="d-btn d-btn-primary" id="modalSubmitBtn">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- DETAIL PANEL --}}
<div class="detail-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetail()">
  <div class="detail-panel">
    <div class="detail-header">
      <div style="flex:1;min-width:0;">
        <h2 style="margin:0;font-size:18px;color:var(--text);font-weight:700;" id="detailTitle"></h2>
        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;" id="detailCreator"></div>
        <span id="detailOverdueBadge" class="overdue-badge" style="display:none;">⏰ OVERDUE</span>
      </div>
      <div style="display:flex;gap:5px;align-items:center;">
        <button class="d-btn d-btn-sm d-btn-ghost" onclick="editFromDetail()" title="Edit">✏️</button>
        <button class="d-btn d-btn-sm d-btn-danger" onclick="deleteFromDetail()" title="Delete">🗑️</button>
        <button class="detail-close" onclick="closeDetail()">✕</button>
      </div>
    </div>
    <div class="detail-body">
      <div class="detail-meta">
        <div class="detail-meta-item">
          <label>Status</label>
          <select id="detailStatus" onchange="updateField('status',this.value)">
            <option value="todo">To Do</option>
            <option value="in_progress">In Progress</option>
            <option value="done">Done</option>
          </select>
        </div>
        <div class="detail-meta-item">
          <label>Priority</label>
          <select id="detailPriority" onchange="updateField('priority',this.value)">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="detail-meta-item">
          <label>Date</label>
          <input type="date" id="detailDate" onchange="updateField('task_date',this.value)">
        </div>
        <div class="detail-meta-item" style="grid-column:1/-1;">
          <label>Assignees</label>
          <div class="assignee-chip-container" id="detailAssigneeContainer">
            @foreach($users as $u)
              <span class="assignee-chip" data-value="{{ $u->id }}" onclick="toggleDetailAssigneeChip(this)">
                <span class="chip-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                {{ $u->name }}
                <span class="chip-check">✓</span>
              </span>
            @endforeach
          </div>
        </div>
        <div class="detail-meta-item">
          <label>Start Time</label>
          <input type="time" id="detailStart" onchange="updateField('start_time',this.value)">
        </div>
        <div class="detail-meta-item">
          <label>End Time</label>
          <input type="time" id="detailEnd" onchange="updateField('end_time',this.value)">
        </div>
      </div>

      <div class="detail-section">
        <div class="detail-section-title">📄 Description</div>
        <div id="detailDesc" style="font-size:13px;color:var(--text-secondary);line-height:1.6;white-space:pre-wrap;padding:10px;background:var(--bg);border-radius:8px;"></div>
      </div>

      <div class="detail-section">
        <div class="detail-section-title">☑️ Subtask <span id="ckProgress" style="font-size:11px;color:var(--text-secondary);font-weight:400;"></span></div>
        <div id="ckContainer"></div>
        <div class="ck-add">
          <input type="text" id="ckInput" placeholder="Add subtask..." onkeydown="if(event.key==='Enter'){event.preventDefault();addChecklist();}">
          <button class="d-btn d-btn-sm d-btn-primary" onclick="addChecklist()">＋</button>
        </div>
      </div>

      <div class="detail-section">
        <div class="detail-section-title">📎 Attachments</div>
        <div id="atContainer"></div>
        <div style="margin-top:8px;">
          <input type="file" id="atInput" style="display:none;" onchange="uploadAttachment()">
          <button class="d-btn d-btn-sm d-btn-ghost" onclick="document.getElementById('atInput').click()">📁 Upload File</button>
        </div>
      </div>

      <div class="detail-section">
        <div class="detail-section-title">💬 Comments</div>
        <div id="cmContainer"></div>
        <div class="cm-add">
          <textarea id="cmInput" placeholder="Write a comment..." rows="2"></textarea>
          <button class="d-btn d-btn-sm d-btn-primary" style="align-self:flex-end;" onclick="addComment()">Send</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const BASE = '{{ url("") }}';
const STORAGE = '{{ asset("storage") }}';
const CSRF = document.querySelector('meta[name=csrf-token]').content;
let currentTaskId = null;

const weekStartStr = '{{ $weekStart->format("Y-m-d") }}';
const weekStart = new Date(weekStartStr + 'T00:00:00');
let tasksData = @json($tasksByDate);

// ============ RENDER TASKS ON CALENDAR ============
function renderTasks() {
  const container = document.getElementById('taskBlocksContainer');
  container.innerHTML = '';
  const grid = document.getElementById('calendarGrid');
  const gridRect = grid.getBoundingClientRect();

  const filterP = document.getElementById('filterPriority').value;
  const filterA = document.getElementById('filterAssignee').value;

  const firstRow = grid.querySelectorAll('.wp-cell');
  const colPositions = [];
  for (let i = 0; i < 7; i++) {
    const cell = firstRow[i];
    if (cell) {
      const r = cell.getBoundingClientRect();
      colPositions.push({ left: r.left - gridRect.left, width: r.width });
    }
  }

  const hourHeight = 40;
  const startHour = 6;

  Object.keys(tasksData).forEach(date => {
    const dayIndex = Math.round((new Date(date + 'T00:00:00') - weekStart) / 86400000);
    if (dayIndex < 0 || dayIndex > 6 || !colPositions[dayIndex]) return;

    tasksData[date].forEach(task => {
      if (filterP && task.priority !== filterP) return;
      if (filterA) {
        const assigneesList = (task.assignees || []).map(a => String(a.id));
        if (String(task.assigned_to) !== filterA && !assigneesList.includes(filterA)) return;
      }
      if (!task.start_time || !task.end_time) return;

      const [sh, sm] = task.start_time.split(':').map(Number);
      const [eh, em] = task.end_time.split(':').map(Number);

      const topOffset = (sh - startHour + sm / 60) * hourHeight;
      const height = Math.max(((eh - sh) + (em - sm) / 60) * hourHeight, 20);

      const col = colPositions[dayIndex];
      const isOverdue = task.is_overdue && task.status !== 'done';

      const el = document.createElement('div');
      el.className = `wp-task priority-${task.priority} ${task.status === 'done' ? 'done' : ''} ${isOverdue ? 'overdue' : ''}`;
      el.style.cssText = `position:absolute;top:${topOffset}px;left:${col.left + 3}px;width:${col.width - 6}px;height:${height}px;pointer-events:auto;`;
      el.onclick = () => openDetail(task.id);

      let inner = `<div class="wp-task-title">${esc(task.title)}</div>`;
      if (height > 35) {
        inner += `<div class="wp-task-time">${task.start_time.substring(0,5)} – ${task.end_time.substring(0,5)}</div>`;
      }
      if (task.assignees && task.assignees.length) {
        if (task.assignees.length === 1) {
          inner += `<div class="wp-task-assignee">${task.assignees[0].initial}</div>`;
        } else {
          const initials = task.assignees.slice(0,2).map(a => a.initial).join('');
          const more = task.assignees.length > 2 ? '+' + (task.assignees.length - 2) : '';
          inner += `<div class="wp-task-assignee">${initials}${more}</div>`;
        }
      } else if (task.assignee_initial) {
        inner += `<div class="wp-task-assignee">${task.assignee_initial}</div>`;
      }
      if (isOverdue) {
        inner += `<div class="wp-task-badge">overdue</div>`;
      } else if (task.status === 'in_progress') {
        inner += `<div class="wp-task-badge">in progress</div>`;
      }
      el.innerHTML = inner;
      container.appendChild(el);
    });
  });
}

// ============ NOW LINE ============
function updateNowLine() {
  const now = new Date();
  const todayStr = now.toISOString().substring(0, 10);
  const dayIndex = Math.round((new Date(todayStr + 'T00:00:00') - weekStart) / 86400000);
  const line = document.getElementById('nowLine');

  if (dayIndex < 0 || dayIndex > 6) { line.style.display = 'none'; return; }
  const hour = now.getHours();
  const min = now.getMinutes();
  if (hour < 6 || hour > 23) { line.style.display = 'none'; return; }

  const cellHeight = 40;
  const offset = (hour - 6 + min / 60) * cellHeight;
  line.style.display = 'block';
  line.style.top = offset + 'px';
}

// ============ NAVIGATION ============
function localDateStr(d) {
  return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function navWeek(dir) {
  const ws = new Date(weekStartStr + 'T00:00:00');
  if (dir === 0) {
    const now = new Date();
    const dow = now.getDay();
    const mon = new Date(now);
    mon.setDate(now.getDate() - (dow === 0 ? 6 : dow - 1));
    location.href = BASE + '/tasks?week=' + localDateStr(mon);
  } else {
    ws.setDate(ws.getDate() + dir * 7);
    location.href = BASE + '/tasks?week=' + localDateStr(ws);
  }
}

function applyFilters() { renderTasks(); }

// ============ CLICK CELL ============
function clickCell(date, hour) {
  document.getElementById('editTaskId').value = '';
  document.getElementById('modalTitle').textContent = 'Add Task';
  document.getElementById('modalSubmitBtn').textContent = 'Save';
  document.getElementById('fTitle').value = '';
  document.getElementById('fDesc').value = '';
  document.getElementById('fPriority').value = 'medium';
  document.getElementById('fDate').value = date;
  document.getElementById('fStartTime').value = String(hour).padStart(2, '0') + ':00';
  document.getElementById('fEndTime').value = String(Math.min(hour + 1, 23)).padStart(2, '0') + ':00';
  document.querySelectorAll('#fAssigneeContainer .assignee-chip').forEach(c => c.classList.remove('selected'));
  document.getElementById('fStatus').value = 'todo';
  document.getElementById('addModal').classList.add('open');
}

// ============ ADD/EDIT MODAL ============
function openAddModal() {
  document.getElementById('editTaskId').value = '';
  document.getElementById('modalTitle').textContent = 'Add Task';
  document.getElementById('modalSubmitBtn').textContent = 'Save';
  document.getElementById('fTitle').value = '';
  document.getElementById('fDesc').value = '';
  document.getElementById('fPriority').value = 'medium';
  document.getElementById('fDate').value = new Date().toISOString().substring(0, 10);
  document.getElementById('fStartTime').value = '09:00';
  document.getElementById('fEndTime').value = '10:00';
  document.querySelectorAll('#fAssigneeContainer .assignee-chip').forEach(c => c.classList.remove('selected'));
  document.getElementById('fStatus').value = 'todo';
  document.getElementById('addModal').classList.add('open');
}

function openEditModal(task) {
  document.getElementById('editTaskId').value = task.id;
  document.getElementById('modalTitle').textContent = 'Edit Task';
  document.getElementById('modalSubmitBtn').textContent = 'Update';
  document.getElementById('fTitle').value = task.title;
  document.getElementById('fDesc').value = task.description || '';
  document.getElementById('fPriority').value = task.priority;
  document.getElementById('fDate').value = task.task_date ? task.task_date.substring(0, 10) : '';
  document.getElementById('fStartTime').value = task.start_time ? task.start_time.substring(0, 5) : '09:00';
  document.getElementById('fEndTime').value = task.end_time ? task.end_time.substring(0, 5) : '10:00';
  document.querySelectorAll('#fAssigneeContainer .assignee-chip').forEach(c => c.classList.remove('selected'));
  if (task.assignees && task.assignees.length) {
    task.assignees.forEach(a => {
      const chip = document.querySelector('#fAssigneeContainer .assignee-chip[data-value="' + a.id + '"]'); if (chip) chip.classList.add('selected');
    });
  } else if (task.assigned_to) {
    const chip = document.querySelector('#fAssigneeContainer .assignee-chip[data-value="' + task.assigned_to + '"]'); if (chip) chip.classList.add('selected');
  }
  document.getElementById('fStatus').value = task.status;
  document.getElementById('addModal').classList.add('open');
}

function closeAddModal() { document.getElementById('addModal').classList.remove('open'); }

function submitTask(e) {
  e.preventDefault();
  const id = document.getElementById('editTaskId').value;
  const data = {
    title: document.getElementById('fTitle').value,
    description: document.getElementById('fDesc').value || null,
    priority: document.getElementById('fPriority').value,
    task_date: document.getElementById('fDate').value,
    start_time: document.getElementById('fStartTime').value,
    end_time: document.getElementById('fEndTime').value,
    assigned_to: (document.querySelector('#fAssigneeContainer .assignee-chip.selected') ? document.querySelector('#fAssigneeContainer .assignee-chip.selected').dataset.value : null),
    assignees: Array.from(document.querySelectorAll('#fAssigneeContainer .assignee-chip.selected')).map(c => c.dataset.value),
    status: document.getElementById('fStatus').value,
  };

  const url = id ? BASE + '/tasks/' + id : BASE + '/tasks';
  const method = id ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) { closeAddModal(); location.reload(); }
    else { alert('Failed to save. Check your input.'); }
  })
  .catch(() => alert('An error occurred.'));
}

// ============ DETAIL ============
function openDetail(taskId) {
  currentTaskId = taskId;
  fetch(BASE + '/tasks/' + taskId)
    .then(r => r.json())
    .then(task => {
      document.getElementById('detailTitle').textContent = task.title;
      document.getElementById('detailCreator').textContent = 'Created by ' + (task.creator ? task.creator.name : '-') + ' • ' + fmtDate(task.created_at);
      document.getElementById('detailStatus').value = task.status;
      document.getElementById('detailPriority').value = task.priority;
      document.querySelectorAll('#detailAssigneeContainer .assignee-chip').forEach(c => c.classList.remove('selected'));
      if (task.assignees && task.assignees.length) {
        task.assignees.forEach(a => { const chip = document.querySelector('#detailAssigneeContainer .assignee-chip[data-value="' + a.id + '"]'); if (chip) chip.classList.add('selected'); });
      } else if (task.assigned_to) {
        const chip = document.querySelector('#detailAssigneeContainer .assignee-chip[data-value="' + task.assigned_to + '"]'); if (chip) chip.classList.add('selected');
      }
      document.getElementById('detailDate').value = task.task_date ? task.task_date.substring(0, 10) : '';
      document.getElementById('detailStart').value = task.start_time ? task.start_time.substring(0, 5) : '';
      document.getElementById('detailEnd').value = task.end_time ? task.end_time.substring(0, 5) : '';
      document.getElementById('detailDesc').textContent = task.description || 'No description.';

      // Show overdue badge - check if task is overdue based on end_time
      const overdueBadge = document.getElementById('detailOverdueBadge');
      const isOverdue = checkOverdue(task);
      overdueBadge.style.display = isOverdue ? 'inline-block' : 'none';

      renderChecklists(task.checklists || []);
      renderComments(task.comments || []);
      renderAttachments(task.attachments || []);

      document.getElementById('detailOverlay').classList.add('open');
    });
}

function checkOverdue(task) {
  if (task.status === 'done') return false;
  if (!task.task_date || !task.end_time) return false;
  const dateStr = task.task_date.substring(0, 10);
  const timeStr = task.end_time.substring(0, 5);
  const endDt = new Date(dateStr + 'T' + timeStr + ':00');
  return endDt < new Date();
}

function closeDetail() { document.getElementById('detailOverlay').classList.remove('open'); currentTaskId = null; }

function updateField(field, value) {
  if (!currentTaskId) return;
  const data = {}; data[field] = value || null;
  fetch(BASE + '/tasks/' + currentTaskId, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify(data)
  }).then(r => r.json()).then(res => { if (res.success) location.reload(); });
}

function editFromDetail() {
  fetch(BASE + '/tasks/' + currentTaskId).then(r => r.json()).then(task => { closeDetail(); openEditModal(task); });
}

function deleteFromDetail() {
  if (!confirm('Are you sure you want to delete this task?')) return;
  fetch(BASE + '/tasks/' + currentTaskId, {
    method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF }
  }).then(r => r.json()).then(res => { if (res.success) { closeDetail(); location.reload(); } });
}

function toggleAssigneeChip(el) { el.classList.toggle('selected'); }
function toggleDetailAssigneeChip(el) { el.classList.toggle('selected'); updateDetailAssignees(); }

function updateDetailAssignees() {
  if (!currentTaskId) return;
  const assignees = Array.from(document.querySelectorAll('#detailAssigneeContainer .assignee-chip.selected')).map(c => c.dataset.value);
  fetch(BASE + '/tasks/' + currentTaskId, {
    method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ assignees })
  }).then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}

// ============ CHECKLISTS ============
function renderChecklists(items) {
  const c = document.getElementById('ckContainer');
  const done = items.filter(i => i.is_completed).length;
  document.getElementById('ckProgress').textContent = items.length ? `(${done}/${items.length})` : '';
  c.innerHTML = items.map(i => `
    <div class="ck-item">
      <input type="checkbox" ${i.is_completed ? 'checked' : ''} onchange="toggleCk(${i.id})">
      <span class="${i.is_completed ? 'done' : ''}">${esc(i.title)}</span>
      <button class="del" onclick="delCk(${i.id})">✕</button>
    </div>
  `).join('');
}
function addChecklist() {
  const inp = document.getElementById('ckInput');
  if (!inp.value.trim()) return;
  fetch(BASE + '/tasks/' + currentTaskId + '/checklists', {
    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ title: inp.value.trim() })
  }).then(r => r.json()).then(res => { if (res.success) { inp.value = ''; openDetail(currentTaskId); } });
}
function toggleCk(id) {
  fetch(BASE + '/checklists/' + id + '/toggle', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}
function delCk(id) {
  fetch(BASE + '/checklists/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}

// ============ COMMENTS ============
function renderComments(items) {
  const c = document.getElementById('cmContainer');
  c.innerHTML = items.map(i => `
    <div class="cm-item">
      <div class="cm-avatar">${i.user ? i.user.name.charAt(0).toUpperCase() : '?'}</div>
      <div class="cm-body">
        <span class="cm-name">${esc(i.user ? i.user.name : 'Unknown')}</span>
        <span class="cm-time">${timeAgo(i.created_at)}</span>
        <div class="cm-text">${esc(i.content)}</div>
      </div>
    </div>
  `).join('') || '<div style="font-size:12px;color:var(--text-muted);padding:8px 0;">No comments yet.</div>';
}
function addComment() {
  const inp = document.getElementById('cmInput');
  if (!inp.value.trim()) return;
  fetch(BASE + '/tasks/' + currentTaskId + '/comments', {
    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ content: inp.value.trim() })
  }).then(r => r.json()).then(res => { if (res.success) { inp.value = ''; openDetail(currentTaskId); } });
}

// ============ ATTACHMENTS ============
function renderAttachments(items) {
  const c = document.getElementById('atContainer');
  c.innerHTML = items.map(a => `
    <div class="at-item">
      <span>${fileIcon(a.file_type)}</span>
      <span class="at-name">${esc(a.file_name)}</span>
      <a href="${STORAGE}/${a.file_path}" target="_blank" class="d-btn d-btn-sm d-btn-ghost">📥</a>
      <button class="d-btn d-btn-sm d-btn-danger" style="padding:2px 6px;" onclick="delAt(${a.id})">✕</button>
    </div>
  `).join('') || '<div style="font-size:12px;color:var(--text-muted);padding:8px 0;">No attachments yet.</div>';
}
function uploadAttachment() {
  const inp = document.getElementById('atInput');
  if (!inp.files.length) return;
  const fd = new FormData(); fd.append('file', inp.files[0]);
  fetch(BASE + '/tasks/' + currentTaskId + '/attachments', {
    method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }, body: fd
  }).then(r => r.json()).then(res => { if (res.success) { inp.value = ''; openDetail(currentTaskId); } });
}
function delAt(id) {
  if (!confirm('Delete this attachment?')) return;
  fetch(BASE + '/attachments/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}

// ============ HELPERS ============
function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function fmtDate(d) { if (!d) return '-'; return new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }); }
function timeAgo(d) {
  if (!d) return '';
  const diff = (Date.now() - new Date(d).getTime()) / 1000;
  if (diff < 60) return 'just now';
  if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
  if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
  return Math.floor(diff / 86400) + 'd ago';
}
function fileIcon(t) {
  if (!t) return '📄';
  if (t.includes('image')) return '🖼️';
  if (t.includes('pdf')) return '📕';
  if (t.includes('word') || t.includes('document')) return '📘';
  if (t.includes('sheet') || t.includes('excel')) return '📗';
  return '📄';
}

// ============ INIT ============
document.addEventListener('DOMContentLoaded', function() {
  renderTasks();
  updateNowLine();
  setInterval(updateNowLine, 10000);

  // Scroll to current hour or 8am
  const scroll = document.getElementById('gridScroll');
  const now = new Date();
  const currentHour = now.getHours();
  const scrollToHour = (currentHour >= 6 && currentHour <= 23) ? Math.max(currentHour - 1, 6) : 8;
  scroll.scrollTop = (scrollToHour - 6) * 40;

  window.addEventListener('resize', () => { renderTasks(); updateNowLine(); });
  scroll.addEventListener('scroll', updateNowLine);
});
</script>
</body>
</html>
