<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasResourcePermissions;

class AttendanceResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'attendance';

    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = '인사관리';

    protected static ?string $navigationLabel = '근태 관리';

    protected static ?string $modelLabel = '근태';

    protected static ?string $pluralModelLabel = '근태';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('근태 정보')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('직원')
                            ->relationship('employee', 'employee_code')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name . ' (' . $record->employee_code . ')')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->label('날짜')
                            ->required()
                            ->default(now()),

                        Forms\Components\TimePicker::make('check_in')
                            ->label('출근'),

                        Forms\Components\TimePicker::make('check_out')
                            ->label('퇴근'),

                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '정상' => '정상',
                                '지각' => '지각',
                                '조퇴' => '조퇴',
                                '결근' => '결근',
                                '휴가' => '휴가',
                            ])
                            ->default('정상')
                            ->required(),

                        Forms\Components\Textarea::make('note')
                            ->label('비고')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('직원')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('날짜')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('출근')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('퇴근')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('work_time')
                    ->label('근무시간'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '정상' => 'success',
                        '지각' => 'warning',
                        '조퇴' => 'warning',
                        '결근' => 'danger',
                        '휴가' => 'info',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '정상' => '정상',
                        '지각' => '지각',
                        '조퇴' => '조퇴',
                        '결근' => '결근',
                        '휴가' => '휴가',
                    ]),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('근태 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('employee.user.name')->label('직원'),
                        Infolists\Components\TextEntry::make('date')->label('날짜')->date('Y-m-d'),
                        Infolists\Components\TextEntry::make('check_in')->label('출근')->time('H:i'),
                        Infolists\Components\TextEntry::make('check_out')->label('퇴근')->time('H:i'),
                        Infolists\Components\TextEntry::make('work_time')->label('근무시간'),
                        Infolists\Components\TextEntry::make('status')->label('상태')->badge(),
                        Infolists\Components\TextEntry::make('note')->label('비고')->columnSpanFull(),
                    ])->columns(2),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
