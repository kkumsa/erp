<?php

namespace App\Filament\Resources;

use App\Enums\AttendanceStatus;
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

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.hr');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.attendance');
    }

    public static function getModelLabel(): string
    {
        return __('models.attendance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.attendance_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.attendance_info'))
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(__('fields.employee_id'))
                            ->relationship('employee', 'employee_code')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name . ' (' . $record->employee_code . ')')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('fields.date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\TimePicker::make('check_in')
                            ->label(__('fields.check_in')),

                        Forms\Components\TimePicker::make('check_out')
                            ->label(__('fields.check_out')),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(AttendanceStatus::class)
                            ->default(AttendanceStatus::Normal)
                            ->required(),

                        Forms\Components\Textarea::make('note')
                            ->label(__('fields.note'))
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
                    ->label(__('fields.employee'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('fields.date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label(__('fields.check_in'))
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label(__('fields.check_out'))
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('work_time')
                    ->label(__('fields.work_time')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(AttendanceStatus::class),
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
                Infolists\Components\Section::make(__('common.sections.attendance_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('employee.user.name')->label(__('fields.employee')),
                        Infolists\Components\TextEntry::make('date')->label(__('fields.date'))->date('Y.m.d'),
                        Infolists\Components\TextEntry::make('check_in')->label(__('fields.check_in'))->time('H:i'),
                        Infolists\Components\TextEntry::make('check_out')->label(__('fields.check_out'))->time('H:i'),
                        Infolists\Components\TextEntry::make('work_time')->label(__('fields.work_time')),
                        Infolists\Components\TextEntry::make('status')->label(__('fields.status'))->badge(),
                        Infolists\Components\TextEntry::make('note')->label(__('fields.note'))->columnSpanFull(),
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
