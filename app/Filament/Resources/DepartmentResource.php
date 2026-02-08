<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class DepartmentResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'department';

    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.hr');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.department');
    }

    public static function getModelLabel(): string
    {
        return __('models.department');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.department_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.department_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.department_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('parent_id')
                            ->label(__('fields.parent_department'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder(__('common.placeholders.none_top_department')),

                        Forms\Components\Select::make('manager_id')
                            ->label(__('fields.department_manager'))
                            ->relationship('manager', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('fields.sort_order'))
                            ->numeric()
                            ->default(0),
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
                    ->label(__('fields.department_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('fields.parent_department'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label(__('fields.department_manager'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('employees_count')
                    ->label(__('fields.employees_count'))
                    ->counts('employees'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),
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
                Infolists\Components\Section::make(__('common.sections.department_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('code')->label(__('fields.code')),
                        Infolists\Components\TextEntry::make('name')->label(__('fields.department_name')),
                        Infolists\Components\TextEntry::make('parent.name')->label(__('fields.parent_department'))->placeholder('-'),
                        Infolists\Components\TextEntry::make('manager.name')->label(__('fields.department_manager'))->placeholder('-'),
                        Infolists\Components\IconEntry::make('is_active')->label(__('fields.is_active'))->boolean(),
                        Infolists\Components\TextEntry::make('sort_order')->label(__('fields.sort_order')),
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
