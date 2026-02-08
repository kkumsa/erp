<?php

namespace App\Filament\Resources;

use App\Enums\TimesheetStatus;
use App\Filament\Resources\TimesheetResource\Pages;
use App\Models\Timesheet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class TimesheetResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'timesheet';

    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.project');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.timesheet');
    }

    public static function getModelLabel(): string
    {
        return __('models.timesheet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.timesheet_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.timesheet_info'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('fields.user_id'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('project_id')
                            ->label(__('fields.project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('task_id', null)),

                        Forms\Components\Select::make('task_id')
                            ->label(__('fields.task'))
                            ->options(function (Forms\Get $get) {
                                $projectId = $get('project_id');
                                if (!$projectId) {
                                    return [];
                                }
                                return \App\Models\Task::where('project_id', $projectId)
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder(fn (Forms\Get $get) => $get('project_id') ? __('common.placeholders.select_task') : __('common.placeholders.select_project_first'))
                            ->disabled(fn (Forms\Get $get) => !$get('project_id')),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('fields.date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('hours')
                            ->label(__('fields.hours'))
                            ->numeric()
                            ->required()
                            ->suffix('h'),

                        Forms\Components\Textarea::make('description')
                            ->label(__('fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.billing_and_status'))
                    ->schema([
                        Forms\Components\Toggle::make('is_billable')
                            ->label(__('fields.is_billable'))
                            ->default(false),

                        Forms\Components\TextInput::make('hourly_rate')
                            ->label(__('fields.hourly_rate'))
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(TimesheetStatus::class)
                            ->default(TimesheetStatus::Pending)
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.timesheet_info'))
                    ->id('timesheet-info')
                    ->description(fn ($record) => $record->date?->format('Y-m-d'))
                    ->schema([
                        Infolists\Components\TextEntry::make('date')
                            ->label(__('fields.date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('fields.user_id')),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('fields.project')),

                        Infolists\Components\TextEntry::make('task.title')
                            ->label(__('fields.task')),

                        Infolists\Components\TextEntry::make('hours')
                            ->label(__('fields.hours'))
                            ->suffix('h'),

                        Infolists\Components\IconEntry::make('is_billable')
                            ->label(__('fields.is_billable'))
                            ->boolean(),

                        Infolists\Components\TextEntry::make('hourly_rate')
                            ->label(__('fields.hourly_rate'))
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof TimesheetStatus ? $state->color() : (TimesheetStatus::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('fields.date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('fields.project'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('task.title')
                    ->label(__('fields.task'))
                    ->limit(30),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('fields.user_id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours')
                    ->label(__('fields.hours'))
                    ->suffix('h')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_billable')
                    ->label(__('fields.is_billable'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof TimesheetStatus ? $state->color() : (TimesheetStatus::tryFrom($state)?->color() ?? 'gray')),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(TimesheetStatus::class),

                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('fields.project'))
                    ->relationship('project', 'name'),

                Tables\Filters\TernaryFilter::make('is_billable')
                    ->label(__('fields.is_billable')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
