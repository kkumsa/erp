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

    protected static ?string $permissionPrefix = 'product';

    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.product_category');
    }

    public static function getModelLabel(): string
    {
        return __('models.product_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.product_category_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.product_category_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.category_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('parent_id')
                            ->label(__('fields.parent_id'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.none_top_category')),

                        Forms\Components\Select::make('sales_account_id')
                            ->label(__('fields.sales_account_id'))
                            ->relationship('salesAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.select'))
                            ->helperText(__('common.helpers.sales_account')),

                        Forms\Components\Select::make('purchase_account_id')
                            ->label(__('fields.purchase_account_id'))
                            ->relationship('purchaseAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.select'))
                            ->helperText(__('common.helpers.purchase_account')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('fields.sort_order'))
                            ->numeric()
                            ->default(0),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
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
                    ->label(__('fields.category_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('fields.parent_id'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('salesAccount.name')
                    ->label(__('fields.sales_account_id'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('purchaseAccount.name')
                    ->label(__('fields.purchase_account_id'))
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('fields.sort_order'))
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fields.is_active')),
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
                Infolists\Components\Section::make(__('common.sections.product_category_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.category_name')),
                        Infolists\Components\TextEntry::make('code')
                            ->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('parent.name')
                            ->label(__('fields.parent_id'))
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('salesAccount.name')
                            ->label(__('fields.sales_account_id'))
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('purchaseAccount.name')
                            ->label(__('fields.purchase_account_id'))
                            ->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('fields.is_active'))
                            ->boolean(),
                        Infolists\Components\TextEntry::make('sort_order')
                            ->label(__('fields.sort_order')),
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),
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
