<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = '재고관리';

    protected static ?string $navigationLabel = '상품 관리';

    protected static ?string $modelLabel = '상품';

    protected static ?string $pluralModelLabel = '상품';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('상품 정보')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('상품 코드 (SKU)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('name')
                            ->label('상품명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('카테고리')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('카테고리명')
                                    ->required(),
                                Forms\Components\TextInput::make('code')
                                    ->label('코드')
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('unit')
                            ->label('단위')
                            ->default('개')
                            ->maxLength(20),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('가격')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('매입가')
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\TextInput::make('selling_price')
                            ->label('판매가')
                            ->numeric()
                            ->prefix('₩'),
                    ])->columns(2),

                Forms\Components\Section::make('재고 설정')
                    ->schema([
                        Forms\Components\Toggle::make('is_stockable')
                            ->label('재고 관리')
                            ->default(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),

                        Forms\Components\TextInput::make('min_stock')
                            ->label('최소 재고')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('max_stock')
                            ->label('최대 재고')
                            ->numeric(),

                        Forms\Components\TextInput::make('barcode')
                            ->label('바코드')
                            ->maxLength(100),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('상품 이미지')
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
                    ->label('코드')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('상품명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('카테고리')
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('매입가')
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('판매가')
                    ->money('KRW'),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label('재고')
                    ->color(fn ($record) => $record->is_low_stock ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('카테고리')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화'),

                Tables\Filters\TernaryFilter::make('is_stockable')
                    ->label('재고 관리'),
            ])
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
