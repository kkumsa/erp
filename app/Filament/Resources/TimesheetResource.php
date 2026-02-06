<?php

namespace App\Filament\Resources;

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

    protected static ?string $navigationGroup = '프로젝트';

    protected static ?string $navigationLabel = '근무기록';

    protected static ?string $modelLabel = '근무기록';

    protected static ?string $pluralModelLabel = '근무기록';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('근무기록 정보')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('작업자')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('project_id')
                            ->label('프로젝트')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive(),

                        Forms\Components\Select::make('task_id')
                            ->label('작업')
                            ->relationship('task', 'title')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->label('날짜')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('hours')
                            ->label('시간')
                            ->numeric()
                            ->required()
                            ->suffix('h'),

                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('청구 및 상태')
                    ->schema([
                        Forms\Components\Toggle::make('is_billable')
                            ->label('청구 가능')
                            ->default(false),

                        Forms\Components\TextInput::make('hourly_rate')
                            ->label('시간당 단가')
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '대기' => '대기',
                                '승인' => '승인',
                                '반려' => '반려',
                            ])
                            ->default('대기')
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('근무기록 정보')
                    ->id('timesheet-info')
                    ->description(fn ($record) => $record->date?->format('Y-m-d'))
                    ->schema([
                        Infolists\Components\TextEntry::make('date')
                            ->label('날짜')
                            ->date('Y-m-d'),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('작업자'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('프로젝트'),

                        Infolists\Components\TextEntry::make('task.title')
                            ->label('작업'),

                        Infolists\Components\TextEntry::make('hours')
                            ->label('시간')
                            ->suffix('h'),

                        Infolists\Components\IconEntry::make('is_billable')
                            ->label('청구 가능')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('hourly_rate')
                            ->label('시간당 단가')
                            ->money('KRW'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('상태')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                '대기' => 'gray',
                                '승인' => 'success',
                                '반려' => 'danger',
                                default => 'gray',
                            }),

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
                Tables\Columns\TextColumn::make('date')
                    ->label('날짜')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('작업자')
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('프로젝트')
                    ->sortable(),

                Tables\Columns\TextColumn::make('task.title')
                    ->label('작업')
                    ->limit(30),

                Tables\Columns\TextColumn::make('hours')
                    ->label('시간')
                    ->suffix('h')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_billable')
                    ->label('청구가능')
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '대기' => 'gray',
                        '승인' => 'success',
                        '반려' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '대기' => '대기',
                        '승인' => '승인',
                        '반려' => '반려',
                    ]),

                Tables\Filters\SelectFilter::make('project_id')
                    ->label('프로젝트')
                    ->relationship('project', 'name'),

                Tables\Filters\TernaryFilter::make('is_billable')
                    ->label('청구 가능'),
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
