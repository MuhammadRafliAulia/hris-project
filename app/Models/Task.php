<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'status', 'priority',
        'deadline', 'task_date', 'start_time', 'end_time',
        'assigned_to', 'created_by', 'position',
    ];

    protected $casts = [
        'deadline' => 'date',
        'task_date' => 'date',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Many assignees support (multiple users can be assigned to a task)
     */
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function isOverdue()
    {
        if ($this->status === 'done') return false;
        if (!$this->task_date) return false;

        // If end_time is set, compare precisely against task_date + end_time
        if ($this->end_time) {
            try {
                $endDatetime = $this->task_date->copy()->setTimeFromTimeString($this->end_time);
                return $endDatetime->isPast();
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Fallback: date-only check
        return $this->task_date->endOfDay()->isPast();
    }

    /**
     * Computed status label considering overdue
     */
    public function getComputedStatusAttribute()
    {
        if ($this->status === 'done') return 'done';
        if ($this->isOverdue()) return 'overdue';
        return $this->status;
    }
}
