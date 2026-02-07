<?php

namespace App\Observers;

use App\Models\Leave;
use App\Models\User;
use App\Notifications\LeaveRequestedNotification;
use App\Notifications\LeaveStatusChangedNotification;

class LeaveObserver
{
    public function created(Leave $leave): void
    {
        // 휴가 신청 시 HR Manager 역할 사용자들에게 알림
        $hrManagers = User::role('HR Manager')->get();
        foreach ($hrManagers as $manager) {
            if ($manager->wantsNotification('leave_requested')) {
                $manager->notify(new LeaveRequestedNotification($leave));
            }
        }
    }

    public function updated(Leave $leave): void
    {
        // 휴가 상태 변경(승인/반려) 시 신청자에게 알림
        if ($leave->isDirty('status')) {
            $newStatus = $leave->status;
            if (in_array($newStatus, ['승인', '반려'])) {
                $leave->loadMissing('employee.user');
                $user = $leave->employee?->user;
                if ($user && $user->wantsNotification('leave_status_changed')) {
                    $user->notify(new LeaveStatusChangedNotification($leave, $newStatus));
                }
            }
        }
    }
}
