<?php

namespace App\Observers;

use App\Enums\MilestoneStatus;
use App\Models\Milestone;
use App\Notifications\ProjectMilestoneCompletedNotification;

class MilestoneObserver
{
    public function updated(Milestone $milestone): void
    {
        // 마일스톤이 '완료'로 변경 시 프로젝트 매니저에게 알림
        if ($milestone->isDirty('status') && $milestone->status === MilestoneStatus::Completed) {
            $milestone->loadMissing('project');
            $manager = $milestone->project?->manager;
            if ($manager && $manager->wantsNotification('milestone_completed')) {
                $manager->notify(new ProjectMilestoneCompletedNotification($milestone));
            }
        }
    }
}
