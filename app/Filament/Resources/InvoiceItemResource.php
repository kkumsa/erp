<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceItemResource\Pages;
use App\Models\InvoiceItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class InvoiceItemResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'invoice';

    protected static ?string $model = InvoiceItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.invoice_item');
    }

    public static function getModelLabel(): string
    {
        return __('models.invoice_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.invoice_item_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('invoice_id')
                    ->relationship('invoice', 'invoice_number')
                    ->label(__('fields.invoice_id'))
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
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('unit')
                    ->label(__('fields.unit')),

                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required()
                    ->label(__('fields.unit_price')),

                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->label(__('fields.discount')),

                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->label(__('fields.tax_rate')),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.invoice_item_info'))
                    ->id('invoice-item-info')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice.invoice_number')
                            ->label(__('fields.invoice')),

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

                        Infolists\Components\TextEntry::make('discount')
                            ->label(__('fields.discount')),

                        Infolists\Components\TextEntry::make('tax_rate')
                            ->label(__('fields.tax_rate')),

                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('fields.amount'))
                            ->money('KRW'),
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
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label(__('fields.invoice'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('fields.product'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('fields.description'))
                    ->limit(30),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('fields.quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label(__('fields.unit_price'))
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount')
                    ->label(__('fields.discount'))
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('KRW')
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
            'index' => Pages\ListInvoiceItems::route('/'),
            'create' => Pages\CreateInvoiceItem::route('/create'),
            'edit' => Pages\EditInvoiceItem::route('/{record}/edit'),
        ];
    }
}
