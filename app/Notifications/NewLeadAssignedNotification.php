<?php

namespace App\Notifications;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewLeadAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Lead $lead,
        public bool $isNew = true,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $title = $this->isNew ? '새 리드 배정' : '리드 담당자 변경';
        $body = $this->isNew
            ? "새 리드 '{$this->lead->company_name}'이(가) 배정되었습니다."
            : "리드 '{$this->lead->company_name}'의 담당자로 변경되었습니다.";

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-user-plus')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(LeadResource::getUrl('edit', ['record' => $this->lead])),
            ])
            ->getDatabaseMessage();
    }
}
