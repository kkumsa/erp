<?php

namespace App\Notifications;

use App\Enums\LeaveStatus;
use App\Filament\Resources\LeaveResource;
use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LeaveStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Leave $leave,
        public LeaveStatus $newStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $leaveType = $this->leave->leaveType?->name ?? '휴가';
        $isApproved = $this->newStatus === LeaveStatus::Approved;
        $statusLabel = $this->newStatus->getLabel();

        return FilamentNotification::make()
            ->title("휴가 {$statusLabel}")
            ->body("신청하신 {$leaveType}이(가) {$statusLabel}되었습니다.")
            ->icon($isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->iconColor($isApproved ? 'success' : 'danger')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(LeaveResource::getUrl('edit', ['record' => $this->leave])),
            ])
            ->getDatabaseMessage();
    }
}
