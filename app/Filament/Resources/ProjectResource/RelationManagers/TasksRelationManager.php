<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = null;

    protected static ?string $modelLabel = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('models.task');
    }

    public static function getModelLabel(): string
    {
        return __('models.task');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('fields.title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label(__('fields.description'))
                    ->rows(3)
                    ->columnSpanFull(),

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

                Forms\Components\DatePicker::make('start_date')
                    ->label(__('fields.start_date')),

                Forms\Components\DatePicker::make('due_date')
                    ->label(__('fields.deadline')),

                Forms\Components\TextInput::make('estimated_hours')
                    ->label(__('fields.estimated_hours'))
                    ->numeric()
                    ->suffix('h'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fields.title'))
                    ->searchable()
                    ->limit(40),

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
                    ->color(fn ($record) => $record->is_delayed ? 'danger' : null),

                Tables\Columns\TextColumn::make('actual_hours')
                    ->label(__('fields.actual_hours'))
                    ->suffix('h'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
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
}
