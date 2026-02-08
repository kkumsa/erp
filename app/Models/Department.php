<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'manager_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'manager_id', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * 지정 부서 + 모든 하위 부서 ID를 재귀적으로 반환 (MySQL CTE 사용).
     *
     * @return array<int>
     */
    public static function getDescendantIds(int $departmentId): array
    {
        return DB::select("
            WITH RECURSIVE dept_tree AS (
                SELECT id FROM departments WHERE id = ? AND deleted_at IS NULL
                UNION ALL
                SELECT d.id FROM departments d
                INNER JOIN dept_tree dt ON d.parent_id = dt.id
                WHERE d.deleted_at IS NULL
            )
            SELECT id FROM dept_tree
        ", [$departmentId]);
    }

    /**
     * 지정 부서 + 모든 하위 부서 ID를 flat 배열로 반환.
     *
     * @return array<int>
     */
    public static function getDescendantIdArray(int $departmentId): array
    {
        return collect(static::getDescendantIds($departmentId))
            ->pluck('id')
            ->toArray();
    }
}
