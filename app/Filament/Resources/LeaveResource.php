<?php

namespace App\Filament\Resources;

use App\Enums\LeaveStatus;
use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasResourcePermissions;

class LeaveResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'leave';

    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.hr');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.leave');
    }

    public static function getModelLabel(): string
    {
        return __('models.leave');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.leave_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.leave_request'))
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(__('fields.employee_id'))
                            ->relationship('employee', 'employee_code')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->user->name} ({$record->employee_code})")
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('leave_type_id')
                            ->label(__('fields.leave_type_id'))
                            ->relationship('leaveType', 'name')
                            ->required()
                            ->preload(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('fields.start_date'))
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('fields.end_date'))
                            ->required()
                            ->default(now())
                            ->afterOrEqual('start_date'),

                        Forms\Components\TextInput::make('days')
                            ->label(__('fields.used_days'))
                            ->numeric()
                            ->required()
                            ->step(0.5)
                            ->default(1),

                        Forms\Components\Textarea::make('reason')
                            ->label(__('fields.reason'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.approval_info'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('fields.status'))
                            ->options(LeaveStatus::class)
                            ->default(LeaveStatus::Pending)
                            ->required(),

                        Forms\Components\Select::make('approved_by')
                            ->label(__('fields.approved_by'))
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('fields.rejection_reason'))
                            ->visible(fn (Forms\Get $get) => $get('status') === LeaveStatus::Rejected->value),
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

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label(__('fields.leave_type'))
                    ->badge()
                    ->color(fn ($record) => $record->leaveType?->color ?? 'gray'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('fields.start_date'))
                    ->date('Y.m.d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('fields.end_date'))
                    ->date('Y.m.d'),

                Tables\Columns\TextColumn::make('days')
                    ->label(__('fields.days'))
                    ->suffix(__('common.general.days_suffix')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label(__('fields.approver'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.application_date'))
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options(LeaveStatus::class),

                Tables\Filters\SelectFilter::make('leave_type_id')
                    ->label(__('fields.leave_type'))
                    ->relationship('leaveType', 'name'),
            ])
            ->recordUrl(null)
            ->recordAction('selectRecord')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('common.buttons.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === LeaveStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => LeaveStatus::Approved,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label(__('common.buttons.reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === LeaveStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('fields.rejection_reason'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => LeaveStatus::Rejected,
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.leave_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('employee.user.name')->label(__('fields.employee')),
                        Infolists\Components\TextEntry::make('leaveType.name')->label(__('fields.leave_type')),
                        Infolists\Components\TextEntry::make('start_date')->label(__('fields.start_date'))->date('Y.m.d'),
                        Infolists\Components\TextEntry::make('end_date')->label(__('fields.end_date'))->date('Y.m.d'),
                        Infolists\Components\TextEntry::make('days')->label(__('fields.days'))->suffix(__('common.general.days_suffix')),
                        Infolists\Components\TextEntry::make('status')->label(__('fields.status'))->badge(),
                        Infolists\Components\TextEntry::make('approver.name')->label(__('fields.approver'))->placeholder('-'),
                        Infolists\Components\TextEntry::make('created_at')->label(__('fields.application_date'))->dateTime('Y.m.d H:i'),
                        Infolists\Components\TextEntry::make('reason')->label(__('fields.reason'))->columnSpanFull(),
                        Infolists\Components\TextEntry::make('rejection_reason')->label(__('fields.rejection_reason'))->columnSpanFull(),
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
