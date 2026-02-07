<?php

namespace App\Notifications;

use App\Filament\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ContractExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Contract $contract,
        public int $daysRemaining,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $customerName = $this->contract->customer?->company_name ?? '고객';

        return FilamentNotification::make()
            ->title('계약 만료 임박')
            ->body("{$customerName} '{$this->contract->title}' 계약이 {$this->daysRemaining}일 후 만료됩니다.")
            ->icon('heroicon-o-document-text')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url(ContractResource::getUrl('view', ['record' => $this->contract])),
            ])
            ->getDatabaseMessage();
    }
}
