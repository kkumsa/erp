<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Permission\Models\Role;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_flow_id',
        'current_step',
        'total_steps',
        'status',
        'requested_by',
        'requested_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class)->orderBy('step_order')->orderBy('acted_at');
    }

    /**
     * 현재 단계의 FlowStep 정의 가져오기
     */
    public function getCurrentStep(): ?ApprovalFlowStep
    {
        return $this->flow?->steps->firstWhere('step_order', $this->current_step);
    }

    /**
     * 현재 단계 승인자인지 확인
     */
    public function isCurrentApprover(User $user): bool
    {
        if ($this->status !== '진행중') {
            return false;
        }

        $step = $this->getCurrentStep();
        if (!$step) {
            return false;
        }

        $requester = $this->requester;
        return $step->getApprovers($requester)->contains('id', $user->id);
    }

    /**
     * 현재 단계를 신청자가 스킵해야 하는지 판단
     *
     * - 역할 기반 단계: 신청자가 해당 역할을 보유하면 스킵
     * - 사용자 기반 단계: 신청자 본인이면 스킵
     *
     * 예) Manager가 신청 → Manager 단계 스킵 → Admin으로 바로 이동
     *     Admin이 신청 → Manager, Admin 단계 모두 스킵 → 대표로 바로 이동
     */
    public function shouldSkipCurrentStep(): bool
    {
        $step = $this->getCurrentStep();
        if (!$step) {
            return false;
        }

        $requester = $this->requester;
        if (!$requester) {
            return false;
        }

        // 역할 기반 단계: 신청자가 해당 역할(또는 상위 역할)을 보유하면 스킵
        if ($step->approver_type === 'role') {
            $roleName = Role::find($step->approver_id)?->name;
            if ($roleName && $requester->hasRole($roleName)) {
                return true;
            }

            // Super Admin은 모든 역할 단계 스킵
            if ($requester->hasRole('Super Admin')) {
                return true;
            }

            // Admin은 Manager 단계도 스킵
            if ($requester->hasRole('Admin') && $roleName === 'Manager') {
                return true;
            }
        }

        // 사용자 기반 단계: 신청자 본인이면 스킵
        if ($step->approver_type === 'user' && (int) $step->approver_id === $requester->id) {
            return true;
        }

        return false;
    }

    /**
     * 스킵 가능한 단계를 건너뛰고 첫 유효 단계로 이동
     * 모든 단계가 스킵되면 자동 승인 처리
     *
     * @return bool true: 유효 단계 발견 또는 자동 승인, false: 오류
     */
    public function skipToFirstValidStep(): bool
    {
        $maxSteps = $this->total_steps;

        while ($this->current_step <= $maxSteps) {
            if (!$this->shouldSkipCurrentStep()) {
                // 유효한 단계 → 승인자에게 알림
                $this->notifyCurrentStepApprovers();
                return true;
            }

            // 스킵 기록 남기기
            $step = $this->getCurrentStep();
            ApprovalAction::create([
                'approval_request_id' => $this->id,
                'step_order' => $this->current_step,
                'approver_id' => $this->requested_by,
                'action' => '자동스킵',
                'comment' => '신청자가 해당 단계 권한 보유 (자동 스킵)',
                'acted_at' => now(),
            ]);

            // 마지막 단계였으면 자동 승인
            if ($this->current_step >= $maxSteps) {
                $this->update([
                    'status' => '승인',
                    'completed_at' => now(),
                ]);
                $this->updateApprovableOnApprove();
                return true;
            }

            // 다음 단계로
            $this->update(['current_step' => $this->current_step + 1]);
            $this->refresh();
        }

        return false;
    }

    /**
     * 승인 처리
     */
    public function approve(int $userId, ?string $comment = null): bool
    {
        $step = $this->getCurrentStep();
        if (!$step) {
            return false;
        }

        $actionType = $step->action_type === '참조' ? '참조확인' : '승인';

        // 이미 이 단계에서 이 사용자가 액션을 수행했는지 체크
        $existing = $this->actions()
            ->where('step_order', $this->current_step)
            ->where('approver_id', $userId)
            ->first();

        if ($existing) {
            return false;
        }

        // 액션 기록
        ApprovalAction::create([
            'approval_request_id' => $this->id,
            'step_order' => $this->current_step,
            'approver_id' => $userId,
            'action' => $actionType,
            'comment' => $comment,
            'acted_at' => now(),
        ]);

        // 다음 단계로 진행
        return $this->advanceToNextStep();
    }

    /**
     * 반려 처리
     */
    public function reject(int $userId, ?string $comment = null): bool
    {
        $step = $this->getCurrentStep();
        if (!$step) {
            return false;
        }

        // 참조 단계는 반려 불가
        if ($step->action_type === '참조') {
            return false;
        }

        ApprovalAction::create([
            'approval_request_id' => $this->id,
            'step_order' => $this->current_step,
            'approver_id' => $userId,
            'action' => '반려',
            'comment' => $comment,
            'acted_at' => now(),
        ]);

        $this->update([
            'status' => '반려',
            'completed_at' => now(),
        ]);

        // 대상 모델 상태 업데이트
        $this->updateApprovableOnReject();

        return true;
    }

    /**
     * 다음 단계로 진행 (스킵 로직 포함)
     */
    protected function advanceToNextStep(): bool
    {
        if ($this->current_step >= $this->total_steps) {
            // 최종 승인
            $this->update([
                'status' => '승인',
                'completed_at' => now(),
            ]);

            $this->updateApprovableOnApprove();
            return true;
        }

        // 다음 단계로
        $this->update([
            'current_step' => $this->current_step + 1,
        ]);
        $this->refresh();

        // 다음 단계도 스킵해야 하면 계속 진행
        return $this->skipToFirstValidStep();
    }

    /**
     * 최종 승인 시 대상 모델 업데이트
     */
    protected function updateApprovableOnApprove(): void
    {
        $model = $this->approvable;
        if (!$model) {
            return;
        }

        $lastAction = $this->actions()
            ->where('action', '!=', '자동스킵')
            ->latest('acted_at')
            ->first();

        $approverId = $lastAction?->approver_id ?? $this->requested_by;

        if ($model instanceof PurchaseOrder) {
            $model->update([
                'status' => '승인',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
        } elseif ($model instanceof Expense || $model instanceof Leave || $model instanceof Timesheet) {
            $model->update([
                'status' => '승인',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
        }
    }

    /**
     * 반려 시 대상 모델 업데이트
     */
    protected function updateApprovableOnReject(): void
    {
        $model = $this->approvable;
        if (!$model) {
            return;
        }

        $lastAction = $this->actions()->where('action', '반려')->latest('acted_at')->first();
        $rejectionReason = $lastAction?->comment;

        if ($model instanceof PurchaseOrder) {
            $model->update(['status' => '초안']);
        } elseif ($model instanceof Expense || $model instanceof Leave) {
            $updateData = ['status' => '반려'];
            if ($rejectionReason) {
                $updateData['rejection_reason'] = $rejectionReason;
            }
            $model->update($updateData);
        } elseif ($model instanceof Timesheet) {
            $model->update(['status' => '반려']);
        }
    }

    /**
     * 현재 단계 승인자에게 알림 발송
     */
    public function notifyCurrentStepApprovers(): void
    {
        $step = $this->getCurrentStep();
        if (!$step) {
            return;
        }

        $requester = $this->requester;
        $approvers = $step->getApprovers($requester);
        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\ApprovalStepNotification($this, $step));
        }
    }
}
