<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalFlow extends Model
{
    protected $fillable = [
        'name',
        'target_type',
        'conditions',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalFlowStep::class)->orderBy('step_order');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ApprovalRequest::class);
    }

    /**
     * 대상 모델에 맞는 결재라인 자동 선택
     * 1) conditions로 매칭되는 것 우선
     * 2) 없으면 is_default=true인 것
     */
    public static function findForTarget(Model $model): ?self
    {
        $targetType = get_class($model);

        // 활성 결재라인 중 대상 타입이 같은 것
        $flows = static::where('target_type', $targetType)
            ->where('is_active', true)
            ->with('steps')
            ->get();

        if ($flows->isEmpty()) {
            return null;
        }

        // 조건 매칭 (금액 범위 등)
        foreach ($flows as $flow) {
            if ($flow->matchesConditions($model)) {
                return $flow;
            }
        }

        // 조건 매칭 없으면 기본 결재라인
        return $flows->firstWhere('is_default', true);
    }

    /**
     * 조건 매칭 체크
     */
    public function matchesConditions(Model $model): bool
    {
        $conditions = $this->conditions;
        if (empty($conditions)) {
            return false;
        }

        // 금액 조건
        if (isset($conditions['min_amount']) || isset($conditions['max_amount'])) {
            $amount = $model->total_amount ?? 0;

            if (isset($conditions['min_amount']) && $amount < $conditions['min_amount']) {
                return false;
            }
            if (isset($conditions['max_amount']) && $amount > $conditions['max_amount']) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * 대상 타입 한글 라벨
     */
    public function getTargetLabelAttribute(): string
    {
        return match ($this->target_type) {
            'App\\Models\\PurchaseOrder' => '구매주문',
            'App\\Models\\Expense' => '비용',
            'App\\Models\\Leave' => '휴가',
            'App\\Models\\Timesheet' => '근무기록',
            default => class_basename($this->target_type),
        };
    }
}
