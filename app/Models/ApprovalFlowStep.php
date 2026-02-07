<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class ApprovalFlowStep extends Model
{
    protected $fillable = [
        'approval_flow_id',
        'step_order',
        'approver_type',
        'approver_id',
        'action_type',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    /**
     * 실제 승인자 User(s) 반환
     *
     * @param User|null $requester 신청자 (역할 기반일 때 부서 기준 필터링에 사용)
     */
    public function getApprovers(?User $requester = null): Collection
    {
        return match ($this->approver_type) {
            'user' => collect([User::find($this->approver_id)])->filter(),
            'role' => $this->getApproversByRole($requester),
            default => collect(),
        };
    }

    /**
     * 역할 기반 승인자 조회
     * 1) 신청자와 같은 부서에서 해당 역할을 가진 사용자
     * 2) 없으면 상위 부서로 에스컬레이션 (최대 5단계)
     * 3) 상위 부서에도 없으면 전체 시스템에서 해당 역할자 반환 (fallback)
     */
    protected function getApproversByRole(?User $requester): Collection
    {
        $roleName = Role::find($this->approver_id)?->name;
        if (!$roleName) {
            return collect();
        }

        // 신청자 정보가 없으면 전체 시스템에서 조회 (fallback)
        if (!$requester) {
            return User::role($roleName)->get();
        }

        // 신청자의 부서 확인
        $department = $requester->employee?->department;
        if (!$department) {
            // 부서가 없는 사용자 → 전체 시스템에서 조회
            return User::role($roleName)->get();
        }

        // 같은 부서 → 상위 부서 순으로 탐색 (최대 5단계)
        $currentDept = $department;
        $maxDepth = 5;

        for ($i = 0; $i < $maxDepth; $i++) {
            $approvers = $this->findRoleUsersInDepartment($roleName, $currentDept, $requester);

            if ($approvers->isNotEmpty()) {
                return $approvers;
            }

            // 상위 부서로 이동
            $parentDept = $currentDept->parent;
            if (!$parentDept) {
                break;
            }
            $currentDept = $parentDept;
        }

        // 어떤 부서에서도 못 찾으면 전체 시스템에서 조회 (fallback)
        return User::role($roleName)
            ->where('id', '!=', $requester->id)
            ->get();
    }

    /**
     * 특정 부서에서 해당 역할을 가진 사용자 조회
     * 신청자 본인은 제외
     */
    protected function findRoleUsersInDepartment(string $roleName, Department $department, User $requester): Collection
    {
        // 해당 부서 소속 직원의 user_id 목록
        $userIds = Employee::withoutGlobalScopes()
            ->where('department_id', $department->id)
            ->pluck('user_id');

        if ($userIds->isEmpty()) {
            return collect();
        }

        return User::role($roleName)
            ->whereIn('id', $userIds)
            ->where('id', '!=', $requester->id) // 신청자 본인 제외
            ->get();
    }

    /**
     * 승인자 라벨 (표시용)
     */
    public function getApproverLabelAttribute(): string
    {
        return match ($this->approver_type) {
            'user' => User::find($this->approver_id)?->name ?? '(삭제된 사용자)',
            'role' => Role::find($this->approver_id)?->name ?? '(삭제된 역할)',
            default => '-',
        };
    }
}
