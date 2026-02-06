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

    protected static ?string $navigationGroup = '재고관리';

    protected static ?string $navigationLabel = '창고 관리';

    protected static ?string $modelLabel = '창고';

    protected static ?string $pluralModelLabel = '창고';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('창고 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('창고명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('코드')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('address')
                            ->label('주소')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('전화번호')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Select::make('manager_id')
                            ->label('관리자')
                            ->relationship('manager', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),

                        Forms\Components\Toggle::make('is_default')
                            ->label('기본 창고'),

                        Forms\Components\Textarea::make('note')
                            ->label('메모')
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
                    ->label('코드')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('창고명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label('관리자')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('기본 창고')
                    ->boolean(),
            ])
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
                Infolists\Components\Section::make('창고 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('code')->label('코드'),
                        Infolists\Components\TextEntry::make('name')->label('창고명'),
                        Infolists\Components\TextEntry::make('address')->label('주소')->placeholder('-'),
                        Infolists\Components\TextEntry::make('phone')->label('전화번호')->placeholder('-'),
                        Infolists\Components\TextEntry::make('manager.name')->label('관리자')->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')->label('활성화')->boolean(),
                        Infolists\Components\IconEntry::make('is_default')->label('기본 창고')->boolean(),
                        Infolists\Components\TextEntry::make('note')->label('메모')->columnSpanFull(),
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
