<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ProductCategoryResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'setting';

    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = '시스템설정';

    protected static ?string $navigationLabel = '상품 카테고리';

    protected static ?string $modelLabel = '상품 카테고리';

    protected static ?string $pluralModelLabel = '상품 카테고리';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('상품 카테고리 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('카테고리명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('코드')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('parent_id')
                            ->label('상위 카테고리')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('없음 (최상위 카테고리)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('정렬')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('카테고리명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('코드')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('상위 카테고리')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('정렬')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화'),
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
                Infolists\Components\Section::make('상품 카테고리 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label('카테고리명'),
                        Infolists\Components\TextEntry::make('code')->label('코드'),
                        Infolists\Components\TextEntry::make('parent.name')->label('상위 카테고리')->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')->label('활성화')->boolean(),
                        Infolists\Components\TextEntry::make('sort_order')->label('정렬'),
                        Infolists\Components\TextEntry::make('description')->label('설명')->columnSpanFull(),
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
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
