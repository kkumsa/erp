<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class WarehouseResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'warehouse';

    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.warehouse');
    }

    public static function getModelLabel(): string
    {
        return __('models.warehouse');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.warehouse_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.warehouse_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.warehouse_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('address')
                            ->label(__('fields.address'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('fields.phone'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Select::make('manager_id')
                            ->label(__('fields.manager'))
                            ->relationship('manager', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\Toggle::make('is_default')
                            ->label(__('fields.is_default')),

                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.memo'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.warehouse_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label(__('fields.manager'))
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('fields.is_default'))
                    ->boolean(),
            ])
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
                Infolists\Components\Section::make(__('common.sections.warehouse_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('code')
                            ->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.warehouse_name')),
                        Infolists\Components\TextEntry::make('address')
                            ->label(__('fields.address'))
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('fields.phone'))
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('manager.name')
                            ->label(__('fields.manager'))
                            ->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('fields.is_active'))
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_default')
                            ->label(__('fields.is_default'))
                            ->boolean(),
                        Infolists\Components\TextEntry::make('note')
                            ->label(__('fields.memo'))
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
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
