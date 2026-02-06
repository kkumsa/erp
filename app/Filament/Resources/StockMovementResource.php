<?php

namespace App\Filament\Resources;

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

    protected static ?string $navigationGroup = '재고/물류';

    protected static ?string $navigationLabel = '재고 이동';

    protected static ?string $modelLabel = '재고 이동';

    protected static ?string $pluralModelLabel = '재고 이동';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('창고')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('상품')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('type')
                    ->options([
                        '입고' => '입고',
                        '출고' => '출고',
                        '이동' => '이동',
                        '조정' => '조정',
                        '반품' => '반품',
                    ])
                    ->required()
                    ->label('유형'),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->label('수량'),

                Forms\Components\TextInput::make('unit_cost')
                    ->numeric()
                    ->label('단가'),

                Forms\Components\Select::make('destination_warehouse_id')
                    ->relationship('destinationWarehouse', 'name')
                    ->label('목적지 창고')
                    ->searchable()
                    ->preload(),

                Forms\Components\Textarea::make('reason')
                    ->label('사유'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('재고 이동 정보')
                    ->id('stock-movement-info')
                    ->schema([
                        Infolists\Components\TextEntry::make('reference_number')
                            ->label('참조번호'),

                        Infolists\Components\TextEntry::make('warehouse.name')
                            ->label('창고'),

                        Infolists\Components\TextEntry::make('product.name')
                            ->label('상품'),

                        Infolists\Components\TextEntry::make('type')
                            ->label('유형')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '입고' => 'success',
                                '출고' => 'danger',
                                '이동' => 'info',
                                '조정' => 'warning',
                                '반품' => 'gray',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('quantity')
                            ->label('수량'),

                        Infolists\Components\TextEntry::make('before_quantity')
                            ->label('이전수량'),

                        Infolists\Components\TextEntry::make('after_quantity')
                            ->label('이후수량'),

                        Infolists\Components\TextEntry::make('unit_cost')
                            ->label('단가')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('destinationWarehouse.name')
                            ->label('목적지 창고'),

                        Infolists\Components\TextEntry::make('reason')
                            ->label('사유'),

                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('처리자'),
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
                    ->label('참조번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('창고')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('상품')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('유형')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '입고' => 'success',
                        '출고' => 'danger',
                        '이동' => 'info',
                        '조정' => 'warning',
                        '반품' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('수량')
                    ->sortable(),

                Tables\Columns\TextColumn::make('before_quantity')
                    ->label('이전수량')
                    ->sortable(),

                Tables\Columns\TextColumn::make('after_quantity')
                    ->label('이후수량')
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('처리자'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->dateTime('Y-m-d H:i')
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
