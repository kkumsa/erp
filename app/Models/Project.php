<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Enums\TimesheetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'description',
        'customer_id',
        'contract_id',
        'manager_id',
        'start_date',
        'end_date',
        'actual_end_date',
        'budget',
        'actual_cost',
        'status',
        'progress',
        'priority',
        'meta',
        'timesheet_automation_enabled',
        'timesheet_integration_enabled',
    ];

    protected $casts = [
        'timesheet_automation_enabled' => 'boolean',
        'timesheet_integration_enabled' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'meta' => 'array',
        'status' => ProjectStatus::class,
        'priority' => Priority::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'progress', 'manager_id', 'budget'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->code)) {
                $project->code = 'PRJ-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(ProjectIntegration::class);
    }

    public function jiraIntegration(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProjectIntegration::class)->where('provider', 'jira')->where('is_active', true);
    }

    // 진행률 계산
    public function calculateProgress(): int
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', TaskStatus::Completed)->count();
        return (int) round(($completedTasks / $totalTasks) * 100);
    }

    // 실비용 계산
    public function calculateActualCost(): float
    {
        $expenseTotal = $this->expenses()->where('status', ExpenseStatus::Approved)->sum('total_amount');
        $timesheetCost = $this->timesheets()
            ->where('status', TimesheetStatus::Approved)
            ->where('is_billable', true)
            ->sum(\DB::raw('hours * COALESCE(hourly_rate, 0)'));

        return $expenseTotal + $timesheetCost;
    }

    // 예산 초과 여부
    public function getIsOverBudgetAttribute(): bool
    {
        return $this->budget && $this->actual_cost > $this->budget;
    }

    // 일정 지연 여부
    public function getIsDelayedAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast() && $this->status !== ProjectStatus::Completed;
    }
}
