<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    protected static ?string $title = '타임시트';

    protected static ?string $modelLabel = '타임시트';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('작업자')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(auth()->id()),

                Forms\Components\Select::make('task_id')
                    ->label('태스크')
                    ->relationship('task', 'title', function ($query) {
                        return $query->where('project_id', $this->ownerRecord->id);
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('date')
                    ->label('날짜')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('hours')
                    ->label('작업 시간')
                    ->numeric()
                    ->required()
                    ->step(0.5)
                    ->suffix('시간'),

                Forms\Components\Toggle::make('is_billable')
                    ->label('청구 가능')
                    ->default(true),

                Forms\Components\TextInput::make('hourly_rate')
                    ->label('시간당 단가')
                    ->numeric()
                    ->prefix('₩'),

                Forms\Components\Textarea::make('description')
                    ->label('작업 내용')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('날짜')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('작업자'),

                Tables\Columns\TextColumn::make('task.title')
                    ->label('태스크')
                    ->limit(30),

                Tables\Columns\TextColumn::make('hours')
                    ->label('시간')
                    ->suffix('h'),

                Tables\Columns\IconColumn::make('is_billable')
                    ->label('청구')
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '대기' => 'warning',
                        '승인' => 'success',
                        '반려' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('작업 내용')
                    ->limit(40),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('승인')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === '대기')
                    ->action(function ($record) {
                        $record->update([
                            'status' => '승인',
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
