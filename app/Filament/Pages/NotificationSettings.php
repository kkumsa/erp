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
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.notification-settings';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.my_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.notification_settings');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('common.pages.notification_settings');
    }

    /**
     * 알림 타입 정의 (key => [label, description, category])
     */
    public static function notificationTypes(): array
    {
        return [
            // 업무 관련
            'task_assigned' => ['label' => __('common.notification_settings.task_assigned_label'), 'desc' => __('common.notification_settings.task_assigned_desc'), 'category' => __('common.notification_settings.category_work')],
            'task_status_changed' => ['label' => __('common.notification_settings.task_status_changed_label'), 'desc' => __('common.notification_settings.task_status_changed_desc'), 'category' => __('common.notification_settings.category_work')],
            'milestone_completed' => ['label' => __('common.notification_settings.milestone_completed_label'), 'desc' => __('common.notification_settings.milestone_completed_desc'), 'category' => __('common.notification_settings.category_work')],

            // 승인/결재
            'leave_requested' => ['label' => __('common.notification_settings.leave_requested_label'), 'desc' => __('common.notification_settings.leave_requested_desc'), 'category' => __('common.notification_settings.category_approval')],
            'leave_status_changed' => ['label' => __('common.notification_settings.leave_status_changed_label'), 'desc' => __('common.notification_settings.leave_status_changed_desc'), 'category' => __('common.notification_settings.category_approval')],
            'expense_submitted' => ['label' => __('common.notification_settings.expense_submitted_label'), 'desc' => __('common.notification_settings.expense_submitted_desc'), 'category' => __('common.notification_settings.category_approval')],
            'expense_status_changed' => ['label' => __('common.notification_settings.expense_status_changed_label'), 'desc' => __('common.notification_settings.expense_status_changed_desc'), 'category' => __('common.notification_settings.category_approval')],
            'purchase_order_approval' => ['label' => __('common.notification_settings.purchase_order_approval_label'), 'desc' => __('common.notification_settings.purchase_order_approval_desc'), 'category' => __('common.notification_settings.category_approval')],

            // CRM
            'lead_assigned' => ['label' => __('common.notification_settings.lead_assigned_label'), 'desc' => __('common.notification_settings.lead_assigned_desc'), 'category' => __('common.notification_settings.category_crm')],
            'opportunity_stage_changed' => ['label' => __('common.notification_settings.opportunity_stage_changed_label'), 'desc' => __('common.notification_settings.opportunity_stage_changed_desc'), 'category' => __('common.notification_settings.category_crm')],

            // 재무/재고
            'invoice_overdue' => ['label' => __('common.notification_settings.invoice_overdue_label'), 'desc' => __('common.notification_settings.invoice_overdue_desc'), 'category' => __('common.notification_settings.category_finance')],
            'contract_expiring' => ['label' => __('common.notification_settings.contract_expiring_label'), 'desc' => __('common.notification_settings.contract_expiring_desc'), 'category' => __('common.notification_settings.category_finance')],
            'low_stock' => ['label' => __('common.notification_settings.low_stock_label'), 'desc' => __('common.notification_settings.low_stock_desc'), 'category' => __('common.notification_settings.category_finance')],
            'payment_received' => ['label' => __('common.notification_settings.payment_received_label'), 'desc' => __('common.notification_settings.payment_received_desc'), 'category' => __('common.notification_settings.category_finance')],
        ];
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $prefs = $user->getNotificationPreferences();

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
            ->title(__('common.notification_settings.saved'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}
