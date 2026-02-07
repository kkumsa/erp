<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use App\Models\ApprovalFlow;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('submitApproval')
                ->label('승인요청')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->modalHeading('결재 요청')
                ->modalDescription('결재라인을 선택하고 승인을 요청하세요.')
                ->modalSubmitActionLabel('승인요청')
                ->modalWidth('lg')
                ->visible(fn () =>
                    $this->record->status === '대기'
                    && !$this->record->hasPendingApproval()
                )
                ->form(function () {
                    $flows = ApprovalFlow::where('target_type', 'App\\Models\\Timesheet')
                        ->where('is_active', true)
                        ->with('steps')
                        ->get();

                    $recommended = ApprovalFlow::findForTarget($this->record);

                    if ($flows->isEmpty()) {
                        return [
                            Forms\Components\Placeholder::make('no_flow')
                                ->label('')
                                ->content('사용 가능한 결재라인이 없습니다. 시스템 관리자에게 문의하세요.'),
                        ];
                    }

                    $options = [];
                    $descriptions = [];
                    foreach ($flows as $flow) {
                        $stepLabels = $flow->steps->map(fn ($s) => $s->approver_label)->implode(' → ');
                        $label = $flow->name;
                        if ($flow->is_default) $label .= ' (기본)';
                        if ($recommended && $recommended->id === $flow->id) $label .= ' ★ 추천';
                        $options[$flow->id] = $label;
                        $descriptions[$flow->id] = $flow->steps->count() . "단계: {$stepLabels}";
                    }

                    return [
                        Forms\Components\Placeholder::make('info')
                            ->label('근무기록 정보')
                            ->content($this->record->date?->format('Y-m-d') . ' · ' . $this->record->hours . '시간'),
                        Forms\Components\Radio::make('approval_flow_id')
                            ->label('결재라인 선택')
                            ->options($options)
                            ->descriptions($descriptions)
                            ->default($recommended?->id)
                            ->required(),
                    ];
                })
                ->action(function (array $data) {
                    $request = $this->record->submitForApproval(flowId: $data['approval_flow_id'] ?? null);
                    if ($request) {
                        Notification::make()->title('승인요청 완료')->success()->send();
                        $this->redirect(TimesheetResource::getUrl('index'));
                    } else {
                        Notification::make()->title('승인요청 실패')->danger()->send();
                    }
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
