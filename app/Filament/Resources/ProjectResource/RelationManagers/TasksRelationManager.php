<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = '태스크';

    protected static ?string $modelLabel = '태스크';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('제목')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('설명')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Select::make('assigned_to')
                    ->label('담당자')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('status')
                    ->label('상태')
                    ->options([
                        '할일' => '할일',
                        '진행중' => '진행중',
                        '검토중' => '검토중',
                        '완료' => '완료',
                        '보류' => '보류',
                    ])
                    ->default('할일')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->label('우선순위')
                    ->options([
                        '낮음' => '낮음',
                        '보통' => '보통',
                        '높음' => '높음',
                        '긴급' => '긴급',
                    ])
                    ->default('보통')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('시작일'),

                Forms\Components\DatePicker::make('due_date')
                    ->label('마감일'),

                Forms\Components\TextInput::make('estimated_hours')
                    ->label('예상 시간')
                    ->numeric()
                    ->suffix('시간'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '할일' => 'gray',
                        '진행중' => 'info',
                        '검토중' => 'warning',
                        '완료' => 'success',
                        '보류' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('우선순위')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '낮음' => 'gray',
                        '보통' => 'info',
                        '높음' => 'warning',
                        '긴급' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('마감일')
                    ->date('Y-m-d')
                    ->color(fn ($record) => $record->is_delayed ? 'danger' : null),

                Tables\Columns\TextColumn::make('actual_hours')
                    ->label('실제 시간')
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
