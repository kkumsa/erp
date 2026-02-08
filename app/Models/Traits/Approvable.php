<?php

namespace App\Models\Traits;

use App\Enums\ApprovalStatus;
use App\Models\ApprovalFlow;
use App\Models\ApprovalRequest;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Approvable
{
    /**
     * 현재 활성 결재 요청 (가장 최근)
     */
    public function approvalRequest(): MorphOne
    {
        return $this->morphOne(ApprovalRequest::class, 'approvable')->latestOfMany();
    }

    /**
     * 결재 요청 생성
     *
     * @param int|null $userId 신청자 ID (기본: 현재 로그인 사용자)
     * @param int|null $flowId 사용자가 선택한 결재라인 ID (지정하지 않으면 자동 선택)
     */
    public function submitForApproval(?int $userId = null, ?int $flowId = null): ?ApprovalRequest
    {
        $userId = $userId ?? auth()->id();

        // 이미 진행 중인 결재가 있으면 불가
        if ($this->hasPendingApproval()) {
            return null;
        }

        // 결재라인 결정: 사용자 선택 우선, 없으면 자동 선택
        if ($flowId) {
            $flow = ApprovalFlow::with('steps')->find($flowId);
        } else {
            $flow = ApprovalFlow::findForTarget($this);
        }

        if (!$flow || $flow->steps->isEmpty()) {
            return null;
        }

        $request = ApprovalRequest::create([
            'approvable_type' => get_class($this),
            'approvable_id' => $this->id,
            'approval_flow_id' => $flow->id,
            'current_step' => 1,
            'total_steps' => $flow->steps->count(),
            'status' => ApprovalStatus::InProgress,
            'requested_by' => $userId,
            'requested_at' => now(),
        ]);

        // 대상 모델 상태를 '승인요청'으로 변경
        if (method_exists($this, 'onApprovalSubmitted')) {
            $this->onApprovalSubmitted();
        } else {
            $this->update(['status' => 'approval_requested']);
        }

        // 신청자 역할에 따라 불필요한 단계 스킵 후 첫 유효 단계로 이동
        // (예: Manager가 신청 → Manager 단계 스킵 → Admin 단계부터 시작)
        $request->skipToFirstValidStep();

        // 자동 스킵으로 전체 승인 완료된 경우 체크
        $request->refresh();
        if ($request->status === ApprovalStatus::Approved) {
            // 이미 자동 승인됨 → 신청자에게 완료 알림
            $requester = \App\Models\User::find($userId);
            if ($requester) {
                $requester->notify(new \App\Notifications\ApprovalCompletedNotification($request, 'approved'));
            }
        } else {
            // 신청자에게 접수 알림
            $requester = \App\Models\User::find($userId);
            if ($requester) {
                $requester->notify(new \App\Notifications\ApprovalCompletedNotification($request, 'submitted'));
            }
        }

        return $request;
    }

    /**
     * 진행 중인 결재가 있는지
     */
    public function hasPendingApproval(): bool
    {
        return $this->approvalRequest()
            ->where('status', ApprovalStatus::InProgress)
            ->exists();
    }

    /**
     * 최신 결재 요청의 상태
     */
    public function getApprovalStatus(): ?string
    {
        return $this->approvalRequest?->status;
    }
}
