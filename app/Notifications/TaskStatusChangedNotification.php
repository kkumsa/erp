<?php

namespace App\Notifications;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class TaskStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('태스크 상태 변경')
            ->body("'{$this->task->title}' 태스크가 '{$this->oldStatus}' → '{$this->newStatus}'(으)로 변경되었습니다.")
            ->icon('heroicon-o-check-circle')
            ->iconColor($this->newStatus === '완료' ? 'success' : 'warning')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(TaskResource::getUrl('edit', ['record' => $this->task])),
            ])
            ->getDatabaseMessage();
    }
}
