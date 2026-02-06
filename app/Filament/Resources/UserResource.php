<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Filament\Traits\HasResourcePermissions;

class UserResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $permissionPrefix = 'user';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = '시스템설정';

    protected static ?string $navigationLabel = '사용자 관리';

    protected static ?string $modelLabel = '사용자';

    protected static ?string $pluralModelLabel = '사용자';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('이름')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('이메일')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('비밀번호')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),

                        Forms\Components\Select::make('roles')
                            ->label('역할')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('추가 설정')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('활성화')
                            ->default(true),

                        Forms\Components\Select::make('locale')
                            ->label('언어')
                            ->options([
                                'ko' => '한국어',
                                'en' => 'English',
                            ])
                            ->default('ko'),

                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('프로필 이미지')
                            ->image()
                            ->directory('avatars')
                            ->visibility('public'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),

                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('이메일')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('역할')
                    ->badge()
                    ->separator(','),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('가입일')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('역할')
                    ->relationship('roles', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화'),
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
                Infolists\Components\Section::make('사용자 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('이름'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('이메일'),
                        Infolists\Components\TextEntry::make('roles.name')
                            ->label('역할')
                            ->badge()
                            ->separator(','),
                        Infolists\Components\TextEntry::make('locale')
                            ->label('언어'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('활성화')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('가입일')
                            ->dateTime('Y-m-d'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
