<?php

namespace App\Filament\Resources;

use App\Enums\StockMovementType;
use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class StockMovementResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'stock';

    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.inventory_logistics');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.stock_movement');
    }

    public static function getModelLabel(): string
    {
        return __('models.stock_movement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.stock_movement_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label(__('fields.warehouse_id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label(__('fields.product_id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('type')
                    ->options(StockMovementType::class)
                    ->required()
                    ->label(__('fields.type')),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->label(__('fields.quantity')),

                Forms\Components\TextInput::make('unit_cost')
                    ->numeric()
                    ->label(__('fields.unit_cost')),

                Forms\Components\Select::make('destination_warehouse_id')
                    ->relationship('destinationWarehouse', 'name')
                    ->label(__('fields.destination_warehouse_id'))
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('reason')
                    ->label(__('fields.reason')),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.stock_movement_info'))
                    ->id('stock-movement-info')
                    ->schema([
                        Infolists\Components\TextEntry::make('reference_number')
                            ->label(__('fields.reference_number')),

                        Infolists\Components\TextEntry::make('warehouse.name')
                            ->label(__('fields.warehouse')),

                        Infolists\Components\TextEntry::make('product.name')
                            ->label(__('fields.product')),

                        Infolists\Components\TextEntry::make('type')
                            ->label(__('fields.type'))
                            ->badge()
                            ->color(fn ($state) => $state?->color() ?? 'gray'),

                        Infolists\Components\TextEntry::make('quantity')
                            ->label(__('fields.quantity')),

                        Infolists\Components\TextEntry::make('before_quantity')
                            ->label(__('fields.before_quantity')),

                        Infolists\Components\TextEntry::make('after_quantity')
                            ->label(__('fields.after_quantity')),

                        Infolists\Components\TextEntry::make('unit_cost')
                            ->label(__('fields.unit_cost'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('destinationWarehouse.name')
                            ->label(__('fields.destination_warehouse_id')),

                        Infolists\Components\TextEntry::make('reason')
                            ->label(__('fields.reason')),

                        Infolists\Components\TextEntry::make('creator.name')
                            ->label(__('fields.creator')),
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
                Tables\Columns\TextColumn::make('reference_number')
                    ->label(__('fields.reference_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label(__('fields.warehouse'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('fields.product'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('fields.type'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('fields.quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('before_quantity')
                    ->label(__('fields.before_quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('after_quantity')
                    ->label(__('fields.after_quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('fields.creator')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }
}
