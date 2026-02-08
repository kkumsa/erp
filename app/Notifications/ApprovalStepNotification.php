<?php

namespace App\Notifications;

use App\Enums\ApprovalActionType;
use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\LeaveResource;
use App\Filament\Resources\PurchaseOrderResource;
use App\Filament\Resources\TimesheetResource;
use App\Models\ApprovalFlowStep;
use App\Models\ApprovalRequest;
use App\Models\Expense;
use App\Models\Leave;
use App\Models\PurchaseOrder;
use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ApprovalStepNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ApprovalRequest $approvalRequest,
        public ApprovalFlowStep $step,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $approvable = $this->approvalRequest->approvable;
        $requester = $this->approvalRequest->requester;
        $stepOrder = $this->step->step_order;
        $totalSteps = $this->approvalRequest->total_steps;
        $actionType = $this->step->action_type;
        $isRef = $actionType === ApprovalActionType::Reference;

        $info = $this->getApprovableInfo($approvable, $requester, $stepOrder, $totalSteps, $isRef);

        return FilamentNotification::make()
            ->title($info['title'])
            ->body($info['body'])
            ->icon($info['icon'])
            ->iconColor($isRef ? 'info' : 'warning')
            ->actions([
                Action::make('view')
                    ->label($isRef ? '확인' : '결재하기')
                    ->url($info['url']),
            ])
            ->getDatabaseMessage();
    }

    protected function getApprovableInfo($approvable, $requester, int $stepOrder, int $totalSteps, bool $isRef): array
    {
        $name = $requester?->name ?? '사용자';
        $step = "({$stepOrder}/{$totalSteps}단계)";
        $type = $isRef ? '참조' : '요청';

        if ($approvable instanceof PurchaseOrder) {
            $amount = number_format($approvable->total_amount) . '원';
            return [
                'title' => "구매주문 결재 {$type} {$step}",
                'body' => "{$name}님이 구매주문 ({$approvable->po_number}, {$amount})의 결재를 요청했습니다.",
                'icon' => 'heroicon-o-shopping-cart',
                'url' => PurchaseOrderResource::getUrl('view', ['record' => $approvable]),
            ];
        }

        if ($approvable instanceof Expense) {
            $amount = number_format($approvable->total_amount) . '원';
            return [
                'title' => "비용 결재 {$type} {$step}",
                'body' => "{$name}님이 비용 ({$approvable->expense_number}, {$amount})의 결재를 요청했습니다.",
                'icon' => 'heroicon-o-banknotes',
                'url' => ExpenseResource::getUrl('view', ['record' => $approvable]),
            ];
        }

        if ($approvable instanceof Leave) {
            $days = $approvable->days . '일';
            return [
                'title' => "휴가 결재 {$type} {$step}",
                'body' => "{$name}님이 휴가 ({$approvable->start_date?->format('m/d')}~{$approvable->end_date?->format('m/d')}, {$days})의 결재를 요청했습니다.",
                'icon' => 'heroicon-o-calendar-days',
                'url' => LeaveResource::getUrl('edit', ['record' => $approvable]),
            ];
        }

        if ($approvable instanceof Timesheet) {
            return [
                'title' => "타임시트 결재 {$type} {$step}",
                'body' => "{$name}님이 타임시트 ({$approvable->date?->format('Y-m-d')}, {$approvable->hours}시간)의 결재를 요청했습니다.",
                'icon' => 'heroicon-o-clock',
                'url' => TimesheetResource::getUrl('edit', ['record' => $approvable]),
            ];
        }

        return [
            'title' => "결재 {$type} {$step}",
            'body' => "{$name}님이 결재를 요청했습니다.",
            'icon' => 'heroicon-o-clipboard-document-check',
            'url' => '#',
        ];
    }
}
