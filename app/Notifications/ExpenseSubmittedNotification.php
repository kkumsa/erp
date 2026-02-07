<?php

namespace App\Notifications;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ExpenseSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Expense $expense,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $employeeName = $this->expense->employee?->user?->name ?? '직원';
        $amount = number_format($this->expense->total_amount) . '원';

        return FilamentNotification::make()
            ->title('비용 청구 승인 요청')
            ->body("{$employeeName}님이 '{$this->expense->title}' ({$amount}) 비용 승인을 요청했습니다.")
            ->icon('heroicon-o-banknotes')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(ExpenseResource::getUrl('view', ['record' => $this->expense])),
            ])
            ->getDatabaseMessage();
    }
}
