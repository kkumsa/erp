<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

/**
 * 부서별 데이터 범위 제한 Global Scope.
 *
 * 일반 직원(Employee)은 자기 부서 데이터만 조회 가능.
 * Manager 이상 역할(Super Admin, Admin, Manager)은 전체 데이터 접근 가능.
 *
 * 모델의 department_id 필드 유무에 따라 두 가지 모드로 동작:
 * 1. 직접 모드: 모델에 department_id가 있는 경우 (예: Employee)
 * 2. 릴레이션 모드: employee 관계를 통해 department_id에 접근 (예: Leave, Attendance, Expense)
 */
class DepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user) return;

        // Manager 이상 역할은 전체 데이터 접근 가능
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'HR Manager'])) {
            return;
        }

        // 현재 사용자의 부서 ID를 DB에서 직접 조회 (Global Scope 순환 참조 방지)
        $departmentId = DB::table('employees')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->value('department_id');

        if (!$departmentId) {
            // 부서 정보 없으면 빈 결과 반환 (보안)
            $builder->whereRaw('1 = 0');
            return;
        }

        // 모델에 department_id 컬럼이 있는지 확인
        if ($this->hasDepartmentIdColumn($model)) {
            // 직접 모드: department_id로 필터
            $builder->where($model->getTable() . '.department_id', $departmentId);
        } elseif (method_exists($model, 'employee')) {
            // 릴레이션 모드: employee 테이블을 통해 필터
            $builder->whereHas('employee', function (Builder $query) use ($departmentId) {
                $query->withoutGlobalScope(self::class)
                    ->where('employees.department_id', $departmentId);
            });
        }
    }

    /**
     * 모델 테이블에 department_id 컬럼이 있는지 확인
     */
    protected function hasDepartmentIdColumn(Model $model): bool
    {
        return in_array('department_id', $model->getFillable());
    }
}
