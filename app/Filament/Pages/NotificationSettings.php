<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class NotificationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = '내 설정';
    protected static ?string $navigationLabel = '알림 설정';
    protected static ?string $title = '알림 설정';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.notification-settings';

    /**
     * 알림 타입 정의 (key => [label, description, category])
     */
    public static function notificationTypes(): array
    {
        return [
            // 업무 관련
            'task_assigned' => ['label' => '태스크 배정', 'desc' => '태스크가 나에게 배정되었을 때', 'category' => '업무'],
            'task_status_changed' => ['label' => '태스크 완료', 'desc' => '담당 프로젝트의 태스크가 완료되었을 때', 'category' => '업무'],
            'milestone_completed' => ['label' => '마일스톤 완료', 'desc' => '담당 프로젝트의 마일스톤이 완료되었을 때', 'category' => '업무'],

            // 승인/결재
            'leave_requested' => ['label' => '휴가 신청', 'desc' => '새로운 휴가 신청이 접수되었을 때', 'category' => '승인/결재'],
            'leave_status_changed' => ['label' => '휴가 승인/반려', 'desc' => '신청한 휴가가 승인 또는 반려되었을 때', 'category' => '승인/결재'],
            'expense_submitted' => ['label' => '비용 청구', 'desc' => '새로운 비용 청구 승인 요청이 접수되었을 때', 'category' => '승인/결재'],
            'expense_status_changed' => ['label' => '비용 승인/반려', 'desc' => '청구한 비용이 승인 또는 반려되었을 때', 'category' => '승인/결재'],
            'purchase_order_approval' => ['label' => '구매주문 승인', 'desc' => '새로운 구매주문 승인 요청이 접수되었을 때', 'category' => '승인/결재'],

            // CRM
            'lead_assigned' => ['label' => '리드 배정', 'desc' => '새 리드가 나에게 배정되었을 때', 'category' => 'CRM'],
            'opportunity_stage_changed' => ['label' => '영업기회 단계 변경', 'desc' => '담당 영업기회의 단계가 변경되었을 때', 'category' => 'CRM'],

            // 재무/재고
            'invoice_overdue' => ['label' => '송장 연체', 'desc' => '송장 결제 기한이 초과되었을 때', 'category' => '재무/재고'],
            'contract_expiring' => ['label' => '계약 만료 임박', 'desc' => '계약 만료일이 가까울 때', 'category' => '재무/재고'],
            'low_stock' => ['label' => '재고 부족', 'desc' => '상품 재고가 최소 수량 이하일 때', 'category' => '재무/재고'],
            'payment_received' => ['label' => '결제 수신', 'desc' => '담당 송장에 결제가 입금되었을 때', 'category' => '재무/재고'],
        ];
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $prefs = $user->getNotificationPreferences();

        // 기본값: 모든 알림 활성화
        $data = [];
        foreach (self::notificationTypes() as $key => $info) {
            $data[$key] = $prefs[$key] ?? true;
        }

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        $schema = [];
        $grouped = [];

        // 카테고리별 그룹화
        foreach (self::notificationTypes() as $key => $info) {
            $grouped[$info['category']][$key] = $info;
        }

        foreach ($grouped as $category => $types) {
            $toggles = [];
            foreach ($types as $key => $info) {
                $toggles[] = Forms\Components\Toggle::make($key)
                    ->label($info['label'])
                    ->helperText($info['desc'])
                    ->default(true);
            }

            $schema[] = Forms\Components\Section::make($category)
                ->schema($toggles)
                ->columns(2)
                ->collapsible();
        }

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();
        $user->setNotificationPreferences($data);

        Notification::make()
            ->title('알림 설정이 저장되었습니다.')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}
