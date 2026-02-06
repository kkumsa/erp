<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ExpenseResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'expense';

    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = '재무/회계';

    protected static ?string $navigationLabel = '비용 관리';

    protected static ?string $modelLabel = '비용';

    protected static ?string $pluralModelLabel = '비용';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('비용 정보')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('카테고리')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('employee_id')
                            ->label('직원')
                            ->relationship('employee', 'employee_code')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name)
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('project_id')
                            ->label('프로젝트')
                            ->relationship('project', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('supplier_id')
                            ->label('공급업체')
                            ->relationship('supplier', 'company_name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('expense_date')
                            ->label('비용일')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('amount')
                            ->label('금액')
                            ->numeric()
                            ->prefix('₩')
                            ->required(),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('세액')
                            ->numeric()
                            ->prefix('₩')
                            ->default(0),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('승인')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '대기' => '대기',
                                '승인' => '승인',
                                '반려' => '반려',
                                '취소' => '취소',
                            ])
                            ->default('대기')
                            ->required(),

                        Forms\Components\Select::make('approved_by')
                            ->label('승인자')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('반려 사유')
                            ->visible(fn (Forms\Get $get) => $get('status') === '반려'),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('비용 정보')
                    ->id('expense-info')
                    ->description(fn ($record) => $record->expense_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('expense_number')
                            ->label('비용번호'),

                        Infolists\Components\TextEntry::make('title')
                            ->label('제목'),

                        Infolists\Components\TextEntry::make('category.name')
                            ->label('카테고리'),

                        Infolists\Components\TextEntry::make('employee.user.name')
                            ->label('직원'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('프로젝트')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('supplier.company_name')
                            ->label('공급업체')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('expense_date')
                            ->label('비용일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('금액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label('세액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('합계')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('설명')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('승인')
                    ->id('expense-approval')
                    ->description(fn ($record) => $record->status)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '대기' => 'warning',
                                '승인' => 'success',
                                '반려' => 'danger',
                                '취소' => 'gray',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('approver.name')
                            ->label('승인자')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('승인일시')
                            ->dateTime('Y-m-d H:i')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('반려 사유')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_number')
                    ->label('비용번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('카테고리'),

                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('직원'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('금액')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '대기' => 'warning',
                        '승인' => 'success',
                        '반려' => 'danger',
                        '취소' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('expense_date')
                    ->label('비용일')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '대기' => '대기',
                        '승인' => '승인',
                        '반려' => '반려',
                        '취소' => '취소',
                    ]),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('카테고리')
                    ->relationship('category', 'name'),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('승인')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === '대기')
                    ->action(function ($record) {
                        $record->update([
                            'status' => '승인',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('반려')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === '대기')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('반려 사유')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => '반려',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
