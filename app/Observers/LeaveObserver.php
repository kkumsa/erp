<?php

namespace App\Observers;

use App\Enums\LeaveStatus;
use App\Models\ApprovalFlow;
use App\Models\Leave;
use App\Models\User;
use App\Notifications\LeaveRequestedNotification;
use App\Notifications\LeaveStatusChangedNotification;

class LeaveObserver
{
    public function created(Leave $leave): void
    {
        // 결재라인이 설정되어 있으면 결재라인 시스템으로 대체 (수동 승인요청 대기)
        $flow = ApprovalFlow::findForTarget($leave);
        if ($flow) {
            return;
        }

        // 결재라인이 없는 경우 기존 로직: HR Manager에게 알림
        $hrManagers = User::role('HR Manager')->get();
        foreach ($hrManagers as $manager) {
            if ($manager->wantsNotification('leave_requested')) {
                $manager->notify(new LeaveRequestedNotification($leave));
            }
        }
    }

    public function updated(Leave $leave): void
    {
        // 상태 변경 시 신청자에게 알림 (결재라인 통한 변경은 ApprovalRequest에서 처리)
        if ($leave->isDirty('status') && !$leave->hasPendingApproval()) {
            $newStatus = $leave->status;
            if (in_array($newStatus, [LeaveStatus::Approved, LeaveStatus::Rejected])) {
                $leave->loadMissing('employee.user');
                $user = $leave->employee?->user;
                if ($user && $user->wantsNotification('leave_status_changed')) {
                    $user->notify(new LeaveStatusChangedNotification($leave, $newStatus));
                }
            }
        }
    }
}
