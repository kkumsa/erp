<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadAssignedNotification;

class LeadObserver
{
    public function created(Lead $lead): void
    {
        // 리드 생성 시 담당자에게 알림
        if ($lead->assigned_to) {
            $user = User::find($lead->assigned_to);
            if ($user && $user->wantsNotification('lead_assigned')) {
                $user->notify(new NewLeadAssignedNotification($lead, isNew: true));
            }
        }
    }

    public function updated(Lead $lead): void
    {
        // 담당자 변경 시 새 담당자에게 알림
        if ($lead->isDirty('assigned_to') && $lead->assigned_to) {
            $user = User::find($lead->assigned_to);
            if ($user && $user->wantsNotification('lead_assigned')) {
                $user->notify(new NewLeadAssignedNotification($lead, isNew: false));
            }
        }
    }
}
