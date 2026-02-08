<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Enums\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = null;

    protected static ?string $modelLabel = null;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('models.payment_plural');
    }

    public static function getModelLabel(): string
    {
        return __('models.payment');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('payment_date')
                    ->label(__('fields.payment_date'))
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('amount')
                    ->label(__('fields.amount'))
                    ->numeric()
                    ->required()
                    ->prefix('₩'),

                Forms\Components\Select::make('method')
                    ->label(__('fields.method'))
                    ->options(PaymentMethod::class)
                    ->required()
                    ->default(PaymentMethod::BankTransfer),

                Forms\Components\Select::make('account_id')
                    ->label(__('fields.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('common.placeholders.select')),

                Forms\Components\TextInput::make('reference')
                    ->label(__('fields.reference'))
                    ->maxLength(100),

                Forms\Components\Textarea::make('note')
                    ->label(__('fields.memo'))
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label(__('fields.payment_number')),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('fields.payment_date'))
                    ->date('Y.m.d'),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('method')
                    ->label(__('fields.method'))
                    ->badge(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('fields.account_id'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('reference')
                    ->label(__('fields.reference')),

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label(__('fields.recorder')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['recorded_by'] = auth()->id();
                        return $data;
                    }),
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
}
