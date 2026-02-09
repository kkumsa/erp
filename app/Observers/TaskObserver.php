<?php

namespace App\Observers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusChangedNotification;
use App\Services\JiraIntegrationService;
use App\Services\TimesheetAutomationService;

class TaskObserver
{
    public function __construct(
        protected TimesheetAutomationService $timesheetAutomation
    ) {}

    public function created(Task $task): void
    {
        $this->notifyAndCreateTimesheetDrafts($task);
        $this->pushTaskToJiraIfNeeded($task);
    }

    public function updated(Task $task): void
    {
        // 담당자 변경 시 알림 + 타임시트 자동화
        if ($task->isDirty('assigned_to') && $task->assigned_to) {
            $this->notifyAndCreateTimesheetDrafts($task);
        }

        // ERP → JIRA 푸시 (연동 방향이 erp_to_jira 또는 bidirectional일 때)
        $this->pushTaskToJiraIfNeeded($task);

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

    protected function notifyAndCreateTimesheetDrafts(Task $task): void
    {
        if (!$task->assigned_to) {
            return;
        }

        $task->loadMissing('project');
        $project = $task->project;
        $assignee = User::find($task->assigned_to);

        if (!$assignee) {
            return;
        }

        // 타임시트 자동화 사용 시: 기간 내 빈 타임시트 초안 생성
        if ($project && $project->timesheet_automation_enabled) {
            $this->timesheetAutomation->createDraftTimesheetsForTask($task);
        }

        // 태스크 배정 알림
        if ($assignee->wantsNotification('task_assigned')) {
            $assignerName = auth()->user()?->name ?? '시스템';
            $assignee->notify(new TaskAssignedNotification($task, $assignerName));
        }
    }

    protected function pushTaskToJiraIfNeeded(Task $task): void
    {
        $task->loadMissing('project');
        $integration = $task->project?->integrations()->where('provider', 'jira')->where('is_active', true)->first();
        if (!$integration || !$integration->canPushToExternal()) {
            return;
        }
        try {
            app(JiraIntegrationService::class)->pushToJira($task);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
