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

    protected static ?string $navigationGroup = '재무/회계';

    protected static ?string $navigationLabel = '청구서 항목';

    protected static ?string $modelLabel = '청구서 항목';

    protected static ?string $pluralModelLabel = '청구서 항목';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('invoice_id')
                    ->relationship('invoice', 'invoice_number')
                    ->label('청구서')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('상품')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('description')
                    ->label('설명'),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('unit')
                    ->label('단위'),

                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required()
                    ->label('단가'),

                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->label('할인율 %'),

                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->label('세율 %'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('청구서 항목 정보')
                    ->id('invoice-item-info')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice.invoice_number')
                            ->label('청구서'),

                        Infolists\Components\TextEntry::make('product.name')
                            ->label('상품'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('설명'),

                        Infolists\Components\TextEntry::make('quantity')
                            ->label('수량'),

                        Infolists\Components\TextEntry::make('unit')
                            ->label('단위'),

                        Infolists\Components\TextEntry::make('unit_price')
                            ->label('단가')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('discount')
                            ->label('할인율 %'),

                        Infolists\Components\TextEntry::make('tax_rate')
                            ->label('세율 %'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('금액')
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
                    ->label('청구서')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('상품')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('설명')
                    ->limit(30),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('수량')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('단가')
                    ->money('KRW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount')
                    ->label('할인율')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('금액')
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
