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

    protected static ?string $title = null;

    protected static ?string $modelLabel = null;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('models.invoice_item_plural');
    }

    public static function getModelLabel(): string
    {
        return __('models.invoice_item');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('fields.product_id'))
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
                    ->label(__('fields.description'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('quantity')
                    ->label(__('fields.quantity'))
                    ->numeric()
                    ->required()
                    ->default(1),

                Forms\Components\TextInput::make('unit')
                    ->label(__('fields.unit'))
                    ->default('개')
                    ->maxLength(20),

                Forms\Components\TextInput::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->numeric()
                    ->required()
                    ->prefix('₩'),

                Forms\Components\TextInput::make('discount')
                    ->label(__('fields.discount'))
                    ->numeric()
                    ->default(0)
                    ->suffix('%'),

                Forms\Components\TextInput::make('tax_rate')
                    ->label(__('fields.tax_rate'))
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
                    ->label(__('fields.description'))
                    ->limit(50),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('fields.quantity'))
                    ->numeric(),

                Tables\Columns\TextColumn::make('unit')
                    ->label(__('fields.unit')),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('discount')
                    ->label(__('fields.discount'))
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
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
