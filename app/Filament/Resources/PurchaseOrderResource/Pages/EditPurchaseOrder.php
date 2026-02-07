<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\ApprovalFlow;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

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
                    $this->record->status === '초안'
                    && !$this->record->hasPendingApproval()
                )
                ->form(function () {
                    // 사용 가능한 결재라인 목록 (대상 유형이 PurchaseOrder인 활성 라인)
                    $flows = ApprovalFlow::where('target_type', 'App\\Models\\PurchaseOrder')
                        ->where('is_active', true)
                        ->with('steps')
                        ->get();

                    // 조건 매칭으로 추천 결재라인 결정
                    $recommended = ApprovalFlow::findForTarget($this->record);

                    $options = [];
                    $descriptions = [];
                    foreach ($flows as $flow) {
                        $stepCount = $flow->steps->count();
                        $stepLabels = $flow->steps->map(fn ($s) => $s->approver_label)->implode(' → ');
                        $condLabel = '';
                        if (!empty($flow->conditions)) {
                            $parts = [];
                            if (isset($flow->conditions['min_amount'])) {
                                $parts[] = '₩' . number_format($flow->conditions['min_amount']) . ' 이상';
                            }
                            if (isset($flow->conditions['max_amount'])) {
                                $parts[] = '₩' . number_format($flow->conditions['max_amount']) . ' 미만';
                            }
                            $condLabel = ' (' . implode(', ', $parts) . ')';
                        } elseif ($flow->is_default) {
                            $condLabel = ' (기본)';
                        }

                        $label = $flow->name . $condLabel;
                        if ($recommended && $recommended->id === $flow->id) {
                            $label .= ' ★ 추천';
                        }
                        $options[$flow->id] = $label;
                        $descriptions[$flow->id] = "{$stepCount}단계: {$stepLabels}";
                    }

                    return [
                        Forms\Components\Placeholder::make('order_info')
                            ->label('구매주문 정보')
                            ->content(fn () =>
                                $this->record->po_number . ' · '
                                . ($this->record->supplier?->company_name ?? '') . ' · '
                                . '₩' . number_format($this->record->total_amount)
                            ),

                        Forms\Components\Radio::make('approval_flow_id')
                            ->label('결재라인 선택')
                            ->options($options)
                            ->descriptions($descriptions)
                            ->default($recommended?->id)
                            ->required()
                            ->helperText('★ 추천: 금액 조건에 맞는 결재라인이 자동으로 선택됩니다.'),
                    ];
                })
                ->action(function (array $data) {
                    $flowId = $data['approval_flow_id'];
                    $request = $this->record->submitForApproval(flowId: $flowId);

                    if ($request) {
                        Notification::make()
                            ->title('승인요청 완료')
                            ->body('결재라인에 따라 승인자에게 알림이 발송되었습니다.')
                            ->success()
                            ->send();

                        $this->redirect(PurchaseOrderResource::getUrl('view', ['record' => $this->record]));
                    } else {
                        Notification::make()
                            ->title('승인요청 실패')
                            ->body('결재라인 처리 중 오류가 발생했습니다.')
                            ->danger()
                            ->send();
                    }
                }),

            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
