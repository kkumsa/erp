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

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.system_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.leave_type');
    }

    public static function getModelLabel(): string
    {
        return __('models.leave_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.leave_type_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.leave_type_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.type_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('default_days')
                            ->label(__('fields.default_days'))
                            ->numeric(),

                        Forms\Components\Toggle::make('is_paid')
                            ->label(__('fields.is_paid'))
                            ->default(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\TextInput::make('color')
                            ->label(__('fields.color'))
                            ->maxLength(50),

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
                    ->label(__('fields.type_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(__('fields.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('default_days')
                    ->label(__('fields.default_days'))
                    ->suffix(__('common.general.days_suffix')),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label(__('fields.is_paid'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('color')
                    ->label(__('fields.color'))
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fields.is_active')),

                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label(__('fields.is_paid')),
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
                Infolists\Components\Section::make(__('common.sections.leave_type_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label(__('fields.type_name')),
                        Infolists\Components\TextEntry::make('code')->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('default_days')->label(__('fields.default_days'))->suffix(__('common.general.days_suffix')),
                        Infolists\Components\IconEntry::make('is_paid')->label(__('fields.is_paid'))->boolean(),
                        Infolists\Components\IconEntry::make('is_active')->label(__('fields.is_active'))->boolean(),
                        Infolists\Components\TextEntry::make('color')->label(__('fields.color'))->badge(),
                        Infolists\Components\TextEntry::make('description')->label(__('fields.description'))->columnSpanFull(),
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
