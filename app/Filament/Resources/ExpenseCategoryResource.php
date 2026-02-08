<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseCategoryResource\Pages;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ExpenseCategoryResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'expense';

    protected static ?string $model = ExpenseCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.expense_category');
    }

    public static function getModelLabel(): string
    {
        return __('models.expense_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.expense_category_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.expense_category_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.category_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('account_id')
                            ->label(__('fields.linked_account'))
                            ->relationship('account', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.none')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\TextInput::make('color')
                            ->label(__('fields.color'))
                            ->maxLength(50),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.category_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('fields.linked_account'))
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fields.is_active')),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.expense_category_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label(__('fields.category_name')),
                        Infolists\Components\TextEntry::make('code')->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('account.name')->label(__('fields.linked_account'))->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')->label(__('fields.is_active'))->boolean(),
                        Infolists\Components\TextEntry::make('color')->label(__('fields.color'))->badge(),
                        Infolists\Components\TextEntry::make('description')->label(__('fields.description'))->columnSpanFull(),
                    ])->columns(2),
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
            'index' => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'edit' => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
