<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderItemResource\Pages;
use App\Models\PurchaseOrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class PurchaseOrderItemResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'purchase_order';

    protected static ?string $model = PurchaseOrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.purchasing');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.purchase_order_item');
    }

    public static function getModelLabel(): string
    {
        return __('models.purchase_order_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.purchase_order_item_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'po_number')
                    ->label(__('fields.po_number'))
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label(__('fields.product_id'))
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('description')
                    ->label(__('fields.description')),

                Forms\Components\TextInput::make('quantity')
                    ->label(__('fields.quantity'))
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('unit')
                    ->label(__('fields.unit')),

                Forms\Components\TextInput::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('tax_rate')
                    ->label(__('fields.tax_rate'))
                    ->numeric(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.purchase_order_item_info'))
                    ->id('purchase-order-item-info')
                    ->schema([
                        Infolists\Components\TextEntry::make('purchaseOrder.po_number')
                            ->label(__('fields.po_number')),

                        Infolists\Components\TextEntry::make('product.name')
                            ->label(__('fields.product')),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description')),

                        Infolists\Components\TextEntry::make('quantity')
                            ->label(__('fields.quantity')),

                        Infolists\Components\TextEntry::make('unit')
                            ->label(__('fields.unit')),

                        Infolists\Components\TextEntry::make('unit_price')
                            ->label(__('fields.unit_price'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('tax_rate')
                            ->label(__('fields.tax_rate')),

                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('received_quantity')
                            ->label(__('fields.received_quantity')),
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
                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label(__('fields.po_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('fields.product'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('fields.quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('received_quantity')
                    ->label(__('fields.received_quantity'))
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
            'index' => Pages\ListPurchaseOrderItems::route('/'),
            'create' => Pages\CreatePurchaseOrderItem::route('/create'),
            'edit' => Pages\EditPurchaseOrderItem::route('/{record}/edit'),
        ];
    }
}
