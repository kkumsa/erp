<?php

namespace App\Filament\Resources;

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

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = '프로젝트';

    protected static ?string $navigationLabel = '프로젝트 관리';

    protected static ?string $modelLabel = '프로젝트';

    protected static ?string $pluralModelLabel = '프로젝트';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('프로젝트 정보')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('프로젝트 코드')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('자동 생성'),

                        Forms\Components\TextInput::make('name')
                            ->label('프로젝트명')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_id')
                            ->label('고객')
                            ->relationship('customer', 'company_name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('contract_id')
                            ->label('계약')
                            ->relationship('contract', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('manager_id')
                            ->label('프로젝트 매니저')
                            ->relationship('manager', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('일정')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('시작일')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('종료 예정일')
                            ->afterOrEqual('start_date'),

                        Forms\Components\DatePicker::make('actual_end_date')
                            ->label('실제 종료일'),
                    ])->columns(3),

                Forms\Components\Section::make('예산 및 상태')
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->label('예산')
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '계획중' => '계획중',
                                '진행중' => '진행중',
                                '보류' => '보류',
                                '완료' => '완료',
                                '취소' => '취소',
                            ])
                            ->default('계획중')
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

                        Forms\Components\TextInput::make('progress')
                            ->label('진행률')
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
                Infolists\Components\Section::make('프로젝트 정보')
                    ->id('project-info')
                    ->description(fn ($record) => $record->name)
                    ->schema([
                        Infolists\Components\TextEntry::make('code')
                            ->label('프로젝트 코드'),

                        Infolists\Components\TextEntry::make('name')
                            ->label('프로젝트명'),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label('고객'),

                        Infolists\Components\TextEntry::make('contract.title')
                            ->label('계약'),

                        Infolists\Components\TextEntry::make('manager.name')
                            ->label('프로젝트 매니저'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('설명')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('일정')
                    ->id('project-schedule')
                    ->description(fn ($record) => $record->start_date?->format('Y-m-d') . ' ~ ' . ($record->actual_end_date?->format('Y-m-d') ?? $record->end_date?->format('Y-m-d') ?? '미정'))
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('시작일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label('종료 예정일')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('actual_end_date')
                            ->label('실제 종료일')
                            ->date('Y-m-d')
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->persistCollapsed(),

                Infolists\Components\Section::make('예산 및 상태')
                    ->id('project-budget')
                    ->description(fn ($record) => $record->budget ? '₩' . number_format($record->budget) : '-')
                    ->schema([
                        Infolists\Components\TextEntry::make('budget')
                            ->label('예산')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '계획중' => 'gray',
                                '진행중' => 'info',
                                '보류' => 'warning',
                                '완료' => 'success',
                                '취소' => 'danger',
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

                        Infolists\Components\TextEntry::make('progress')
                            ->label('진행률')
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
                    ->label('코드')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('프로젝트명')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('고객')
                    ->sortable(),

                Tables\Columns\TextColumn::make('manager.name')
                    ->label('PM'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('시작일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('종료 예정일')
                    ->date('Y-m-d')
                    ->color(fn ($record) => $record->is_delayed ? 'danger' : null),

                Tables\Columns\TextColumn::make('progress')
                    ->label('진행률')
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'gray')),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '계획중' => 'gray',
                        '진행중' => 'info',
                        '보류' => 'warning',
                        '완료' => 'success',
                        '취소' => 'danger',
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '계획중' => '계획중',
                        '진행중' => '진행중',
                        '보류' => '보류',
                        '완료' => '완료',
                        '취소' => '취소',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('우선순위')
                    ->options([
                        '낮음' => '낮음',
                        '보통' => '보통',
                        '높음' => '높음',
                        '긴급' => '긴급',
                    ]),

                Tables\Filters\SelectFilter::make('manager_id')
                    ->label('PM')
                    ->relationship('manager', 'name'),
            ])
            ->recordUrl(null)
            ->recordAction('selectProject')
            ->actions([
                Tables\Actions\Action::make('selectProject')
                    ->label('')
                    ->icon('heroicon-m-chevron-right')
                    ->color('gray')
                    ->action(fn (Project $record, $livewire) => $livewire->selectProject($record->id)),
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
