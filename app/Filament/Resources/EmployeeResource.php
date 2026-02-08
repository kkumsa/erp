<?php

namespace App\Filament\Resources;

use App\Enums\EmployeeStatus;
use App\Enums\EmploymentType;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasResourcePermissions;

class EmployeeResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'employee';

    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.hr');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.employee');
    }

    public static function getModelLabel(): string
    {
        return __('models.employee');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.employee_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.basic_info'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('fields.user_account'))
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('fields.name'))
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label(__('fields.email'))
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email'),
                                Forms\Components\TextInput::make('password')
                                    ->label(__('fields.password'))
                                    ->password()
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('employee_code')
                            ->label(__('fields.employee_code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT)),

                        Forms\Components\Select::make('department_id')
                            ->label(__('fields.department_id'))
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('position')
                            ->label(__('fields.position'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('job_title')
                            ->label(__('fields.job_title'))
                            ->maxLength(100),

                        Forms\Components\DatePicker::make('hire_date')
                            ->label(__('fields.hire_date'))
                            ->required()
                            ->default(now()),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.personal_info'))
                    ->schema([
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('fields.birth_date')),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('fields.phone'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('emergency_contact')
                            ->label(__('fields.emergency_contact'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('address')
                            ->label(__('fields.address'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make(__('common.sections.work_info'))
                    ->schema([
                        Forms\Components\Select::make('employment_type')
                            ->label(__('fields.employment_type'))
                            ->options(EmploymentType::class)
                            ->required()
                            ->default(EmploymentType::FullTime),

                        Forms\Components\Select::make('status')
                            ->label(__('fields.employment_status'))
                            ->options(EmployeeStatus::class)
                            ->required()
                            ->default(EmployeeStatus::Active),

                        Forms\Components\TextInput::make('base_salary')
                            ->label(__('fields.base_salary'))
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\TextInput::make('annual_leave_days')
                            ->label(__('fields.annual_leave_days'))
                            ->numeric()
                            ->default(15),

                        Forms\Components\DatePicker::make('resignation_date')
                            ->label(__('fields.resignation_date'))
                            ->visible(fn (Forms\Get $get) => $get('status') === EmployeeStatus::Resigned->value),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_code')
                    ->label(__('fields.employee_code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('fields.department'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(__('fields.position')),

                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('fields.job_title')),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label(__('fields.employment_type'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray'),

                Tables\Columns\TextColumn::make('hire_date')
                    ->label(__('fields.hire_date'))
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label(__('fields.department'))
                    ->relationship('department', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('fields.employment_status'))
                    ->options(EmployeeStatus::class),

                Tables\Filters\SelectFilter::make('employment_type')
                    ->label(__('fields.employment_type'))
                    ->options(EmploymentType::class),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('common.sections.employee_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('employee_code')->label(__('fields.employee_code')),
                        Infolists\Components\TextEntry::make('user.name')->label(__('fields.name')),
                        Infolists\Components\TextEntry::make('department.name')->label(__('fields.department')),
                        Infolists\Components\TextEntry::make('position')->label(__('fields.position')),
                        Infolists\Components\TextEntry::make('job_title')->label(__('fields.job_title')),
                        Infolists\Components\TextEntry::make('hire_date')->label(__('fields.hire_date'))->date('Y.m.d'),
                        Infolists\Components\TextEntry::make('employment_type')->label(__('fields.employment_type'))->badge(),
                        Infolists\Components\TextEntry::make('status')->label(__('fields.employment_status'))->badge(),
                        Infolists\Components\TextEntry::make('phone')->label(__('fields.phone')),
                        Infolists\Components\TextEntry::make('emergency_contact')->label(__('fields.emergency_contact')),
                        Infolists\Components\TextEntry::make('address')->label(__('fields.address'))->columnSpanFull(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
