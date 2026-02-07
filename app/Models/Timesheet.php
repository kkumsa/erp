<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Timesheet extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'date',
        'hours',
        'description',
        'is_billable',
        'hourly_rate',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'is_billable' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($timesheet) {
            if ($timesheet->task) {
                $timesheet->task->updateActualHours();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // 금액 계산
    public function getAmountAttribute(): float
    {
        if (!$this->is_billable || !$this->hourly_rate) {
            return 0;
        }
        return $this->hours * $this->hourly_rate;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'hours', 'is_billable', 'approved_by'])
            ->logOldValues()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
