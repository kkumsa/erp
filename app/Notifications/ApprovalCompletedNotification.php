<?php

namespace App\Notifications;

use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\LeaveResource;
use App\Filament\Resources\PurchaseOrderResource;
use App\Filament\Resources\TimesheetResource;
use App\Models\ApprovalRequest;
use App\Models\Expense;
use App\Models\Leave;
use App\Models\PurchaseOrder;
use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ApprovalCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ApprovalRequest $approvalRequest,
        public string $event = 'approved',
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $approvable = $this->approvalRequest->approvable;
        $info = $this->getInfo($approvable);

        return FilamentNotification::make()
            ->title($info['title'])
            ->body($info['body'])
            ->icon($info['icon'])
            ->iconColor($info['color'])
            ->actions([
                Action::make('view')
                    ->label('확인')
                    ->url($info['url']),
            ])
            ->getDatabaseMessage();
    }

    protected function getInfo($approvable): array
    {
        $label = $this->getModelLabel($approvable);
        $identifier = $this->getIdentifier($approvable);
        $url = $this->getUrl($approvable);

        switch ($this->event) {
            case 'submitted':
                return [
                    'title' => "{$label} 승인요청 접수",
                    'body' => "{$label} ({$identifier})의 승인요청이 접수되었습니다.",
                    'icon' => 'heroicon-o-clipboard-document-check',
                    'color' => 'info',
                    'url' => $url,
                ];

            case 'approved':
                return [
                    'title' => "{$label} 최종 승인 완료",
                    'body' => "{$label} ({$identifier})이(가) 최종 승인되었습니다.",
                    'icon' => 'heroicon-o-check-circle',
                    'color' => 'success',
                    'url' => $url,
                ];

            case 'rejected':
                $lastAction = $this->approvalRequest->actions()
                    ->where('action', '반려')
                    ->latest('acted_at')
                    ->first();
                $rejectBy = $lastAction?->approver?->name ?? '승인자';
                $reason = $lastAction?->comment ? " (사유: {$lastAction->comment})" : '';

                return [
                    'title' => "{$label} 반려",
                    'body' => "{$rejectBy}님이 {$label} ({$identifier})을(를) 반려했습니다.{$reason}",
                    'icon' => 'heroicon-o-x-circle',
                    'color' => 'danger',
                    'url' => $url,
                ];

            default:
                return [
                    'title' => "{$label} 알림",
                    'body' => "{$label} ({$identifier}) 상태가 변경되었습니다.",
                    'icon' => 'heroicon-o-bell',
                    'color' => 'gray',
                    'url' => $url,
                ];
        }
    }

    protected function getModelLabel($approvable): string
    {
        return match (true) {
            $approvable instanceof PurchaseOrder => '구매주문',
            $approvable instanceof Expense => '비용',
            $approvable instanceof Leave => '휴가',
            $approvable instanceof Timesheet => '근무기록',
            default => '문서',
        };
    }

    protected function getIdentifier($approvable): string
    {
        return match (true) {
            $approvable instanceof PurchaseOrder => $approvable->po_number . ', ₩' . number_format($approvable->total_amount),
            $approvable instanceof Expense => $approvable->expense_number . ', ₩' . number_format($approvable->total_amount),
            $approvable instanceof Leave => $approvable->start_date?->format('m/d') . '~' . $approvable->end_date?->format('m/d') . ', ' . $approvable->days . '일',
            $approvable instanceof Timesheet => $approvable->date?->format('Y-m-d') . ', ' . $approvable->hours . '시간',
            default => '#' . $approvable->id,
        };
    }

    protected function getUrl($approvable): string
    {
        return match (true) {
            $approvable instanceof PurchaseOrder => PurchaseOrderResource::getUrl('view', ['record' => $approvable]),
            $approvable instanceof Expense => ExpenseResource::getUrl('view', ['record' => $approvable]),
            $approvable instanceof Leave => LeaveResource::getUrl('edit', ['record' => $approvable]),
            $approvable instanceof Timesheet => TimesheetResource::getUrl('edit', ['record' => $approvable]),
            default => '#',
        };
    }
}
