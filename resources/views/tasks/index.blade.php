<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Task Management - HRIS</title>
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
<style>
*{box-sizing:border-box;}
:root{
  --primary:#003e6f;--primary-light:#0a5a9e;--primary-dark:#002a4f;
  --bg:#f0f4f8;--card:#fff;--border:#e2e8f0;--border-light:#f1f5f9;
  --text:#0f172a;--text-secondary:#64748b;--text-muted:#94a3b8;
  --success:#10b981;--warning:#f59e0b;--danger:#ef4444;
  --radius:12px;--transition:all .2s cubic-bezier(.4,0,.2,1);
}
.tm-header{
  background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);
  padding:16px 24px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;
  color:#fff;position:relative;overflow:hidden;
}
.tm-header::before{content:'';position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.06);}
.tm-header::after{content:'';position:absolute;bottom:-50px;left:30%;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);}
.tm-search{padding:8px 12px;border:1px solid rgba(255,255,255,.25);border-radius:8px;font-size:13px;width:220px;font-family:inherit;background:rgba(255,255,255,.1);color:#fff;backdrop-filter:blur(4px);z-index:1;}
.tm-search::placeholder{color:rgba(255,255,255,.6);}
.tm-filter{padding:8px 10px;border:1px solid rgba(255,255,255,.25);border-radius:8px;font-size:13px;background:rgba(255,255,255,.1);font-family:inherit;cursor:pointer;color:#fff;z-index:1;backdrop-filter:blur(4px);}
.tm-filter option{color:#0f172a;background:#fff;}
.tm-btn{padding:8px 16px;border:none;border-radius:8px;font-size:13px;cursor:pointer;font-weight:600;font-family:inherit;transition:var(--transition);}
.tm-btn-primary{background:#fff;color:var(--primary);box-shadow:0 2px 8px rgba(0,0,0,.1);z-index:1;}
.tm-btn-primary:hover{background:#f0f4ff;transform:translateY(-1px);}
.tm-btn-sm{padding:5px 10px;font-size:12px;}
.tm-btn-danger{background:var(--danger);color:#fff;}
.tm-btn-danger:hover{background:#dc2626;}
.tm-btn-ghost{background:transparent;color:var(--text-secondary);border:1px solid var(--border);}
.tm-btn-ghost:hover{background:var(--border-light);}

/* View Switcher */
.view-switch{display:flex;gap:2px;background:rgba(255,255,255,.15);border-radius:8px;padding:3px;z-index:1;}
.view-switch a{padding:6px 16px;border-radius:6px;font-size:12px;color:rgba(255,255,255,.7);text-decoration:none;font-weight:500;transition:var(--transition);}
.view-switch a:hover{color:#fff;background:rgba(255,255,255,.1);}
.view-switch a.active{background:#fff;color:var(--primary);font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.1);}
.view-switcher{display:flex;gap:2px;background:rgba(255,255,255,.12);border-radius:8px;padding:3px;z-index:1;}
.view-btn{padding:6px 14px;border:none;background:transparent;border-radius:6px;font-size:12px;cursor:pointer;color:rgba(255,255,255,.7);font-weight:500;font-family:inherit;transition:var(--transition);}
.view-btn.active{background:rgba(255,255,255,.2);color:#fff;font-weight:600;}

/* Board */
.tm-body{flex:1;padding:20px;overflow:auto;display:flex;gap:16px;}
.tm-body.list-view{flex-direction:column;gap:8px;}
.tm-body.list-view .kanban-col{flex:none;width:100%;}
.tm-body.list-view .kanban-col-body{display:flex;flex-direction:row;flex-wrap:wrap;gap:8px;max-height:none;}
.kanban-col{flex:1;min-width:280px;max-width:400px;display:flex;flex-direction:column;background:var(--bg);border-radius:12px;border:1px solid var(--border);}
.kanban-col-header{padding:14px 16px;font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);}
.kanban-col-header .count{background:var(--border);color:var(--text-secondary);font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;margin-left:8px;}
.kanban-col-body{padding:10px;flex:1;overflow-y:auto;max-height:calc(100vh - 280px);min-height:80px;}
.kanban-col-body.drag-over{background:#dbeafe;border-radius:0 0 12px 12px;}

/* Task Card */
.task-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:8px;cursor:grab;transition:var(--transition);position:relative;}
.task-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.08);border-color:#cbd5e1;transform:translateY(-1px);}
.task-card.dragging{opacity:.5;transform:rotate(2deg);}
.task-card.overdue{border-left:3px solid var(--danger);}
.task-card-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:8px;line-height:1.4;}
.task-card-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:11px;}
.task-badge{padding:2px 8px;border-radius:999px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.3px;}
.priority-low{background:#d1fae5;color:#065f46;}
.priority-medium{background:#fef3c7;color:#92400e;}
.priority-high{background:#fed7aa;color:#9a3412;}
.priority-urgent{background:#fecaca;color:#991b1b;}
.task-assignee{display:flex;align-items:center;gap:4px;color:var(--text-secondary);}
.task-assignee-avatar{width:20px;height:20px;border-radius:6px;background:linear-gradient(135deg,#003e6f,#6366f1);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;}
.task-deadline{color:var(--text-secondary);display:flex;align-items:center;gap:3px;}
.task-deadline.overdue{color:var(--danger);font-weight:600;}
.task-checklist-progress{display:flex;align-items:center;gap:4px;color:var(--text-secondary);}
.task-checklist-bar{width:40px;height:4px;background:var(--border);border-radius:999px;overflow:hidden;}
.task-checklist-bar-fill{height:100%;background:var(--success);border-radius:999px;transition:width .3s;}
.overdue-tag{font-size:9px;padding:1px 6px;background:#fef2f2;color:#dc2626;border-radius:4px;font-weight:600;border:1px solid #fecaca;}

/* Detail Panel */
.detail-overlay{position:fixed;inset:0;background:rgba(15,23,42,.4);z-index:1000;display:none;justify-content:flex-end;}
.detail-overlay.open{display:flex;}
.detail-panel{width:560px;max-width:100%;background:#fff;height:100%;overflow-y:auto;box-shadow:-4px 0 20px rgba(0,0,0,.1);display:flex;flex-direction:column;}
.detail-header{padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
.detail-close{background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;padding:4px;}
.detail-close:hover{color:#475569;}
.detail-body{flex:1;padding:20px 24px;overflow-y:auto;}
.detail-section{margin-bottom:24px;}
.detail-section-title{font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;display:flex;align-items:center;gap:6px;}
.detail-meta{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;}
.detail-meta-item label{font-size:11px;color:#94a3b8;display:block;margin-bottom:4px;}
.detail-meta-item span,.detail-meta-item select,.detail-meta-item input{font-size:13px;color:#0f172a;}
.detail-meta-item select,.detail-meta-item input{padding:6px 8px;border:1px solid #e2e8f0;border-radius:4px;width:100%;font-family:inherit;background:#fff;}

/* Checklist */
.checklist-item{display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f1f5f9;}
.checklist-item input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:#003e6f;}
.checklist-item span{flex:1;font-size:13px;color:#334155;}
.checklist-item span.completed{text-decoration:line-through;color:#94a3b8;}
.checklist-item .del-btn{background:none;border:none;color:#cbd5e1;cursor:pointer;font-size:14px;padding:2px;}
.checklist-item .del-btn:hover{color:#dc2626;}
.checklist-add{display:flex;gap:6px;margin-top:8px;}
.checklist-add input{flex:1;padding:6px 8px;border:1px solid #e2e8f0;border-radius:4px;font-size:13px;font-family:inherit;}

/* Comments */
.comment-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9;}
.comment-avatar{width:28px;height:28px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#475569;flex-shrink:0;}
.comment-body{flex:1;min-width:0;}
.comment-name{font-size:12px;font-weight:600;color:#0f172a;}
.comment-time{font-size:10px;color:#94a3b8;margin-left:6px;}
.comment-text{font-size:13px;color:#334155;margin-top:2px;line-height:1.5;}
.comment-add{display:flex;gap:6px;margin-top:10px;}
.comment-add textarea{flex:1;padding:8px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;font-family:inherit;resize:vertical;min-height:38px;}

/* Attachments */
.att-item{display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;margin-bottom:6px;}
.att-icon{font-size:18px;}
.att-info{flex:1;min-width:0;}
.att-name{font-size:12px;font-weight:500;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.att-uploader{font-size:10px;color:#94a3b8;}

/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:1100;display:none;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-box{background:#fff;border-radius:10px;padding:28px;width:480px;max-width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 10px 40px rgba(0,0,0,.15);}
.modal-title{font-size:18px;font-weight:700;color:#0f172a;margin-bottom:20px;}
.form-group{margin-bottom:14px;}
.form-group label{display:block;font-size:12px;color:#475569;margin-bottom:5px;font-weight:500;}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-family:inherit;color:#0f172a;background:#fff;}
.form-group textarea{resize:vertical;min-height:70px;}
.form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:20px;}

/* Footer Stats */
.tm-footer{background:var(--card);border-top:1px solid var(--border);padding:12px 24px;display:flex;align-items:center;gap:16px;font-size:12px;color:var(--text-secondary);}
.tm-footer .stat{display:flex;align-items:center;gap:5px;padding:4px 12px;border-radius:6px;background:var(--bg);transition:var(--transition);}
.tm-footer .stat:hover{background:var(--border);}
.tm-footer .stat-val{font-weight:700;color:var(--text);}
.stat-done .stat-val{color:var(--success);}
.stat-overdue .stat-val{color:var(--danger);}
.stat-active .stat-val{color:#2563eb;}

/* Timeline view */
.timeline-view{flex-direction:column;gap:0;}
.timeline-group{margin-bottom:20px;}
.timeline-date{font-size:13px;font-weight:700;color:#334155;padding:8px 0;border-bottom:2px solid #e2e8f0;margin-bottom:8px;}
.timeline-task{display:flex;align-items:center;gap:12px;padding:10px 14px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:6px;cursor:pointer;}
.timeline-task:hover{border-color:#cbd5e1;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.timeline-status{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
.status-todo{background:#94a3b8;}
.status-in_progress{background:#f59e0b;}
.status-done{background:#22c55e;}

@media(max-width:768px){
  .tm-header{flex-direction:column;align-items:stretch;}
  .tm-search{width:100%;}
  .view-switcher{margin-left:0;align-self:flex-start;}
  .tm-body{flex-direction:column;}
  .kanban-col{max-width:100%;}
  .detail-panel{width:100%;}
  .detail-meta{grid-template-columns:1fr;}
  .modal-box{width:95%;padding:20px;}
}
</style>
</head>
<body style="margin:0;">
<div style="display:flex;min-height:100vh;background:var(--bg);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
@include('layouts.sidebar')
<div class="main" style="flex:1;display:flex;flex-direction:column;min-width:0;">

  {{-- HEADER --}}
  <div class="tm-header">
    <h1 style="margin:0;font-size:18px;color:#fff;font-weight:700;z-index:1;">📋 Kanban Board</h1>
    <div class="view-switch">
      <a href="{{ url('hrissdi/tasks') }}">Weekly</a>
      <a href="{{ url('hrissdi/tasks?view=kanban') }}" class="active">Kanban</a>
    </div>
    <input type="text" class="tm-search" id="searchInput" placeholder="Search tasks..." oninput="filterTasks()">
    <select class="tm-filter" id="filterPriority" onchange="filterTasks()">
      <option value="">All Priority</option>
      <option value="low">Low</option>
      <option value="medium">Medium</option>
      <option value="high">High</option>
      <option value="urgent">Urgent</option>
    </select>
    <select class="tm-filter" id="filterAssignee" onchange="filterTasks()">
      <option value="">All Assignee</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }}</option>
      @endforeach
    </select>
    <button class="tm-btn tm-btn-primary" onclick="openAddModal()">＋ Add Task</button>
    <div class="view-switcher">
      <button class="view-btn active" data-view="board" onclick="switchView('board',this)">Board</button>
      <button class="view-btn" data-view="list" onclick="switchView('list',this)">List</button>
      <button class="view-btn" data-view="timeline" onclick="switchView('timeline',this)">Timeline</button>
    </div>
  </div>

  {{-- BOARD VIEW --}}
  <div class="tm-body" id="boardView">
    {{-- TO DO --}}
    <div class="kanban-col" data-status="todo">
      <div class="kanban-col-header">📝 To Do <span class="count" id="count-todo">{{ $todo->count() }}</span></div>
      <div class="kanban-col-body" data-status="todo" ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event,'todo')">
        @foreach($todo as $task)
        @include('tasks._card', ['task' => $task])
        @endforeach
      </div>
    </div>
    {{-- IN PROGRESS --}}
    <div class="kanban-col" data-status="in_progress">
      <div class="kanban-col-header">🔄 In Progress <span class="count" id="count-in_progress">{{ $inProgress->count() }}</span></div>
      <div class="kanban-col-body" data-status="in_progress" ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event,'in_progress')">
        @foreach($inProgress as $task)
        @include('tasks._card', ['task' => $task])
        @endforeach
      </div>
    </div>
    {{-- DONE --}}
    <div class="kanban-col" data-status="done">
      <div class="kanban-col-header">✅ Done <span class="count" id="count-done">{{ $done->count() }}</span></div>
      <div class="kanban-col-body" data-status="done" ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event,'done')">
        @foreach($done as $task)
        @include('tasks._card', ['task' => $task])
        @endforeach
      </div>
    </div>
  </div>

  {{-- TIMELINE VIEW (hidden by default) --}}
  <div class="tm-body timeline-view" id="timelineView" style="display:none;"></div>

  {{-- FOOTER --}}
  <div class="tm-footer">
    <div class="stat stat-active">📊 Active: <span class="stat-val" id="stat-active">{{ $totalActive }}</span></div>
    <div class="stat stat-done">✅ Done: <span class="stat-val" id="stat-done">{{ $totalDone }}</span></div>
    <div class="stat stat-overdue">⚠️ Overdue: <span class="stat-val" id="stat-overdue">{{ $totalOverdue }}</span></div>
    <div class="stat">📈 Total: <span class="stat-val" id="stat-total">{{ $totalActive + $totalDone }}</span></div>
  </div>
</div>
</div>

{{-- ADD/EDIT MODAL --}}
<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-title" id="modalTitle">Add New Task</div>
    <form id="taskForm" onsubmit="submitTask(event)">
      <input type="hidden" id="editTaskId" value="">
      <div class="form-group">
        <label>Task Title *</label>
        <input type="text" id="fTitle" required placeholder="Enter task title">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="fDesc" placeholder="Task description (optional)"></textarea>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label>Priority *</label>
          <select id="fPriority" required>
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="form-group">
          <label>Deadline</label>
          <input type="date" id="fDeadline">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label>Assignee</label>
          <select id="fAssignee">
            <option value="">-- Not assigned --</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select id="fStatus">
            <option value="todo">To Do</option>
            <option value="in_progress">In Progress</option>
            <option value="done">Done</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="tm-btn tm-btn-ghost" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="tm-btn tm-btn-primary" id="modalSubmitBtn">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- DETAIL PANEL --}}
<div class="detail-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetail()">
  <div class="detail-panel">
    <div class="detail-header">
      <div style="flex:1;min-width:0;">
        <h2 style="margin:0;font-size:18px;color:#0f172a;" id="detailTitle"></h2>
        <div style="font-size:11px;color:#94a3b8;margin-top:4px;" id="detailCreator"></div>
      </div>
      <div style="display:flex;gap:6px;align-items:center;">
        <button class="tm-btn tm-btn-sm tm-btn-ghost" onclick="editFromDetail()">✏️ Edit</button>
        <button class="tm-btn tm-btn-sm tm-btn-danger" onclick="deleteFromDetail()">🗑️</button>
        <button class="detail-close" onclick="closeDetail()">✕</button>
      </div>
    </div>
    <div class="detail-body">
      {{-- Meta --}}
      <div class="detail-meta">
        <div class="detail-meta-item">
          <label>Status</label>
          <select id="detailStatus" onchange="updateDetailField('status',this.value)">
            <option value="todo">To Do</option>
            <option value="in_progress">In Progress</option>
            <option value="done">Done</option>
          </select>
        </div>
        <div class="detail-meta-item">
          <label>Priority</label>
          <select id="detailPriority" onchange="updateDetailField('priority',this.value)">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="detail-meta-item">
          <label>Assignee</label>
          <select id="detailAssignee" onchange="updateDetailField('assigned_to',this.value)">
            <option value="">Not assigned</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="detail-meta-item">
          <label>Deadline</label>
          <input type="date" id="detailDeadline" onchange="updateDetailField('deadline',this.value)">
        </div>
      </div>

      {{-- Description --}}
      <div class="detail-section">
        <div class="detail-section-title">📄 Description</div>
        <div id="detailDesc" style="font-size:13px;color:#475569;line-height:1.6;white-space:pre-wrap;"></div>
      </div>

      {{-- Checklist --}}
      <div class="detail-section">
        <div class="detail-section-title">☑️ Subtask / Checklist <span id="checklistProgress" style="font-size:11px;color:#64748b;font-weight:400;"></span></div>
        <div id="checklistContainer"></div>
        <div class="checklist-add">
          <input type="text" id="newChecklistInput" placeholder="Add subtask..." onkeydown="if(event.key==='Enter'){event.preventDefault();addChecklist();}">
          <button class="tm-btn tm-btn-sm tm-btn-primary" onclick="addChecklist()">＋</button>
        </div>
      </div>

      {{-- Attachments --}}
      <div class="detail-section">
        <div class="detail-section-title">📎 Attachments</div>
        <div id="attachmentContainer"></div>
        <div style="margin-top:8px;">
          <input type="file" id="attachmentInput" style="display:none;" onchange="uploadAttachment()">
          <button class="tm-btn tm-btn-sm tm-btn-ghost" onclick="document.getElementById('attachmentInput').click()">📁 Upload File</button>
        </div>
      </div>

      {{-- Comments --}}
      <div class="detail-section">
        <div class="detail-section-title">💬 Comments</div>
        <div id="commentContainer"></div>
        <div class="comment-add">
          <textarea id="newCommentInput" placeholder="Write a comment..." rows="2"></textarea>
          <button class="tm-btn tm-btn-sm tm-btn-primary" style="align-self:flex-end;" onclick="addComment()">Send</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const BASE = '{{ url("hrissdi") }}';
const STORAGE = '{{ asset("storage") }}';
const CSRF = document.querySelector('meta[name=csrf-token]').content;
let currentTaskId = null;
let allCards = [];

// ============ DRAG & DROP ============
function onDragStart(e, id) {
  e.dataTransfer.setData('taskId', id);
  e.target.classList.add('dragging');
}
function onDragEnd(e) { e.target.classList.remove('dragging'); }
function onDragOver(e) { e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function onDragLeave(e) { e.currentTarget.classList.remove('drag-over'); }

function onDrop(e, newStatus) {
  e.preventDefault();
  e.currentTarget.classList.remove('drag-over');
  const taskId = e.dataTransfer.getData('taskId');
  const card = document.querySelector(`.task-card[data-id="${taskId}"]`);
  if (!card) return;

  const oldStatus = card.dataset.status;
  card.dataset.status = newStatus;
  e.currentTarget.appendChild(card);

  // Collect new positions
  const tasks = [];
  e.currentTarget.querySelectorAll('.task-card').forEach((c, i) => {
    tasks.push({ id: parseInt(c.dataset.id), status: newStatus, position: i });
  });

  fetch(BASE + '/tasks/reorder', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ tasks })
  }).then(() => updateCounts());
}

// ============ VIEW SWITCHING ============
function switchView(view, btn) {
  document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const board = document.getElementById('boardView');
  const timeline = document.getElementById('timelineView');

  if (view === 'board') {
    board.style.display = 'flex';
    board.classList.remove('list-view');
    timeline.style.display = 'none';
  } else if (view === 'list') {
    board.style.display = 'flex';
    board.classList.add('list-view');
    timeline.style.display = 'none';
  } else if (view === 'timeline') {
    board.style.display = 'none';
    timeline.style.display = 'flex';
    buildTimeline();
  }
}

function buildTimeline() {
  const cards = document.querySelectorAll('#boardView .task-card');
  const byDate = {};
  cards.forEach(c => {
    if (c.style.display === 'none') return;
    const dl = c.dataset.deadline || 'No Deadline';
    if (!byDate[dl]) byDate[dl] = [];
    byDate[dl].push(c);
  });

  const sorted = Object.keys(byDate).sort((a, b) => {
    if (a === 'No Deadline') return 1;
    if (b === 'No Deadline') return -1;
    return a.localeCompare(b);
  });

  let html = '';
  sorted.forEach(date => {
    html += `<div class="timeline-group"><div class="timeline-date">📅 ${date}</div>`;
    byDate[date].forEach(c => {
      const status = c.dataset.status;
      const title = c.querySelector('.task-card-title').textContent;
      const priority = c.dataset.priority;
      html += `<div class="timeline-task" onclick="openDetail(${c.dataset.id})">
        <div class="timeline-status status-${status}"></div>
        <div style="flex:1;font-size:13px;font-weight:500;color:#0f172a;">${title}</div>
        <span class="task-badge priority-${priority}">${priority}</span>
      </div>`;
    });
    html += '</div>';
  });

  document.getElementById('timelineView').innerHTML = html || '<div style="padding:40px;text-align:center;color:#94a3b8;">No tasks found.</div>';
}

// ============ FILTER ============
function filterTasks() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const priority = document.getElementById('filterPriority').value;
  const assignee = document.getElementById('filterAssignee').value;

  document.querySelectorAll('.task-card').forEach(card => {
    const title = card.querySelector('.task-card-title').textContent.toLowerCase();
    const matchSearch = !search || title.includes(search);
    const matchPriority = !priority || card.dataset.priority === priority;
    const matchAssignee = !assignee || card.dataset.assignee === assignee;
    card.style.display = (matchSearch && matchPriority && matchAssignee) ? '' : 'none';
  });

  updateCounts();
  if (document.getElementById('timelineView').style.display !== 'none') buildTimeline();
}

function updateCounts() {
  ['todo', 'in_progress', 'done'].forEach(status => {
    const col = document.querySelector(`.kanban-col-body[data-status="${status}"]`);
    const count = col.querySelectorAll('.task-card:not([style*="display: none"])').length;
    document.getElementById('count-' + status).textContent = count;
  });
}

// ============ ADD/EDIT MODAL ============
function openAddModal() {
  document.getElementById('editTaskId').value = '';
  document.getElementById('modalTitle').textContent = 'Add New Task';
  document.getElementById('modalSubmitBtn').textContent = 'Save';
  document.getElementById('fTitle').value = '';
  document.getElementById('fDesc').value = '';
  document.getElementById('fPriority').value = 'medium';
  document.getElementById('fDeadline').value = '';
  document.getElementById('fAssignee').value = '';
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
  document.getElementById('fDeadline').value = task.deadline ? task.deadline.substring(0, 10) : '';
  document.getElementById('fAssignee').value = task.assigned_to || '';
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
    deadline: document.getElementById('fDeadline').value || null,
    assigned_to: document.getElementById('fAssignee').value || null,
    status: document.getElementById('fStatus').value,
  };

  const url = id ? BASE + '/tasks/' + id : BASE + '/tasks';
  const method = id ? 'PUT' : 'POST';

  fetch(url, {
    method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) { closeAddModal(); location.reload(); }
  });
}

// ============ DETAIL PANEL ============
function openDetail(taskId) {
  currentTaskId = taskId;
  fetch(BASE + '/tasks/' + taskId)
    .then(r => r.json())
    .then(task => {
      document.getElementById('detailTitle').textContent = task.title;
      document.getElementById('detailCreator').textContent = 'Created by ' + (task.creator ? task.creator.name : '-') + ' • ' + formatDate(task.created_at);
      document.getElementById('detailStatus').value = task.status;
      document.getElementById('detailPriority').value = task.priority;
      document.getElementById('detailAssignee').value = task.assigned_to || '';
      document.getElementById('detailDeadline').value = task.deadline ? task.deadline.substring(0, 10) : '';
      document.getElementById('detailDesc').textContent = task.description || 'No description.';

      renderChecklists(task.checklists || []);
      renderComments(task.comments || []);
      renderAttachments(task.attachments || []);

      document.getElementById('detailOverlay').classList.add('open');
    });
}

function closeDetail() {
  document.getElementById('detailOverlay').classList.remove('open');
  currentTaskId = null;
}

function updateDetailField(field, value) {
  if (!currentTaskId) return;
  const data = {};
  data[field] = value || null;
  fetch(BASE + '/tasks/' + currentTaskId, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify(data)
  }).then(r => r.json()).then(res => {
    if (res.success) {
      // Update card in board
      const card = document.querySelector(`.task-card[data-id="${currentTaskId}"]`);
      if (card && field === 'status') {
        card.dataset.status = value;
        const col = document.querySelector(`.kanban-col-body[data-status="${value}"]`);
        if (col) col.appendChild(card);
        updateCounts();
      }
      if (card && field === 'priority') {
        card.dataset.priority = value;
        const badge = card.querySelector('.task-badge');
        if (badge) { badge.className = 'task-badge priority-' + value; badge.textContent = value; }
      }
    }
  });
}

function editFromDetail() {
  fetch(BASE + '/tasks/' + currentTaskId)
    .then(r => r.json())
    .then(task => { closeDetail(); openEditModal(task); });
}

function deleteFromDetail() {
  if (!confirm('Are you sure you want to delete this task?')) return;
  fetch(BASE + '/tasks/' + currentTaskId, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': CSRF }
  }).then(r => r.json()).then(res => {
    if (res.success) { closeDetail(); location.reload(); }
  });
}

// ============ CHECKLISTS ============
function renderChecklists(items) {
  const container = document.getElementById('checklistContainer');
  const done = items.filter(i => i.is_completed).length;
  document.getElementById('checklistProgress').textContent = items.length ? `(${done}/${items.length})` : '';

  container.innerHTML = items.map(item => `
    <div class="checklist-item" data-id="${item.id}">
      <input type="checkbox" ${item.is_completed ? 'checked' : ''} onchange="toggleChecklist(${item.id})">
      <span class="${item.is_completed ? 'completed' : ''}">${escHtml(item.title)}</span>
      <button class="del-btn" onclick="deleteChecklist(${item.id})">✕</button>
    </div>
  `).join('');
}

function addChecklist() {
  const input = document.getElementById('newChecklistInput');
  if (!input.value.trim()) return;
  fetch(BASE + '/tasks/' + currentTaskId + '/checklists', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ title: input.value.trim() })
  }).then(r => r.json()).then(res => {
    if (res.success) { input.value = ''; openDetail(currentTaskId); }
  });
}

function toggleChecklist(id) {
  fetch(BASE + '/checklists/' + id + '/toggle', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF }
  }).then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}

function deleteChecklist(id) {
  fetch(BASE + '/checklists/' + id, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': CSRF }
  }).then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}

// ============ COMMENTS ============
function renderComments(items) {
  const container = document.getElementById('commentContainer');
  container.innerHTML = items.map(c => `
    <div class="comment-item">
      <div class="comment-avatar">${c.user ? c.user.name.charAt(0).toUpperCase() : '?'}</div>
      <div class="comment-body">
        <span class="comment-name">${escHtml(c.user ? c.user.name : 'Unknown')}</span>
        <span class="comment-time">${timeAgo(c.created_at)}</span>
        <div class="comment-text">${escHtml(c.content)}</div>
      </div>
    </div>
  `).join('') || '<div style="font-size:12px;color:#94a3b8;">No comments yet.</div>';
}

function addComment() {
  const input = document.getElementById('newCommentInput');
  if (!input.value.trim()) return;
  fetch(BASE + '/tasks/' + currentTaskId + '/comments', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ content: input.value.trim() })
  }).then(r => r.json()).then(res => {
    if (res.success) { input.value = ''; openDetail(currentTaskId); }
  });
}

// ============ ATTACHMENTS ============
function renderAttachments(items) {
  const container = document.getElementById('attachmentContainer');
  container.innerHTML = items.map(a => `
    <div class="att-item">
      <div class="att-icon">${getFileIcon(a.file_type)}</div>
      <div class="att-info">
        <div class="att-name">${escHtml(a.file_name)}</div>
        <div class="att-uploader">by ${a.user ? a.user.name : '-'} • ${timeAgo(a.created_at)}</div>
      </div>
      <a href="${STORAGE}/${a.file_path}" target="_blank" class="tm-btn tm-btn-sm tm-btn-ghost" style="font-size:11px;">📥</a>
      <button class="tm-btn tm-btn-sm tm-btn-danger" style="font-size:11px;padding:3px 6px;" onclick="deleteAttachment(${a.id})">✕</button>
    </div>
  `).join('') || '<div style="font-size:12px;color:#94a3b8;">No attachments yet.</div>';
}

function uploadAttachment() {
  const input = document.getElementById('attachmentInput');
  if (!input.files.length) return;
  const formData = new FormData();
  formData.append('file', input.files[0]);
  fetch(BASE + '/tasks/' + currentTaskId + '/attachments', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF },
    body: formData
  }).then(r => r.json()).then(res => {
    if (res.success) { input.value = ''; openDetail(currentTaskId); }
  });
}

function deleteAttachment(id) {
  if (!confirm('Delete this attachment?')) return;
  fetch(BASE + '/attachments/' + id, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': CSRF }
  }).then(r => r.json()).then(res => { if (res.success) openDetail(currentTaskId); });
}

// ============ HELPERS ============
function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function formatDate(d) { if (!d) return '-'; return new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }); }
function timeAgo(d) {
  if (!d) return '';
  const diff = (Date.now() - new Date(d).getTime()) / 1000;
  if (diff < 60) return 'just now';
  if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
  if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
  return Math.floor(diff / 86400) + 'd ago';
}
function getFileIcon(type) {
  if (!type) return '📄';
  if (type.includes('image')) return '🖼️';
  if (type.includes('pdf')) return '📕';
  if (type.includes('word') || type.includes('document')) return '📘';
  if (type.includes('sheet') || type.includes('excel')) return '📗';
  return '📄';
}
</script>
</body>
</html>
