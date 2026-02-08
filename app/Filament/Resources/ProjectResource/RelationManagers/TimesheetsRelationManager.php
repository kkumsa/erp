<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Enums\TimesheetStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    protected static ?string $title = null;

    protected static ?string $modelLabel = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('models.timesheet');
    }

    public static function getModelLabel(): string
    {
        return __('models.timesheet');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('fields.user_id'))
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(auth()->id()),

                Forms\Components\Select::make('task_id')
                    ->label(__('fields.task'))
                    ->relationship('task', 'title', function ($query) {
                        return $query->where('project_id', $this->ownerRecord->id);
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('date')
                    ->label(__('fields.date'))
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('hours')
                    ->label(__('fields.hours'))
                    ->numeric()
                    ->required()
                    ->step(0.5)
                    ->suffix('h'),

                Forms\Components\Toggle::make('is_billable')
                    ->label(__('fields.is_billable'))
                    ->default(true),

                Forms\Components\TextInput::make('hourly_rate')
                    ->label(__('fields.hourly_rate'))
                    ->numeric()
                    ->prefix('₩'),

                Forms\Components\Textarea::make('description')
                    ->label(__('fields.description'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('fields.date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('fields.user_id')),

                Tables\Columns\TextColumn::make('task.title')
                    ->label(__('fields.task'))
                    ->limit(30),

                Tables\Columns\TextColumn::make('hours')
                    ->label(__('fields.hours'))
                    ->suffix('h'),

                Tables\Columns\IconColumn::make('is_billable')
                    ->label(__('fields.is_billable'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state instanceof TimesheetStatus ? $state->color() : (TimesheetStatus::tryFrom($state)?->color() ?? 'gray')),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('fields.description'))
                    ->limit(40),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('common.buttons.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === TimesheetStatus::Pending->value)
                    ->action(function ($record) {
                        $record->update([
                            'status' => TimesheetStatus::Approved->value,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
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
