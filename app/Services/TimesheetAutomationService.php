<?php

namespace App\Services;

use App\Enums\TimesheetStatus;
use App\Models\Task;
use App\Models\Timesheet;
use Carbon\Carbon;

class TimesheetAutomationService
{
    /** 기간 내 최대 생성 일수 (과도한 행 방지) */
    public const MAX_DAYS_TO_CREATE = 60;

    /**
     * 태스크 기간에 맞춰 담당자에게 빈 타임시트(0시간) 초안 생성.
     * 프로젝트의 timesheet_automation_enabled 일 때만 호출.
     */
    public function createDraftTimesheetsForTask(Task $task): int
    {
        if (!$task->assigned_to) {
            return 0;
        }

        $start = $task->start_date ?? $task->due_date ?? now();
        $end = $task->due_date ?? $task->start_date ?? now();

        if ($start instanceof \Carbon\Carbon) {
            $start = Carbon::parse($start);
        } else {
            $start = Carbon::parse($start);
        }
        if ($end instanceof \Carbon\Carbon) {
            $end = Carbon::parse($end);
        } else {
            $end = Carbon::parse($end);
        }

        if ($start->isAfter($end)) {
            [$start, $end] = [$end, $start];
        }

        $days = $start->diffInDays($end) + 1;
        if ($days > self::MAX_DAYS_TO_CREATE) {
            $days = self::MAX_DAYS_TO_CREATE;
            $end = $start->copy()->addDays($days - 1);
        }

        $created = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            $exists = Timesheet::where('user_id', $task->assigned_to)
                ->where('project_id', $task->project_id)
                ->where('task_id', $task->id)
                ->whereDate('date', $current)
                ->exists();

            if (!$exists) {
                Timesheet::create([
                    'user_id' => $task->assigned_to,
                    'project_id' => $task->project_id,
                    'task_id' => $task->id,
                    'date' => $current->toDateString(),
                    'hours' => 0,
                    'description' => "자동 생성 (태스크: {$task->title})",
                    'is_billable' => true,
                    'hourly_rate' => null,
                    'status' => TimesheetStatus::Pending,
                ]);
                $created++;
            }
            $current->addDay();
        }

        return $created;
    }
}
