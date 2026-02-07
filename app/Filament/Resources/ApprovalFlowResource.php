<?php

namespace App\Filament\Resources;

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

    protected static ?string $navigationGroup = '시스템설정';

    protected static ?string $navigationLabel = '결재라인 관리';

    protected static ?string $modelLabel = '결재라인';

    protected static ?string $pluralModelLabel = '결재라인';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('결재라인 이름')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('예: 구매주문 기본 결재'),

                        Forms\Components\Select::make('target_type')
                            ->label('대상 유형')
                            ->options([
                                'App\\Models\\PurchaseOrder' => '구매주문',
                                'App\\Models\\Expense' => '비용',
                                'App\\Models\\Leave' => '휴가',
                                'App\\Models\\Timesheet' => '근무기록',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('is_default')
                            ->label('기본 결재라인')
                            ->helperText('해당 대상 유형의 기본 결재라인으로 설정합니다. 조건에 매칭되는 결재라인이 없을 때 사용됩니다.')
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('활성')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('적용 조건')
                    ->description('조건을 설정하면 해당 조건에 맞는 문서에 자동으로 이 결재라인이 적용됩니다. 조건이 없으면 "기본 결재라인"으로만 사용됩니다.')
                    ->schema([
                        Forms\Components\TextInput::make('conditions.min_amount')
                            ->label('최소 금액')
                            ->numeric()
                            ->prefix('₩')
                            ->placeholder('0'),

                        Forms\Components\TextInput::make('conditions.max_amount')
                            ->label('최대 금액')
                            ->numeric()
                            ->prefix('₩')
                            ->placeholder('무제한'),
                    ])->columns(2),

                Forms\Components\Section::make('결재 단계')
                    ->description('결재 단계를 순서대로 추가하세요. 순서는 자동으로 부여됩니다.')
                    ->schema([
                        Forms\Components\Repeater::make('steps')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('approver_type')
                                    ->label('승인자 유형')
                                    ->options([
                                        'user' => '특정 사용자',
                                        'role' => '역할',
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('approver_id')
                                    ->label('승인자')
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
                                    ->label('결재 유형')
                                    ->options([
                                        '승인' => '승인 (승인/반려 가능)',
                                        '합의' => '합의 (의견 제출, 거부 불가)',
                                        '참조' => '참조 (열람만, 알림 발송)',
                                    ])
                                    ->default('승인')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->orderColumn('step_order')
                            ->addActionLabel('단계 추가')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['approver_type'], $state['action_type'])
                                    ? ($state['action_type'] ?? '승인') . ' - ' . match ($state['approver_type'] ?? '') {
                                        'user' => User::find($state['approver_id'] ?? 0)?->name ?? '사용자 선택',
                                        'role' => Role::find($state['approver_id'] ?? 0)?->name ?? '역할 선택',
                                        default => '선택 필요',
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
                    ->label('이름')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('target_label')
                    ->label('대상 유형'),

                Tables\Columns\TextColumn::make('steps_count')
                    ->label('단계 수')
                    ->counts('steps')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('conditions')
                    ->label('조건')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '-';
                        }
                        $parts = [];
                        if (isset($state['min_amount'])) {
                            $parts[] = '최소: ₩' . number_format($state['min_amount']);
                        }
                        if (isset($state['max_amount'])) {
                            $parts[] = '최대: ₩' . number_format($state['max_amount']);
                        }
                        return implode(', ', $parts) ?: '-';
                    }),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('기본')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('target_type')
                    ->label('대상 유형')
                    ->options([
                        'App\\Models\\PurchaseOrder' => '구매주문',
                        'App\\Models\\Expense' => '비용',
                        'App\\Models\\Leave' => '휴가',
                        'App\\Models\\Timesheet' => '근무기록',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성 여부'),
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
