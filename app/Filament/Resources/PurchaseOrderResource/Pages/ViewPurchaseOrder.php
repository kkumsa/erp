<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected static string $view = 'filament.resources.purchase-order-resource.pages.view-purchase-order';

    protected function getHeaderActions(): array
    {
        $actions = [];

        // 현재 사용자가 승인자인 경우 승인/반려 액션
        $approvalRequest = $this->record->approvalRequest;
        if ($approvalRequest && $approvalRequest->isCurrentApprover(auth()->user())) {
            $currentStep = $approvalRequest->getCurrentStep();

            // 참조가 아닌 경우에만 승인/반려 버튼 표시
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
                            // 최종 승인 또는 반려 시 알림
                            $approvalRequest->refresh();
                            if ($approvalRequest->status === '승인') {
                                $requester = $approvalRequest->requester;
                                if ($requester) {
                                    $requester->notify(new \App\Notifications\ApprovalCompletedNotification($approvalRequest, 'approved'));
                                }
                            }

                            Notification::make()
                                ->title('승인 완료')
                                ->body('결재가 처리되었습니다.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('처리 실패')
                                ->body('이미 처리된 단계이거나 권한이 없습니다.')
                                ->danger()
                                ->send();
                        }

                        $this->redirect(PurchaseOrderResource::getUrl('view', ['record' => $this->record]));
                    });

                $actions[] = Actions\Action::make('reject')
                    ->label('반려')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('반려 사유')
                            ->placeholder('반려 사유를 입력하세요')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data) use ($approvalRequest) {
                        $result = $approvalRequest->reject(auth()->id(), $data['comment'] ?? null);

                        if ($result) {
                            $requester = $approvalRequest->requester;
                            if ($requester) {
                                $requester->notify(new \App\Notifications\ApprovalCompletedNotification($approvalRequest, 'rejected'));
                            }

                            Notification::make()
                                ->title('반려 완료')
                                ->body('결재가 반려되었습니다.')
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('처리 실패')
                                ->body('이미 처리된 단계이거나 권한이 없습니다.')
                                ->danger()
                                ->send();
                        }

                        $this->redirect(PurchaseOrderResource::getUrl('view', ['record' => $this->record]));
                    });
            }

            // 참조 확인 액션
            if ($currentStep && $currentStep->action_type === '참조') {
                $actions[] = Actions\Action::make('acknowledge')
                    ->label('참조 확인')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('참조 확인')
                    ->modalDescription('이 결재 건을 확인하시겠습니까?')
                    ->action(function () use ($approvalRequest) {
                        $approvalRequest->approve(auth()->id(), '참조 확인');

                        Notification::make()
                            ->title('참조 확인 완료')
                            ->success()
                            ->send();

                        $this->redirect(PurchaseOrderResource::getUrl('view', ['record' => $this->record]));
                    });
            }
        }

        $actions[] = Actions\EditAction::make();

        return $actions;
    }

}
