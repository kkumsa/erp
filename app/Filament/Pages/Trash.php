<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class Trash extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.trash';

    public string $selectedModel = '';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.system_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.trash');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('common.pages.trash');
    }

    public function mount(): void
    {
        $this->selectedModel = '__all__';
    }

    /**
     * SoftDeletes가 적용된 모델 목록과 메타 정보
     */
    public static function getTrashableModels(): array
    {
        return [
            \App\Models\User::class => [
                'label' => __('models.user'),
                'displayColumn' => 'name',
                'columns' => ['name', 'email'],
            ],
            \App\Models\Department::class => [
                'label' => __('models.department'),
                'displayColumn' => 'name',
                'columns' => ['name', 'code'],
            ],
            \App\Models\Employee::class => [
                'label' => __('models.employee'),
                'displayColumn' => 'employee_code',
                'columns' => ['employee_code', 'position'],
            ],
            \App\Models\Customer::class => [
                'label' => __('models.customer'),
                'displayColumn' => 'company_name',
                'columns' => ['company_name', 'email'],
            ],
            \App\Models\Contact::class => [
                'label' => __('models.contact'),
                'displayColumn' => 'name',
                'columns' => ['name', 'email'],
            ],
            \App\Models\Lead::class => [
                'label' => __('models.lead'),
                'displayColumn' => 'company_name',
                'columns' => ['company_name', 'contact_name'],
            ],
            \App\Models\Opportunity::class => [
                'label' => __('models.opportunity'),
                'displayColumn' => 'name',
                'columns' => ['name', 'stage'],
            ],
            \App\Models\Contract::class => [
                'label' => __('models.contract'),
                'displayColumn' => 'title',
                'columns' => ['contract_number', 'title'],
            ],
            \App\Models\Invoice::class => [
                'label' => __('models.invoice'),
                'displayColumn' => 'invoice_number',
                'columns' => ['invoice_number', 'total_amount'],
            ],
            \App\Models\Payment::class => [
                'label' => __('models.payment'),
                'displayColumn' => 'payment_number',
                'columns' => ['payment_number', 'amount'],
            ],
            \App\Models\Expense::class => [
                'label' => __('models.expense'),
                'displayColumn' => 'title',
                'columns' => ['expense_number', 'title'],
            ],
            \App\Models\Project::class => [
                'label' => __('models.project'),
                'displayColumn' => 'name',
                'columns' => ['code', 'name'],
            ],
            \App\Models\Task::class => [
                'label' => __('models.task'),
                'displayColumn' => 'title',
                'columns' => ['title', 'status'],
            ],
            \App\Models\Leave::class => [
                'label' => __('models.leave'),
                'displayColumn' => 'id',
                'columns' => ['start_date', 'end_date', 'status'],
            ],
            \App\Models\Product::class => [
                'label' => __('models.product'),
                'displayColumn' => 'name',
                'columns' => ['code', 'name'],
            ],
            \App\Models\Supplier::class => [
                'label' => __('models.supplier'),
                'displayColumn' => 'company_name',
                'columns' => ['code', 'company_name'],
            ],
            \App\Models\PurchaseOrder::class => [
                'label' => __('models.purchase_order'),
                'displayColumn' => 'po_number',
                'columns' => ['po_number', 'status'],
            ],
            \App\Models\Warehouse::class => [
                'label' => __('models.warehouse'),
                'displayColumn' => 'name',
                'columns' => ['code', 'name'],
            ],
        ];
    }

    /**
     * 모델 선택 옵션
     */
    public function getModelOptions(): array
    {
        $options = [];

        foreach (static::getTrashableModels() as $modelClass => $meta) {
            $count = $modelClass::onlyTrashed()->count();
            if ($count > 0) {
                $options[$modelClass] = "{$meta['label']} ({$count})";
            } else {
                $options[$modelClass] = $meta['label'];
            }
        }

        return $options;
    }

    /**
     * 전체 삭제 항목 수
     */
    public function getTotalTrashedCount(): int
    {
        $total = 0;
        foreach (static::getTrashableModels() as $modelClass => $meta) {
            $total += $modelClass::onlyTrashed()->count();
        }
        return $total;
    }

    public function updatedSelectedModel(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $modelClass = $this->selectedModel;

                if (!$modelClass || $modelClass === '__all__' || !array_key_exists($modelClass, static::getTrashableModels())) {
                    $modelClass = array_key_first(static::getTrashableModels());
                }

                return $modelClass::query()
                    ->withoutGlobalScope(SoftDeletingScope::class)
                    ->whereNotNull('deleted_at');
            })
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label(__('common.trash_page.name_col'))
                    ->getStateUsing(function (Model $record): string {
                        $meta = static::getTrashableModels()[get_class($record)] ?? null;
                        if (!$meta) return (string) $record->getKey();
                        $col = $meta['displayColumn'];
                        return (string) ($record->{$col} ?? $record->getKey());
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        if (!array_key_exists($this->selectedModel, static::getTrashableModels())) {
                            return $query;
                        }
                        $meta = static::getTrashableModels()[$this->selectedModel];
                        return $query->where(function (Builder $q) use ($meta, $search) {
                            foreach ($meta['columns'] as $col) {
                                $q->orWhere($col, 'like', "%{$search}%");
                            }
                        });
                    }),

                Tables\Columns\TextColumn::make('detail_1')
                    ->label(__('common.trash_page.detail_1'))
                    ->getStateUsing(function (Model $record): ?string {
                        $meta = static::getTrashableModels()[get_class($record)] ?? null;
                        if (!$meta || !isset($meta['columns'][0])) return null;
                        return (string) ($record->{$meta['columns'][0]} ?? '-');
                    }),

                Tables\Columns\TextColumn::make('detail_2')
                    ->label(__('common.trash_page.detail_2'))
                    ->getStateUsing(function (Model $record): ?string {
                        $meta = static::getTrashableModels()[get_class($record)] ?? null;
                        if (!$meta || !isset($meta['columns'][1])) return null;
                        return (string) ($record->{$meta['columns'][1]} ?? '-');
                    }),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('fields.deleted_at'))
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->defaultSort('deleted_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label(__('common.buttons.restore'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('common.confirmations.restore_heading'))
                    ->modalDescription(__('common.confirmations.restore'))
                    ->action(function (Model $record) {
                        $record->restore();
                        Notification::make()
                            ->title(__('common.notifications.restored'))
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('forceDelete')
                    ->label(__('common.buttons.force_delete'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('common.confirmations.force_delete_heading'))
                    ->modalDescription(__('common.confirmations.force_delete'))
                    ->action(function (Model $record) {
                        $record->forceDelete();
                        Notification::make()
                            ->title(__('common.notifications.force_deleted'))
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('restoreSelected')
                        ->label(__('common.buttons.restore_selected'))
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading(__('common.confirmations.restore_selected_heading'))
                        ->modalDescription(__('common.confirmations.restore_selected'))
                        ->action(function ($records) {
                            $records->each->restore();
                            Notification::make()
                                ->title(__('common.notifications.restored_count', ['count' => $records->count()]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('forceDeleteSelected')
                        ->label(__('common.buttons.force_delete_selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('common.confirmations.force_delete_selected_heading'))
                        ->modalDescription(__('common.confirmations.force_delete_selected'))
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each->forceDelete();
                            Notification::make()
                                ->title(__('common.notifications.force_deleted_count', ['count' => $count]))
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading(__('common.empty_states.no_deleted_items'))
            ->emptyStateDescription(__('common.empty_states.trash_empty'))
            ->emptyStateIcon('heroicon-o-trash')
            ->poll('30s');
    }

    /**
     * "전체" 모드에서 모든 모델의 삭제 항목을 가져온다.
     */
    public function getAllTrashedRecords(): \Illuminate\Support\Collection
    {
        $allRecords = collect();

        foreach (static::getTrashableModels() as $modelClass => $meta) {
            $records = $modelClass::onlyTrashed()
                ->latest('deleted_at')
                ->limit(50)
                ->get()
                ->map(function ($record) use ($meta, $modelClass) {
                    $record->_model_label = $meta['label'];
                    $record->_model_class = $modelClass;
                    $record->_display_name = $record->{$meta['displayColumn']} ?? $record->getKey();
                    $record->_detail_1 = isset($meta['columns'][0]) ? ($record->{$meta['columns'][0]} ?? '-') : '-';
                    $record->_detail_2 = isset($meta['columns'][1]) ? ($record->{$meta['columns'][1]} ?? '-') : '-';
                    return $record;
                });

            $allRecords = $allRecords->concat($records);
        }

        return $allRecords->sortByDesc('deleted_at')->values();
    }

    /**
     * "전체" 모드에서 단일 레코드 복원
     */
    public function restoreRecord(string $modelClass, int $id): void
    {
        if (!array_key_exists($modelClass, static::getTrashableModels())) {
            return;
        }

        $record = $modelClass::onlyTrashed()->find($id);
        $record?->restore();

        Notification::make()
            ->title(__('common.notifications.restored'))
            ->success()
            ->send();
    }

    /**
     * "전체" 모드에서 단일 레코드 영구 삭제
     */
    public function forceDeleteRecord(string $modelClass, int $id): void
    {
        if (!array_key_exists($modelClass, static::getTrashableModels())) {
            return;
        }

        $record = $modelClass::onlyTrashed()->find($id);
        $record?->forceDelete();

        Notification::make()
            ->title(__('common.notifications.force_deleted'))
            ->warning()
            ->send();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('Super Admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
