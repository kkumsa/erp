<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseSubmittedNotification;
use App\Notifications\ExpenseStatusChangedNotification;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        // 비용 청구 시 Manager/Admin 역할에게 알림
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
        if ($expense->isDirty('status')) {
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
