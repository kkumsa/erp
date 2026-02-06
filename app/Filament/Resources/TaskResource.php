<?php

namespace App\Filament\Resources;

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

    protected static ?string $navigationGroup = '프로젝트';

    protected static ?string $navigationLabel = '작업';

    protected static ?string $modelLabel = '작업';

    protected static ?string $pluralModelLabel = '작업';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('작업 정보')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('프로젝트')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('milestone_id')
                            ->label('마일스톤')
                            ->relationship('milestone', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('parent_id')
                            ->label('상위 작업')
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description')
                            ->label('설명')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('담당 및 상태')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('담당자')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '대기' => '대기',
                                '진행중' => '진행중',
                                '검토중' => '검토중',
                                '완료' => '완료',
                                '보류' => '보류',
                            ])
                            ->default('대기')
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
                    ])->columns(3),

                Forms\Components\Section::make('일정')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('시작일'),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('마감일'),

                        Forms\Components\TextInput::make('estimated_hours')
                            ->label('예상 시간')
                            ->numeric()
                            ->suffix('h'),
                    ])->columns(3),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('작업 정보')
                    ->id('task-info')
                    ->description(fn ($record) => $record->title)
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('제목'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('프로젝트'),

                        Infolists\Components\TextEntry::make('assignee.name')
                            ->label('담당자'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '대기' => 'gray',
                                '진행중' => 'info',
                                '검토중' => 'warning',
                                '완료' => 'success',
                                '보류' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('priority')
                            ->label('우선순위')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '낮음' => 'gray',
                                '보통' => 'info',
                                '높음' => 'warning',
                                '긴급' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('start_date')
                            ->label('시작일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label('마감일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('estimated_hours')
                            ->label('예상 시간')
                            ->suffix('h'),

                        Infolists\Components\TextEntry::make('actual_hours')
                            ->label('실제 시간')
                            ->suffix('h'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('설명')
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
                    ->label('제목')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('프로젝트')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('담당자'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '대기' => 'gray',
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label('예상/실제')
                    ->formatStateUsing(fn ($record) => ($record->estimated_hours ?? 0) . 'h / ' . ($record->actual_hours ?? 0) . 'h'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '대기' => '대기',
                        '진행중' => '진행중',
                        '검토중' => '검토중',
                        '완료' => '완료',
                        '보류' => '보류',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('우선순위')
                    ->options([
                        '낮음' => '낮음',
                        '보통' => '보통',
                        '높음' => '높음',
                        '긴급' => '긴급',
                    ]),

                Tables\Filters\SelectFilter::make('project_id')
                    ->label('프로젝트')
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
