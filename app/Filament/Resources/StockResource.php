<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class StockResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'stock';

    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = '재고관리';

    protected static ?string $navigationLabel = '재고 현황';

    protected static ?string $modelLabel = '재고';

    protected static ?string $pluralModelLabel = '재고';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('재고 정보')
                    ->schema([
                        Forms\Components\Select::make('warehouse_id')
                            ->label('창고')
                            ->relationship('warehouse', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('product_id')
                            ->label('상품')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('수량')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('reserved_quantity')
                            ->label('예약 수량')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('창고')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('상품')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.code')
                    ->label('상품코드'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('수량'),

                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->label('예약'),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('가용 수량')
                    ->getStateUsing(fn ($record) => $record->quantity - $record->reserved_quantity),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label('창고')
                    ->relationship('warehouse', 'name'),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('상품')
                    ->relationship('product', 'name'),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
            ->actions([
                Tables\Actions\EditAction::make(),
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
                Infolists\Components\Section::make('재고 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('warehouse.name')->label('창고'),
                        Infolists\Components\TextEntry::make('product.name')->label('상품'),
                        Infolists\Components\TextEntry::make('product.code')->label('상품코드'),
                        Infolists\Components\TextEntry::make('quantity')->label('수량'),
                        Infolists\Components\TextEntry::make('reserved_quantity')->label('예약 수량'),
                        Infolists\Components\TextEntry::make('available_quantity')
                            ->label('가용 수량')
                            ->getStateUsing(fn ($record) => $record->quantity - $record->reserved_quantity),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
