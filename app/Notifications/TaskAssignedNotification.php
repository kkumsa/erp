<?php

namespace App\Notifications;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $assignerName = '시스템',
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('태스크 배정')
            ->body("{$this->assignerName}님이 '{$this->task->title}' 태스크를 배정했습니다.")
            ->icon('heroicon-o-clipboard-document-check')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(TaskResource::getUrl('edit', ['record' => $this->task])),
            ])
            ->getDatabaseMessage();
    }
}
