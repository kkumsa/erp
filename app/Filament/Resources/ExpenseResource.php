<?php

namespace App\Filament\Resources;

use App\Enums\ExpenseStatus;
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

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.expense');
    }

    public static function getModelLabel(): string
    {
        return __('models.expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.expense_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.expense_info'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('fields.title'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label(__('fields.category_id'))
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('employee_id')
                            ->label(__('fields.employee_id'))
                            ->relationship('employee', 'employee_code')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name)
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('project_id')
                            ->label(__('fields.project_id'))
                            ->relationship('project', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('supplier_id')
                            ->label(__('fields.supplier_id'))
                            ->relationship('supplier', 'company_name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('expense_date')
                            ->label(__('fields.expense_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('fields.amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->required(),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label(__('fields.tax_amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->default(0),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.approval'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(ExpenseStatus::class)
                            ->default(ExpenseStatus::Pending)
                            ->required(),

                        Forms\Components\Select::make('approved_by')
                            ->label(__('fields.approved_by'))
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('fields.rejection_reason'))
                            ->visible(fn (Forms\Get $get) => $get('status') === ExpenseStatus::Rejected->value),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.expense_info'))
                    ->id('expense-info')
                    ->description(fn ($record) => $record->expense_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('expense_number')
                            ->label(__('fields.expense_number')),

                        Infolists\Components\TextEntry::make('title')
                            ->label(__('fields.title')),

                        Infolists\Components\TextEntry::make('category.name')
                            ->label(__('fields.category')),

                        Infolists\Components\TextEntry::make('employee.user.name')
                            ->label(__('fields.employee')),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('fields.project'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('supplier.company_name')
                            ->label(__('fields.supplier'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('expense_date')
                            ->label(__('fields.expense_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label(__('fields.tax_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label(__('fields.total_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.approval'))
                    ->id('expense-approval')
                    ->description(fn ($record) => $record->status?->getLabel())
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state?->color() ?? 'gray'),

                        Infolists\Components\TextEntry::make('approver.name')
                            ->label(__('fields.approver'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('approved_at')
                            ->label(__('fields.approved_at'))
                            ->dateTime('Y.m.d H:i')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label(__('fields.rejection_reason'))
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
                    ->label(__('fields.expense_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('fields.category')),

                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('fields.employee')),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('expense_date')
                    ->label(__('fields.expense_date'))
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(ExpenseStatus::class),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('fields.category_id'))
                    ->relationship('category', 'name'),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('common.buttons.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === ExpenseStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => ExpenseStatus::Approved,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label(__('common.buttons.reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === ExpenseStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('fields.rejection_reason'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => ExpenseStatus::Rejected,
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
