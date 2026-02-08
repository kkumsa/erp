<?php

namespace App\Notifications;

use App\Enums\TaskStatus;
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
        public TaskStatus $oldStatus,
        public TaskStatus $newStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $oldLabel = $this->oldStatus->getLabel();
        $newLabel = $this->newStatus->getLabel();

        return FilamentNotification::make()
            ->title('태스크 상태 변경')
            ->body("'{$this->task->title}' 태스크가 '{$oldLabel}' → '{$newLabel}'(으)로 변경되었습니다.")
            ->icon('heroicon-o-check-circle')
            ->iconColor($this->newStatus === TaskStatus::Completed ? 'success' : 'warning')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(TaskResource::getUrl('edit', ['record' => $this->task])),
            ])
            ->getDatabaseMessage();
    }
}
