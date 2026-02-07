<?php

namespace App\Notifications;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ExpenseStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Expense $expense,
        public string $newStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isApproved = $this->newStatus === '승인';
        $statusLabel = $isApproved ? '승인' : '반려';
        $amount = number_format($this->expense->total_amount) . '원';

        return FilamentNotification::make()
            ->title("비용 청구 {$statusLabel}")
            ->body("'{$this->expense->title}' ({$amount}) 비용이 {$statusLabel}되었습니다.")
            ->icon($isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->iconColor($isApproved ? 'success' : 'danger')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(ExpenseResource::getUrl('edit', ['record' => $this->expense])),
            ])
            ->getDatabaseMessage();
    }
}
