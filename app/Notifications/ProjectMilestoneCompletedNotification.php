<?php

namespace App\Notifications;

use App\Filament\Resources\MilestoneResource;
use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ProjectMilestoneCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Milestone $milestone,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $projectName = $this->milestone->project?->name ?? '프로젝트';

        return FilamentNotification::make()
            ->title('마일스톤 완료')
            ->body("[{$projectName}] '{$this->milestone->name}' 마일스톤이 완료되었습니다.")
            ->icon('heroicon-o-flag')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(MilestoneResource::getUrl('edit', ['record' => $this->milestone])),
            ])
            ->getDatabaseMessage();
    }
}
