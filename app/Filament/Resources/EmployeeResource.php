<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = '인사관리';

    protected static ?string $navigationLabel = '직원 관리';

    protected static ?string $modelLabel = '직원';

    protected static ?string $pluralModelLabel = '직원';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('사용자 계정')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('이름')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('이메일')
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email'),
                                Forms\Components\TextInput::make('password')
                                    ->label('비밀번호')
                                    ->password()
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('employee_code')
                            ->label('사원번호')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT)),

                        Forms\Components\Select::make('department_id')
                            ->label('부서')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('position')
                            ->label('직책')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('job_title')
                            ->label('직급')
                            ->maxLength(100),

                        Forms\Components\DatePicker::make('hire_date')
                            ->label('입사일')
                            ->required()
                            ->default(now()),
                    ])->columns(2),

                Forms\Components\Section::make('개인 정보')
                    ->schema([
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('생년월일'),

                        Forms\Components\TextInput::make('phone')
                            ->label('연락처')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('emergency_contact')
                            ->label('비상연락처')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('address')
                            ->label('주소')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('근무 정보')
                    ->schema([
                        Forms\Components\Select::make('employment_type')
                            ->label('고용형태')
                            ->options([
                                '정규직' => '정규직',
                                '계약직' => '계약직',
                                '인턴' => '인턴',
                                '파트타임' => '파트타임',
                            ])
                            ->required()
                            ->default('정규직'),

                        Forms\Components\Select::make('status')
                            ->label('재직상태')
                            ->options([
                                '재직' => '재직',
                                '휴직' => '휴직',
                                '퇴직' => '퇴직',
                            ])
                            ->required()
                            ->default('재직'),

                        Forms\Components\TextInput::make('base_salary')
                            ->label('기본급')
                            ->numeric()
                            ->prefix('₩'),

                        Forms\Components\TextInput::make('annual_leave_days')
                            ->label('연차 일수')
                            ->numeric()
                            ->default(15),

                        Forms\Components\DatePicker::make('resignation_date')
                            ->label('퇴직일')
                            ->visible(fn (Forms\Get $get) => $get('status') === '퇴직'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('사원번호')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('이름')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('부서')
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('직책'),

                Tables\Columns\TextColumn::make('job_title')
                    ->label('직급'),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('고용형태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '정규직' => 'success',
                        '계약직' => 'warning',
                        '인턴' => 'info',
                        '파트타임' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '재직' => 'success',
                        '휴직' => 'warning',
                        '퇴직' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('hire_date')
                    ->label('입사일')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('부서')
                    ->relationship('department', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('재직상태')
                    ->options([
                        '재직' => '재직',
                        '휴직' => '휴직',
                        '퇴직' => '퇴직',
                    ]),

                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('고용형태')
                    ->options([
                        '정규직' => '정규직',
                        '계약직' => '계약직',
                        '인턴' => '인턴',
                        '파트타임' => '파트타임',
                    ]),
            ])
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
