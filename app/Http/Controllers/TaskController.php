<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Weekly planner view (default) OR Kanban view via ?view=kanban
     */
    public function index(Request $request)
    {
        $viewType = $request->get('view', 'weekly');
        // Only internal_hr and recruitmentteam can be assigned
        $users = User::whereIn('role', ['internal_hr', 'recruitmentteam'])->orderBy('name')->get();

        if ($viewType === 'kanban') {
            return $this->kanbanView($request, $users);
        }

        return $this->weeklyView($request, $users);
    }

    private function weeklyView(Request $request, $users)
    {
        // Determine the week
        $weekStart = $request->filled('week')
            ? \Carbon\Carbon::parse($request->week)->startOfWeek(\Carbon\Carbon::MONDAY)
            : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $query = Task::with(['assignee', 'assignees', 'creator', 'checklists', 'comments', 'attachments'])
            ->whereBetween('task_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);

        // Non-admin users only see their own assigned tasks
        $authRole = auth()->user()->role;
        if (!in_array($authRole, ['superadmin', 'top_level_management'])) {
            $query->where(function($q){
                $q->where('assigned_to', auth()->id())
                  ->orWhereHas('assignees', function($q2){ $q2->where('id', auth()->id()); });
            });
        }

        $tasks = $query->orderBy('start_time')->get();

        // Stats (time-aware overdue)
        $totalWeek = $tasks->count();
        $totalDone = $tasks->where('status', 'done')->count();
        $totalOverdue = $tasks->filter(fn($t) => $t->isOverdue())->count();
        $totalInProgress = $tasks->where('status', 'in_progress')->count();
        $totalToday = $tasks->where('task_date', today()->toDateString())->count();
        $totalTodo = $tasks->where('status', 'todo')->count();

        // Group tasks by date for JS
        $tasksByDate = [];
        foreach ($tasks as $task) {
            $date = $task->task_date->format('Y-m-d');
            $tasksByDate[$date][] = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'computed_status' => $task->computed_status,
                'priority' => $task->priority,
                'task_date' => $date,
                'start_time' => $task->start_time,
                'end_time' => $task->end_time,
                'assigned_to' => $task->assigned_to,
                'assignee_name' => $task->assignee ? $task->assignee->name : null,
                'assignee_initial' => $task->assignee ? strtoupper(substr($task->assignee->name, 0, 1)) : null,
                'assignees' => $task->assignees->map(function($u){ return ['id'=>$u->id,'name'=>$u->name,'initial'=>strtoupper(substr($u->name,0,1))]; })->toArray(),
                'created_by' => $task->created_by,
                'creator_name' => $task->creator ? $task->creator->name : null,
                'checklists_count' => $task->checklists->count(),
                'checklists_done' => $task->checklists->where('is_completed', true)->count(),
                'comments_count' => $task->comments->count(),
                'attachments_count' => $task->attachments->count(),
                'is_overdue' => $task->isOverdue(),
            ];
        }

        return view('tasks.weekly', compact(
            'weekStart', 'weekEnd', 'users', 'tasksByDate',
            'totalWeek', 'totalDone', 'totalOverdue', 'totalInProgress', 'totalToday', 'totalTodo'
        ));
    }

    private function kanbanView(Request $request, $users)
    {
        $query = Task::with(['assignee', 'assignees', 'creator', 'checklists', 'comments', 'attachments']);

        // Non-admin users only see their own assigned tasks
        $authRole = auth()->user()->role;
        if (!in_array($authRole, ['superadmin', 'top_level_management'])) {
            $query->where(function($q){
                $q->where('assigned_to', auth()->id())
                  ->orWhereHas('assignees', function($q2){ $q2->where('id', auth()->id()); });
            });
        }

        $tasks = $query->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->get();

        $todo = $tasks->where('status', 'todo');
        $inProgress = $tasks->where('status', 'in_progress');
        $done = $tasks->where('status', 'done');

        $totalActive = $todo->count() + $inProgress->count();
        $totalDone = $done->count();
        $totalOverdue = $tasks->filter(fn($t) => $t->isOverdue())->count();

        return view('tasks.index', compact('tasks', 'users', 'todo', 'inProgress', 'done', 'totalActive', 'totalDone', 'totalOverdue'));
    }

    /**
     * Store new task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'task_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'assigned_to' => 'nullable|exists:users,id',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'status' => 'nullable|in:todo,in_progress,done',
        ]);

        // Ensure assigned user(s) have allowed role
        if (!empty($validated['assigned_to'])) {
            $assignee = User::find($validated['assigned_to']);
            if (!$assignee || !in_array($assignee->role, ['internal_hr', 'recruitmentteam'])) {
                return response()->json(['success' => false, 'message' => 'Invalid assignee role.'], 422);
            }
        }
        if (!empty($validated['assignees'])) {
            foreach ($validated['assignees'] as $aid) {
                $u = User::find($aid);
                if (!$u || !in_array($u->role, ['internal_hr', 'recruitmentteam'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid assignee role for user id ' . $aid], 422);
                }
            }
            // set legacy assigned_to to first assignee for compatibility
            if (empty($validated['assigned_to'])) {
                $validated['assigned_to'] = $validated['assignees'][0] ?? null;
            }
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'todo';
        $validated['deadline'] = $validated['task_date'];

        $task = Task::create($validated);
        ActivityLog::log('create', 'task', 'Membuat task: ' . $task->title);

        // Sync assignees pivot if provided
        if (!empty($validated['assignees'])) {
            $task->assignees()->sync($validated['assignees']);
        } elseif (!empty($validated['assigned_to'])) {
            $task->assignees()->sync([$validated['assigned_to']]);
        }

        $task->load(['assignee', 'assignees', 'creator', 'checklists', 'comments', 'attachments']);
        foreach ($task->attachments as $att) {
            $att->url = Storage::url($att->file_path);
        }

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Get task detail
     */
    public function show(Task $task)
    {
        $task->load(['assignee', 'assignees', 'creator', 'checklists', 'comments.user', 'attachments.user']);
        foreach ($task->attachments as $att) {
            $att->url = Storage::url($att->file_path);
        }

        $data = $task->toArray();
        $data['is_overdue'] = $task->isOverdue();
        $data['computed_status'] = $task->computed_status;

        return response()->json($data);
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'task_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'assigned_to' => 'nullable|exists:users,id',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'status' => 'sometimes|in:todo,in_progress,done',
        ]);

        // Ensure assigned user(s) have allowed role
        if (!empty($validated['assigned_to'])) {
            $assignee = User::find($validated['assigned_to']);
            if (!$assignee || !in_array($assignee->role, ['internal_hr', 'recruitmentteam'])) {
                return response()->json(['success' => false, 'message' => 'Invalid assignee role.'], 422);
            }
        }
        if (!empty($validated['assignees'])) {
            foreach ($validated['assignees'] as $aid) {
                $u = User::find($aid);
                if (!$u || !in_array($u->role, ['internal_hr', 'recruitmentteam'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid assignee role for user id ' . $aid], 422);
                }
            }
            if (empty($validated['assigned_to'])) {
                $validated['assigned_to'] = $validated['assignees'][0] ?? null;
            }
        }

        if (isset($validated['task_date'])) {
            $validated['deadline'] = $validated['task_date'];
        }

        $task->update($validated);
        ActivityLog::log('update', 'task', 'Mengupdate task: ' . $task->title);
        // Sync pivot assignees
        if (array_key_exists('assignees', $validated)) {
            $task->assignees()->sync($validated['assignees'] ?? []);
        } elseif (!empty($validated['assigned_to'])) {
            $task->assignees()->sync([$validated['assigned_to']]);
        }

        $task->load(['assignee', 'assignees', 'creator', 'checklists', 'comments', 'attachments']);
        foreach ($task->attachments as $att) {
            $att->url = Storage::url($att->file_path);
        }

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {
        // Delete attachments from storage
        foreach ($task->attachments as $att) {
            Storage::disk('public')->delete($att->file_path);
        }

        ActivityLog::log('delete', 'task', 'Menghapus task: ' . $task->title);
        $task->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update task status (drag-drop)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
            'position' => 'required|integer|min:0',
        ]);

        $task->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Reorder tasks within a column
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.status' => 'required|in:todo,in_progress,done',
            'tasks.*.position' => 'required|integer|min:0',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::where('id', $taskData['id'])->update([
                'status' => $taskData['status'],
                'position' => $taskData['position'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ---- Checklists ----

    public function addChecklist(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $checklist = $task->checklists()->create($validated);

        return response()->json(['success' => true, 'checklist' => $checklist]);
    }

    public function toggleChecklist(TaskChecklist $checklist)
    {
        $checklist->update(['is_completed' => !$checklist->is_completed]);

        return response()->json(['success' => true, 'checklist' => $checklist]);
    }

    public function deleteChecklist(TaskChecklist $checklist)
    {
        $checklist->delete();
        return response()->json(['success' => true]);
    }

    // ---- Comments ----

    public function addComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        $comment->load('user');

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deleteComment(TaskComment $comment)
    {
        $comment->delete();
        return response()->json(['success' => true]);
    }

    // ---- Attachments ----

    public function addAttachment(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('tasks/attachments', 'public');

        $attachment = $task->attachments()->create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
        ]);

        $attachment->load('user');
        $attachment->url = Storage::url($attachment->file_path);

        return response()->json(['success' => true, 'attachment' => $attachment]);
    }

    public function deleteAttachment(TaskAttachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return response()->json(['success' => true]);
    }
}
