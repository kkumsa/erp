<?php

namespace App\Observers;

use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\OpportunityStageChangedNotification;

class OpportunityObserver
{
    public function updated(Opportunity $opportunity): void
    {
        // 영업기회 단계 변경 시 담당자에게 알림
        if ($opportunity->isDirty('stage') && $opportunity->assigned_to) {
            $oldStage = $opportunity->getOriginal('stage');
            $newStage = $opportunity->stage;

            $user = User::find($opportunity->assigned_to);
            if ($user && $user->wantsNotification('opportunity_stage_changed')) {
                $user->notify(new OpportunityStageChangedNotification($opportunity, $oldStage, $newStage));
            }
        }
    }
}
