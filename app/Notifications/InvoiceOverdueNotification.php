<?php

namespace App\Notifications;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class InvoiceOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $customerName = $this->invoice->customer?->company_name ?? '고객';
        $balance = number_format($this->invoice->balance) . '원';
        $daysOverdue = now()->diffInDays($this->invoice->due_date);

        return FilamentNotification::make()
            ->title('송장 결제 기한 초과')
            ->body("{$this->invoice->invoice_number} ({$customerName}) 결제 기한이 {$daysOverdue}일 초과되었습니다. 미수금: {$balance}")
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('danger')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(InvoiceResource::getUrl('edit', ['record' => $this->invoice])),
            ])
            ->getDatabaseMessage();
    }
}
