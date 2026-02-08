<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ProductResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'product';

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.product');
    }

    public static function getModelLabel(): string
    {
        return __('models.product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.product_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.product_info'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.product_code_sku'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.product_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label(__('fields.category_id'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('fields.category_name'))
                                    ->required(),
                                Forms\Components\TextInput::make('code')
                                    ->label(__('fields.code'))
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('unit')
                            ->label(__('fields.unit'))
                            ->default('개')
                            ->maxLength(20),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.price_info'))
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->label(__('fields.purchase_price'))
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\TextInput::make('selling_price')
                            ->label(__('fields.selling_price'))
                            ->numeric()
                            ->prefix('₩'),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.stock_settings'))
                    ->schema([
                        Forms\Components\Toggle::make('is_stockable')
                            ->label(__('fields.is_stockable'))
                            ->default(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\TextInput::make('min_stock')
                            ->label(__('fields.min_stock'))
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('max_stock')
                            ->label(__('fields.max_stock'))
                            ->numeric(),

                        Forms\Components\TextInput::make('barcode')
                            ->label(__('fields.barcode'))
                            ->maxLength(100),

                        Forms\Components\FileUpload::make('image_path')
                            ->label(__('fields.image_path'))
                            ->image()
                            ->directory('products'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('')
                    ->circular(),

                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.product_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('fields.category'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchase_price')
                    ->label(__('fields.purchase_price'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label(__('fields.selling_price'))
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label(__('fields.total_stock'))
                    ->color(fn ($record) => $record->is_low_stock ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('fields.category_id'))
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fields.is_active')),

                Tables\Filters\TernaryFilter::make('is_stockable')
                    ->label(__('fields.is_stockable')),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.product_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('code')
                            ->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.product_name')),
                        Infolists\Components\TextEntry::make('category.name')
                            ->label(__('fields.category')),
                        Infolists\Components\TextEntry::make('unit')
                            ->label(__('fields.unit')),
                        Infolists\Components\TextEntry::make('barcode')
                            ->label(__('fields.barcode')),
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),
                    ])->columns(2),
                Infolists\Components\Section::make(__('common.sections.price_stock'))
                    ->schema([
                        Infolists\Components\TextEntry::make('purchase_price')
                            ->label(__('fields.purchase_price'))
                            ->money('KRW'),
                        Infolists\Components\TextEntry::make('selling_price')
                            ->label(__('fields.selling_price'))
                            ->money('KRW'),
                        Infolists\Components\TextEntry::make('total_stock')
                            ->label(__('fields.total_stock')),
                        Infolists\Components\IconEntry::make('is_stockable')
                            ->label(__('fields.is_stockable'))
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('fields.is_active'))
                            ->boolean(),
                    ])->columns(3),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
