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

    protected static ?string $navigationGroup = '시스템설정';

    protected static ?string $navigationLabel = '휴지통';

    protected static ?string $title = '휴지통';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.trash';

    public string $selectedModel = '';

    public function mount(): void
    {
        // 기본 선택: 삭제 항목이 있는 첫 번째 모델
        foreach (static::getTrashableModels() as $modelClass => $meta) {
            if ($modelClass::onlyTrashed()->count() > 0) {
                $this->selectedModel = $modelClass;
                return;
            }
        }

        // 삭제 항목이 없으면 첫 번째 모델
        $this->selectedModel = array_key_first(static::getTrashableModels());
    }

    /**
     * SoftDeletes가 적용된 모델 목록과 메타 정보
     */
    public static function getTrashableModels(): array
    {
        return [
            \App\Models\User::class => [
                'label' => '사용자',
                'displayColumn' => 'name',
                'columns' => ['name', 'email'],
            ],
            \App\Models\Department::class => [
                'label' => '부서',
                'displayColumn' => 'name',
                'columns' => ['name', 'code'],
            ],
            \App\Models\Employee::class => [
                'label' => '직원',
                'displayColumn' => 'employee_code',
                'columns' => ['employee_code', 'position'],
            ],
            \App\Models\Customer::class => [
                'label' => '고객',
                'displayColumn' => 'company_name',
                'columns' => ['company_name', 'email'],
            ],
            \App\Models\Contact::class => [
                'label' => '연락처',
                'displayColumn' => 'name',
                'columns' => ['name', 'email'],
            ],
            \App\Models\Lead::class => [
                'label' => '리드',
                'displayColumn' => 'company_name',
                'columns' => ['company_name', 'contact_name'],
            ],
            \App\Models\Opportunity::class => [
                'label' => '영업 기회',
                'displayColumn' => 'name',
                'columns' => ['name', 'stage'],
            ],
            \App\Models\Contract::class => [
                'label' => '계약',
                'displayColumn' => 'title',
                'columns' => ['contract_number', 'title'],
            ],
            \App\Models\Invoice::class => [
                'label' => '청구서',
                'displayColumn' => 'invoice_number',
                'columns' => ['invoice_number', 'total_amount'],
            ],
            \App\Models\Payment::class => [
                'label' => '결제',
                'displayColumn' => 'payment_number',
                'columns' => ['payment_number', 'amount'],
            ],
            \App\Models\Expense::class => [
                'label' => '비용',
                'displayColumn' => 'title',
                'columns' => ['expense_number', 'title'],
            ],
            \App\Models\Project::class => [
                'label' => '프로젝트',
                'displayColumn' => 'name',
                'columns' => ['code', 'name'],
            ],
            \App\Models\Task::class => [
                'label' => '작업',
                'displayColumn' => 'title',
                'columns' => ['title', 'status'],
            ],
            \App\Models\Leave::class => [
                'label' => '휴가',
                'displayColumn' => 'id',
                'columns' => ['start_date', 'end_date', 'status'],
            ],
            \App\Models\Product::class => [
                'label' => '상품',
                'displayColumn' => 'name',
                'columns' => ['code', 'name'],
            ],
            \App\Models\Supplier::class => [
                'label' => '공급업체',
                'displayColumn' => 'company_name',
                'columns' => ['code', 'company_name'],
            ],
            \App\Models\PurchaseOrder::class => [
                'label' => '구매주문',
                'displayColumn' => 'po_number',
                'columns' => ['po_number', 'status'],
            ],
            \App\Models\Warehouse::class => [
                'label' => '창고',
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

    public function updatedSelectedModel(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $modelClass = $this->selectedModel;

                if (!$modelClass || !array_key_exists($modelClass, static::getTrashableModels())) {
                    $modelClass = array_key_first(static::getTrashableModels());
                }

                return $modelClass::query()
                    ->withoutGlobalScope(SoftDeletingScope::class)
                    ->whereNotNull('deleted_at');
            })
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('이름')
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
                    ->label('상세 1')
                    ->getStateUsing(function (Model $record): ?string {
                        $meta = static::getTrashableModels()[get_class($record)] ?? null;
                        if (!$meta || !isset($meta['columns'][0])) return null;
                        return (string) ($record->{$meta['columns'][0]} ?? '-');
                    }),

                Tables\Columns\TextColumn::make('detail_2')
                    ->label('상세 2')
                    ->getStateUsing(function (Model $record): ?string {
                        $meta = static::getTrashableModels()[get_class($record)] ?? null;
                        if (!$meta || !isset($meta['columns'][1])) return null;
                        return (string) ($record->{$meta['columns'][1]} ?? '-');
                    }),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('삭제일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('deleted_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('복원')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('복원 확인')
                    ->modalDescription('이 항목을 복원하시겠습니까?')
                    ->action(function (Model $record) {
                        $record->restore();
                        Notification::make()
                            ->title('복원 완료')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('forceDelete')
                    ->label('영구 삭제')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('영구 삭제 확인')
                    ->modalDescription('이 항목을 영구적으로 삭제합니다. 이 작업은 되돌릴 수 없습니다.')
                    ->action(function (Model $record) {
                        $record->forceDelete();
                        Notification::make()
                            ->title('영구 삭제 완료')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('restoreSelected')
                        ->label('선택 복원')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('선택 항목 복원')
                        ->modalDescription('선택한 항목을 모두 복원하시겠습니까?')
                        ->action(function ($records) {
                            $records->each->restore();
                            Notification::make()
                                ->title($records->count() . '건 복원 완료')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('forceDeleteSelected')
                        ->label('선택 영구 삭제')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('선택 항목 영구 삭제')
                        ->modalDescription('선택한 항목을 영구적으로 삭제합니다. 이 작업은 되돌릴 수 없습니다.')
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each->forceDelete();
                            Notification::make()
                                ->title($count . '건 영구 삭제 완료')
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('삭제된 항목 없음')
            ->emptyStateDescription('휴지통이 비어 있습니다.')
            ->emptyStateIcon('heroicon-o-trash')
            ->poll('30s');
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
