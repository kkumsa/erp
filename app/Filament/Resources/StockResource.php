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

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.stock');
    }

    public static function getModelLabel(): string
    {
        return __('models.stock');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.stock_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.stock_info'))
                    ->schema([
                        Forms\Components\Select::make('warehouse_id')
                            ->label(__('fields.warehouse_id'))
                            ->relationship('warehouse', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('product_id')
                            ->label(__('fields.product_id'))
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('quantity')
                            ->label(__('fields.quantity'))
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('reserved_quantity')
                            ->label(__('fields.reserved_quantity'))
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
                    ->label(__('fields.warehouse'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('fields.product'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.code')
                    ->label(__('fields.code')),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('fields.quantity')),

                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->label(__('fields.reserved_quantity')),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label(__('fields.available_quantity'))
                    ->getStateUsing(fn ($record) => $record->quantity - $record->reserved_quantity),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label(__('fields.warehouse_id'))
                    ->relationship('warehouse', 'name'),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label(__('fields.product_id'))
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
                Infolists\Components\Section::make(__('common.sections.stock_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('warehouse.name')
                            ->label(__('fields.warehouse')),
                        Infolists\Components\TextEntry::make('product.name')
                            ->label(__('fields.product')),
                        Infolists\Components\TextEntry::make('product.code')
                            ->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('quantity')
                            ->label(__('fields.quantity')),
                        Infolists\Components\TextEntry::make('reserved_quantity')
                            ->label(__('fields.reserved_quantity')),
                        Infolists\Components\TextEntry::make('available_quantity')
                            ->label(__('fields.available_quantity'))
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
