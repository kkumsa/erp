<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = '인사관리';

    protected static ?string $navigationLabel = '휴가 관리';

    protected static ?string $modelLabel = '휴가';

    protected static ?string $pluralModelLabel = '휴가';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('휴가 신청')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('직원')
                            ->relationship('employee', 'employee_code')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->user->name} ({$record->employee_code})")
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('leave_type_id')
                            ->label('휴가 유형')
                            ->relationship('leaveType', 'name')
                            ->required()
                            ->preload(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('시작일')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('종료일')
                            ->required()
                            ->default(now())
                            ->afterOrEqual('start_date'),

                        Forms\Components\TextInput::make('days')
                            ->label('사용 일수')
                            ->numeric()
                            ->required()
                            ->step(0.5)
                            ->default(1),

                        Forms\Components\Textarea::make('reason')
                            ->label('사유')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('승인 정보')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                '대기' => '대기',
                                '승인' => '승인',
                                '반려' => '반려',
                                '취소' => '취소',
                            ])
                            ->default('대기')
                            ->required(),

                        Forms\Components\Select::make('approved_by')
                            ->label('승인자')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('반려 사유')
                            ->visible(fn (Forms\Get $get) => $get('status') === '반려'),
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

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('휴가 유형')
                    ->badge()
                    ->color(fn ($record) => $record->leaveType?->color ?? 'gray'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('시작일')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('종료일')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('days')
                    ->label('일수')
                    ->suffix('일'),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '대기' => 'warning',
                        '승인' => 'success',
                        '반려' => 'danger',
                        '취소' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label('승인자')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('신청일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '대기' => '대기',
                        '승인' => '승인',
                        '반려' => '반려',
                        '취소' => '취소',
                    ]),

                Tables\Filters\SelectFilter::make('leave_type_id')
                    ->label('휴가 유형')
                    ->relationship('leaveType', 'name'),
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

                Tables\Actions\Action::make('reject')
                    ->label('반려')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === '대기')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('반려 사유')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => '반려',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    }),

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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
