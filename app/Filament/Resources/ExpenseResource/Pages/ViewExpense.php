<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        $approvalRequest = $this->record->approvalRequest;
        if ($approvalRequest && $approvalRequest->isCurrentApprover(auth()->user())) {
            $currentStep = $approvalRequest->getCurrentStep();

            if ($currentStep && $currentStep->action_type !== '참조') {
                $actions[] = Actions\Action::make('approve')
                    ->label('승인')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('코멘트')
                            ->placeholder('승인 의견을 입력하세요 (선택)')
                            ->rows(3),
                    ])
                    ->action(function (array $data) use ($approvalRequest) {
                        $result = $approvalRequest->approve(auth()->id(), $data['comment'] ?? null);
                        if ($result) {
                            $approvalRequest->refresh();
                            if ($approvalRequest->status === '승인') {
                                $approvalRequest->requester?->notify(
                                    new \App\Notifications\ApprovalCompletedNotification($approvalRequest, 'approved')
                                );
                            }
                            Notification::make()->title('승인 완료')->success()->send();
                        } else {
                            Notification::make()->title('처리 실패')->danger()->send();
                        }
                        $this->redirect(ExpenseResource::getUrl('view', ['record' => $this->record]));
                    });

                $actions[] = Actions\Action::make('reject')
                    ->label('반려')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('반려 사유')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data) use ($approvalRequest) {
                        $result = $approvalRequest->reject(auth()->id(), $data['comment'] ?? null);
                        if ($result) {
                            $approvalRequest->requester?->notify(
                                new \App\Notifications\ApprovalCompletedNotification($approvalRequest, 'rejected')
                            );
                            Notification::make()->title('반려 완료')->warning()->send();
                        } else {
                            Notification::make()->title('처리 실패')->danger()->send();
                        }
                        $this->redirect(ExpenseResource::getUrl('view', ['record' => $this->record]));
                    });
            }

            if ($currentStep && $currentStep->action_type === '참조') {
                $actions[] = Actions\Action::make('acknowledge')
                    ->label('참조 확인')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function () use ($approvalRequest) {
                        $approvalRequest->approve(auth()->id(), '참조 확인');
                        Notification::make()->title('참조 확인 완료')->success()->send();
                        $this->redirect(ExpenseResource::getUrl('view', ['record' => $this->record]));
                    });
            }
        }

        $actions[] = Actions\EditAction::make();

        return $actions;
    }
}
