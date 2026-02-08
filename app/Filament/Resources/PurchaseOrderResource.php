<?php

namespace App\Filament\Resources;

use App\Enums\PurchaseOrderStatus;
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

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.purchasing');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.purchase_order');
    }

    public static function getModelLabel(): string
    {
        return __('models.purchase_order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.purchase_order_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.order_info'))
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->label(__('fields.supplier_id'))
                            ->relationship('supplier', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('project_id')
                            ->label(__('fields.project_id'))
                            ->relationship('project', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('order_date')
                            ->label(__('fields.order_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('expected_date')
                            ->label(__('fields.expected_date')),

                        Forms\Components\Textarea::make('shipping_address')
                            ->label(__('fields.shipping_address'))
                            ->rows(2),

                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.note'))
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.order_items'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('fields.product_id'))
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
                                    ->label(__('fields.description'))
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('fields.quantity'))
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        $set('amount', round(($state ?? 0) * ($get('unit_price') ?? 0), 2))
                                    ),

                                Forms\Components\TextInput::make('unit')
                                    ->label(__('fields.unit'))
                                    ->default('개')
                                    ->required(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label(__('fields.unit_price'))
                                    ->numeric()
                                    ->required()
                                    ->prefix('₩')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        $set('amount', round(($get('quantity') ?? 0) * ($state ?? 0), 2))
                                    ),

                                Forms\Components\TextInput::make('tax_rate')
                                    ->label(__('fields.tax_rate'))
                                    ->numeric()
                                    ->default(10)
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('amount')
                                    ->label(__('fields.amount'))
                                    ->numeric()
                                    ->prefix('₩')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0),
                            ])
                            ->columns(4)
                            ->orderColumn('sort_order')
                            ->addActionLabel(__('common.buttons.add_item'))
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(1)
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['description'] ?? null)
                                    ? ($state['description'] . ' - ₩' . number_format($state['amount'] ?? 0))
                                    : null
                            ),
                    ]),

                Forms\Components\Section::make(__('common.sections.approval'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(PurchaseOrderStatus::class)
                            ->default(PurchaseOrderStatus::Draft)
                            ->required(),

                        Forms\Components\Select::make('approved_by')
                            ->label(__('fields.approved_by'))
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
                Infolists\Components\Section::make(__('common.sections.order_info'))
                    ->id('purchase-order-info')
                    ->description(fn ($record) => $record->po_number)
                    ->schema([
                        Infolists\Components\TextEntry::make('po_number')
                            ->label(__('fields.po_number')),

                        Infolists\Components\TextEntry::make('supplier.company_name')
                            ->label(__('fields.supplier')),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('fields.project'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('order_date')
                            ->label(__('fields.order_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('expected_date')
                            ->label(__('fields.expected_date'))
                            ->date('Y.m.d')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label(__('fields.total_amount'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('shipping_address')
                            ->label(__('fields.shipping_address'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('note')
                            ->label(__('fields.note'))
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.order_items'))
                    ->id('purchase-order-items')
                    ->description(fn ($record) => __('common.general.items_count', ['count' => $record->items->count()]))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('fields.description')),

                                Infolists\Components\TextEntry::make('quantity')
                                    ->label(__('fields.quantity'))
                                    ->formatStateUsing(fn ($state, $record) => number_format($state, 0) . ' ' . ($record->unit ?? '개')),

                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label(__('fields.unit_price'))
                                    ->money('KRW'),

                                Infolists\Components\TextEntry::make('tax_rate')
                                    ->label(__('fields.tax_rate'))
                                    ->formatStateUsing(fn ($state) => $state . '%'),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label(__('fields.amount'))
                                    ->money('KRW'),
                            ])
                            ->columns(5),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label(__('fields.subtotal'))
                                    ->money('KRW'),

                                Infolists\Components\TextEntry::make('tax_amount')
                                    ->label(__('fields.tax_amount'))
                                    ->money('KRW'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label(__('fields.total_amount'))
                                    ->money('KRW')
                                    ->weight('bold')
                                    ->size('lg'),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.approval'))
                    ->id('purchase-order-approval')
                    ->description(fn ($record) => $record->status?->getLabel())
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state?->color() ?? 'gray'),

                        Infolists\Components\TextEntry::make('approver.name')
                            ->label(__('fields.approved_by'))
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
                    ->label(__('fields.po_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.company_name')
                    ->label(__('fields.supplier'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('fields.total_amount'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('order_date')
                    ->label(__('fields.order_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_date')
                    ->label(__('fields.expected_date'))
                    ->date('Y.m.d'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(PurchaseOrderStatus::class),

                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label(__('fields.supplier_id'))
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
