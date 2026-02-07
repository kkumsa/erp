<?php

namespace App\Observers;

use App\Models\ApprovalFlow;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseSubmittedNotification;
use App\Notifications\ExpenseStatusChangedNotification;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        // 결재라인이 설정되어 있으면 결재라인 시스템으로 대체 (수동 승인요청 대기)
        $flow = ApprovalFlow::findForTarget($expense);
        if ($flow) {
            return;
        }

        // 결재라인이 없는 경우 기존 로직: Manager/Admin에게 단순 알림
        $managers = User::role(['Manager', 'Admin'])->get();
        foreach ($managers as $manager) {
            if ($manager->wantsNotification('expense_submitted')) {
                $manager->notify(new ExpenseSubmittedNotification($expense));
            }
        }
    }

    public function updated(Expense $expense): void
    {
        // 비용 상태 변경(승인/반려) 시 신청자에게 알림
        // 결재라인을 통한 변경은 ApprovalRequest에서 처리하므로 여기서는 수동 변경만
        if ($expense->isDirty('status') && !$expense->hasPendingApproval()) {
            $newStatus = $expense->status;
            if (in_array($newStatus, ['승인', '반려'])) {
                $expense->loadMissing('employee.user');
                $user = $expense->employee?->user;
                if ($user && $user->wantsNotification('expense_status_changed')) {
                    $user->notify(new ExpenseStatusChangedNotification($expense, $newStatus));
                }
            }
        }
    }
}
