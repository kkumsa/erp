<?php

namespace App\Filament\Resources;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class ProjectResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'project';

    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.project');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.project');
    }

    public static function getModelLabel(): string
    {
        return __('models.project');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.project_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.project_info'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(__('fields.code'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('common.placeholders.auto_generated')),

                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_id')
                            ->label(__('fields.customer'))
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('contract_id')
                            ->label(__('fields.contract'))
                            ->relationship('contract', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('manager_id')
                            ->label(__('fields.manager_id'))
                            ->relationship('manager', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.schedule'))
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('fields.start_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('fields.end_date'))
                            ->afterOrEqual('start_date'),

                        Forms\Components\DatePicker::make('actual_end_date')
                            ->label(__('fields.actual_end_date')),
                    ])->columns(3),

                Forms\Components\Section::make(__('common.sections.budget_and_status'))
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->label(__('fields.budget'))
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::Planning)
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label(__('fields.priority'))
                            ->options(Priority::class)
                            ->default(Priority::Normal)
                            ->required(),

                        Forms\Components\TextInput::make('progress')
                            ->label(__('fields.progress'))
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(4),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.project_info'))
                    ->id('project-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('code')
                            ->label(__('fields.code')),

                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.name')),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label(__('fields.customer')),

                        Infolists\Components\TextEntry::make('contract.title')
                            ->label(__('fields.contract')),

                        Infolists\Components\TextEntry::make('manager.name')
                            ->label(__('fields.manager_id')),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.schedule'))
                    ->id('project-schedule')
                    ->description(fn ($record) => $record->start_date?->format('Y-m-d') . ' ~ ' . ($record->actual_end_date?->format('Y-m-d') ?? $record->end_date?->format('Y-m-d') ?? '-'))
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label(__('fields.start_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label(__('fields.end_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('actual_end_date')
                            ->label(__('fields.actual_end_date'))
                            ->date('Y.m.d')
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make(__('common.sections.budget_and_status'))
                    ->id('project-budget')
                    ->description(fn ($record) => $record->budget ? '₩' . number_format($record->budget) : '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('budget')
                            ->label(__('fields.budget'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof ProjectStatus ? $state->color() : (ProjectStatus::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('priority')
                            ->label(__('fields.priority'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof Priority ? $state->color() : (Priority::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('progress')
                            ->label(__('fields.progress'))
                            ->suffix('%'),
                    ])
                    ->columns(4)
                    ->collapsible()
                    ->persistCollapsed(),
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
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label(__('fields.customer'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label(__('fields.manager_id')),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('fields.start_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('fields.end_date'))
                    ->date('Y.m.d')
                    ->color(fn ($record) => $record->is_delayed ? 'danger' : null),

                Tables\Columns\TextColumn::make('progress')
                    ->label(__('fields.progress'))
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'gray')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof ProjectStatus ? $state->color() : (ProjectStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('priority')
                    ->label(__('fields.priority'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof Priority ? $state->color() : (Priority::tryFrom($state)?->color() ?? 'gray')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(ProjectStatus::class),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('fields.priority'))
                    ->options(Priority::class),

                Tables\Filters\SelectFilter::make('manager_id')
                    ->label(__('fields.manager_id'))
                    ->relationship('manager', 'name'),
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
            RelationManagers\TasksRelationManager::class,
            RelationManagers\TimesheetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
