<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveTypeResource\Pages;
use App\Models\LeaveType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class LeaveTypeResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'setting';

    protected static ?string $model = LeaveType::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = '시스템설정';

    protected static ?string $navigationLabel = '휴가 유형';

    protected static ?string $modelLabel = '휴가 유형';

    protected static ?string $pluralModelLabel = '휴가 유형';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('휴가 유형 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('유형명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('코드')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('default_days')
                            ->label('기본 일수')
                            ->numeric(),

                        Forms\Components\Toggle::make('is_paid')
                            ->label('유급')
                            ->default(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),

                        Forms\Components\TextInput::make('color')
                            ->label('색상')
                            ->maxLength(50),

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
                    ->label('유형명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('코드')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('default_days')
                    ->label('기본 일수')
                    ->suffix('일'),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('유급')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),

                Tables\Columns\TextColumn::make('color')
                    ->label('색상')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화'),

                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('유급'),
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
                Infolists\Components\Section::make('휴가 유형 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label('유형명'),
                        Infolists\Components\TextEntry::make('code')->label('코드'),
                        Infolists\Components\TextEntry::make('default_days')->label('기본 일수')->suffix('일'),
                        Infolists\Components\IconEntry::make('is_paid')->label('유급')->boolean(),
                        Infolists\Components\IconEntry::make('is_active')->label('활성화')->boolean(),
                        Infolists\Components\TextEntry::make('color')->label('색상')->badge(),
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
            'index' => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'edit' => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
