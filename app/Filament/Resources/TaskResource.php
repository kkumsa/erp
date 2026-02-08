<?php

namespace App\Filament\Resources;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class TaskResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'task';

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.project');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.task');
    }

    public static function getModelLabel(): string
    {
        return __('models.task');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.task_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.task_info'))
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label(__('fields.project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('milestone_id')
                            ->label(__('fields.milestone'))
                            ->relationship('milestone', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('parent_id')
                            ->label(__('fields.parent_id'))
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('title')
                            ->label(__('fields.title'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description')
                            ->label(__('fields.description'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.assignment_and_status'))
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label(__('fields.assigned_to'))
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(TaskStatus::class)
                            ->default(TaskStatus::Pending)
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label(__('fields.priority'))
                            ->options(Priority::class)
                            ->default(Priority::Normal)
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make(__('common.sections.schedule'))
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('fields.start_date')),

                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('fields.deadline')),

                        Forms\Components\TextInput::make('estimated_hours')
                            ->label(__('fields.estimated_hours'))
                            ->numeric()
                            ->suffix('h'),
                    ])->columns(3),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.task_info'))
                    ->id('task-info')
                    ->description(fn ($record) => $record->title)
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label(__('fields.title')),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('fields.project')),

                        Infolists\Components\TextEntry::make('assignee.name')
                            ->label(__('fields.assigned_to')),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('fields.status'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof TaskStatus ? $state->color() : (TaskStatus::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('priority')
                            ->label(__('fields.priority'))
                            ->badge()
                            ->color(fn ($state) => $state instanceof Priority ? $state->color() : (Priority::tryFrom($state)?->color() ?? 'gray')),

                        Infolists\Components\TextEntry::make('start_date')
                            ->label(__('fields.start_date'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label(__('fields.deadline'))
                            ->date('Y.m.d'),

                        Infolists\Components\TextEntry::make('estimated_hours')
                            ->label(__('fields.estimated_hours'))
                            ->suffix('h'),

                        Infolists\Components\TextEntry::make('actual_hours')
                            ->label(__('fields.actual_hours'))
                            ->suffix('h'),

                        Infolists\Components\TextEntry::make('description')
                            ->label(__('fields.description'))
                            ->html()
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
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('fields.project'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label(__('fields.assigned_to')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof TaskStatus ? $state->color() : (TaskStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('priority')
                    ->label(__('fields.priority'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof Priority ? $state->color() : (Priority::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('fields.deadline'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label(__('fields.estimated_hours'))
                    ->formatStateUsing(fn ($record) => ($record->estimated_hours ?? 0) . 'h / ' . ($record->actual_hours ?? 0) . 'h'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(TaskStatus::class),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('fields.priority'))
                    ->options(Priority::class),

                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('fields.project'))
                    ->relationship('project', 'name'),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
