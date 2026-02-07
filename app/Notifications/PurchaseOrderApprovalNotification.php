<?php

namespace App\Notifications;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class PurchaseOrderApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $purchaseOrder,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $supplierName = $this->purchaseOrder->supplier?->name ?? '공급업체';
        $amount = number_format($this->purchaseOrder->total_amount) . '원';
        $creatorName = $this->purchaseOrder->creator?->name ?? '담당자';

        return FilamentNotification::make()
            ->title('구매주문 승인 요청')
            ->body("{$creatorName}님이 {$supplierName} 구매주문 ({$this->purchaseOrder->po_number}, {$amount}) 승인을 요청했습니다.")
            ->icon('heroicon-o-shopping-cart')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(PurchaseOrderResource::getUrl('edit', ['record' => $this->purchaseOrder])),
            ])
            ->getDatabaseMessage();
    }
}
