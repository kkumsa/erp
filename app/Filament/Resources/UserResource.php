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

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.groups.system_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.labels.user');
    }

    public static function getModelLabel(): string
    {
        return __('models.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.user_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('common.sections.basic_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('fields.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label(__('fields.password'))
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),

                        Forms\Components\Select::make('roles')
                            ->label(__('fields.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make(__('common.sections.additional_settings'))
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true),

                        Forms\Components\Select::make('locale')
                            ->label(__('fields.locale'))
                            ->options([
                                'ko' => __('common.general.korean'),
                                'en' => __('common.general.english'),
                            ])
                            ->default('ko'),

                        Forms\Components\FileUpload::make('avatar_url')
                            ->label(__('fields.avatar_url'))
                            ->image()
                            ->disk('public')
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
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => $record->getInitialsAvatarUrl()),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('fields.email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('fields.roles'))
                    ->badge()
                    ->separator(','),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('fields.roles'))
                    ->relationship('roles', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fields.is_active')),
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
                Infolists\Components\Section::make(__('common.sections.user_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('fields.name')),
                        Infolists\Components\TextEntry::make('email')
                            ->label(__('fields.email')),
                        Infolists\Components\TextEntry::make('roles.name')
                            ->label(__('fields.roles'))
                            ->badge()
                            ->separator(','),
                        Infolists\Components\TextEntry::make('locale')
                            ->label(__('fields.locale')),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('fields.is_active'))
                            ->boolean(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('fields.created_at'))
                            ->dateTime('Y.m.d'),
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
