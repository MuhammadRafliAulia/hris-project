<div class="task-card {{ $task->isOverdue() ? 'overdue' : '' }}" data-id="{{ $task->id }}" data-status="{{ $task->status }}" data-priority="{{ $task->priority }}" data-assignee="{{ $task->assigned_to }}" data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}" draggable="true" ondragstart="onDragStart(event,{{ $task->id }})" ondragend="onDragEnd(event)" onclick="openDetail({{ $task->id }})">
  <div class="task-card-title">{{ $task->title }}</div>
  <div class="task-card-meta">
    <span class="task-badge priority-{{ $task->priority }}">{{ $task->priority }}</span>
    @if($task->isOverdue())
    <span class="overdue-tag">⏰ Overdue</span>
    @endif
    @if($task->assignee)
    <span class="task-assignee">
      <span class="task-assignee-avatar">{{ strtoupper(substr($task->assignee->name, 0, 1)) }}</span>
      {{ $task->assignee->name }}
    </span>
    @endif
    @if($task->task_date)
    <span class="task-deadline {{ $task->isOverdue() ? 'overdue' : '' }}">
      📅 {{ $task->task_date->format('d M') }}
      @if($task->end_time) · {{ \Illuminate\Support\Str::substr($task->end_time, 0, 5) }} @endif
    </span>
    @elseif($task->deadline)
    <span class="task-deadline {{ $task->isOverdue() ? 'overdue' : '' }}">
      📅 {{ $task->deadline->format('d M') }}
    </span>
    @endif
    @if($task->checklists->count() > 0)
    <span class="task-checklist-progress">
      ☑️ {{ $task->checklists->where('is_completed', true)->count() }}/{{ $task->checklists->count() }}
      <span class="task-checklist-bar">
        <span class="task-checklist-bar-fill" style="width:{{ $task->checklists->count() > 0 ? round($task->checklists->where('is_completed', true)->count() / $task->checklists->count() * 100) : 0 }}%"></span>
      </span>
    </span>
    @endif
    @if($task->comments->count() > 0)
    <span style="color:var(--text-muted, #94a3b8);">💬 {{ $task->comments->count() }}</span>
    @endif
    @if($task->attachments->count() > 0)
    <span style="color:var(--text-muted, #94a3b8);">📎 {{ $task->attachments->count() }}</span>
    @endif
  </div>
</div>
