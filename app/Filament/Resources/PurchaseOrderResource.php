<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class PurchaseOrderResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'purchase_order';

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = '구매관리';

    protected static ?string $navigationLabel = '구매주문';

    protected static ?string $modelLabel = '구매주문';

    protected static ?string $pluralModelLabel = '구매주문';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('주문 정보')
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->label('공급업체')
                            ->relationship('supplier', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('project_id')
                            ->label('프로젝트')
                            ->relationship('project', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('order_date')
                            ->label('주문일')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('expected_date')
                            ->label('예상 납기일'),

                        Forms\Components\Textarea::make('shipping_address')
                            ->label('배송 주소')
                            ->rows(2),

                        Forms\Components\Textarea::make('note')
                            ->label('비고')
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make('주문 품목')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('제품')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('description', $product->name);
                                                $set('unit_price', $product->purchase_price ?? 0);
                                                $set('unit', $product->unit ?? '개');
                                            }
                                        }
                                    })
                                    ->live(),

                                Forms\Components\TextInput::make('description')
                                    ->label('품명/설명')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('수량')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        $set('amount', round(($state ?? 0) * ($get('unit_price') ?? 0), 2))
                                    ),

                                Forms\Components\TextInput::make('unit')
                                    ->label('단위')
                                    ->default('개')
                                    ->required(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('단가')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₩')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        $set('amount', round(($get('quantity') ?? 0) * ($state ?? 0), 2))
                                    ),

                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('세율(%)')
                                    ->numeric()
                                    ->default(10)
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('amount')
                                    ->label('금액')
                                    ->numeric()
                                    ->prefix('₩')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0),
                            ])
                            ->columns(4)
                            ->orderColumn('sort_order')
                            ->addActionLabel('품목 추가')
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(1)
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['description'] ?? null)
                                    ? ($state['description'] . ' - ₩' . number_format($state['amount'] ?? 0))
                                    : null
                            ),
                    ]),

                Forms\Components\Section::make('승인')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '초안' => '초안',
                                '승인요청' => '승인요청',
                                '승인' => '승인',
                                '발주완료' => '발주완료',
                                '부분입고' => '부분입고',
                                '입고완료' => '입고완료',
                                '취소' => '취소',
                            ])
                            ->default('초안')
                            ->required(),

                        Forms\Components\Select::make('approved_by')
                            ->label('승인자')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('주문 정보')
                    ->id('purchase-order-info')
                    ->description(fn ($record) => $record->po_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('po_number')
                            ->label('주문번호'),

                        Infolists\Components\TextEntry::make('supplier.company_name')
                            ->label('공급업체'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('프로젝트')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('order_date')
                            ->label('주문일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('expected_date')
                            ->label('예상 납기일')
                            ->date('Y-m-d')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('금액')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('shipping_address')
                            ->label('배송 주소')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('note')
                            ->label('비고')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('주문 품목')
                    ->id('purchase-order-items')
                    ->description(fn ($record) => $record->items->count() . '개 품목')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->label('품명'),

                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('수량')
                                    ->formatStateUsing(fn ($state, $record) => number_format($state, 0) . ' ' . ($record->unit ?? '개')),

                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label('단가')
                                    ->money('KRW'),

                                Infolists\Components\TextEntry::make('tax_rate')
                                    ->label('세율')
                                    ->formatStateUsing(fn ($state) => $state . '%'),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label('금액')
                                    ->money('KRW'),
                            ])
                            ->columns(5),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('소계')
                                    ->money('KRW'),

                                Infolists\Components\TextEntry::make('tax_amount')
                                    ->label('세액')
                                    ->money('KRW'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label('합계')
                                    ->money('KRW')
                                    ->weight('bold')
                                    ->size('lg'),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('승인')
                    ->id('purchase-order-approval')
                    ->description(fn ($record) => $record->status)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '초안' => 'gray',
                                '승인요청' => 'warning',
                                '승인' => 'info',
                                '발주완료' => 'primary',
                                '부분입고' => 'warning',
                                '입고완료' => 'success',
                                '취소' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('approver.name')
                            ->label('승인자')
                            ->placeholder('-'),
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
                Tables\Columns\TextColumn::make('po_number')
                    ->label('주문번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.company_name')
                    ->label('공급업체')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('금액')
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '초안' => 'gray',
                        '승인요청' => 'warning',
                        '승인' => 'info',
                        '발주완료' => 'primary',
                        '부분입고' => 'warning',
                        '입고완료' => 'success',
                        '취소' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('order_date')
                    ->label('주문일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_date')
                    ->label('납기일')
                    ->date('Y-m-d'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '초안' => '초안',
                        '승인요청' => '승인요청',
                        '승인' => '승인',
                        '발주완료' => '발주완료',
                        '부분입고' => '부분입고',
                        '입고완료' => '입고완료',
                        '취소' => '취소',
                    ]),

                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('공급업체')
                    ->relationship('supplier', 'company_name'),
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
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
