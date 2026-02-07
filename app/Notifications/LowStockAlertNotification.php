<?php

namespace App\Notifications;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LowStockAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Product $product,
        public int $currentStock,
        public int $minStock,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('재고 부족 경고')
            ->body("'{$this->product->name}' 현재 재고 {$this->currentStock}개 (최소 {$this->minStock}개)")
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('danger')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(ProductResource::getUrl('edit', ['record' => $this->product])),
            ])
            ->getDatabaseMessage();
    }
}
