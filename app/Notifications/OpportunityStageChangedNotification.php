<?php

namespace App\Notifications;

use App\Filament\Resources\OpportunityResource;
use App\Models\Opportunity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class OpportunityStageChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Opportunity $opportunity,
        public string $oldStage,
        public string $newStage,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $amount = number_format($this->opportunity->amount) . '원';

        return FilamentNotification::make()
            ->title('영업기회 단계 변경')
            ->body("'{$this->opportunity->name}' ({$amount})이(가) '{$this->oldStage}' → '{$this->newStage}' 단계로 변경되었습니다.")
            ->icon('heroicon-o-arrow-trending-up')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(OpportunityResource::getUrl('edit', ['record' => $this->opportunity])),
            ])
            ->getDatabaseMessage();
    }
}
