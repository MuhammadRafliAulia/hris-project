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
     * Weekly planner view
     */
    public function index(Request $request)
    {
        // Determine the week
        $weekStart = $request->filled('week')
            ? \Carbon\Carbon::parse($request->week)->startOfWeek(\Carbon\Carbon::MONDAY)
            : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $tasks = Task::with(['assignee', 'creator', 'checklists', 'comments', 'attachments'])
            ->whereBetween('task_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('start_time')
            ->get();

        $users = User::whereIn('role', ['internal_hr', 'recruitmentteam'])->orderBy('name')->get();

        // Stats
        $totalWeek = $tasks->count();
        $totalDone = $tasks->where('status', 'done')->count();
        $totalOverdue = $tasks->filter(function ($t) { return $t->isOverdue(); })->count();
        $totalToday = $tasks->where('task_date', today()->toDateString())->count();

        // Group tasks by date for JS
        $tasksByDate = [];
        foreach ($tasks as $task) {
            $date = $task->task_date->format('Y-m-d');
            $tasksByDate[$date][] = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'task_date' => $date,
                'start_time' => $task->start_time,
                'end_time' => $task->end_time,
                'assigned_to' => $task->assigned_to,
                'assignee_name' => $task->assignee ? $task->assignee->name : null,
                'assignee_initial' => $task->assignee ? strtoupper(substr($task->assignee->name, 0, 1)) : null,
                'created_by' => $task->created_by,
                'creator_name' => $task->creator ? $task->creator->name : null,
                'checklists_count' => $task->checklists->count(),
                'checklists_done' => $task->checklists->where('is_completed', true)->count(),
                'comments_count' => $task->comments->count(),
                'attachments_count' => $task->attachments->count(),
            ];
        }

        return view('tasks.weekly', compact(
            'weekStart', 'weekEnd', 'users', 'tasksByDate',
            'totalWeek', 'totalDone', 'totalOverdue', 'totalToday'
        ));
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
            'status' => 'nullable|in:todo,in_progress,done',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'todo';
        $validated['deadline'] = $validated['task_date'];

        $task = Task::create($validated);
        ActivityLog::log('create', 'task', 'Membuat task: ' . $task->title);

        return response()->json(['success' => true, 'task' => $task->load(['assignee', 'creator', 'checklists', 'comments', 'attachments'])]);
    }

    /**
     * Get task detail
     */
    public function show(Task $task)
    {
        $task->load(['assignee', 'creator', 'checklists', 'comments.user', 'attachments.user']);
        return response()->json($task);
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
            'status' => 'sometimes|in:todo,in_progress,done',
        ]);

        if (isset($validated['task_date'])) {
            $validated['deadline'] = $validated['task_date'];
        }

        $task->update($validated);
        ActivityLog::log('update', 'task', 'Mengupdate task: ' . $task->title);

        return response()->json(['success' => true, 'task' => $task->load(['assignee', 'creator', 'checklists', 'comments', 'attachments'])]);
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

        return response()->json(['success' => true, 'attachment' => $attachment]);
    }

    public function deleteAttachment(TaskAttachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return response()->json(['success' => true]);
    }
}
