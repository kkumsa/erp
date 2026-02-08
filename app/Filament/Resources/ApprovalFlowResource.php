<?php

namespace App\Filament\Resources;

use App\Enums\ApprovalActionType;
use App\Filament\Resources\ApprovalFlowResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\ApprovalFlow;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class ApprovalFlowResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'approval_flow';

    protected static ?string $model = ApprovalFlow::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.system_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.approval_flow');
    }

    public static function getModelLabel(): string
    {
        return __('models.approval_flow');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.approval_flow_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('common.placeholders.example_approval_name')),

                        Forms\Components\Select::make('target_type')
                            ->label(__('fields.target_type'))
                            ->options([
                                'App\\Models\\PurchaseOrder' => __('common.target_types.purchase_order'),
                                'App\\Models\\Expense' => __('common.target_types.expense'),
                                'App\\Models\\Leave' => __('common.target_types.leave'),
                                'App\\Models\\Timesheet' => __('common.target_types.timesheet'),
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('is_default')
                            ->label(__('fields.is_default'))
                            ->helperText(__('common.helpers.default_approval_flow'))
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.conditions'))
                    ->description(__('common.helpers.approval_conditions'))
                    ->schema([
                        Forms\Components\TextInput::make('conditions.min_amount')
                            ->label(__('fields.min_amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->placeholder('0'),

                        Forms\Components\TextInput::make('conditions.max_amount')
                            ->label(__('fields.max_amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->placeholder(__('common.helpers.unlimited')),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.approval_steps'))
                    ->description(__('common.helpers.approval_steps'))
                    ->schema([
                        Forms\Components\Repeater::make('steps')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('approver_type')
                                    ->label(__('fields.approver_type'))
                                    ->options([
                                        'user' => __('common.approval.specific_user'),
                                        'role' => __('common.approval.role'),
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('approver_id')
                                    ->label(__('fields.approver_id'))
                                    ->options(function (Forms\Get $get) {
                                        return match ($get('approver_type')) {
                                            'user' => User::pluck('name', 'id')->toArray(),
                                            'role' => Role::pluck('name', 'id')->toArray(),
                                            default => [],
                                        };
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('action_type')
                                    ->label(__('fields.action_type'))
                                    ->options(ApprovalActionType::class)
                                    ->default(ApprovalActionType::Approval)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->orderColumn('step_order')
                            ->addActionLabel(__('common.buttons.add_step'))
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['approver_type'], $state['action_type'])
                                    ? (ApprovalActionType::tryFrom($state['action_type'] ?? '')?->getLabel() ?? $state['action_type']) . ' - ' . match ($state['approver_type'] ?? '') {
                                        'user' => User::find($state['approver_id'] ?? 0)?->name ?? __('common.approval.specific_user'),
                                        'role' => Role::find($state['approver_id'] ?? 0)?->name ?? __('common.approval.role'),
                                        default => __('common.placeholders.select'),
                                    }
                                    : null
                            )
                            ->defaultItems(1)
                            ->minItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('target_label')
                    ->label(__('fields.target_type')),

                Tables\Columns\TextColumn::make('steps_count')
                    ->label(__('fields.steps_count'))
                    ->counts('steps')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('conditions')
                    ->label(__('fields.conditions'))
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '-';
                        }
                        $parts = [];
                        if (isset($state['min_amount'])) {
                            $parts[] = __('fields.min_amount') . ': ₩' . number_format($state['min_amount']);
                        }
                        if (isset($state['max_amount'])) {
                            $parts[] = __('fields.max_amount') . ': ₩' . number_format($state['max_amount']);
                        }
                        return implode(', ', $parts) ?: '-';
                    }),

                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('fields.is_default'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y.m.d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('target_type')
                    ->label(__('fields.target_type'))
                    ->options([
                        'App\\Models\\PurchaseOrder' => __('common.target_types.purchase_order'),
                        'App\\Models\\Expense' => __('common.target_types.expense'),
                        'App\\Models\\Leave' => __('common.target_types.leave'),
                        'App\\Models\\Timesheet' => __('common.target_types.timesheet'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fields.is_active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalFlows::route('/'),
            'create' => Pages\CreateApprovalFlow::route('/create'),
            'edit' => Pages\EditApprovalFlow::route('/{record}/edit'),
        ];
    }
}
