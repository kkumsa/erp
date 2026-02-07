<?php

namespace App\Notifications;

use App\Filament\Resources\LeaveResource;
use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LeaveRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Leave $leave,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $employeeName = $this->leave->employee?->user?->name ?? '직원';
        $leaveType = $this->leave->leaveType?->name ?? '휴가';
        $period = $this->leave->start_date?->format('m/d') . ' ~ ' . $this->leave->end_date?->format('m/d');

        return FilamentNotification::make()
            ->title('휴가 신청')
            ->body("{$employeeName}님이 {$leaveType}을(를) 신청했습니다. ({$period}, {$this->leave->days}일)")
            ->icon('heroicon-o-calendar-days')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(LeaveResource::getUrl('edit', ['record' => $this->leave])),
            ])
            ->getDatabaseMessage();
    }
}
