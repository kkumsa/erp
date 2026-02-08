<?php

namespace App\Observers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusChangedNotification;

class TaskObserver
{
    public function created(Task $task): void
    {
        // 태스크 배정 알림
        if ($task->assigned_to) {
            $assignee = User::find($task->assigned_to);
            if ($assignee && $assignee->wantsNotification('task_assigned')) {
                $creatorName = auth()->user()?->name ?? '시스템';
                $assignee->notify(new TaskAssignedNotification($task, $creatorName));
            }
        }
    }

    public function updated(Task $task): void
    {
        // 담당자 변경 시 알림
        if ($task->isDirty('assigned_to') && $task->assigned_to) {
            $assignee = User::find($task->assigned_to);
            if ($assignee && $assignee->wantsNotification('task_assigned')) {
                $changerName = auth()->user()?->name ?? '시스템';
                $assignee->notify(new TaskAssignedNotification($task, $changerName));
            }
        }

        // 상태가 '완료'로 변경 시 프로젝트 매니저에게 알림
        if ($task->isDirty('status')) {
            $oldStatus = $task->getOriginal('status');
            $newStatus = $task->status;

            if ($newStatus === TaskStatus::Completed) {
                $task->loadMissing('project');
                $manager = $task->project?->manager;
                if ($manager && $manager->wantsNotification('task_status_changed')) {
                    $manager->notify(new TaskStatusChangedNotification($task, $oldStatus, $newStatus));
                }
            }
        }
    }
}
