<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankDepositResource\Pages;
use App\Models\BankDeposit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class BankDepositResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'payment';

    protected static ?string $model = BankDeposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.bank_deposit');
    }

    public static function getModelLabel(): string
    {
        return __('models.bank_deposit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.bank_deposit_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.deposit_info'))
                    ->schema([
                        Forms\Components\DateTimePicker::make('deposited_at')
                            ->label(__('fields.deposited_at'))
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('depositor_name')
                            ->label(__('fields.depositor_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('fields.amount'))
                            ->numeric()
                            ->required()
                            ->prefix('₩'),

                        Forms\Components\TextInput::make('transaction_number')
                            ->label(__('fields.transaction_number'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bank_account')
                            ->label(__('fields.bank_account'))
                            ->maxLength(255)
                            ->placeholder(__('common.placeholders.example_bank_account')),

                        Forms\Components\Textarea::make('memo')
                            ->label(__('fields.memo'))
                            ->rows(2),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.deposit_info'))
                    ->id('bank-deposit-info')
                    ->schema([
                        Infolists\Components\TextEntry::make('deposited_at')
                            ->label(__('fields.deposited_at'))
                            ->dateTime('Y.m.d H:i'),

                        Infolists\Components\TextEntry::make('depositor_name')
                            ->label(__('fields.depositor_name')),

                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('transaction_number')
                            ->label(__('fields.transaction_number'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('bank_account')
                            ->label(__('fields.bank_account'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('processed_at')
                            ->label(__('fields.processed_at'))
                            ->formatStateUsing(fn ($state) => $state ? __('common.statuses.processed') . ' (' . $state->format('Y-m-d H:i') . ')' : __('common.statuses.unprocessed'))
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'warning'),

                        Infolists\Components\TextEntry::make('payment.payment_number')
                            ->label(__('fields.matched_payment'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('memo')
                            ->label(__('fields.memo'))
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
                Tables\Columns\TextColumn::make('deposited_at')
                    ->label(__('fields.deposited_at'))
                    ->dateTime('m-d, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('depositor_name')
                    ->label(__('fields.depositor_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_number')
                    ->label(__('fields.transaction_number'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('bank_account')
                    ->label(__('fields.bank_account'))
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_processed')
                    ->label(__('fields.is_processed'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->processed_at !== null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y.m.d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('deposited_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('processed')
                    ->label(__('fields.processed_at'))
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('processed_at'),
                        false: fn ($query) => $query->whereNull('processed_at'),
                    ),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
            ->actions([
                Tables\Actions\ViewAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankDeposits::route('/'),
            'create' => Pages\CreateBankDeposit::route('/create'),
            'edit' => Pages\EditBankDeposit::route('/{record}/edit'),
        ];
    }
}
