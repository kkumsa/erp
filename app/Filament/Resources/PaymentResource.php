<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class PaymentResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'payment';

    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.payment');
    }

    public static function getModelLabel(): string
    {
        return __('models.payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.payment_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.payment_info'))
                    ->schema([
                        Forms\Components\DatePicker::make('payment_date')
                            ->label(__('fields.payment_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('fields.amount'))
                            ->numeric()
                            ->prefix('₩')
                            ->required(),

                        Forms\Components\Select::make('method')
                            ->label(__('fields.method'))
                            ->options(PaymentMethod::class)
                            ->required(),

                        Forms\Components\Select::make('account_id')
                            ->label(__('fields.account_id'))
                            ->relationship('account', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.select'))
                            ->helperText(__('common.helpers.account_for_payment')),

                        Forms\Components\TextInput::make('reference')
                            ->label(__('fields.reference'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.memo'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.payment_info'))
                    ->id('payment-info')
                    ->description(fn ($record) => $record->payment_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_number')
                            ->label(__('fields.payment_number')),

                        Infolists\Components\TextEntry::make('payment_date')
                            ->label(__('fields.payment_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('method')
                            ->label(__('fields.method'))
                            ->badge(),

                        Infolists\Components\TextEntry::make('account.name')
                            ->label(__('fields.account_id'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('reference')
                            ->label(__('fields.reference'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('recorder.name')
                            ->label(__('fields.recorder'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('note')
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
                Tables\Columns\TextColumn::make('payment_number')
                    ->label(__('fields.payment_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('fields.payment_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label(__('fields.method'))
                    ->badge(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('fields.account_id'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label(__('fields.recorder')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y.m.d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->label(__('fields.method'))
                    ->options(PaymentMethod::class),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
