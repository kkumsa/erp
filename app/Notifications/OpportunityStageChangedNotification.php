<?php

namespace App\Notifications;

use App\Enums\OpportunityStage;
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
        public OpportunityStage $oldStage,
        public OpportunityStage $newStage,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $amount = number_format($this->opportunity->amount) . '원';
        $oldLabel = $this->oldStage->getLabel();
        $newLabel = $this->newStage->getLabel();

        return FilamentNotification::make()
            ->title('영업기회 단계 변경')
            ->body("'{$this->opportunity->name}' ({$amount})이(가) '{$oldLabel}' → '{$newLabel}' 단계로 변경되었습니다.")
            ->icon('heroicon-o-arrow-trending-up')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('보기')
                    ->url(OpportunityResource::getUrl('view', ['record' => $this->opportunity])),
            ])
            ->getDatabaseMessage();
    }
}
