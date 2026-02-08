<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class AccountResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'account';

    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 9;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.account');
    }

    public static function getModelLabel(): string
    {
        return __('models.account');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.account_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.account_info'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.account_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label(__('fields.type'))
                            ->options(AccountType::class)
                            ->required(),

                        Forms\Components\Select::make('parent_id')
                            ->label(__('fields.parent_account'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.none')),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.account_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('fields.type'))
                    ->badge(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('fields.parent_account'))
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('fields.type'))
                    ->options(AccountType::class),

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
                Infolists\Components\Section::make(__('common.sections.account_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('code')->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('name')->label(__('fields.account_name')),
                        Infolists\Components\TextEntry::make('type')->label(__('fields.type'))->badge(),
                        Infolists\Components\TextEntry::make('parent.name')->label(__('fields.parent_account'))->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')->label(__('fields.is_active'))->boolean(),
                        Infolists\Components\TextEntry::make('sort_order')->label(__('fields.sort_order')),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
