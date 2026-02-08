<?php

namespace App\Scopes;

use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

/**
 * 금액 관련 모델(청구서, 구매주문, 결제, 비용)의 부서 기반 필터 Scope.
 *
 * - Super Admin, Accountant 역할은 전체 데이터에 접근 가능.
 * - 그 외 역할은 자기 부서 + 하위 부서 데이터만 조회 가능.
 *
 * 모델별 필터링 방식 ($financeScopeMode 프로퍼티):
 *   'creator_or_project' → created_by 또는 project.manager 부서 기준 (Invoice, PurchaseOrder)
 *   'creator'            → created_by 부서 기준 (BankDeposit)
 *   'recorder'           → recorded_by 부서 기준 (Payment)
 *   'employee'           → employee 부서 기준 (Expense)
 */
class FinanceDepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user) return;

        // Super Admin, Accountant는 전체 데이터 접근 가능
        if ($user->hasAnyRole(['Super Admin', 'Accountant'])) {
            return;
        }

        // 현재 사용자의 부서 ID를 DB에서 직접 조회 (Global Scope 순환 참조 방지)
        $departmentId = DB::table('employees')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->value('department_id');

        if (!$departmentId) {
            // 부서 정보 없으면 본인이 작성한 것만 볼 수 있도록
            $this->filterOwnRecordsOnly($builder, $model, $user->id);
            return;
        }

        // 내 부서 + 모든 하위 부서 ID
        $departmentIds = Department::getDescendantIdArray($departmentId);

        if (empty($departmentIds)) {
            $departmentIds = [$departmentId];
        }

        // 모델별 필터링 모드 결정
        $mode = $model->financeScopeMode ?? $this->detectMode($model);

        match ($mode) {
            'creator_or_project' => $this->applyCreatorOrProjectFilter($builder, $departmentIds),
            'creator' => $this->applyCreatorFilter($builder, $departmentIds),
            'recorder' => $this->applyRecorderFilter($builder, $departmentIds),
            'employee' => $this->applyEmployeeFilter($builder, $departmentIds),
            default => $builder->whereRaw('1 = 0'),
        };
    }

    /**
     * 작성자 OR 프로젝트 PM 부서 기준 필터 (Invoice, PurchaseOrder)
     */
    protected function applyCreatorOrProjectFilter(Builder $builder, array $departmentIds): void
    {
        $builder->where(function (Builder $query) use ($departmentIds) {
            // 작성자가 내 부서 이하
            $query->whereHas('creator', function (Builder $uq) use ($departmentIds) {
                $uq->whereHas('employee', function (Builder $eq) use ($departmentIds) {
                    $eq->withoutGlobalScopes()
                        ->whereIn('employees.department_id', $departmentIds);
                });
            })
            // OR 프로젝트 PM이 내 부서 이하
            ->orWhereHas('project', function (Builder $pq) use ($departmentIds) {
                $pq->whereHas('manager', function (Builder $uq) use ($departmentIds) {
                    $uq->whereHas('employee', function (Builder $eq) use ($departmentIds) {
                        $eq->withoutGlobalScopes()
                            ->whereIn('employees.department_id', $departmentIds);
                    });
                });
            });
        });
    }

    /**
     * 작성자 부서 기준 필터 (BankDeposit)
     */
    protected function applyCreatorFilter(Builder $builder, array $departmentIds): void
    {
        $builder->whereHas('creator', function (Builder $uq) use ($departmentIds) {
            $uq->whereHas('employee', function (Builder $eq) use ($departmentIds) {
                $eq->withoutGlobalScopes()
                    ->whereIn('employees.department_id', $departmentIds);
            });
        });
    }

    /**
     * 등록자 부서 기준 필터 (Payment)
     */
    protected function applyRecorderFilter(Builder $builder, array $departmentIds): void
    {
        $builder->whereHas('recorder', function (Builder $uq) use ($departmentIds) {
            $uq->whereHas('employee', function (Builder $eq) use ($departmentIds) {
                $eq->withoutGlobalScopes()
                    ->whereIn('employees.department_id', $departmentIds);
            });
        });
    }

    /**
     * 직원 부서 기준 필터 (Expense)
     */
    protected function applyEmployeeFilter(Builder $builder, array $departmentIds): void
    {
        $builder->whereHas('employee', function (Builder $eq) use ($departmentIds) {
            $eq->withoutGlobalScopes()
                ->whereIn('employees.department_id', $departmentIds);
        });
    }

    /**
     * 부서 미배정 사용자: 본인이 만든 데이터만 표시
     */
    protected function filterOwnRecordsOnly(Builder $builder, Model $model, int $userId): void
    {
        $mode = $model->financeScopeMode ?? $this->detectMode($model);

        match ($mode) {
            'creator_or_project' => $builder->where('created_by', $userId),
            'creator' => $builder->where('created_by', $userId),
            'recorder' => $builder->where('recorded_by', $userId),
            'employee' => $builder->whereHas('employee', fn (Builder $q) => $q->withoutGlobalScopes()->where('user_id', $userId)),
            default => $builder->whereRaw('1 = 0'),
        };
    }

    /**
     * 모델의 관계를 기반으로 필터 모드를 자동 감지
     */
    protected function detectMode(Model $model): string
    {
        if (method_exists($model, 'creator') && method_exists($model, 'project')) {
            return 'creator_or_project';
        }

        if (method_exists($model, 'creator') && !method_exists($model, 'project')) {
            return 'creator';
        }

        if (method_exists($model, 'recorder')) {
            return 'recorder';
        }

        if (method_exists($model, 'employee')) {
            return 'employee';
        }

        return 'unknown';
    }
}
