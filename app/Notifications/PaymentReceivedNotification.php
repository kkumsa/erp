<?php

namespace App\Notifications;

use App\Filament\Resources\InvoiceResource;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $amount = number_format($this->payment->amount) . '원';
        $payable = $this->payment->payable;
        $reference = '';
        $url = '#';

        if ($payable && method_exists($payable, 'getAttribute')) {
            $reference = $payable->invoice_number ?? $payable->expense_number ?? '';
            if ($payable instanceof \App\Models\Invoice) {
                $customerName = $payable->customer?->company_name ?? '';
                $reference = "{$reference} ({$customerName})";
                $url = InvoiceResource::getUrl('edit', ['record' => $payable]);
            }
        }

        return FilamentNotification::make()
            ->title('결제 수신 확인')
            ->body("{$reference} {$amount} 결제가 확인되었습니다.")
            ->icon('heroicon-o-currency-dollar')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url($url),
            ])
            ->getDatabaseMessage();
    }
}
