<?php

namespace App\Models;

use App\Scopes\DepartmentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected static function booted(): void
    {
        static::addGlobalScope(new DepartmentScope);
    }

    protected $fillable = [
        'user_id',
        'department_id',
        'employee_code',
        'position',
        'job_title',
        'hire_date',
        'birth_date',
        'phone',
        'emergency_contact',
        'address',
        'employment_type',
        'status',
        'resignation_date',
        'base_salary',
        'annual_leave_days',
        'meta',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'resignation_date' => 'date',
        'base_salary' => 'decimal:2',
        'meta' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['department_id', 'position', 'job_title', 'status', 'employment_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // 남은 연차 계산
    public function getRemainingLeaveDaysAttribute(): float
    {
        $usedDays = $this->leaves()
            ->where('status', '승인')
            ->whereYear('start_date', now()->year)
            ->sum('days');

        return $this->annual_leave_days - $usedDays;
    }

    // 이름 접근자 (User의 이름 반환)
    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }
}
