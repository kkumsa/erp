<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneResource\Pages;
use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class MilestoneResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'project';

    protected static ?string $model = Milestone::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = '프로젝트';

    protected static ?string $navigationLabel = '마일스톤';

    protected static ?string $modelLabel = '마일스톤';

    protected static ?string $pluralModelLabel = '마일스톤';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->label('프로젝트')
                    ->relationship('project', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('마일스톤명')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('설명')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('due_date')
                    ->label('마감일'),

                Forms\Components\Select::make('status')
                    ->label('상태')
                    ->options([
                        '대기' => '대기',
                        '진행중' => '진행중',
                        '완료' => '완료',
                    ])
                    ->default('대기'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('순서')
                    ->numeric(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('마일스톤 정보')
                    ->id('milestone-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('마일스톤명'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('프로젝트'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '대기' => 'gray',
                                '진행중' => 'info',
                                '완료' => 'success',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label('마감일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('completed_date')
                            ->label('완료일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('설명')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('마일스톤명')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('프로젝트')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '대기' => 'gray',
                        '진행중' => 'info',
                        '완료' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('마감일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('작업 수')
                    ->counts('tasks')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
