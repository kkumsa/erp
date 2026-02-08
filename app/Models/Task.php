<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'project_id',
        'milestone_id',
        'parent_id',
        'title',
        'description',
        'assigned_to',
        'created_by',
        'status',
        'priority',
        'start_date',
        'due_date',
        'completed_date',
        'estimated_hours',
        'actual_hours',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_date' => 'date',
        'status' => TaskStatus::class,
        'priority' => Priority::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'assigned_to', 'priority'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    // 실제 소요 시간 업데이트
    public function updateActualHours(): void
    {
        $this->actual_hours = $this->timesheets()->sum('hours');
        $this->save();
    }

    // 지연 여부
    public function getIsDelayedAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== TaskStatus::Completed;
    }
}
