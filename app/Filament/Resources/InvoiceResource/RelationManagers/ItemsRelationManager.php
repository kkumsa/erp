<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = '청구 항목';

    protected static ?string $modelLabel = '항목';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('상품')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $product = \App\Models\Product::find($state);
                            if ($product) {
                                $set('description', $product->name);
                                $set('unit', $product->unit);
                                $set('unit_price', $product->selling_price);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('description')
                    ->label('설명')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('quantity')
                    ->label('수량')
                    ->numeric()
                    ->required()
                    ->default(1),

                Forms\Components\TextInput::make('unit')
                    ->label('단위')
                    ->default('개')
                    ->maxLength(20),

                Forms\Components\TextInput::make('unit_price')
                    ->label('단가')
                    ->numeric()
                    ->required()
                    ->prefix('₩'),

                Forms\Components\TextInput::make('discount')
                    ->label('할인율')
                    ->numeric()
                    ->default(0)
                    ->suffix('%'),

                Forms\Components\TextInput::make('tax_rate')
                    ->label('세율')
                    ->numeric()
                    ->default(10)
                    ->suffix('%'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('설명')
                    ->limit(50),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('수량')
                    ->numeric(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('단위'),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('단가')
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('discount')
                    ->label('할인')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('금액')
                    ->money('KRW'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
